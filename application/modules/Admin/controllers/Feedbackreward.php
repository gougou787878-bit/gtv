<?php

/**
 * Class FeedbackrewardController
 *
 * @date 2024-08-30 20:18:12
 */
class FeedbackrewardController extends BackendBaseController
{
    /**
     * 列表数据过滤
     * @return Closure
     *
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        return function (FeedbackRewardModel $item) {
            $item->status_str = FeedbackRewardModel::STATUS_TIPS[$item->status];
            $item->type_str = FeedbackRewardModel::TYPE_TIPS[$item->type];
            $imags = $item->images;
            $show_img = [];
            if ($imags){
                $arr = json_decode($imags, true);
                foreach ($arr as $v) {
                    $show_img[] = url_avatar($v);
                }
            }
            $item->show_imgs = $show_img;
            return $item;
        };
    }

    /**
     * 试图渲染
     * @return string
     *
     * @date 2024-08-30 20:18:12
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     *
     * @date 2024-08-30 20:18:12
     */
    protected function getModelClass(): string
    {
       return FeedbackRewardModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     *
     * @date 2024-08-30 20:18:12
     */
    protected function getPkName(): string
    {
        return 'id';
    }

    /**
     * 定义数据操作日志
     * @return string
     *
     * @date 2019-11-04 17:19:41
     */
    protected function getLogDesc(): string {
        // TODO: Implement getLogDesc() method.
        return '';
    }

    public function saveAction()
    {
        try {
            $data = $_POST;
            $pk = $data['_pk'];
            $replay = $data['replay'];
            test_assert($replay, '回复内容不能为空');
            $model = FeedbackRewardModel::find($pk);
            test_assert($model, '求片不存在');
            test_assert($model->status != FeedbackRewardModel::STATUS_END, '求片已经完结，不能回复');
            $model->status = FeedbackRewardModel::STATUS_YES;
            $model->updated_at = \Carbon\Carbon::now();
            $model->replay = $data['replay'];
//            $content = Carbon\Carbon::now() . "|" . $replay;
//            $model->replay = $model->replay . "||" . $content;
//            $model->replay = ltrim($model->replay, "||");
            $model->save();
            return $this->ajaxSuccessMsg('回复成功');
        }catch (Exception $e){
            return $this->ajaxError($e->getMessage());
        }
    }

    public function endAction(){
        try {
            $data = $_POST;
            $pk = $data['id'];
            test_assert($pk, '数据异常');
            $model = FeedbackRewardModel::find($pk);
            test_assert($model, '反馈不存在');
            test_assert($model->status != FeedbackRewardModel::STATUS_END, '已经完结不需要重复标记');
            $model->status = FeedbackRewardModel::STATUS_END;
            $model->updated_at = \Carbon\Carbon::now();
            $model->save();
            return $this->ajaxSuccessMsg('标记完成成功');
        }catch (Exception $e){
            return $this->ajaxError($e->getMessage());
        }
    }
}