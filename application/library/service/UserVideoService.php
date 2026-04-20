<?php

namespace service;


use helper\QueryHelper;
use tools\RedisService;

class UserVideoService
{

    /**
     * UserVideoService constructor.
     * @author xiongba
     */
    public function __construct()
    {
    }

    /**
     * 获取指定用户的视频
     * @param $uid
     * @param $kwy
     * @param $member
     * @return array|mixed
     * @author xiongba
     */
    public function getVideosByUid($uid , $kwy, $member)
    {
        list($page, $limit) = QueryHelper::pageLimit();
        $list = \MvModel::queryBase()
            ->with('user_topic')
            ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff')
            ->when($kwy, function ($query, $val) {
                return $query->where('mv.title', 'like', "%{$val}%");
            })
            ->where(['uid' => $uid])
            ->forPage($page, $limit)
            ->orderByDesc('is_top')
            ->orderByDesc('id')
            ->get();
        return (new MvService())->v2format($list, $member);
    }


    /**
     * 用户喜欢的视频
     * @param string $uid
     * @param $kwy
     * @param $member
     * @return array|bool|mixed|string
     */
    /**
     * 用户喜欢的视频
     * @param string $uid
     * @param $kwy
     * @param $member
     * @return array|bool|mixed|string
     */
    public function getUserLikesVideoList(string $uid, $kwy, $member)
    {
        list($page, $limit) = QueryHelper::pageLimit();
        $key = "user:like:mv:{$uid}:{$page}";
        $kwy && $key = $key.substr(md5($kwy),0,4);
        if ($member->likes_count > 20){
            $items = cached($key)
                ->group('like:' . $uid)
                ->fetchPhp(function ()use($uid,$kwy,$page,$limit){
                    $likeVid = \UserLikeModel::query()
                        ->join('mv', 'mv.id', '=', 'user_likes.mv_id')
                        ->where('user_likes.uid', $uid)
                        ->where('mv.status', \MvModel::STAT_CALLBACK_DONE)
                        ->where('mv.is_hide', \MvModel::IS_HIDE_NO)
                        ->when($kwy, function ($query, $value) {
                            return $query->where('mv.title', 'like', "%{$value}%");
                        })
                        ->forPage($page, $limit)
                        ->pluck('mv.id')
                        ->toArray();

                    $items = \MvModel::queryBase()
                        ->with('user_topic')
                        ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff')
                        ->whereIn('id', $likeVid)
                        ->get();
                    return array_keep_idx($items, $likeVid);
                },60);
        }else{
            $likeVid = cached('tb_ul:idv-' . $uid)
                ->expired(300)
                ->serializerJSON()
                ->setSaveEmpty(true)
                ->fetch(function () use ($uid) {
                    return \UserLikeModel::where('uid', $uid)
                        ->orderByDesc('id')
                        ->pluck('mv_id')
                        ->toArray();
                });
            $items = cached($key)
                ->group('like:' . $uid)
                ->fetchPhp(function ()use($likeVid,$kwy,$page,$limit){

                    $items = \MvModel::queryBase()
                        ->with('user_topic')
                        ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff')
                        ->whereIn('id', $likeVid)
                        ->when($kwy, function ($query, $value) {
                            return $query->where('title', 'like', "%{$value}%");
                        })
                        ->forPage($page, $limit)
                        ->orderByDesc('id')
                        ->get();
                    return $items;
                },60);

        }
        return (new MvService())->v2format($items, $member);
    }

    public function getUserBuysVideoList(\MemberModel $member, $uid, $kwy)
    {
        list($page, $limit) = QueryHelper::pageLimit();
        $key = sprintf('member:mv:buys:%d:%d:%d:%s',$uid,$page,$limit,$kwy);
        $list = cached($key)->fetchPhp(function () use ($uid,$kwy,$page,$limit){
            if(!$kwy){
                $likeVid = \MvPayModel::query()
                    ->where('uid', $uid)
                    ->forPage($page, $limit)
                    ->pluck('mv_id')
                    ->toArray();

                $items = \MvModel::queryBase()
                    ->with('user_topic')
                    ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff')
                    ->whereIn('id', $likeVid)
                    ->get();
                return array_keep_idx($items, $likeVid);

            }else{
                $buyIds = \MvPayModel::where('uid', $uid)
                    ->orderByDesc('id')
                    ->pluck('mv_id')
                    ->toArray();

                return \MvModel::queryBase()
                    ->with('user_topic')
                    ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff')
                    ->whereIn('id', $buyIds)
                    ->when($kwy, function ($query, $value) {
                        return $query->where('title', 'like', "%{$value}%");
                    })
                    ->forPage($page, $limit)
                    ->orderByDesc('id')
                    ->get();
            }

        },600);

        return (new MvService())->v2format($list, $member);
    }
}