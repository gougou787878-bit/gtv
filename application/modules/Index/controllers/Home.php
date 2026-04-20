<?php

/**
 * 主页,常用操作
 * Class IndexController
 */
class HomeController extends IndexController
{

    public function init()
    {

        parent::init();

    }


    public function headAction()
    {
        $this->getView()
            ->display('Public/head.phtml');
    }

    public function footerAction()
    {
        $this->getView()
            ->display('Public/footer.phtml');
    }

    public function reportAction()
    {
        $data = $_GET;
        //var_dump($data);die();
        $s = $data['sign'];
        unset($data['sign'], $data['m'], $data['a']);
        $sign = $this->sign($data);
        if ($s != $sign) {
            echo json_encode(['code' => 0, 'msg' => 'access deny']);exit(0);
        }
        date_default_timezone_set('Asia/Shanghai');
        $start_date = date('Y-m-d', strtotime('-1 day'));
        $start = strtotime(date('Y-m-d', strtotime('-1 day')));
        $end = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
        $monthStart = strtotime(date('Y-m', $start));

        $order = OrdersModel::query()
            ->where('status', OrdersModel::STATUS_SUCCESS)
            ->where('order_type','!=', OrdersModel::TYPE_GAME)
            ->where('updated_at', '>=', $start)
            ->where('updated_at', '<=', $end);

        $month = OrdersModel::query()
            ->where('status', OrdersModel::STATUS_SUCCESS)
            ->where('updated_at', '>=', $monthStart)
            ->where('order_type','!=', OrdersModel::TYPE_GAME)
            ->sum('pay_amount');


        $total = MemberLogModel::query()->select('id')->orderBy('id', 'desc')->first();
        $reg = MemberModel::query()
            ->where('regdate', '>=', $start)
            ->where('regdate', '<=', $end)
            ->count();
        $active = MemberLogModel::query()->where('lastactivity', '>=', $start)->count();
        $count = $order->count();
        $amount = $order->sum('pay_amount');
        //game
        $game_amount = 0;
        $game_draw_amount = 0;
        $game_trans_amount = 0;
        $data = [
            'code' => 1,
            'data' => [
                'activity'          => $active,
                'reg'               => $reg,
                'total'             => $total->id,
                'amount'            => intval($amount / 100),
                'count'             => $count,
                'month'             => intval($month / 100),
                'game_amount'       => intval($game_amount / 100),
                'game_draw_amount'  => intval($game_draw_amount),
                'game_trans_amount' => intval($game_trans_amount)-intval($game_amount / 100),
            ]
        ];
        echo json_encode($data);
    }

    private function sign(array $items)
    {
        ksort($items);
        $strings = '';
        foreach ($items as $key => $item) {
            $strings .= "{$key}={$item}&";
        }
        $strings = rtrim($strings, '&') . config('app.data_sync_key');
        return md5(hash('sha256', $strings));
    }

    private function coins_to_rmb_format($coins): string
    {
        return number_format($coins / 10, 2, '.', '');
    }

