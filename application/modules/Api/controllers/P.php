<?php

use service\AppCenterService;
use service\AppReportService;
use service\EventTrackerService;
use service\VerifyService;
use Yaf\Exception;

class PController extends BaseController
{
    use \repositories\PayRepository,
        \repositories\ProxyRepository,
        \repositories\UsersRepository;


    //获取订单列表
    public function orderListAction()
    {
        $type = $this->post['type'] ?? 0;
        if(!in_array($type,[0,1,2])){
            throw new \Yaf\Exception('参数错误', 422);
        }
        $return['list'] = $this->getOrder($type);
        $this->showJson($return);
    }

    // 生成支付链接 --- 和前端一起可优化下逻辑
    public function createPAction()
    {
        $date_f = date('Y-m-d H:i:s');
        $member = $this->member;
        $channel = $this->channel;
        $pay_way = $this->post['pw'] ?? '';
        $is_sdk = $this->post['is_sdk'] ?? '0';
        $product_id = (int)$this->post['product_id'];
        $oauth_type = $this->post['oauth_type'];
        $pay_type = isset($this->post['pt'])  ? $this->post['pt'] : "online";
        $verify_code = $this->post['verify_code'] ?? '';
        if (!$product_id || !isset(\OrdersModel::PAY_WAY_MAP_NEW[$pay_way])) {
            throw new \Yaf\Exception('请传入正确的产品id或支付方式', 422);
        }
        if (MemberModel::USER_ROLE_BLACK == $member['role_id']) {
            throw new \Yaf\Exception('订单创建失败，请稍后重试爸爸~');
        }

        //订单频换创建设置
        $key = sprintf('pay:limit:%s', $member['uid']);
        if ((int)redis()->get($key) == 1){
            error_log("限制创建订单,用户ID:" .$member['uid'] . PHP_EOL, 3, APP_PATH . '/storage/logs/order-limit.log');
            return $this->errorJson('订单创建太频繁，30分钟之后再试~');
        }
        $wait_num = OrdersModel::tenWaitNum($member['uuid']);
        if ($wait_num >= 10){
            redis()->setex($key, 1800, 1);
            return $this->errorJson('订单创建太频换，30分钟之后再试~');
        }

        if (false &&  in_array($member['uid'], ['2392411', '2989329',18,'2989613'])) {
            $vfquency = OrdersModel::verifyFrequency([
                ['uuid', '=', $member['uuid']],
                ['order_type', '!=', OrdersModel::TYPE_GAME],
                ['created_at', '>=', strtotime("-1 hours")],//1 小时未支付非游戏单大于等于4笔
            ],4);
            //$vfquency = true;
            if($vfquency ){
                if($verify_code){
                    //verifycode
                    if(!(new VerifyService())->verifyCheck($member['uid'],$verify_code)){
                        return $this->errorJson(VerifyService::VERIFY_CODE_TEXT);
                    }
                }else{
                    if ($oauth_type != 'ios') {
                        return $this->errorJson('多次下单未支付,请更新到最新版本试试', VerifyService::VERIFY_CODE);
                    }
                }
            }
        }

        $product = $this->getProductById($product_id);
        if (empty($product) || $product['status'] != 1) {
            throw new \Yaf\Exception('该产品不存在', 422);
        }


        if ($pay_way == 'pw' && $is_sdk == 1) {//微信支付
            $is_sdk = 0;
        }
        if ($is_sdk == 2) {//微信|支付宝 兼容支付
            $is_sdk = 1;
        }
        //强制安卓 微信支付 h5
        if ($oauth_type == 'android') {
            $is_sdk = 0;
        }

        $data['app_name'] = SYSTEM_ID;
        $data['app_type'] = ($oauth_type == 'pwa') ? 'pc' : $oauth_type;
        $data['aff'] = "{$member['aff']}:{$product_id}";
        $pay_amount = ($product['promo_price'] > 0) ? $product['promo_price'] : $product['price'];
        $data['amount'] = (string)($pay_amount / 100);

        $sign = $this->make_sign_pay($data, config('pay.pay_signkey'));
        $data['ip'] = USER_IP;
        $data['pay_type'] = \OrdersModel::PAY_WAY_MAP_NEW[$pay_way];
        $data['type'] = 'pg' == $pay_way ? 'agent' : $pay_type;
        $data['sign'] = $sign;
        $data['is_sdk'] = $is_sdk;
        $data['product'] = $product['type'] == ProductModel::TYPE_VIP ? 'vip' :'coins';

        $curl = new \tools\CurlService();
        $result = $curl->curlPost(config('pay.pay_url'), $data);
        $result = json_decode($result, true);
        //$result = JAddSlashes($result);

        if (isset($result['success']) && $result['success'] == true) {

            // 返回信息
            $retrun['pUrl'] = isset($result['data']['pay_url']) ? str_replace('&amp;', '&', $result['data']['pay_url']) : '';
            $retrun['pUrl'] = str_replace('&quot;', '"', $retrun['pUrl']);
            $retrun['order_id'] = isset($result['data']['order_id']) ? $result['data']['order_id'] : '';
            $retrun['pay_type'] = $result['data']['type'] ?? 'url';

            if ('pg' == $pay_way) {
                errLog("agentOrder [$date_f]:" . var_export([$this->post, $data, $retrun], 1));
                $this->showJson($retrun);
                return true;
            }

            // 如果代理订单已存在， 直接返回订单地址
            if ($result['data']['channel'] == 'AgentPay') {
                $order = $this->getOrdertFirst('',$result['data']['order_id']);

               if ($order) {
                    $this->showJson($retrun);
                    return true;
                }
            }

            if ($result['data']['channel'] == 'AgentPay'){
                $pay_type = "agent";
            }else{
                $pay_type = "online";
            }
            $order = array(
                'uuid'       => $member['uuid'],
                'product_id' => $product_id,
                'amount'     => $pay_amount,
                'status'     => 0,
                'order_id'   => $result['data']['order_id'],
                'order_type' => $product['type'],
                'channel'    => $result['data']['channel'],
                'descp'      => $product['pname'],
                'payway'     => \OrdersModel::PAY_WAY_MAP_NEW[$pay_way],
                'updated_at' => TIMESTAMP,
                'created_at' => TIMESTAMP,
                'expired_at' => 0,
                'pay_type'   => $pay_type,
                'oauth_type' => $oauth_type,
                'build_id'   => $member['build_id'] ?? $channel,
                'pay_url'    => str_replace('&amp;', '&', $result['data']['pay_url']),
                'platform'   => 0
            );
            $listid = OrdersModel::query()->insert($order);
            // 生成订单存入数据库
            if ($listid) {
                SettingModel::payChannelMerge($result['data']['channel']);
                //上报订单 只报vip类型
                if ($member['build_id']) {
                    $_type = (ProductModel::TYPE_VIP == $order['order_type']) ? 0 : 1;
                    (new AppCenterService())->addOrder($order['order_id'], $order['uuid'], $data['amount'], $order['oauth_type'], $_type,
                        $member['build_id'], $member['invited_by'],0,TIMESTAMP,$member['phone']);
                }
                //数据中心上报 订单创建
                (new AppReportService())->addOrder([
                    'order_id'   => $order['order_id'],//order_sn 全局唯一
                    'uid'        => $member['aff'] ? $member['aff'] : $member['uid'],
                    'oauth_type' => $oauth_type,
                    'amount'     => $data['amount'],//订单金额 （单位元）
                    'product'    => $order['order_type'],
                    'way'        => $order['payway'],
                    'created_at' => $order['created_at'],

                ]);

                //公司上报
                (new EventTrackerService(
                    $this->member['oauth_type'],
                    $this->member['invited_by'],
                    $this->member['uid'],
                    $this->member['oauth_id'],
                    $this->post['device_brand'] ?? '',
                    $this->post['device_model'] ?? ''
                ))->addTask([
                    'event'                 => EventTrackerService::EVENT_ORDER_CREATED,
                    'order_id'              => $order['order_id'],
                    'order_type'            => $product['type'] == ProductModel::TYPE_VIP ? 'vip_subscription' : 'coin_purchase',
                    'product_id'            => (string)$product_id,
                    'product_name'          => $product['pname'],
                    'amount'                => (int)$product['promo_price'],
                    'currency'              => 'CNY',
                    'coin_quantity'         => $product['type'] == ProductModel::TYPE_DIAMOND ? (int)$product['coins'] : 0,
                    'vip_duration_type'     => (string)$product_id,
                    'vip_duration_name'     => $product['pname'],
                    'source_page_key'       => 'user_center',
                    'source_page_name'      => '个人中心',
                    'create_time'           => to_timestamp($order['created_at'])
                ]);

                $this->showJson($retrun);
            } else {
                throw new \Yaf\Exception('插入订单失败,请稍后重试', 422);
            }
        } else {
            throw new \Yaf\Exception('生成订单失败,建议换200充值重试~', 422);
        }
    }

