<?php

/**
 * Class UsertopicController
 * @author xiongba
 * @date 2021-02-23 15:57:33
 */
class UsertopicController extends BackendBaseController
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
            return $item;
        };
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2021-02-23 15:57:33
     */
    public function indexAction()
    {
        $this->display();
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2021-02-23 15:57:33
     */
    public function waitAction()
    {
        $this->display();
    }



    public function delAllAction()
    {
    }

    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2021-02-23 15:57:33
     */
    protected function getModelClass(): string
    {
       return UserTopicModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2021-02-23 15:57:33
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

    function saveAfterCallback($model)
    {
        if(!is_null($model)){
            /** @var UserTopicModel $newModel */
           $newModel = UserTopicModel::where('id',$model->id)->first();
           if($newModel->status == UserTopicModel::STAT_REJECT){
               $member = $newModel->user;
               if(!is_null($member) && $newModel->deny_str)
               //messageCenter
                   MessageModel::createSystemMessage($member->uuid, MessageModel::SYSTEM_MSG_TPL_TOPIC_REFUSE,
                       ['title' => $model->title, 'reason' => $newModel->deny_str]);
           }
        }

    }

}