    /**
     * 内部细分上报统计
     */
    public function dataAction()
    {
        $data = $_GET;
        //var_dump($data);die();
        $s = $data['sign'];
        unset($data['sign'], $data['m'], $data['a']);
        $sign = $this->sign($data);
        if ($s != $sign) {
            echo json_encode(['code' => 0, 'msg' => 'access deny']);
            exit(0);
        }
        date_default_timezone_set('Asia/Shanghai');
        $start = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
        $end = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
        $charge_and = OrdersModel::query()
            ->where('status', OrdersModel::STATUS_SUCCESS)
            ->where('oauth_type', '=', 'android')
            ->where('updated_at', '>=', $start)
            ->where('updated_at', '<=', $end)
            ->sum('pay_amount');
        $charge_pwa = OrdersModel::query()
            ->where('status', OrdersModel::STATUS_SUCCESS)
            ->where('oauth_type', '=', 'pwa')
            ->where('updated_at', '>=', $start)
            ->where('updated_at', '<=', $end)->sum('pay_amount');;
        $charge_ios = 0;
        /*$charge_ios = OrdersModel::query()
            ->where('status', OrdersModel::STATUS_SUCCESS)
            ->where('oauth_type', '=', 'ios')
            ->where('order_type', '!=', OrdersModel::TYPE_GAME)
            ->where('updated_at', '>=', $start)
            ->where('updated_at', '<=', $end)->sum('pay_amount');*/
        $reg_and = MemberModel::query()
            ->where('oauth_type', '=', 'android')
            ->where('regdate', '>=', $start)
            ->where('regdate', '<=', $end)
            ->count('uid');
        $reg_ios = 0;
        /*$reg_ios = MemberModel::query()
            ->where('oauth_type', '=', 'ios')
            ->where('regdate', '>=', $start)
            ->where('regdate', '<=', $end)
            ->count('uid');*/
        $reg_pwa = MemberModel::query()
            ->where('oauth_type', '=', 'pwa')
            ->where('regdate', '>=', $start)
            ->where('regdate', '<=', $end)
            ->count('uid');

        $start1 = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $end1 = date('Y-m-d 23:59:59', strtotime('-1 day'));
        $ai_draw_consumption = 0;
        $ai_draw_ct = 0;
        $ai_image_to_video_consumption = 0;
        $ai_image_to_video_ct = 0;
        $ai_strip_consumption = 0;
        $ai_strip_ct = 0;

        $ai_image_change_face_consumption = MemberFaceModel::query()
            ->whereBetween('created_at', [$start1, $end1])
            ->where('type', MemberFaceModel::TYPE_COINS)
            ->sum('coins');
        $ai_image_change_face_ct = MemberFaceModel::query()
            ->whereBetween('created_at', [$start1, $end1])
            ->count();

        $ai_video_change_face_consumption = 0;
        $ai_video_change_face_ct = 0;

        $ai_novel = 0;
        $ai_novel_ct = 0;

        $ai_girlfriend_consumption = 0;
        $ai_girlfriend_ct = 0;

        $data = [
            'code' => 1,
            'data' => [
                'reg_and'    => $reg_and,
                'reg_ios'    => $reg_ios,
                'reg_pwa'    => $reg_pwa,
                'charge_and' => intval($charge_and / 100),
                'charge_ios' => intval($charge_ios / 100),
                'charge_pwa' => intval($charge_pwa / 100),
                'ai_draw'                 => $this->coins_to_rmb_format($ai_draw_consumption),
                'ai_draw_ct'              => $ai_draw_ct,
                'ai_image_to_video'       => $this->coins_to_rmb_format($ai_image_to_video_consumption),
                'ai_image_to_video_ct'    => $ai_image_to_video_ct,
                'ai_strip'                => $this->coins_to_rmb_format($ai_strip_consumption),
                'ai_strip_ct'             => $ai_strip_ct,
                'ai_image_change_face'    => $this->coins_to_rmb_format($ai_image_change_face_consumption),
                'ai_image_change_face_ct' => $ai_image_change_face_ct,
                'ai_video_change_face'    => $this->coins_to_rmb_format($ai_video_change_face_consumption),
                'ai_video_change_face_ct' => $ai_video_change_face_ct,
                'ai_girlfriend'           => $this->coins_to_rmb_format($ai_girlfriend_consumption),
                'ai_girlfriend_ct'        => $ai_girlfriend_ct,
                'ai_novel'                => $this->coins_to_rmb_format($ai_novel),
                'ai_novel_ct'             => $ai_novel_ct,
            ]
        ];
        echo json_encode($data);
    }

    /**
     * tg 机器人半小时内 ios下载量统计
     */
    public function botAction()
    {
        $ios_count_and_and = cached('iostj')->serializerJSON()->expired(200)->fetch(function () {
            $total = MemberModel::where([
                //['oauth_type', '=', 'ios'],
                ['regdate', '>=', strtotime("-30 minutes")],
            ])->select(['uid', 'oauth_type'])->get();
            $ios = collect($total)->where('oauth_type', '=', 'ios')->count();
            $and = collect($total)->where('oauth_type', '=', 'android')->count();
            $pwa = collect($total)->count() - $ios - $and;
            return [
                'ios'=>$ios,
                'android'=>$and,
                'pwa'     => $pwa,
            ];
        });
        echo json_encode($ios_count_and_and);
    }

