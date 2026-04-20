<?php

/**
 * Class PosttopiccategoryController
 *
 * @date 2023-08-26 18:51:06
 */
class PosttopiccategoryController extends BackendBaseController
{
    /**
     * 列表数据过滤
     * @return Closure
     *
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        return function (PostTopicCategoryModel $item) {
            return $item;
        };
    }

    /**
     * 试图渲染
     * @return string
     *
     * @date 2023-08-26 18:51:06
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     *
     * @date 2023-08-26 18:51:06
     */
    protected function getModelClass(): string
    {
       return PostTopicCategoryModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     *
     * @date 2023-08-26 18:51:06
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
}