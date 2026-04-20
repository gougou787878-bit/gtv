<?php

/**
 * Class AdsappController
 * @author xiongba
 * @date 2020-10-21 12:30:57
 */
class AdsappController extends BackendBaseController
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
            $result = [];
            $result = $item->toArray();
            $result['img_url_full'] = url_ads($result['img_url']);
            $result['created_at'] = date('Y-m-d', $result['created_at']);
            return $result;
        };
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-10-21 12:30:57
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2020-10-21 12:30:57
     */
    protected function getModelClass(): string
    {
       return AdsAppModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2020-10-21 12:30:57
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
        $post = request()->getPost();
        $post['created_at'] = TIMESTAMP;
        return $post;
    }

    function saveAfterCallback($model)
    {
        AdsAppModel::clearRedisCache();
    }

    function _delActionAfter()
    {
        AdsAppModel::clearRedisCache();
    }
}