<?php

use service\EventTrackerService;
use service\ProductService;
use service\TabService;
use service\UserService;

class SearchController extends BaseController
{


    /**
     * 迭代版本，搜索配置页面  分类推荐配置
     */
    public function confAction()
    {
        $searchTags = TabService::getSearchList();
        $listSearchData = [];
        if($searchTags){
            $listSearchData = collect($searchTags)->map(function ($item){
                if($item['tags_ary']){
                    return [
                        'name'=>$item['tab_name'],
                        'data'=>$item['tags_ary'],
                    ];
                }
                return null;
            })->filter()->toArray();
        }
        $searchService = new \service\SearchService;
        array_unshift($listSearchData,[
            'name'=>'热门搜索',
            'data'=>$searchService->getHotKeyword()
        ]);

        $data = [
            'ads'       => $searchService->getIndexAdList(request()->getMember(), $this->token()),
            'list' => $listSearchData,

        ];
        return $this->showJson($data);
    }

    /**
     * 搜索视频
     * @return bool
     * @throws Throwable
     * @author xiongba
     */
    public function mvAction()
    {
        $kwy = $_POST['kwy'] ?? '';
        $kwy = strip_tags($kwy);
        if (mb_strlen($kwy)<2) {
            return $this->errorJson('至少两位搜索关键字');
        }
        if(preg_match('/[\xf0-\xf7].{3}/', $kwy)){ //过滤Emoji表情
            return $this->errorJson('不支持[Emoji]表情');
        }
        $member = request()->getMember();
        $service = new \service\SearchService();
        try {
            if (preg_match("/^#([1-9]+\d*)$/U", $kwy, $p)) {//eg:指定编号搜索  #666
                if($this->page==1){
                    $data = $service->searchByMVID($p[1], $member);
                }else{
                    $data = [];
                }
            } else {
                $data = $service->searchMv($kwy, $member);

                //公司上报
                (new EventTrackerService(
                    $member->oauth_type,
                    $member->invited_by,
                    $member->uid,
                    $member->oauth_id,
                    $_POST['device_brand'] ?? '',
                    $_POST['device_model'] ?? ''
                ))->addTask([
                    'event'                 => EventTrackerService::EVENT_KEYWORD_SEARCH,
                    'keyword'               => trim($kwy),
                    'search_result_count'   => $data['total']
                ]);

            }
            return $this->showJson($data);
        } catch (Throwable $e) {
            $this->errLog($e->getMessage());
            return $this->errorJson('关键字分析错误');
        }

    }

    /**
     * 剧集搜索
     * @return bool
     */
    public function topicAction(){
        $kwy = $_POST['kwy'] ?? '';
        $kwy = strip_tags($kwy);
        if (mb_strlen($kwy)<2) {
            return $this->errorJson('至少两位搜索关键字');
        }
        if(preg_match('/[\xf0-\xf7].{3}/', $kwy)){ //过滤Emoji表情
            return $this->errorJson('不支持[Emoji]表情');
        }
        $member = request()->getMember();
        $service = new \service\SearchService();
        $data = $service->searchOriginTopicData($kwy,$member);
        return $this->showJson($data);

    }
    /**
     * 搜索用户
     * @return bool
     * @throws Throwable
     * @author xiongba
     */
    public function userAction()
    {
        $kwy = $_POST['kwy'] ?? null;
        $kwy = strip_tags($kwy);
        if (mb_strlen($kwy)<2) {
            return $this->errorJson('至少两位搜索关键字');
        }
        if(preg_match('/[\xf0-\xf7].{3}/', $kwy)){ //过滤Emoji表情
            return $this->errorJson('不支持的关键字信息');
        }
        $member = request()->getMember();
        \helper\Util::PanicFrequency($member->uid, 10, 60, '1分钟内只能搜索10次');
        $service = new \service\SearchService();
        try {
            $data = $service->searchUser($kwy, $member);
            return $this->showJson($data);
        } catch (Throwable $e) {
            $this->errLog($e->getMessage());
            return $this->errorJson('关键字分析错误');
        }
    }

}