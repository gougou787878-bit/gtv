<?php

namespace service;

use AdsModel;
use helper\QueryHelper;
use helper\Util;
use Illuminate\Database\Query\JoinClause;
use MvTotalModel;
use SearchIndexModel;

/**
 * 搜索search层
 * Class SearchService
 * @package service
 * @author xiongba
 * @date 2020-03-16 20:16:56
 */
class SearchService
{
    /**
     * @var MvService
     */
    private $service;
    /**
     * @var MvNewService
     */
    private $serviceNew;

    /**
     * SearchService constructor.
     * @author xiongba
     */
    public function __construct()
    {
        $this->service = new MvService;
        $this->serviceNew = new MvNewService();
    }


    public function getIndexAdList(\MemberModel $memberModel, $token)
    {
        $adsData = \service\AdService::getADsByPosition(AdsModel::POSITION_SEARCH_INDEX);
        $uuid = $memberModel->uuid;
        $uid = $memberModel->uid;
        if ($adsData) {
            foreach ($adsData as &$_ad) {
                if (in_array($_ad['type'], [2, 4])) {//不是外部链接
                    $_ad['url'] = getDataByExplode('#', $_ad['url']);//81592#81589
                }
            }
        }
        return $adsData;
    }

    /**
     * 获取热门搜索的关键词
     * @return string[]
     */
    public function getHotKeyword()
    {
        return cached('keyword:top')
            ->serializerPHP()
            ->expired(3600)
            ->fetch(function () {
                $hotWords = trim(setting('search.hot.words', ''));
                if (empty($hotWords)) {
                    $limit = setting('search.hot.limit', 6);
                    $all = SearchIndexModel::getHotSearch($limit);
                    $hotWords = [];
                    foreach ($all as $item) {
                        $hotWords[] = $item->word;
                    }
                } else {
                    $hotWords = explode(',', $hotWords);
                }
                \CacheKeysModel::createOrEdit('keyword:top','热门搜索');
                return $hotWords;
            });
    }

    /**
     * 获取今天热播视频
     * @param int $limit
     * @return array
     */
    public function getHotPlay($limit = 6)
    {
        $useDb = (bool)setting('search.hotPlay.useDb', 1);
        if ($useDb) {
            return $this->getHotPlayUseDb($limit);
        } else {
            return $this->getHotPlayUseConfig($limit);
        }
    }

    /**
     * 获取今天热播视频
     * @param int $limit
     * @param int $isNew
     * @return array
     */
    public function getHotLike($limit = 6,$isNew=0)
    {
        $all = cached('search:hot:like:limit-' . $limit)
            ->expired(600)
            ->serializerPHP()
            ->fetch(function () use ($limit) {
                return MvTotalModel::getLikeScoreMv($limit);
            });
        if($isNew){
            return $this->serviceNew->v2format($all);
        }
        return $this->service->v2format($all);
    }


    /**
     * 从数据库中获取热播的数据
     * @param int $limit
     * @return array
     */
    public function getHotPlayUseDb($limit = 6)
    {
        $all = cached('search:hot:play:limit-' . $limit)
            ->expired(600)
            ->serializerPHP()
            ->fetch(function () use ($limit) {
                return MvTotalModel::getViewScoreMv($limit);
            });
        return $this->service->v2format($all);
    }

    /**
     * 获取配置中配置的热播数据
     * @return array
     */
    public function getHotPlayUseConfig()
    {
        $ids = explode(',', setting('search.hotPlay.ids', '265,266,267,268,269,270'));
        return $this->service->getByIdsKeepSort($ids);
    }


    /**
     * 获取今天热销的视频
     * @param int $limit
     * @return array
     */
    public function getHotSale($limit = 6)
    {
        $useDb = (bool)setting('search.hotSale.useDb', 1);
        if ($useDb || true) {
            return $this->getHotSaleUseDb($limit);
        } else {
            return $this->getHotSaleConfig($limit);
        }
    }