    public function versionAction()
    {

        $pkg_type = trim($_POST['pkg_type'] ?? '');//plist apk testflight
        $version = trim($_POST['version'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $sha256 = trim($_POST['sha256'] ?? '');
        if (!$pkg_type ||!$address) {
            return;
        }

        //$str = json_encode(['code' => 0, 'msg' => "服务端特殊处理，暂停更新|地址{$address}"], 320);
        //echo $str;
        //die;
        $via = '';
        $type = '';
        if ($pkg_type == 'plist') {
            $via = VersionModel::CHAN_PG;
            $type = VersionModel::TYPE_IOS;
        } elseif ($pkg_type == 'apk') {
            $type = VersionModel::TYPE_ANDROID;
        } elseif ($pkg_type == 'testflight') {
            $via = VersionModel::CHAN_PG;
            $type = VersionModel::TYPE_IOS;
        }

        $where = [
            ['type', '=', $type],
            ['status', '=', 1],
        ];
        if ($version){
            $where[] = ['version' , '=' , $version];
        }
        if ($via) {
            //$where[] = ['via', '=', $via];
        }
        $address = TB_APP_DOWN_URL . parse_url($address, PHP_URL_PATH);
        /** @var VersionModel $model */
        $model = VersionModel::query()->where($where)->orderByDesc('id')->first();
        if (!empty($model)) {
            $flag = $model->update(['apk' => $address, 'sha256' => $sha256]);
        } else {
            $flag = false;
            if (!empty($version)) {
                $flag = VersionModel::insert([
                    'version'    => $version,
                    'type'       => $type,
                    'apk'        => $address,
                    'tips'       => "【新版本来啦】99%爸爸已经下载最新版本~",
                    "must"       => 0,
                    "created_at" => time(),
                    'via'        => $via,
                    'status'     => 1,//启用
                    'sha256'     => $sha256
                ]);
            }
        }

        if ($flag) {
            VersionModel::clearVersionCache(VersionModel::TYPE_IOS);
            VersionModel::clearVersionCache(VersionModel::TYPE_ANDROID);
            jobs([VersionModel::class, 'defend_apk'], [$address]);
            $str = json_encode(['code' => 1, 'msg' => $address], 320);
            VersionModel::report_apk($address);
        }else{
            $str = json_encode(['code' => 0, 'msg' => '更换失败'], 320);
        }
        echo $str;
    }
    /**
     *兑换码
     */
    public function create_codeAction()
    {
        //MessageModel::createSendAppVIPMessage('xblue');return ;
        if(!$this->getRequest()->isPost()){
            echo json_encode(['code' => 0, 'msg' => 'access deny']);
            exit(0);
        }
        $data = $_POST;
        $sign = $_POST['sign'] ?? '';
        $timestamp = $_POST['timestamp'] ?? '';
        //YJgzi43IutF4DgST
        unset($data['sign'], $data['m'], $data['a']);
        if($sign != md5(hash('sha256', $timestamp.'YJgzi43IutF4DgST'))){
            echo json_encode(['code' => 0, 'msg' => 'access deny']);
            exit(0);
        }
        $app = $data['app'] ?? '';
        if ($app == 'xblue') {
            /** @var ExchangeCodeModel $model */
            $model = ExchangeCodeModel::createCode();
            if (!is_null($model)) {
                echo json_encode([
                    'code' => 200,
                    'msg'  => 'success',
                    'data' => [
                        'code' => $model->code,
                        'app_name'=>'GTV',
                        'download'=>getShareURL(),
                    ]
                ]);
                exit(0);
            }
        }
        echo json_encode(['code' => 0, 'msg' => 'access deny']);

    }

    public function package_nameAction(){
        //$data = $_REQUEST;
        if(!$this->getRequest()->isPost()){
            exit('1');
        }
        $inputString = file_get_contents("php://input");
        $data = explode(PHP_EOL,$inputString );
        //$data = array_slice($data,count($data)>=2?-2:0);
        //VersionModel::addBound($data);
        //redis()->sAddArray(VersionModel::VERSION_BOUND,$data);
        //$p =  redis()->sMembers(VersionModel::VERSION_BOUND);
        $p =[];
        errLog('package_nameAction'.var_export([$data,$p],1));
    }
}
