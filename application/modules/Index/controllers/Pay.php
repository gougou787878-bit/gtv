<?php

use service\AppCenterService;
use service\AppReportService;
use service\EventTrackerService;
use service\MarketingLotteryTriggerDispatcher;
use service\ProxyService;

/**
 * 回调
 */
class PayController extends SiteController
{
    use \repositories\PayRepository,
        \repositories\ProxyRepository;

    /**
     * 支付回调
     */
    public function notifyAction()
    {
        if (!$this->getRequest()->isPost()) {
            echo 'failed';return ;
        }
        $this->config = \Yaf\Registry::get('config');
        $signdata = $data = JAddSlashes($_POST);
        NotifyLogModel::addByPay($data['order_id'],json_encode($_POST));
        /*$signdata = $data = array(
            "order_id" => "3484a342-e797-3078-a113-4d1187af9bef",
            "third_id" => "3484a342-e797-3078-a113-4d1187af9bef",
            "pay_money" => "52",
            "pay_time" => "1556972314",
            "success" => "200",
            "sign" => "9030e149a0b49cd0e7a6a0436aa303f9"
        );

        //new agent pay

        Array
(
    [order_id] => ag_20210802170846094737
    [third_id] => OPL202108021708026213
    [pay_money] => 2000
    [product] => vip
    [pay_time] => 1627895326
    [success] => 200
    [channel] => AgentPay
    [aff] => 7143433
    [sign] => c89a76e9b55b8f81644738f0c6f481ab
)
unset($signdata['build_id']);//这个build_id 可能会有
        */
        error_log('订单回调数据\r\n' . print_r($data, true), 3, $this->logFile);
        if (isset($data['success']) && $data['success'] == 200) { //付款成功
            unset($signdata['sign']);
            if (isset($signdata['build_id'])) {
                unset($signdata['build_id']);
            }
            $sign = $this->make_sign_callbak($signdata, config('pay.callback_key'));
            if ($sign != $data['sign']) {
                error_log('签名验证失败' . print_r($data, true), 3, $this->logFile);
                return;
            }
            if (isset($data['channel']) && $data['channel'] == 'AgentPay' && isset($data['aff']) && $data['aff']) {//game agentPay
                //代理订单处理
                $this->agentPayDone($data);
                return;
                //AdminLogModel::addCreate('回调', var_export($data, true));
                //return;
            }

            $order = \OrdersModel::useWritePdo()->where('order_id', $data['order_id'])->first();
            if (is_null($order)) {
                error_log('订单不存在', 3, $this->logFile);
                echo 'success';
                return;
            }
            $order = $order->toArray();
            if ($order['status'] == OrdersModel::STATUS_SUCCESS) {
                error_log('订单已是成功付款状态', 3, $this->logFile);
                echo 'success';
                return;
            }

            $product = \ProductModel::query()->where('id', $order['product_id'])->first();
            if (is_null($product)) {
                error_log('产品不存在', 3, $this->logFile);
                return;
            }
            $product = $product->toArray();

            \DB::beginTransaction();

            $order_amount = $order['amount'];
            $real_pay_amount = $data['pay_money'] * 100;
            $updateMember = 0;//用户更新信息标识 默认
            //如果误差范围4元之内,都视为正常
            if ($order_amount > 0 && ($real_pay_amount >= ($order_amount - 400))) {
                //实际支付金额
                $updateOrder = [
                    'updated_at' => $data['pay_time'],
                    'pay_amount' => $real_pay_amount,
                    'app_order'  => $data['third_id'],
                    'status'     => OrdersModel::STATUS_SUCCESS,
                ];
                $resultOrder = \OrdersModel::query()->where('order_id', $data['order_id'])->update($updateOrder);
                /** @var MemberModel $memberInfo */
                $memberInfo = \MemberModel::query()->where('uuid', $order['uuid'])->first();
                $user_expired_at = $memberInfo->expired_at;
                $user_aff = $memberInfo->aff;

                if ($product['type'] == OrdersModel::TYPE_VIP) {//冲天数
                    $log = true;
                    $present = 0;
                    $period_at = ($product['valid_date'] + $present) * 86400 + max($user_expired_at, TIMESTAMP);
                    if ($period_at > 2147483647){
                        $period_at = 2147483647 - rand(1,10);
                    }
                    $updateMemberData = ['expired_at' => $period_at, 'vip_level' => $product['vip_level']];
                    $presetGold = 0;
                    $freeCoins = $product['free_coins'] ?? 0;
                    $presetGold = $presetGold+$freeCoins;
                    if ($presetGold) {//冲vip 送金币
                        $updateMemberData['coins'] = $memberInfo->coins + $presetGold;
                        $updateMemberData['coins_total'] = $memberInfo->coins_total + $presetGold;
                    }
                    //errLog("updateMember:".var_export($updateMemberData,true));
                    $updateMember = \MemberModel::query()->where('uuid', $order['uuid'])->update($updateMemberData);
                    //$memberInfo->invited_by && $log = $this->addAmountToPreLevels($user_aff, $order_amount, '充值成功');
                    //全新代理模式 1级 简单直接 非渠道用户
                    if (($memberInfo->invited_by && empty($memberInfo->build_id)) ||
                        ($memberInfo->invited_by && $memberInfo->build_id && !isChannel($memberInfo->build_id))) {

                        ProxyService::tuiProxyDetail($user_aff, $memberInfo->invited_by, $data['pay_money'],
                            $order['order_id']);

                        //创作者每月推广绩效统计
                        if($creator = MemberModel::query()->where('uid',$memberInfo->invited_by)->first()){
                            MemberMakerStatModel::addStat($creator->uuid,MemberMakerStatModel::FIELD_TUI,$data['pay_money']* UserProxyCashBackDetailModel::TUI_RATE);
                        }
                    }
                    $presetGold && UsersCoinrecordModel::insert([
                        "type"      => 'income',
                        "action"    => 'buyvipsend',
                        "uid"       => $memberInfo->uid,
                        "touid"     => $memberInfo->uid,
                        "giftid"    => $product['id'],
                        "giftcount" => 1,
                        "totalcoin" => $presetGold,
                        "showid"    => 0,
                        "addtime"   => TIMESTAMP
                    ]);
                    async_task_cgi(function () use ($memberInfo) {
                        // 异步执行，错误了不影响整体
                        //兑换码 送3天会员
                        MessageModel::createSendAppVIPMessage($memberInfo->uuid);
                    });
                }
                elseif ($product['type'] == OrdersModel::TYPE_GLOD) {//充金币
                    $toSend = $product['coins'] + $product['free_coins'];
                    $present = 0;//冲钻送冲钻
                    $presentVip = 0;//冲钻送vip
                    $totalSend = $present + $toSend;
                    $addCoins = $totalSend + $memberInfo->coins;
                    $addTotalCoins = $totalSend + $memberInfo->coins_total;
                    $updateData = [
                        'coins'=>$addCoins,
                        'coins_total'=>$addTotalCoins,
                    ];
                    if ($presentVip) {
                        $period_at = $presentVip * 86400 + max($user_expired_at, TIMESTAMP);
                        $updateData['expired_at'] = $period_at;
                        $updateData['vip_level'] = MemberModel::VIP_LEVEL_MOON;
                    }
                    $updateMember = \MemberModel::query()->where('uuid', $order['uuid'])->update($updateData);
                    $log = UsersCoinrecordModel::addIncome(
                        'recharge', $memberInfo->uid, null, $toSend, $product['id'], 0, "充值金币"
                    );
                }
                //观影券
                if ($product['ticket']) {
                    MvTicketModel::sendUserTicket($memberInfo->uid, null, $product['ticket']);
                }
                // 收费视频免费看
                if ($product['free_day'] > 0) {
                    FreeMemberModel::createInit($memberInfo->uid, $product['free_day']);
                }
                //AI免费次数
                if ($product['free_ai_num']){
                    MemberAiFreeModel::setRecord($memberInfo->aff, $product['free_ai_num']);
                }

                //图生视频免费次数
                if ($product['free_aimagic_num']){
                    MemberAiMagicFreeModel::setRecord($memberInfo->aff, $product['free_aimagic_num']);
                }

                //脱衣免费次数
                if ($product['free_strip_num']){
                    MemberStripFreeModel::setRecord($memberInfo->aff, $product['free_strip_num']);
                }

                //视频下载次数
                if ($product['download_num']){
                    UserDownloadModel::addDownloadNum($memberInfo->aff, $product['download_num'], $data['pay_money']);
                }
                // -------------- end
            }
            if ($updateMember && $log && $resultOrder) {
                \DB::commit();
                MarketingLotteryTriggerDispatcher::trigger(
                    'pay_success',
                    MarketingLotteryTriggerDispatcher::buildPayPayload('notify', $data, $order, $product, $memberInfo)
                );
                //新用户订单数、订单金额统计
                if ($memberInfo->regdate >= strtotime(date('Y-m-d'))){
                    \SysTotalModel::incrBy('pay-amount-new', $order_amount);
                    \SysTotalModel::incrBy('pay-account-new');
                }
                MemberModel::clearFor($memberInfo->toArray());
                OrdersModel::clearFor($memberInfo->toArray());
                //上报 只vip 类型单子
                if ($order['build_id']) {
                    (new AppCenterService())->updateOrder($order['order_id'], $data['pay_money'],1,$data['pay_time'],$order);
                }
                //数据中心 订单更新上报
                (new AppReportService())->updateOrder([
                    'order_id'   => $order['order_id'],
                    'third_id'   => $data['third_id'],
                    'pay_amount' => $data['pay_money'],//支付金额（单位元）
                    'payed_at'   => $data['pay_time']
                ]);

                //公司上报
                (new EventTrackerService(
                    $memberInfo->oauth_type,
                    $memberInfo->invited_by,
                    $memberInfo->uid,
                    $memberInfo->oauth_id
                ))->addTask([
                    'event'                 => EventTrackerService::EVENT_ORDER_PAID,
                    'order_id'              => $order['order_id'],
                    'order_type'            => $product['type'] == ProductModel::TYPE_VIP ? 'vip_subscription' : 'coin_purchase',
                    'product_id'            => (string)$product['id'],
                    'product_name'          => $product['pname'],
                    'amount'                => (int)$product['promo_price'],
                    'currency'              => 'CNY',
                    'coin_quantity'         => $product['type'] == ProductModel::TYPE_DIAMOND ? (int)$product['coins'] : 0,
                    'vip_expiration_time'   => $product['type'] == ProductModel::TYPE_VIP ? ($product['valid_date'] * 86400 + max($memberInfo->expired_at,TIMESTAMP)) : 0,
                    'pay_type'              => $order['payway'],
                    'pay_channel'           => $order['channel'],
                    'transaction_id'        => $data['third_id'],
                    'create_time'           => to_timestamp($data['pay_time'])
                ]);

                echo "success";
            } else {
                error_log(var_export(['updateMember' => $updateMember, 'log' => $log, 'resultOrder' => $resultOrder], true) . PHP_EOL, 3, $this->logFile);
                error_log('回调失败' . PHP_EOL, 3, $this->logFile);
                \DB::rollBack();
                echo "failed";
            }
        }
    }