    /**
     * 从数据库中获取热销视频
     * @param int $limit
     * @param int $isNew
     * @return array
     */
    public function getHotSaleUseDb($limit = 6,$isNew= 0)
    {
        $all = cached('search:hot:sale:limit-' . $limit)
            ->expired(600)
            ->serializerPHP()
            ->fetch(function () use ($limit) {
                return MvTotalModel::getSaleScoreMv($limit);
            });
        if($isNew){
            return $this->serviceNew->v2format($all);
        }
        return $this->service->v2format($all , request()->getMember());
    }

    /**
     * 获取配置中配置的热销视频
     * @return array
     */
    public function getHotSaleConfig()
    {
        $ids = explode(',', setting('search.hotSale.ids', '265,266,267,268,269,270'));
        return $this->service->getByIdsKeepSort($ids);
    }


    /**
     * 分析指定的文本的关键词，并更具关键词查询对应的视频id
     * @param string $text
     * @return array 返回一个二维数组 ，[ 匹配上的视频id [], 分析出来的关键词 [] , 是否走了缓存 boolean ]
     * @author xiongba
     * @date 2020-03-17 11:13:28
     */
    public function getVidWithKeyword(string $text)
    {
        $key = 'search:index:' . SearchIndexModel::generateWordHash($text);
        $keywords = [];
        $useCached = true;
        $vidArray = cached($key)
            ->serializerPHP()
            ->expired(1800)
            ->fetch(function () use ($text, &$keywords, &$useCached) {
                $useCached = false;
                $keywords = Util::keyword($text);
                return \MvWordsModel::getVidByWords($keywords);
            });
        return [$vidArray, $keywords, $useCached];
    }

    /**
     * 使用关键词搜索mv
     * @param string $text
     * @param \MemberModel $member
     * @return array
     * @throws \Throwable
     * @author xiongba
     * @date 2019-12-26 20:05:50
     */
    public function searchMv(string $text, \MemberModel $member)
    {
        if (setting('use:fc:search:', 0)) {
            //使用分词搜索
            list($vidArray, $keywords, $useCached) = $this->getVidWithKeyword($text);
            if (!$useCached) {
                \helper\Util::PanicFrequency($member->uid, 10, 60, '1分钟内只能搜索10次');
            }
            array_shift($keywords);
            list($limit, $offset) = QueryHelper::restLimitOffset();
            if ($offset == 0) {
                //第一页才进行关键字收录
                SearchIndexModel::addOrUpdate($text, count($vidArray), $keywords, $vidArray, $useCached ? true : false);
                \SearchTotalModel::addOrUpdate($text, $keywords);
            }
            $list = [];
            $total = 0;
            if (!empty($vidArray)) {
                $total = count($vidArray);
                $vidArray = array_slice($vidArray, $offset, $limit);
                if (!empty($vidArray)) {
                    $list = $this->service->getByIdsKeepSort($vidArray);
                }
            }

//            return [
//                'total'  => $total,
//                'list'   => $list,
//                'lastId' => 0,
//            ];

            //搜索引擎 有数据 就返回  没有就走原生搜索查下
            if ($list) {
                return [
                    'total'  => $total,
                    'list'   => $list,
                    'lastId' => 0,
                ];
            }
        }

        return $this->searchOriginData($text,$member);
    }

