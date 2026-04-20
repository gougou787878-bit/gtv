<?php


namespace service;


use helper\QueryHelper;
use helper\RestQuery;

class TagMvService
{


    public function videoForVideoForScan($tag, &$pageConfig)
    {
        if (empty($iterator)) {
            $iterator = null;
        }
//        $query = new RestQuery('sssss', $_POST, redis(), 'mv_id');
//        $a = $query->count(function () use ($tag) {
//            return \MvTagModel::where('tag', '=', $tag)->count();
//        })->all(function ($offset, $limit) use ($tag) {
//            return \MvTagModel::where('tag', '=', $tag)->offset($offset)->limit($limit)->get(['mv_id'])->toArray();
//        });

        //$vidAry = \MvTagModel::getMvIdByTag($tag, $iterator);

        $vidAry = \MvTagModel::getMvIdByTag($tag, $iterator, $pageConfig);

        $results = \MvModel::query()->whereIn('id', $vidAry)
            ->with('user_topic')
            ->with('user:uid,nickname,thumb,uid,expired_at,vip_level,uuid,sexType')
            ->get();
        $mvService = new MvService();
        return $mvService->v2format($results);
    }

    /**
     * 使用标签获取数据
     * @param mixed $tag 标签名字
     * @param null $_limit
     * @param string $type 排序类型，enum(newest=最新, hottest=最热)
     * @param null $watchUser
     * @return array
     * @author xiongba
     * @date 2020-06-02 10:42:46
     */
    public function videoForVideo($tag , $_limit = null, $type = 'newest',$watchUser=null)
    {
        list($page, $limit) = QueryHelper::pageLimit();
        if (is_numeric($_limit)){
            $limit = $_limit;
        }
        //redis()->sAdd('tags:list',$tag);
        $query = \MvModel::queryBase()
            ->select([
                'id',
                'uid',
                'coins',
                'vip_coins',
                'title',
                'duration',
                'cover_thumb',
                'gif_thumb',
                'tags',
                'm3u8',
                'full_m3u8',
                'onshelf_tm',
                'rating',
                'refresh_at',
                'is_free',
                'like',
                'comment',
                'created_at',
                'is_recommend',
                'is_feature',
                'status'
            ])
            ->with('user:uid,nickname,thumb,aff,expired_at,vip_level,uuid,sexType');


        $results = [];
        $key = sprintf("tmv:%s,sort-%s,p-%d:%d", $tag, $type, $page, $limit);
        $ids = cached($key)
            ->expired(7200)
            ->serializerJSON()
            ->fetch(function () use ($query, &$results, $type, $tag, $page, $limit) {
                if ($type == 'hottest') {
                    $query = $query->orderByDesc('like')->orderByDesc('id');//最热
                } elseif ($type) {
                    $query = $query->orderByDesc('id');//最新
                }
                $query->whereRaw("match(tags) against(?)", [$tag])->forPage($page, $limit);
                $results = $query->get();
                return collect($results)->pluck('id');
            });

        if (empty($results) && !empty($ids)) {
            $results = $query->whereIn('id', $ids)->get();
        }

        return (new MvService())->v2format($results,$watchUser);
    }


}