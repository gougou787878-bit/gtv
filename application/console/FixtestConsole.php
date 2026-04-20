<?php


namespace App\console;


use DB;
use FreeMemberModel;
use MemberModel;
use MvTicketModel;
use OrdersModel;
use ProductModel;
use service\QingMingService;
use UsersCoinrecordModel;

class FixtestConsole extends AbstractConsole
{

    public $name = 'fix-test';

    public $description = '修复没有回调的数据';


    public function process($argc, $argv)
    {
        $s = [
            'jf1_20220202000659036930',
        ];

        OrdersModel::whereIn('order_id' , $s)->each(function (OrdersModel $item){
            $this->notifyOrder($item , $item->amount , time() , 'tmp-' . date('YmdHis_') . rand(1000000,9999999));
        });


    }


    protected function notifyOrder(\OrdersModel $orderObject, $real_pay_amount, $pay_time,$third_id)
    {
        try{
            $order = $orderObject->toArray();
            if ($order['status'] == OrdersModel::STATUS_SUCCESS) {
                echo 'success';
                return;
            }

            $product = $orderObject->product;
            if (is_null($product)) {
                echo '产品不存在';
                return;
            }
            \DB::beginTransaction();
            $order_amount = $order['amount'];
            $updateMember = 0;//用户更新信息标识 默认
            //如果误差范围4元之内,都视为正常
            if ($order_amount > 0 && ($real_pay_amount >= ($order_amount - 400))) {
                //实际支付金额
                $updateOrder = [
                    'updated_at' => $pay_time,
                    'pay_amount' => $real_pay_amount,
                    'app_order'  => $third_id,
                    'status'     => OrdersModel::STATUS_SUCCESS,
                ];
                $orderObject->update($updateOrder);
                $memberInfo = $orderObject->withMember;
                //观影券
                if ($product['ticket']) {
                    MvTicketModel::sendUserTicket($memberInfo->uid, null,  $product['ticket']);
                }
                // 收费视频免费看
                if ($product['free_day'] > 0) {
                    FreeMemberModel::createInit($memberInfo->uid, $product['free_day']);
                }
            }
            \DB::commit();
            echo "success";
        }catch (\Throwable $e){
            \DB::rollBack();
            echo $e."\r\n";
            echo "failed";
        }
    }

}