    /**
     * 直接搜索 通过mv_id
     * @param $mv_id
     * @param $member
     * @return array
     */
    public function searchByMVID($mv_id,$member){
        $total = 1;
        $list = cached('search:mv_id:' . $mv_id)
            ->expired(1200)
            ->serializerPHP()
            ->fetch(function ()use($mv_id){
                return \MvModel::queryBase()
                    ->where('id','=',(int)$mv_id)
                    ->with('user:uid,aff,thumb,nickname')
                    ->get();
            });
        return [
            'total'  => $total,
            'list'   => (new MvService())->v2format($list,$member),
            'lastId' => 0,
        ];
    }
    /**
     * 原始数据 硬搜索
     * @param string $text
     * @param \MemberModel $member
     * @return array
     */
    public function searchOriginData(string $text, \MemberModel $member){
        //直接使用like 搜索
        list($page,$limit) = QueryHelper::pageLimit();
        $query = \MvModel::queryBase()
            ->where(function ($query)use($text){
                 $query->whereRaw("match(tags) against(?)", [$text])->orWhere('title', 'like', "%$text%");
            });
        $total = cached('search:count:')
            ->suffix($text)
            ->expired(900)
            ->fetch(function () use ($query) {
                return (clone $query)->count('id');
            });
        $list = cached('se:da:' . $text)
            ->suffix($page)
            ->expired(900)
            ->serializerPHP()
            ->fetch(function () use ($query, $limit, $page) {
                return $query->with('user:uid,aff,thumb,nickname')
                    ->orderByDesc('like')->forPage($page , $limit)->get();
            });
        return [
            'total'  => intval($total ?? 0),
            'list'   => (new MvService())->v2format($list,$member),
            'lastId' => 0,
        ];
    }

    /**
     * 原始数据 硬搜索
     * @param string $text
     * @param \MemberModel $member
     * @return array
     */
    public function searchOriginTopicData(string $text, \MemberModel $member){
        //直接使用like 搜索
        list($page,$limit) = QueryHelper::pageLimit();
        //$query = \MvModel::queryBase()->with('user_topic')->where('title', 'like', "%$text%");
        $query = \UserTopicModel::queryUser()
            ->whereRaw("match(tags) against(?)", [$text])
            ->orWhere('title', 'like', "%$text%");
        $total = cached('s:t:c')
            ->suffix($text)
            ->expired(900)
            ->fetch(function () use ($query) {
                return (clone $query)->count('id');
            });
        $list = cached('s:t:list:' . $text)
            ->suffix($page)
            ->expired(900)
            ->serializerPHP()
            ->fetch(function () use ($query, $limit, $page) {
                return $query->orderByDesc('like_count')->forPage($page , $limit)->get();
            });
        return [
            'total'  => intval($total ?? 0),
            'list'   => $list,
            'lastId' => 0,
        ];
    }
    public function searchUser($kwy, \MemberModel $userMember)
    {
        list($limit, $offset) = QueryHelper::restLimitOffset();
        $isMaker = false;
        if (is_numeric($kwy)) {
            $where = [
                ['role_id', '=', \MemberModel::USER_ROLE_LEVEL_MEMBER],
                ['uid', '=', (int)$kwy],
            ];
            $query = \MemberModel::query()
                ->select(['uid', 'aff', 'uuid', 'thumb', 'nickname', 'fans_count', 'videos_count', 'person_signnatrue'])
                ->where($where);
        } else {
            $query = \MemberMakerModel::with(['member' => function ($query) {
                return $query->select(['uid', 'aff', 'uuid', 'thumb', 'nickname', 'fans_count', 'videos_count', 'person_signnatrue']);
            }])->where('nickname', 'like', "%{$kwy}%")->orderByDesc('id');
            $isMaker = true;
        }
        $query->limit($limit)->offset($offset);

        list($total, $items) = cached('search:nickname:' . $kwy)
            //->setSaveEmpty(true)
            ->hash("p-" . ($offset + 1))
            ->serializerPHP()
            ->expired(1800)
            ->fetch(function ($cached) use (&$total, $query, $userMember, $isMaker) {
                $countQuery = clone $query;
                $results = $query->get();
                if ($isMaker) {
                    $results = $results->pluck('member');
                }
                $total = $countQuery->count();
                if ($total == 0) {
                    //如果没有数据，只缓存一分钟
                    $cached->expired(60);
                }
                return [$total, $results];
            });
        $service = new FollowedService();
        $results = $items->toArray();
        foreach ($results as &$result) {
            $result['is_attention'] = $service->isAttentionNew($userMember->aff, $result['aff']);
        }
        return [
            'total'     => $total,
            'list'      => $results,
            'lastIndex' => 0,
        ];

    }


}