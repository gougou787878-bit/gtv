<?php

/**
 * 连续剧 tv模块
 */

use helper\QueryHelper;
use service\AdService;
use service\TagsService;

class DramaController extends BaseController
{
    /**
     * 剧集主页
     *
     * @return bool
     */
    public function indexAction(){
        $return = [];
        $return['ads'] =  AdService::getADsByPosition(AdsModel::POSITION_RECOMMEND);
        $return['cat'] = [
           'name'  => 'cat_drama',
           'items' => collect(UserTopicModel::TYPE_LIST)->map(function ($item){
               return ['label' => $item['name'], 'value' => $item['name'],'type'=>'cat'];
           })->values()->toArray()
       ];
        array_unshift($return['cat']['items'], ['label' => '所有剧集', 'value' => '','type' => 'all']);

        $return['data'][] = [
            'type'    => 'near_topic',
            'name'    => '最近更新',
            'subName' => '',
            'icon'    => '',
            'desc'    => '',
            'item'    => cached('near_topic')
                ->serializerJSON()
                ->expired(900)
                ->usingFuck(false)
                ->fetch(function () {
                    return UserTopicModel::queryUser()
                        ->orderByDesc('id')
                        ->limit(8)
                        ->get()
                        ->toArray();
                })
        ];

        $return['data'][] = [
            'type'    => 'play_count',
            'name'    => '热播剧集',
            'subName' => '',
            'icon'    => '',
            'desc'    => '',
            'item'    => cached('buy_topic')
                ->serializerJSON()
                ->expired(1000)
                ->usingFuck(false)
                ->fetch(function () {
                    return UserTopicModel::queryUser()
                        ->orderByDesc('like_count')
                        ->limit(6)
                        ->get()
                        ->toArray();
                })
        ];
        $return['data'][] = [
            'type'    => 'like_count',
            'name'    => '最赞剧集',
            'subName' => '',
            'icon'    => '',
            'desc'    => '',
            'item'    => cached('hot_topic')
                ->serializerJSON()
                ->expired(1000)
                ->usingFuck(false)
                ->fetch(function () {
                    return UserTopicModel::queryUser()
                        ->orderByDesc('like_count')
                        ->limit(6)
                        ->get()
                        ->toArray();
                })
        ];
        $return['data'][] = [
            'type'    => 'guess_topic',
            'name'    => '猜你喜欢',
            'subName' => '',
            'icon'    => '',
            'desc'    => '',
            'item'    => cached('guess_topic')
                ->serializerJSON()
                ->expired(900)
                ->usingFuck(false)
                ->fetch(function () {
                    return UserTopicModel::queryUser()
                        ->orderByDesc('play_count')
                        ->limit(30)
                        ->get()
                        ->toArray();
                })
        ];

       return $this->showJson($return);
    }



    /**
     * 剧集分类筛选
     */
    public function filtertopicAction()
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

            $type = collect(UserTopicModel::TYPE_LIST)->map(function ($item){
                return ['label' => $item['name'], 'value' => $item['name']];
            })->values()->toArray();
            array_unshift($type, ['label' => '类型', 'value' => '']);

            $area = collect(UserTopicModel::AREA_COUNTRY)->map(function ($item){
                return ['label' => $item, 'value' => $item];
            })->values()->toArray();
            array_unshift($area, ['label' => '地区', 'value' => '']);
            $year = collect(UserTopicModel::YEAR_LIST)->map(function ($item){
                return ['label' => $item, 'value' => $item];
            })->values()->toArray();
            array_unshift($year, ['label' => '年份', 'value' => '']);

            $return['category'] = [
                [
                    'name'  => 'order',
                    'items' => [
                        ['label' => '最新', 'value' => ''],
                       // ['label' => '最新', 'value' => 'id'],
                        ['label' => '热播', 'value' => 'play_count'],
                        ['label' => '最赞', 'value' => 'like_count'],
                    ],
                ],
                [
                    'name'  => 'type',
                    'items' => $type,
                ],
                [
                    'name'  => 'area',
                    'items' => $area,
                ],
                [

                    'name'  => 'tag',
                    'items' => $tags
                ],
                [
                    'name'  => 'year',
                    'items' => $year,
                ],
            ];
        }
        $return['list'] = $this->_searchTopicDataList();
        return $this->showJson($return);
    }

    private function _searchTopicDataList()
    {
        list($limit, $offset, $page) = QueryHelper::restLimitOffset();
        $order = $this->post['order'] ?? '';
        $type = $this->post['type'] ?? '';
        $area = $this->post['area'] ?? '';
        $tag = $this->post['tag'] ?? '';
        $year = $this->post['year'] ?? '';
        if (!in_array($order, ['id', 'play_count', 'like_count'])) {
            $order = 'id';
        }
        //errLog("search:".var_export($this->post,1));
        $cacheKey = "topic:filter:p{$page}:o{$order}:f{$type}:t{$area}:y{$year}";

        return cached($cacheKey)->serializerJSON()
            ->usingFuck(false)
            ->expired(600)
            ->fetch(function () use ($type,$tag, $order,$area,$year, $limit, $offset, $cacheKey) {
                $query = UserTopicModel::queryUser();
                if($type){
                    $query->where('type',$type);
                }if($area){
                    $query->where('area',$area);
                }
                if($tag){
                    $query->whereRaw("match(tags) against(?)", [$tag]);
                }if($year){
                    $query->where("year",'=',(int)$year);
                }
                $data = $query->orderByDesc($order)
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
                $data && CacheKeysModel::createOrEdit($cacheKey, '筛选剧集');
                return $data;
            });
    }

    /**
     * 剧集分类 列表
     * @return bool
     */
    public function getByTypeAction(){
        $type = $this->post['type'] ?? '都市剧情';
        $typeInfo = UserTopicModel::getTypeInfo($type);
        $data = [
            'type'    => 'topic',
            'name'    => $typeInfo['name'],
            'subName' => '',
            'icon'    => '',
            'cover_url'    => url_ads($typeInfo['cover']),
            'desc'    => '',
            'item'    =>$this->_searchTopicDataList()
        ];
        return $this->showJson(['data'=>$data]);
    }

    /**
     * 剧集标签 列表
     * @return bool
     */
    public function getByTagAction(){
        //$type = $this->post['tag'] ?? '原创';
        $data = $this->_searchTopicDataList();
        return $this->showJson($data);
    }

}