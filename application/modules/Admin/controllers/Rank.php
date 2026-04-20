<?php

/**
 * Class RankController
 * @author xiongba
 * @date 2021-08-17 20:43:48
 */
class RankController extends BackendBaseController
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
            if ($item->type == RankModel::TYPE_MV) {
                $item->title = $item->mv->title;
            } elseif ($item->type == RankModel::TYPE_TOPIC) {
                $item->title = $item->topic->title;
            }
            return $item;
        };
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2021-08-17 20:43:48
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2021-08-17 20:43:48
     */
    protected function getModelClass(): string
    {
        return RankModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2021-08-17 20:43:48
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
}