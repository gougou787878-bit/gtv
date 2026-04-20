<?php

/**
 * 视频模块  最新版本
 */

use helper\QueryHelper;
use service\AdService;
use service\MvNewService;
use service\TabService;
use service\TagsService;

class MvnewController extends BaseController
{


    /**
     * 获取导航标签视频
     * @return bool|void
     * @author
     * @date 2020-06-16 11:48:33
     */
    public function listOfTabAction()
    {
        $tabId = $_POST['tabId'] ?? 0;
        $banner = [];
        if ($this->page == 1) {
            $banner = AdService::getADsByPosition(AdsModel::POSITION_LIST);
        }
        try {
            $data = (new MvNewService())->getTabList($tabId, request()->getMember());
            if (!$data) {
                $tagStr = TabModel::getMatchString($tabId);
                $items = MvModel::queryWithUser()
                    ->whereRaw("match(tags) against(? IN BOOLEAN MODE)", [$tagStr])
                    ->orderByDesc('refresh_at')
                    ->forPage($this->page, $this->limit)
                    ->get();
                $data = (new \service\MvNewService())->v2format($items, request()->getMember());
            }

            return $this->showJson([
                'banner' => $banner,
                'list'   => $data
            ]);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    /**
     * 最新视频
     * @return bool|void
     * @author
     */
    public function listOfLatestAction()
    {
        $rediskey = "lastes:mv:{$this->page}";
        $list = cached($rediskey)->serializerPHP()->expired(3600)->fetch(function (){
            return MvModel::queryMv()
                ->forPage($this->page, $this->limit)
                ->orderByDesc('refresh_at')
                ->get();
        });
        $data = (new \service\MvNewService())->v2format($list, request()->getMember());
        $banner = [];
        if ($this->page == 1) {
            $banner = AdService::getADsByPosition(AdsModel::POSITION_INDEX_HOME_UP);
        }
        return $this->showJson([
            'banner' => $banner,
            'list'   => $data
        ]);
    }


    /**
     * 最热视频
     * @return bool|void
     * @author
     */
    public function listOfHottestAction()
    {
        $banner = [];
        if ($this->page == 1) {
            $banner = AdService::getADsByPosition(AdsModel::POSITION_INDEX_HOME_UP);
        }
        $data = (new \service\MvNewService())->hottestList(request()->getMember());//随机
        if (!$data) {
            $list = MvModel::queryMv()
                ->where('refresh_at', '>=', strtotime('-20 days'))
                ->forPage($this->page, $this->limit)->orderByDesc('like')->get();
            $data = (new \service\MvNewService())->v2format($list, request()->getMember());
        }

        return $this->showJson([
            'banner' => $banner,
            'list'   => $data
        ]);
    }

    /**
     * 我关注的发布视频
     * @return bool|void
     */
    public function listOfFollowAction()
    {
        $cached = cached('follow:mv:' . $this->member['uid'])->hash($this->page);
        $list = $cached->serializerJSON()
            ->expired(600)
            ->fetch(function () {
                $items = MvModel::queryMv()
                    ->select(['mv.*'])
                    ->leftJoin("member_attention as ma", 'ma.touid', 'mv.uid')
                    ->where('ma.uid', $this->member['uid'])
                    ->forPage($this->page, $this->limit)
                    ->orderByDesc('id')
                    ->get();
                return (new \service\MvNewService())->v2format($items, request()->getMember());
            });
        $vlist = [];
        if (empty($list)) {
            //$cached = cached('foryou:follow:' . $this->member['uid'])->hash($this->page);
            $cached = cached('foryou:follow:all')->hash($this->page);
            $vlist = $cached->serializerJSON()
                ->expired(3600)
                ->usingFuck(false)
                ->fetch(function () {
                    $items = MvModel::queryMv()
                        ->select(['mv.*'])
                        ->where('created_at', '>=', strtotime('-10 days'))
                        ->forPage($this->page, $this->limit)
                        ->orderByDesc('like')
                        ->get();
                    return (new \service\MvNewService())->v2format($items, request()->getMember());
                });
        }
        $list && $result['list'] = $list;
        $vlist && $result['vlist'] = $vlist;
        return $this->showJson($result);
    }

    /**
     * 视频主页
     * @return bool|void
     */
    public function homeAction()
    {
        $date = date('Y-m-d');
        //每日推荐
        'test' == APP_ENVIRON && $date = '2020-09-24';//测试环境
        $list = DailyVideoModel::getVideoByDate($date, 12);
        $mei_ri = null;
        if (!empty($list)) {
            $list = collect($list)->random(count($list) >= 4 ? 4 : count($list))->values();
            $mei_ri = [
                'type'    => 'mei_ri',
                'name'    => '精选推荐',
                'subName' => substr($date, 5) . '日已更新',
                'icon'    => '',
                'desc'    => '',
                'item'    => (new \service\MvNewService())->v2format($list)
            ];
        }

        $feature = [];
        //广告
        $feature['ads'] = AdService::getADsByPosition(AdsModel::POSITION_INDEX_HOME_UP);
        $feature['mei_ri'] = $mei_ri;
        $feature['hot_topic'] = [
            'type'    => 'play_count',
            'name'    => '热门剧集',
            'subName' => '',
            'icon'    => '',
            'desc'    => '',
            'item'    => cached('hot_topic')
                ->serializerJSON()
                ->expired(900)
                ->usingFuck()
                ->fetch(function () {
                    return UserTopicModel::queryBase()
                        ->orderByDesc('like_count')
                        ->limit(6)
                        ->get()
                        ->toArray();
                })
        ];
        $feature['mid_ads'] = AdService::getADsByPosition(AdsModel::POSITION_INDEX_HOME);
        $feature['rank'] = [
            [
                'name' => '热门榜',
                'type' => 'gv',
                'list' => RankModel::getHomeTop(RankModel::TYPE_MV, RankModel::FIELD_TYPE_LIKE, 3, 'day'),
            ],
            [
                'name' => '剧集榜',
                'type' => 'tv',
                'list' => RankModel::getHomeTop(RankModel::TYPE_TOPIC, RankModel::FIELD_TYPE_PLAY, 3, 'day'),
            ],
            [
                'name' => '达人榜',
                'type' => 'user',
                'list' => UsersCoinrecordModel::getTopProfit(3, 'day'),
            ],
        ];
        $listCat = TabService::getCateList();
        $feature['items'] = null;
        if ($listCat) {
            $feature['items'] = collect($listCat)->map(function ($_tab) {
                return [
                    'type'    => 'cat',
                    'tabId'   => $_tab['tab_id'],
                    'name'    => $_tab['tab_name'],
                    'subName' => '',
                    'icon'    => '',
                    'desc'    => '',
                    'item'    => $this->_getCatDataByID($_tab['tab_id'], 4, 0, $_tab['tags_str'])
                ];
            });
        }
        return $this->showJson($feature);
    }

    /**
     * @return bool
     */
    public function detailAction()
    {
        $id = $this->post['id'] ?? 0;
        if (empty($id)) {
            //errLog("detail".var_export($this->post,1));
            return $this->errorJson('参数错误');
        }
        /** @var MvModel $data */
        $data = \MvModel::detail($id);
        if (empty($data)) {
            return $this->errorJson('视频不存在或已下架~');
        }
        $member = request()->getMember();
        $row = (new MvNewService())->formatItem($data, $member);

        $resource= $data->full_m3u8;
        if(!$resource){
            $resource = $data->m3u8;
        }


        $row->play_url = getPlayUrl($resource, true);

        if ($row->coins > 0) {
            if ($row->is_pay) {
                $row->play_url = $data->getSourceUrl();
            }
        } else {
            if ($member->is_vip)  {
                $row->play_url = $data->getSourceUrl();
            }
        }


        if (APP_TYPE_FLAG == 0) {
            $preview_video = null;
            $preview_tip = '';
            $isPreviewVideo = false;
            if ($row->coins > 0) {
                if (!$row->is_pay) {
                    $preview_tip = sprintf("%d金币解锁完整版>>", $row->coins);
                    $isPreviewVideo = true;
                }
            } else {
                $expired_at = $member ? $member['expired_at'] : 0;
                if ($expired_at < time()) {
                    $preview_tip = '开通VIP解锁完整版>>';
                    $isPreviewVideo = true;
                }
            }
            if ($isPreviewVideo) {
                $preview_video = url_video_short($resource);
            }
            $row->preview_video = $preview_video;
            $row->preview_tip = $preview_tip;
            if (IS_FAKE_CLIENT) {
                $row->preview_video = getPlayUrl(ILLEGAL_ORG_VIDEO);
            }
        }
        if(IS_FAKE_CLIENT){
            $row->play_url = getPlayUrl(ILLEGAL_ORG_VIDEO);
        }



        $ads = AdService::getADsByPosition(AdsModel::POSITION_PLAY);
        $userTopic = null;
        $list = [];
        if ($data->topic_id) {
            /** @var UserTopicModel $userTopic */
            $userTopic = UserTopicModel::queryUser()->where('id', $data->topic_id)->first();
            if (!is_null($userTopic)) {
                $userTopic->watchByUser(request()->getMember())->addHidden(['mv_id_str', 'mv_id_ary']);
                $list = $userTopic->getItems();
                \RankModel::addRank(\RankModel::TYPE_TOPIC, $data->topic_id, \RankModel::FIELD_TYPE_PLAY);
                //覆盖视频相关
                $row->tags = $userTopic->tags;
            }
            UserTopicModel::where('id',$data->topic_id)->increment('play_count');
        }

        //各种排行版
        if($data->construct_id){
            jobs([MvModel::class, 'add2SeeRank'], [$data['id'], $data['construct_id']]);
        }

        $result = [
            'my_ticket_number' => MvTicketModel::myInitMvTicketNumber(request()->getMember()),
            'row'              => $row,
            'ads'              => $ads,
            'topic'            => $userTopic,
            'topic_mv'         => $list,
        ];
        return $this->showJson($result);
    }

    public function detailRecommendAction()
    {
        $id = $this->post['id'] ?? 0;
        if (empty($id)) {
            return $this->errorJson('参数错误');
        }
        $data = \MvModel::detail($id);
        if (empty($data)) {
            return $this->errorJson('视频不存在或已下架~');
        }
        if ($this->page > 1) {
            $result['list'] = [];//随机
            return $this->showJson($result);
        }
        $list = cached('dre:' . $id)->serializerJSON()
            ->expired(3600)
            ->fetch(function () use ($data, $id) {

                $_mvUserList = MvModel::queryMv()
                    ->where('id', '!=', $id)
                    ->where('uid', $data['uid'])
                    ->orderByDesc('like')
                    ->limit(4)->get();

                $tagList = $data->tags_list;
                $_mvList = [];
                if ($tagList) {
                    if (count($tagList) >= 3) {
                        unset($tagList[0]);
                    }
                    $tagStr = trim(implode(' ', $tagList));
                    //推荐高赞视频6条
                    $_mvList = MvModel::queryMv()
                        ->where('id', '<', $id)
                        ->where('uid', '!=', $data['uid'])
                        ->where('like', '>', 20)
                        //->whereRaw("match(tags) against(?)", [$tagStr])
                        //->where('refresh_at', '>=', strtotime('-20 days'))
                        ->orderByDesc('id')
                        ->limit(8)->get();
                } elseif (empty($_mvList)) {
                    //推荐用户 视频6条
                    $_mvList = MvModel::queryMv()
                        ->where('uid', $data->uid)
                        ->where('id', '!=', $id)
                        ->orderByDesc('like')
                        ->limit(9)
                        ->get();
                }
                $listUser = (new MvNewService())->v2format($_mvUserList);
                $list = (new MvNewService())->v2format($_mvList);
                return array_merge($listUser, $list);
            });
        $result['list'] = $list;//随机
        return $this->showJson($result);

    }

    protected function _getCatDataByID($tabID, $limit, $offset, $tabString = null)
    {

        if (is_null($tabString)) {
            $tagStr = TabModel::getMatchString($tabID);
        } else {
            $tagStr = str_replace(',', ' ', trim($tabString, ','));
        }
        return cached("cat:{$tabID}:{$limit}:{$offset}")->serializerJSON()
            ->usingFuck(false)
            ->expired(rand(900,1800))
            ->fetch(function()use($tagStr,$limit,$offset){
                $items = MvModel::queryMv()
                    ->whereRaw("match(tags) against(?)", [$tagStr])
                    ->orderByDesc('refresh_at')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
                return (new \service\MvNewService())->v2format($items, request()->getMember());
            });
    }

    /**
     * 首页 涮新 涮一涮 按分类局部
     * @return bool|void
     * @date 2020-06-16 11:48:33
     */
    public function homeByCatAction()
    {
        $tabId = $this->post['tabId'] ?? 0;
        list($limit, $offset) = QueryHelper::restLimitOffset('page', 'limit', 4);
        $data['list'] = $this->_getCatDataByID($tabId, $limit, $offset);
        return $this->showJson($data);
    }

    /**
     * 分类筛选
     */
    public function filtermvAction()
    {
        $return = [
            'category' => [],
            'list'     => [],
        ];
        list($limit, $offset, $page) = QueryHelper::restLimitOffset();
        if ($page == 0) {
            $tags = collect(TagsService::filterTagsList())->map(function ($item) {
                if (empty($item)) {
                    return [];
                }
                return ['label' => $item['name'], 'value' => $item['name']];
            })->values()->toArray();
            array_unshift($tags, ['label' => '标签', 'value' => '']);
            $return['category'] = [
                [
                    'name'  => 'order',
                    'items' => [
                        ['label' => '排序', 'value' => ''],
                        ['label' => '最新', 'value' => 'id'],
                        ['label' => '热播', 'value' => 'play_count'],
                        ['label' => '最赞', 'value' => 'like'],
                    ],
                ],
                [

                    'name'  => 'tag',
                    'items' => $tags
                ],
                [
                    'name'  => 'is_fee',
                    'items' => [
                        ['label' => '类型', 'value' => ''],
                        ['label' => '免费', 'value' => 'n'],
                        ['label' => '付费', 'value' => 'y']
                    ],
                ],
            ];
        }
        $return['list'] = $this->_searchAvDataList();
        return $this->showJson($return);
    }

    private function _searchAvDataList()
    {
        list($limit, $offset, $page) = QueryHelper::restLimitOffset();
        $order = $this->post['order'] ?? '';
        $tag = $this->post['tag'] ?? '';
        $fee = $this->post['is_fee'] ?? '';


        if (!in_array($order, ['refresh_at', 'play_count', 'like'])) {
            $order = 'id';
        }
        if ($order == 'play_count') {
            $order = 'rating';
        }
        $cacheKey = "gv:filter:p{$page}:o{$order}:f{$fee}:t{$tag}";

        return cached($cacheKey)->serializerJSON()
            ->usingFuck(false)
            ->expired(900)
            ->fetch(function () use ($tag, $order, $fee, $limit, $offset, $cacheKey) {
                $query = MvModel::queryMv();
                if ($tag) {
                    $query = $query->whereRaw("match(tags) against(? IN BOOLEAN MODE)", [$tag]);
                }
                if ($fee) {
                    $query = $query->where("coins", $fee == 'n' ? '=' : '>', '0');
                }
                $data = $query->orderByDesc($order)
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
                $data && CacheKeysModel::createOrEdit($cacheKey, '筛选GV');
                return (new \service\MvNewService())->v2format($data);
            });
    }


}