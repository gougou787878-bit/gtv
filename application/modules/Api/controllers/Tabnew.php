<?php

use helper\QueryHelper;
use service\TabNewService;
use service\AdService;
use helper\Validator;
use service\TopCreatorService;

/**
 * Class TabController
 * @date 2020-10-31 15:46:57
 */
class TabnewController extends BaseController
{

    /**
     * 导航栏
     */
    public function indexAction()
    {
        try {
            $member = request()->getMember();
            $service = new TabNewService();
            $data = $service->getNagList($member);
            $this->showJson($data);
        }catch (Throwable $e){
            $this->errorJson($e->getMessage());
        }
    }

    /**
     * 分类列表
     */
    public function list_constructAction()
    {
        try {
            $member = request()->getMember();
            $nag_id = $this->post['nag_id'] ?? 0;
            $sort = $this->post['sort'] ?? 'new';
            test_assert($nag_id,"数据异常");
            list($page,$limit) = QueryHelper::pageLimit();
            /** @var NavigationModel $nag */
            $nag = \NavigationModel::findById($nag_id);
            test_assert($nag,"导航不存在");
            $result = [];
            $service = new TabNewService();
            if ($page == 1){
                //获取banner
                $result['banner'] = AdService::getADsByPosition(AdsModel::POSITION_TAB_FEATURE);
                //中部
                $mid_list = $service->getMidStyle($nag);
                $result = array_merge($result,$mid_list);
                $result['mei_ri'] = null;
                $result['hot_topic'] = null;
                $result['mid_ads'] = [];
                $result['rank'] = [];
                if ($nag->mid_style == NavigationModel::MID_STYLE_RECOMMEND){
                    $result['mid_ads'] = AdService::getADsByPosition(AdsModel::POSITION_INDEX_HOME);
                    $date = date('Y-m-d');
                    //每日推荐
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
                    //广告
                    $result['mei_ri'] = $mei_ri;
                    $result['hot_topic'] = [
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
                    $result['rank'] = [
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
                }
            }
            $bot_list = $service->getBotStyle($member,$nag,$sort,$page,$limit);
            $result = array_merge($result,$bot_list);
            //混合广告
            $list_ads = AdService::getADsByPosition(AdsModel::POSITION_MV_LIST_MIX);
            $result['list_ads'] = AdsModel::formatMixAds($list_ads, $page);

            return $this->showJson($result);
        }catch (Throwable $e){
            $this->errorJson($e->getMessage());
        }
    }

    public function list_tab_mvAction(){
        try {
            $member = request()->getMember();
            $construct_id = $this->post['construct_id'] ?? 0;
            $sort = $this->post['sort'] ?? 'see';
            test_assert($construct_id,"数据异常");
            list($page,$limit) = QueryHelper::pageLimit();
            /** @var ConstructModel $construct */
            $construct = ConstructModel::findById($construct_id);
            test_assert($construct,"结构不存在");
            test_assert($construct->navigation,"导航未配置");
            $service = new TabNewService();
            $list = $service->getMvDataByCat($member, $construct, $sort, $page, $limit);
            $list_ads = AdService::getADsByPosition(AdsModel::POSITION_MV_LIST_MIX);
            $list_ads = AdsModel::formatMixAds($list_ads, $page);

            $this->listJson($list, ['list_ads' => $list_ads]);
        }catch (Throwable $e){
            $this->errorJson($e->getMessage());
        }
    }

    public function construct_listAction(){
        try {
            $data = $this->post;
            $validator = Validator::make($data, [
                'nag_id' => 'required|numeric'
            ]);
            if ($validator->fail($msg)){
                $this->errorJson($msg);
            }
            list($page,$limit) = QueryHelper::pageLimit();
            $member = request()->getMember();
            $nag_id = $data['nag_id'];
            $service = new TabNewService();
            $result = $service->tab_list($member, $nag_id, $page, $limit);
            $this->listJson($result);
        }catch (Throwable $e){
            $this->errorJson($e->getMessage());
        }
    }

    public function list_discoveryAction(){
        try {
            list($page, $limit) = QueryHelper::pageLimit();
            $banner = [];
            $rank = [];
            $icon = [];
            if ($page == 1){
                //获取banner
                $banner = AdService::getADsByPosition(AdsModel::POSITION_TAB_FEATURE);
                //排行榜单
                $rank = cached("rk:index")->serializerJSON()->expired(3600)->fetch(function () {
                    return (new TopCreatorService)->getAllRank('moon', 3);
                });
                //推荐区域
                $icon = [
                    [
                        'name' => '金币专区',
                        'icon' => url_live('/upload_01/ads/20241025/2024102517354235719.png'),
                        'type' => ConstructModel::FIND_TYPE_COINS,
                        'api' => '/api/tabnew/list_find',
                        'params' => ['type' => ConstructModel::FIND_TYPE_COINS],
                        'has_sort' => 1,
                    ],
                    [
                        'name' => 'VIP专区',
                        'icon' => url_live('/upload_01/ads/20241025/2024102517354812724.png'),
                        'type' => ConstructModel::FIND_TYPE_VIP,
                        'api' => '/api/tabnew/list_find',
                        'params' => ['type' => ConstructModel::FIND_TYPE_VIP],
                        'has_sort' => 1,
                    ],
                    [
                        'name' => '热门点赞',
                        'icon' => url_live('/upload_01/ads/20241025/2024102517353717410.png'),
                        'type' => ConstructModel::FIND_TYPE_LIKE,
                        'api' => '/api/tabnew/list_find',
                        'params' => ['type' => ConstructModel::FIND_TYPE_LIKE],
                        'has_sort' => 0,
                    ],
                    [
                        'name' => '最多观看',
                        'icon' => url_live('/upload_01/ads/20241025/2024102517355482972.png'),
                        'type' => ConstructModel::FIND_TYPE_VIEW,
                        'api' => '/api/tabnew/list_find',
                        'params' => ['type' => ConstructModel::FIND_TYPE_VIEW],
                        'has_sort' => 0,
                    ]
                ];
            }
            $items = TagsModel::getList($page, 100);
            //发现精彩
            $body = [
                'type'    => 'tags-mv',
                'name'    => '发现精彩',
                'subName' => '',
                'icon'    => url_live('/new/xiao/20201014/2020101415240919479.png'),
                'desc'    => '',
                'item'    => $items
            ];

            $data = [
                'banner' => $banner,
                'rank' => $rank,
                'icon' => $icon,
                'body' => $body
            ];
            $this->showJson($data);
        }catch (Throwable $e){
            $this->errorJson($e->getMessage());
        }
    }

    public function list_findAction(){
        try {
            $data = $this->post;
            $validator = Validator::make($data, [
                'type'   => 'required',
            ]);
            if ($validator->fail($msg)){
                $this->errorJson($msg);
            }
            list($page,$limit) = QueryHelper::pageLimit();
            $member = request()->getMember();
            $type = $data['type'];
            $sort = $data['sort'] ?? '';
            $service = new TabNewService();
            $result = $service->listFind($member, $type, $sort, $page, $limit);
            $list_ads = AdService::getADsByPosition(AdsModel::POSITION_MV_LIST_MIX);
            $list_ads = AdsModel::formatMixAds($list_ads, $page);
            $this->listJson($result, ['list_ads' => $list_ads]);
        }catch (Throwable $e){
            $this->errorJson($e->getMessage());
        }
    }

    //排行榜配置
    public function rankConfAction()
    {
        try {
            //排行榜
            $tab = [
                [
                    'current' => 1,
                    'id'      => 1,
                    'name'    => '推荐',
                    'type'    => 'recommend',
                    'api'     => 'api/tabnew/rank',
                    'params'  => ['type' => "recommend"],
                    'has_time'=> 0,
                ],
                [
                    'current' => 0,
                    'id'      => 2,
                    'name'    => '获赞',
                    'type'    => 'liked',
                    'api'     => '/api/topcreator/like',
                    'params'  => ['type' => "moon"],
                    'has_time'=> 1,
                ],
                [
                    'current' => 0,
                    'id'      => 3,
                    'name'    => '上传',
                    'type'    => 'upload',
                    'api'     => '/api/topcreator/up',
                    'params'  => ['type' => "moon"],
                    'has_time'=> 1,
                ],
                [
                    'current' => 0,
                    'id'      => 4,
                    'name'    => '收益',
                    'type'    => 'profit',
                    'api'     => '/api/topcreator/income',
                    'params'  => ['type' => "moon"],
                    'has_time'=> 1,
                ]
            ];

            //时间配置
            $time_conf = [
                [
                    'id'      => 1,
                    'name'    => '日榜',
                    'param'   => 'day'
                ],
                [
                    'id'      => 2,
                    'name'    => '周榜',
                    'param'   => 'week'
                ],
                [
                    'id'      => 3,
                    'name'    => '月榜',
                    'param'   => 'moon'
                ],
            ];

            return $this->listJson($tab, ['time_conf' => $time_conf]);
        }catch (Throwable $exception){
            return $this->errorJson($exception->getMessage());
        }
    }

    public function rankAction(){
        try {
            $member = request()->getMember();
            list($page, $limit) = QueryHelper::pageLimit();
            $list = cached('rank:recommend:member:' . $page . ":" . $limit)
                ->group('rank:recommend:member')
                ->chinese('排行榜推荐用户')
                ->fetchPhp(function () use ($page, $limit){
                    $member_ids = setting('rank_recommend_members', '');
                    if (!$member_ids){
                        return [];
                    }
                    $member_ids = explode(',', $member_ids);
                    $ids = collect($member_ids)->forPage($page, $limit)->values()->toArray();
                    $member = MemberModel::whereIn('uid', $ids)
                        ->get(['uid', 'aff', 'nickname', 'thumb','videos_count']);
                    return array_sort_by_idx($member, $ids, 'uid');
                });
            $result = collect($list)->map(function ($item) use ($member){
                $item->watchByUser($member);
                $item->votes = $item->videos_count;
                return $item;
            });

            return $this->showJson($result);
        }catch (Throwable $e){
            return $this->errorJson($e->getMessage());
        }
    }

    public function hotRankAction(){
        try {
            $member = request()->getMember();
            list($page, $limit) = QueryHelper::pageLimit();
            $service = new service\TabNewService();
            $result = $service->hotRank($member, $page);
            return $this->showJson($result);
        }catch (Throwable $e){
            return $this->errorJson($e->getMessage());
        }
    }

    public function listOfTagAction(){
        try {
            $data = $this->post;
            $validator = Validator::make($data, [
                'tag' => 'required',
            ]);
            if ($validator->fail($msg)){
                $this->errorJson($msg);
            }
            list($page,$limit) = QueryHelper::pageLimit();
            $member = request()->getMember();
            $sort = $data['sort'] ?? 'new';
            $tag = $data['tag'];
            $service = new service\TabNewService();
            $result = $service->listMvByTag($member, $tag, $sort, $page, $limit);
            $list_ads = AdService::getADsByPosition(AdsModel::POSITION_MV_LIST_MIX);
            $list_ads = AdsModel::formatMixAds($list_ads, $page);
            return $this->listJson($result, ['list_ads' => $list_ads]);
        }catch (Throwable $e){
            return $this->errorJson($e->getMessage());
        }
    }
}