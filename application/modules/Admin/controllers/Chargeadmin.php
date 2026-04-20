<?php


/**
 * Class ChargeadminController
 * @author xiongba
 * @date 2021-05-04 11:45:05
 */
class ChargeadminController extends BackendBaseController
{
    /**
     * 列表数据过滤
     * @return Closure
     * @author xiongba
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        return function ($item) {
            $item->ip = substr_replace($item->ip,'****',4,7);
            $item->addtime_str = date('m-d H:i',$item->addtime);
            return $item;
        };
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2021-05-04 11:45:05
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2021-05-04 11:45:05
     */
    protected function getModelClass(): string
    {
        return ChargeAdminModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2021-05-04 11:45:05
     */
    protected function getPkName(): string
    {
        return 'id';
    }

    /**
     * 定义数据操作日志
     * @return string
     * @author xiongba
     * @date 2019-11-04 17:19:41
     */
    protected function getLogDesc(): string {
        // TODO: Implement getLogDesc() method.
        return '';
    }
    public function saveAction() {
        if (!$this->getRequest()->isPost()) {
            return $this->ajaxError('请求错误');
        }
        $admin = $this->getUser()->username;
        $data = $this->postArray();
        /** Array
         * (
         * [type] => 1
         * [touid] => 5298971
         * [coin] => 345
         * [des] => 测试聊天等级需要
         * [_pk] =>
         * )*/
        if(empty($data['type']) || empty($data['touid']) || empty($data['coin']) || empty($data['des']) ){
            return $this->ajaxError('参数非空，注意，注意~');
        }
        //print_r($data);die;
        $uid = $data['touid'];
        $des = $data['des'];
        $type = $data['type']??0;
        $value = abs($data['coin']);

        if ($type == ChargeAdminModel::TYPE_COIN_ADD) {
            $update = MemberModel::where('uid',$uid)->update([
                'coins'       => \DB::raw("coins + {$value}"),
                'coins_total' => \DB::raw("coins_total + {$value}")
            ]);
            if (!$update) {
                return $this->ajaxError('加分失败，注意~');
            }
        } elseif ($type == ChargeAdminModel::TYPE_COIN_SUB) {
            $update = MemberModel::where('uid', $uid)->where('coins', '>=',
                $value)->update([
                'coins'       => \DB::raw("coins - {$value}"),
                'coins_total' => \DB::raw("coins_total - {$value}"),
            ]);
            if (!$update) {
                return $this->ajaxError('减分失败，注意~');
            }

        } elseif ($type == ChargeAdminModel::TYPE_MSG_ADD) {
            $update = MemberModel::where('uid', $uid)->update([
                'exp'       => \DB::raw("exp + {$value}"),
            ]);
            if (!$update) {
                return $this->ajaxError('消息新增失败，注意~');
            }
            UsersCoinrecordModel::createForExpend('buyMessage', $uid, $uid, $value, 0, 0, 0, 0);

        } elseif ($type == ChargeAdminModel::TYPE_TICKET_ADD) {
            $update = MvTicketModel::sendUserTicket($uid,null,$value);
            if (!$update) {
                return $this->ajaxError('影券新增失败，注意~');
            }
        }
        ChargeAdminModel::insert([
            'touid'   => $uid,
            'coin'    => $value,
            'addtime' => TIMESTAMP,
            'admin'   => $admin,
            'ip'      => USER_IP,
            'type'    => $type,
            'des'     => $des
        ]);

        $user = MemberModel::find($uid);
        MemberModel::clearFor($user);
        //防止更换设备
        \tools\RedisService::del('user:'.$user->getDeviceHash());
        \tools\RedisService::del('users:'.$user->getDeviceHash());
        \tools\RedisService::del('users:'.$user->uuid);
        $type_str = ChargeAdminModel::TYPE[$type];
        return $this->ajaxSuccess("{$type_str} #操作成功#~");

    }
}