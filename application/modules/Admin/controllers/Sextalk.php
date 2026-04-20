<?php

/**
 * Class SextalkController
 * @author xiongba
 * @date 2021-01-19 10:32:05
 */
class SextalkController extends BackendBaseController
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
     * @date 2021-01-19 10:32:05
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2021-01-19 10:32:05
     */
    protected function getModelClass(): string
    {
       return SexTalkModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2021-01-19 10:32:05
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
    protected function postArray($setPost = null)
    {
        $post = parent::postArray();
        $post['created_at'] = date('Y-m-d H:i:s',TIMESTAMP);
        return $post;
    }
    protected function saveAfterCallback($model)
    {
        if (is_null($model)) {
            return;
        }
        SexTalkModel::clearSexTalkCache();//清楚缓存
    }

}