    /**
     * 游戏 agentPay回调处理
     * @param $data
     *
     * order_id    string    2d9457ff-90dc-3eb3-8ee8-f40c3bb76405    唯一订单号
     * third_id    string    20190603185141baccd0    第三方支付ID
     * pay_money    string    20    实际支付金额（元）
     * pay_time    int    1558454400    回调时间戳（支付时间）
     * success    int    200    200为成功
     * channel    string    200    AgentPay
     * aff    string    200    123
     * sign    string    18d9bca232d47a80702e85c6632b7dc1    签名
     */
    private function agentPayDone($data)
    {
        if(in_array($data['order_id'],['y_20230316124409043594','ag_20230316133700032536'])){//手动处理的代理订单 直接返回
            die("success");
            return false;
        }

        if(stripos($data['aff'],':')===false){//不存在
            die("fail");
            return false;
        }
        list($aff,$product_id) = @explode(':',$data['aff']);
        /** @var MemberModel $memberInfo */
        $memberInfo = MemberModel::where('aff', $data['aff'])->first();
        if (is_null($memberInfo)) {
            return;
        }
        /** @var ProductModel $product */
        $product = \ProductModel::query()->where('id', $product_id)->first();
        if (is_null($product)) {
            error_log('代理产品不存在'.PHP_EOL, 3, $this->logFile);
            return;
        }
        $member = $memberInfo->toArray();
        $payMoney = $data['pay_money'];
        $order_amount = $product->promo_price?$product->promo_price:$product->promo_price;
        $real_pay_amount = $data['pay_money'] * 100;
        //如果误差范围4元之内,都视为正常
        if ($order_amount > 0 && ($real_pay_amount >= ($order_amount - 400))) {

        }else{
            error_log('代理产品支付金额不固定有误差#'.var_export([$data,$product->promo_price],true), 3, APP_PATH . '/storage/logs/'.date('Y-m-d').'-log.log');
            die("failed");
            return false;
        }

        $order = array(
            'uuid'       => $member['uuid'],
            'product_id' => $product->id,
            'amount'     => $payMoney * 100,
            'status'     => OrdersModel::STATUS_SUCCESS,
            'order_id'   => $data['order_id'],
            'order_type' => $product->type,
            'channel'    => $data['channel'],
            'descp'      => $product->pname,
            'payway'     => 'agent',
            'updated_at' => $data['pay_time'],
            'created_at' => TIMESTAMP,
            'expired_at' => 0,
            'pay_type'   => 'agent',
            'oauth_type' => $member['oauth_type'],
            'build_id'   => $memberInfo->build_id,
            'pay_amount' => $payMoney * 100,
            'app_order'  => $data['third_id'],
            'pay_url'    => $member['aff'] . '-' . $member['uuid'],
        );
        if (OrdersModel::insert($order)) {
            $product = $product->toArray();
            $user_expired_at = $memberInfo->expired_at;
            $user_aff = $memberInfo->aff;
            if ($product['type'] == OrdersModel::TYPE_VIP) {//冲天数
                $log = true;
                $present = 0;
                $period_at = ($product['valid_date'] + $present) * 86400 + max($user_expired_at, TIMESTAMP);
                $updateMemberData = ['expired_at' => $period_at, 'vip_level' => $product['vip_level']];
                $presetGold = 0;
                $freeCoins = $product['free_coins'] ?? 0;
                $presetGold = $presetGold+$freeCoins;
                if ($presetGold) {//冲vip 送金币
                    $updateMemberData['coins'] = $memberInfo->coins + $presetGold;
                    $updateMemberData['coins_total'] = $memberInfo->coins_total + $presetGold;
                }
                //errLog("updateMember:".var_export($updateMemberData,true));
                $updateMember = \MemberModel::query()->where('uuid', $order['uuid'])->update($updateMemberData);
                //$memberInfo->invited_by && $log = $this->addAmountToPreLevels($user_aff, $order_amount, '充值成功');
                //全新代理模式 1级 简单直接 非渠道用户
                if (($memberInfo->invited_by && empty($memberInfo->build_id)) ||
                    ($memberInfo->invited_by && $memberInfo->build_id && !isChannel($memberInfo->build_id))) {

                    ProxyService::tuiProxyDetail($user_aff, $memberInfo->invited_by, $data['pay_money'],
                        $order['order_id']);

                    //创作者每月推广绩效统计
                    if($creator = MemberModel::query()->where('uid',$memberInfo->invited_by)->first()){
                        MemberMakerStatModel::addStat($creator->uuid,MemberMakerStatModel::FIELD_TUI,$data['pay_money']* UserProxyCashBackDetailModel::TUI_RATE);
                    }
                }
                $presetGold && UsersCoinrecordModel::insert([
                    "type"      => 'income',
                    "action"    => 'buyvipsend',
                    "uid"       => $memberInfo->uid,
                    "touid"     => $memberInfo->uid,
                    "giftid"    => $product['id'],
                    "giftcount" => 1,
                    "totalcoin" => $presetGold,
                    "showid"    => 0,
                    "addtime"   => TIMESTAMP
                ]);
                async_task_cgi(function () use ($memberInfo) {
                    // 异步执行，错误了不影响整体
                    //兑换码 送3天会员
                    MessageModel::createSendAppVIPMessage($memberInfo->uuid);
                });
            }
            elseif ($product['type'] == OrdersModel::TYPE_GLOD) {//充金币
                $toSend = $product['coins'] + $product['free_coins'];
                $present = 0;//冲钻送冲钻
                $presentVip = 0;//冲钻送vip
                $totalSend = $present + $toSend;
                $addCoins = $totalSend + $memberInfo->coins;
                $addTotalCoins = $totalSend + $memberInfo->coins_total;
                $updateData = [
                    'coins'       => $addCoins,
                    'coins_total' => $addTotalCoins,
                ];
                if ($presentVip) {
                    $period_at = $presentVip * 86400 + max($user_expired_at, TIMESTAMP);
                    $updateData['expired_at'] = $period_at;
                    $updateData['vip_level'] = MemberModel::VIP_LEVEL_MOON;
                }
                $updateMember = \MemberModel::query()->where('uuid', $order['uuid'])->update($updateData);
                $log = UsersCoinrecordModel::addIncome(
                    'recharge', $memberInfo->uid, null, $toSend, $product['id'], 0, "充值金币"
                );
            }
            //观影券
            if ($product['ticket']) {
                MvTicketModel::sendUserTicket($memberInfo->uid, null, $product['ticket']);
            }
            // 收费视频免费看
            if ($product['free_day'] > 0) {
                FreeMemberModel::createInit($memberInfo->uid, $product['free_day']);
            }
            //AI免费次数
            if ($product['free_ai_num']){
                MemberAiFreeModel::setRecord($memberInfo->aff, $product['free_ai_num']);
            }

            //视频下载次数
            if ($product['download_num']){
                UserDownloadModel::addDownloadNum($memberInfo->aff, $product['download_num'], $data['pay_money']);
            }
            //新用户订单数、订单金额统计
            if ($memberInfo->regdate >= strtotime(date('Y-m-d'))){
                \SysTotalModel::incrBy('pay-amount-new', $order_amount);
                \SysTotalModel::incrBy('pay-account-new');
            }

            //上报联盟
            if ($order['build_id'] && $order['build_id'] != 'gw' && ProductModel::TYPE_GAME != $order['order_type']) {
                $service = new AppCenterService();
                //第一步add
                $_type = (ProductModel::TYPE_VIP == $order['order_type']) ? 0 : 1;
                $service->addOrder($order['order_type']
                    , $order['uuid']
                    , $payMoney
                    , $order['oauth_type']
                    , $_type
                    , $order['build_id']
                    , $memberInfo->invited_by
                    , 0
                    , $order['created_at']
                    , $memberInfo->phone);
                //第二部update
                $service->updateOrder($order['order_id'], $payMoney,1,$data['pay_time'],$order);
            }

            //公司上报
            (new EventTrackerService(
                $memberInfo->oauth_type,
                $memberInfo->invited_by,
                $memberInfo->uid,
                $memberInfo->oauth_id
            ))->addTask([
                'event'                 => EventTrackerService::EVENT_ORDER_PAID,
                'order_id'              => $order['order_id'],
                'order_type'            => $product['type'] == ProductModel::TYPE_VIP ? 'vip_subscription' : 'coin_purchase',
                'product_id'            => (string)$product['id'],
                'product_name'          => $product['pname'],
                'amount'                => (int)$product['promo_price'],
                'currency'              => 'CNY',
                'coin_quantity'         => $product['type'] == ProductModel::TYPE_DIAMOND ? (int)$product['coins'] : 0,
                'vip_expiration_time'   => $product['type'] == ProductModel::TYPE_VIP ? ($product['valid_date'] * 86400 + max($memberInfo->expired_at,TIMESTAMP)) : 0,
                'pay_type'              => $order['payway'],
                'pay_channel'           => $order['channel'],
                'transaction_id'        => $data['third_id'],
                'create_time'           => to_timestamp($data['pay_time'])
            ]);

            MarketingLotteryTriggerDispatcher::trigger(
                'pay_success',
                MarketingLotteryTriggerDispatcher::buildPayPayload('agent_pay', $data, $order, $product, $memberInfo)
            );
            die("success");
        }
        errLog("createGameOrderFailed:" . var_export($order, 1));
        die("failed");
    }