    /**
     * 生成支付签名
     * @param $array
     * @param string $signKey
     * @return string
     */
    public function make_sign_pay($array, $signKey = '')
    {
        if (empty($array)) {
            return '';
        }
        $string = '';
        foreach ($array as $key => $val) {
            $string .= $val;
        }

        $string = md5($string . $signKey);
        return $string;
    }

    /**
     * 上传成功支付截图
     * @param $array
     * @param string $signKey
     * @return string
     */
    public function uploadImgPaysucessAction()
    {
        $order_id = $this->post['order_id'] ?? '';
        $img_url = $this->post['img_url'] ?? '';


        if (empty($order_id) || empty($img_url)){
            $this->showJson([], 0, '请上传订单号和支付成功的截图');
            return;
        }

        $order = $this->getOrdertFirst('',$order_id);
        if (empty($order)){
             $this->showJson([], 0, '订单不存在');
            return;
        }

        $this->setOrdersImg($order_id,$img_url);
        $this->showJson([], 1, '上传成功');
    }

    /**
     * 获取用户提现账号
     * @desc 用于获取用户提现账号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].id 账号ID
     * @return string info[].type 账号类型
     * @return string info[].account_bank 银行名称
     * @return string info[].account 账号
     * @return string info[].name 姓名
     * @return string msg 提示信息
     */
        public function getAccountAction() {

        $data = $this->getAccount($this->member['uid']);
        return $this->showJson($data);
    }

