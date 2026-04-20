<?php

/**
 * Class StoryseriesController
 * @author xiongba
 * @date 2022-06-28 20:56:02
 */
class StoryseriesController extends BackendBaseController
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
     * @date 2022-06-28 20:56:02
     */
    public function indexAction()
    {
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2022-06-28 20:56:02
     */
    protected function getModelClass(): string
    {
       return StorySeriesModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2022-06-28 20:56:02
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

    public function getContentAction(){
        try {
            $id = (int)$_GET['id'] ?? 0;
            if (!$id){
                return $this->ajaxError('参数错误');
            }
            $chapter = StorySeriesModel::find($id);
            test_assert($chapter, '章节不存在');
            test_assert($chapter->url, '章节不存在');
            $txt = parse_url($chapter->url, PHP_URL_PATH);
            $content = \service\StorySeriesService::get_chapter_txt($txt);
            return $this->ajaxSuccess(['content' => $content]);
        }catch (Throwable $e){
            return $this->ajaxError($e->getMessage());
        }
    }

    public function setContentAction(){
        try {
            $id = (int)$_POST['_pk'] ?? 0;
            $content = $_POST['content'] ?? '';
            if (!$id){
                return $this->ajaxError('参数错误');
            }
            $chapter = StorySeriesModel::find($id);
            test_assert($chapter, '章节不存在');
            $old_path = '';
            if($chapter->url){
                $old_path = parse_url($chapter->url, PHP_URL_PATH);
            }
            \service\StorySeriesService::upload_chapter_txt($content, $old_path);
            return $this->ajaxSuccessMsg('修改成功');
        }catch (Throwable $e){
            return $this->ajaxError($e->getMessage());
        }
    }
}