    /**
     * 提现回调
     */
    public function notifywithrawAction()
    {
        if (empty($_POST) || !isset($_POST['sign']) || !isset($_POST['order_id'])) {
            return;
        }
        $this->config = \Yaf\Registry::get('config');
        $signdata = $data = JAddSlashes($_POST);
        NotifyLogModel::addByExchange($data['order_id'], json_encode($data));
        error_log('提现回调参数' . print_r($signdata, true), 3, $this->logFile);

        //签名验证
        unset($signdata['sign']);
        $sign = $this->make_sign_callbak($signdata, config('withdraw.key'));

        if ($sign != $data['sign']) {
            error_log('提现签名错误' . $signdata['order_id'], 3, $this->logFile);
            echo "failed";
            return;
        }

        /** @var UserWithdrawModel $withdraw */
        $withdraw = \UserWithdrawModel::query()->where('cash_id', $data['order_id'])->first();
        if(is_null($withdraw)){
            echo 'success';
            return;
        }

        if ($withdraw->status == UserWithdrawModel::STATUS_POST) {
            echo 'success';
            return;
        }
        $isGame = ($withdraw->withdraw_from == UserWithdrawModel::DRAW_TYPE_GAME);

        //提现成功修改提现订单状态
        if (isset($data['success']) && $data['success'] == 200) {
            if ($withdraw) {
                try {
                    \DB::beginTransaction();
                    /** @var MemberModel $user */
                    $user = MemberModel::onWriteConnection()->where('uuid', $withdraw->uuid)->first();
                    // 金币提现

                    $withdraw->status = UserWithdrawModel::STATUS_POST;
                    $withdraw->trueto_amount = $data['exchange_money'];
                    $withdraw->order_desc = json_encode($data);
                    $withdraw->payed_at = $data['pay_time'] ?? time();
                    $withdraw->updated_at = $data['pay_time'] ?? time();
                    $withdraw->save();
                    \DB::commit();
                    SystemAccountModel::addWithDrawAccount($withdraw);
                    //messageCenter
                    MessageModel::createSystemMessage($withdraw->uuid, MessageModel::SYSTEM_MSG_TPL_WITHDRAW_YES,
                        ['money' => $data['exchange_money']]);

                    //数据中心 提现成功上报控制  成功回调后上报 只限成功的提现
                    if (!$isGame) {

                    (new AppReportService())->exchangeReport([
                        'order_id'    => $withdraw->cash_id,
                        'third_id'    => $data['third_id'] ?? $withdraw->third_id,
                        'uid'         => $user->uid,
                        'oauth_type'  => $user->oauth_type,
                        'name'        => $withdraw->name,
                        'card_number' => $withdraw->account,
                        'amount'      => $withdraw->amount,
                        'pay_amount'  => $data['exchange_money'],
                        'product'     => ($withdraw->from == UserWithdrawModel::DRAW_TYPE_PROXY) ? 1 : 2,
                        'way'         => 'bankcard',
                        'created_at'  => $withdraw->created_at,
                        'payed_at'    => $data['pay_time'] ?? time(),
                        'status'      => 1,
                    ]);
                }
                    echo 'success';
                } catch (Exception $exception) {
                    echo 'fail';
                    return;
                }
            }
            return;
        }

        // 提现失败退回用户金币
        if (isset($data['success']) && $data['success'] == 100) {
            \DB::beginTransaction();
            try {
                if ($withdraw) {
                    /** @var MemberModel $memberinfo */
                    $memberinfo = \MemberModel::onWriteConnection()->where('uuid', $withdraw->uuid)->first();
                    if ($withdraw->withdraw_from == UserWithdrawModel::DRAW_TYPE_MV) { //退回金币
                        $origin = $memberinfo->score;
                        $memberinfo->score = $origin + $withdraw->coins;
                        $flag = $memberinfo->save();
                        if (empty($flag)) {
                            throw new \Exception('退回用户金币失败');
                        }
                        error_log("mv提现退回#状态：{$flag} 原账户：{$origin} 退回：{$withdraw->coins} ID:{$withdraw->id}", 3, $this->logFile);
                    } elseif ($withdraw->withdraw_from == UserWithdrawModel::DRAW_TYPE_PROXY) {  //退回用户提现的
                        $origin = $memberinfo->tui_coins;
                        $memberinfo->tui_coins = $origin + $withdraw->coins;
                        if (empty($flag = $memberinfo->save())) {
                            throw new \Exception('提现退回金币失败');
                        }
                        error_log("推广提现退回#状态：{$flag} 原账户：{$origin} 退回：{$withdraw->coins} ID:{$withdraw->id}", 3, $this->logFile);
                    }else{
                        error_log("游戏推广提现退回#状态：false ； ID:{$withdraw->id}", 3, APP_PATH . '/storage/logs/logwithdrawgame.log');
                    }
                    $updatedataWithdraw = ['status' => 4, 'order_desc' => '提现失败#'.$data['mark']];
                    $itOk = \UserWithdrawModel::query()->where('cash_id', $data['order_id'])->update($updatedataWithdraw);
                    if (empty($itOk)){
                        throw new \Exception('修改提现数据失败');
                    }
                    \DB::commit();
                    //messageCenter
                    MessageModel::createSystemMessage($withdraw->uuid, MessageModel::SYSTEM_MSG_TPL_WITHDRAW_NO, ['reason' => '提现失败#' . $data['mark']]);
                    echo 'success';
                }
            } catch (Exception $exception) {
                \DB::rollBack();
                echo 'failed';
                error_log($exception->getMessage(), 3, $this->logFile);
            }
        }
    }
}