    /**
     * 用户提现账号
     * @desc 用于获取用户提现账号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function addAccountAction()
    {
        $validator = \helper\Validator::make($this->post, [
            'account_bank' => 'required|min:4',
            'account'      => 'required',
            'name'         => 'required|min:1',
        ]);
        if ($validator->fail($msg)) {
            return $this->errorJson('参数错误'.$msg);
        }

        //$type           =   $this->post['type'];
        $type = 1;
        $account_bank = $this->post['account_bank'] ?? '';
        $account = $this->post['account'] ?? '';
        $name = $this->post['name'] ?? '';

        $data = [
            'uid'          => $this->member['uid'],
            'type'         => $type,
            'account_bank' => $account_bank,
            'account'      => $account,
            'name'         => $name,
            'addtime'      => time(),
        ];

        $result = $this->addAccount($data);
        $data['id'] = $result->id;
        if (!$result) {
            return $this->errorJson('添加失败，请重试！');
        }
        return  $this->showJson(['success' => true, 'msg' => '添加成功', 'data' => $data]);
    }


    /**
     * 删除用户提现账号
     * @desc 用于删除用户提现账号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function delAccountAction() {
        $id =  $this->post['id'] ? intval($this->post['id']) : 0;
        if( $id == 0)
           throw new \Yaf\Exception('传入要删除的账号');


        $result = $this->delAccount($id,$this->member['uid']);
        if(!$result)
            throw new \Yaf\Exception('删除失败，请重试！');

        $this->showJson(['success'=>true,'msg'=>'删除成功']);
    }
}