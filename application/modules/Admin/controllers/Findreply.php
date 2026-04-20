<?php

/**
 * Class FindreplyController
 * @author xiongba
 * @date 2020-07-10 16:05:18
 */
class FindreplyController extends BackendBaseController
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
            /** @var FindReplyModel $item */
            $item->mvIds = $item->getMvAry()->map(function ($item){
                return $item->id;
            });
            $item->load('member');
            return $item;
        };
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-07-10 16:05:18
     */
    public function indexAction()
    {
        $this->display();
    }



    public function acceptAction()
    {
        try {
            $reply_id = $_POST['id'] ?? 0;
            test_assert($reply_id, 'id不能为空');

            $replyObject = FindReplyModel::find($reply_id);
            test_assert($replyObject,'无效回复记录');

            $findObject = FindModel::find($replyObject->find_id);
            test_assert($findObject,'查无求片记录');

            if($findObject->is_match || $replyObject->is_accept){
                test_assert(false, '已经采纳并自动分配赏金');
            }

            transaction(function () use ($findObject, $replyObject){
                $coins = $findObject->total_coins;
                $findObject->is_match = FindModel::MACTH_YES;
                $isOk = $findObject->save();
                test_assert($isOk, '采纳失败，请重试');

                $replyObject->is_accept = FindReplyModel::IS_ACCEPT_YES;
                $replyObject->coins = $coins;
                $isOk = $replyObject->save();
                test_assert($isOk, '采纳失败，请重试');

                if($coins){
                    /** @var MemberModel $toMember */
                    $toMember = MemberModel::where('uuid', $replyObject->uuid)->first();
                    $toMember->increment("score", $coins);
                    $toMember->increment("score_total", $coins);
                    $tips = "[回复求片被采纳]# 获取收益： $coins";
                    //\UsersCoinrecordModel::addIncome('buymv', 0, $toMember->uid, $coins, $findObj->id, 0, $tips);
                    \UsersCoinrecordModel::createForExpend('buymv', 0, $toMember->uid,
                        $coins,
                        $findObject->id,
                        0,
                        0,
                        0,
                        null,
                        $tips);
                }
                return true;
            });

            return $this->ajaxSuccessMsg('采纳成功');
        }catch (Throwable $exception){
            return $this->ajaxError($exception->getMessage());
        }
    }


    public function statusAction()
    {
        $id = $_POST['_pk'] ?? 0;
        $model = FindReplyModel::find($id);
        if (empty($model)) {
            return $this->ajaxError('资源不存在');
        }

        $status = $_POST['status'] ?? FindReplyModel::STATUS_INIT;
        if ($model->status != FindReplyModel::STATUS_INIT) {
            return $this->ajaxError('状态不可更改');
        }

        try {
            DB::beginTransaction();
            if ($status == FindReplyModel::STATUS_REJECT) {
                $itOk = FindModel::find($model->find_id)->decrement('reply');
                if (empty($itOk)) {
                    throw new \Exception('操作失败');
                }
            }
            $model->status = $status;
            $itOk = $model->save();
            if (empty($itOk)) {
                throw new \Exception('操作失败');
            }
            DB::commit();
            return $this->ajaxSuccessMsg('操作成功 ');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->ajaxError($e->getMessage());
        }

    }

    protected function getModelObject()
    {
        return FindReplyModel::with('member');
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2020-07-10 16:05:18
     */
    protected function getModelClass(): string
    {
        return FindReplyModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2020-07-10 16:05:18
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
    protected function getLogDesc(): string
    {
        return '';
    }
}