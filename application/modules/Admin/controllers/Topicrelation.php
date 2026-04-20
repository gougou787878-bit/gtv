<?php

use helper\QueryHelper;
use service\TopicService;

/**
 * Class TopicrelationController
 * @author xiongba
 * @date 2020-05-25 22:14:44
 */
class TopicrelationController extends BackendBaseController
{
    /**
     * 列表数据过滤
     * @return Closure
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        return function ($item) {
            $item->topic_title = $item->user_topic->title;

            return $item;
        };
    }


    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-05-25 22:14:44
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2020-05-25 22:14:44
     */
    protected function getModelClass(): string
    {
        return MvModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2020-05-25 22:14:44
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
        // TODO: Implement getLogDesc() method.
        return '';
    }

    /**
     * @deprecated 放弃
     * @return bool
     */
    public function saveAction()
    {
        $data = $_POST;
        if (!empty($data['_pk'])) {
            $_mv_id = (int)$data['mv_id'];
            if($_mv_id){
                //独立修改
                if(TopicRelationModel::where('id',$data['_pk'])->update(['mv_id'=>$_mv_id])){
                    return $this->ajaxSuccessMsg('操作成功');
                }
            }
            return $this->ajaxError('不支持修改');
        }
        if (empty($data['mv_id']) || empty($data['topic_id'])) {
            return $this->ajaxError('参数不对');
        }
        $topic_id = $data['topic_id'];
        $topic = TopicModel::find($topic_id);
        if (empty($topic)) {
            return $this->ajaxError('合集不存在');
        }
        $mv_id_data = explode(',',trim($data['mv_id'],','));
        $oldMvId = TopicRelationModel::where(['topic_id' => $topic_id])->pluck('mv_id');
        $insertMvIds = collect($mv_id_data)->map('trim')->diff($oldMvId)->toArray();
        $values = [];
        foreach ($insertMvIds as $mvId) {
            $values[] = [
                'topic_id' => $topic_id,
                'mv_id'    => $mvId,
            ];
        }
        TopicRelationModel::insert($values);
        return $this->ajaxSuccessMsg('操作成功');
    }


    /**
     * 删除数据
     * 后台全局公共方法
     * @return mixed
     * @author xiongba
     * @date 2019-11-08 11:19:24
     */
    public function delAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->ajaxError('请求错误');
        }
        $post = $this->postArray();
        $className = $this->getModelClass();
        $pkName = $this->getPkName();
        $where = [$pkName => $post['_pk']];
        $model = $className::where($where)->first();
        //清楚缓存
        TopicService::clearTopicMV($model->topic_id);
        if (empty($model) || $model->delete()) {
            //\AdminLogModel::addDelete($this->getUser()->username , "删除了：".$this->getLogDesc());
            return $this->ajaxSuccessMsg('操作成功');
        } else {
            return $this->ajaxError('操作错误');
        }
    }

}