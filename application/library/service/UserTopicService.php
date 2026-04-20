<?php
/**
 *
 * @date 2020/2/27
 * @author
 * @copyright kuaishou by KS
 * @todo 专题合集视频相关
 *
 */

namespace service;

use helper\QueryHelper;
use Illuminate\Support\Collection;

/**
 * Class TopicService
 * @package service
 */
class UserTopicService
{
    const TOPIC_LIST_KEY = 'user-topic:list';
    const TOPIC_ROW_KEY = 'user-topic:row:';
    const TOPIC_MV_KEY = 'user-topic:mv:';



    /**
     * @param $topic_id
     * @param \MemberModel $member
     * @return \UserTopicModel|null
     */
    static function getTopicInfo($topic_id, \MemberModel $member): ?\UserTopicModel
    {
        /** @var \UserTopicModel $model */
        $model = \UserTopicModel::queryUser()->where('id', $topic_id)->first();
        if (is_null($model)) {
            return null;
        }
        $model->watchByUser($member)->addHidden('mv_id_str');
        return $model;
    }

    /**
     * 清除列表缓存
     * @param int $topic_id
     * @return int
     */
    static function clearTopicList($topic_id = 0)
    {
        redis()->del(self::TOPIC_LIST_KEY);
        $topic_id && redis()->del(self::TOPIC_ROW_KEY . $topic_id);
        return true;
    }

    /**
     * 清除合集视频缓存
     * @param int $topic_id
     * @return int
     */
    static function clearTopicMV($topic_id = 0)
    {
        $topic_id && redis()->del(self::TOPIC_MV_KEY . $topic_id);
        return true;
    }

    /**
     * 创建合集
     * @param \MemberModel $member
     * @param array $data
     * @return object|\UserTopicModel
     * @throws \Throwable
     */
    public function create_episodes(\MemberModel $member, array $data)
    {
        $member = \MemberModel::find($member->uid);
        return transaction(function () use ($member, $data) {
            $model = \UserTopicModel::createByData($member->uid, $data);
            if (empty($model)) {
                throw new \Exception('操作失败，请重试1');
            }
            if ($member->maker) {
                $member->maker->topic_count++;
                $itOk = $member->maker->save();
                if (empty($itOk)) {
                    throw new \Exception('操作失败，请重试3');
                }
                \MemberModel::clearFor($member);
            }
            return $model;
        });
    }

    /**
     * 切换点赞
     * @param \MemberModel $member
     * @param int $topic_id
     * @return string
     * @throws \Throwable
     */
    public function toggle_like(\MemberModel $member, int $topic_id): string
    {
        $topic = \UserTopicModel::find($topic_id);
        test_assert($topic , '剧集不存在');
        return transaction(function () use ($topic, $member) {
            $where = [
                'uid'      => $member->uid,
                'topic_id' => $topic->id,
            ];
            $like = \UserTopicLikeModel::where($where)->first();

            if (empty($like)) {
                $isOk = \UserTopicLikeModel::createBy($member , $topic);
                $num = 1;
                $status = 'set';
            } else {
                $isOk = $like->delete();
                $num = -1;
                $status = 'unset';
            }
            if (empty($isOk)) {
                throw new \Exception('操作失败');
            }
            $where = [
                'id'         => $topic->id,
                'like_count' => $topic->like_count
            ];
            $isOk = \UserTopicModel::where($where)->increment('like_count', $num);
            test_assert($isOk , '操作失败');
            return $status;
        });
    }

    /**
     * 切换合集置顶状态
     * @param \MemberModel $member
     * @param int $topic_id
     * @return string
     * @throws \Throwable
     */
    public function toggle_top(\MemberModel $member, int $topic_id): string
    {
        $topic = \UserTopicModel::find($topic_id);
        if (empty($topic) || $topic->uid != $member->uid) {
            throw new \Exception('合集不存在');
        }

        if ($topic->is_top == \UserTopicModel::IS_TOP_NO) {
            $topCount = \UserTopicModel::where(['uid' => $member->uid])
                ->where('is_top', \UserTopicModel::IS_TOP_YES)
                ->count();

            $limitTop = intval(setting('user-topic.top-count', 3));
            if ($topCount >= $limitTop) {
                throw new \Exception("最多允许置顶{$limitTop}个");
            }
        }

        return transaction(function () use ($topic, $member) {
            $status = $topic->is_top == \UserTopicModel::IS_TOP_YES ? 'unset' : 'set';
            $itOk = $topic->toggleColumn('is_top', array_keys(\UserTopicModel::IS_TOP));
            if (empty($itOk)) {
                throw new \Exception('操作失败');
            }
            return $status;
        });
    }

    /**
     * 切换合集置顶状态
     * @param \MemberModel $member
     * @param int $topic_id
     * @return string
     * @throws \Throwable
     */
    public function toggle_hide(\MemberModel $member, int $topic_id): string
    {
        $topic = \UserTopicModel::find($topic_id);
        if (empty($topic) || $topic->uid != $member->uid) {
            throw new \Exception('剧集不存在');
        }

        return transaction(function () use ($topic, $member) {
            $status = $topic->is_hide == \UserTopicModel::IS_HIDE_YES ? 'unset' : 'set';
            $itOk = $topic->toggleColumn('is_hide', array_keys(\UserTopicModel::IS_HIDE));
            if (empty($itOk)) {
                throw new \Exception('操作失败');
            }
            return $status;
        });
    }

    /**
     * 修改合集的视频
     * @param \MemberModel $member
     * @param int $id
     * @param string $idStr
     * @return \UserTopicModel
     * @throws \Throwable
     */
    public function update_topic(\MemberModel $member, int $id, string $idStr)
    {
        $idAry = collect(explode(',', $idStr))->flip()->keys()->filter();
        $topic = \UserTopicModel::find($id);
        test_assert($topic, "剧集不存在");
        test_assert($topic->uid == $member->uid, '无权限操作');
        test_assert($topic->status == \UserTopicModel::STAT_PASS, '剧集状态未通过');
        test_assert($idAry->count(), '视频不能为空');
        /** @var \MvModel[]|Collection $videoAry */
        $videoAry = \MvModel::whereIn('id', $idAry)->where('uid',$member->uid)->get(['id', 'uid', 'topic_id', 'title']);
        if ($videoAry->count() != $idAry->count()) {
            throw new \Exception('数据库的数据量不一致');
        }

        return transaction(function () use ($idAry, $member, $topic) {
            $where = ['uid' => $member->uid, 'topic_id' => $topic->id];
            \MvModel::where($where)->update(['topic_id' => 0]);
            $topic->mv_id_str = trim($idAry->join(','),',');
            $topic->video_count = $idAry->count();
            test_assert($topic->save(), '操作失败，请重试');
            $itOk = \MvModel::whereIn('id', $idAry)->update(['topic_id' => $topic->id]);
            test_assert(!blank($itOk), '操作失败，请重试');
            cached(self::TOPIC_ROW_KEY . $topic->id)->clearCached();
            return $topic;
        });

    }

    /**
     * @param \MemberModel $member
     * @param int $id
     * @return mixed
     * @throws \Throwable
     */
    public function clearVideo(\MemberModel $member, int $id)
    {
        $topic = \UserTopicModel::find($id);
        test_assert($topic, "剧集不存在");
        test_assert($topic->uid == $member->uid, '无权限操作');
        return transaction(function () use ($member, $topic) {
            if (0 == $topic->video_count) {
                return $topic;
            }
            $idAry = $topic->mv_id_ary;
            /** @var \MvModel[]|Collection $videoAry */
            $isOk = \MvModel::whereIn('id', $idAry)
                ->where('uid', $member->uid)
                ->where('topic_id', $topic->id)
                ->update(['topic_id' => 0]);
            test_assert($isOk, '操作失败');
            $topic->mv_id_str = '';
            $topic->video_count = 0;
            test_assert($topic->save(), '操作失败，请重试');
            cached(self::TOPIC_ROW_KEY . $topic->id)->clearCached();
            return $topic;
        });
    }


    public function listOfLike(\MemberModel $member)
    {
        list($page, $limit, $last_ix) = QueryHelper::pageLimit();

        $where = [
            'uid' => $member->uid
        ];
        /** @var \UserTopicModel[] $topics */
        $topics = \UserTopicLikeModel::where($where)
            ->with('topic')
            ->forPage($page, $limit)
            ->orderByDesc('id')
            ->get()
            ->pluck('topic')->map(function ($topic){
                $topic->is_like =1;
                return $topic;

            });
        return $topics;
    }

    public function listOfTopic($uid, $status = 1)
    {
        list($page, $limit, $last_ix) = QueryHelper::pageLimit();
        $topics = \UserTopicModel::query()->where([
            'uid'    => $uid,
            'status' => $status,
        ])->with('user:uid,nickname,thumb,aff,expired_at,vip_level,uuid,sexType')
            ->forPage($page, $limit)
            ->orderByDesc('id')
            ->get();
        return $topics;
    }

    /**
     * 发布视频  我的剧集列表选择
     * @param $uid
     * @return mixed|null
     */
    static function getTopicByUidAll($uid)
    {
        //APP_ENVIRON == 'test' && $uid = 43787;
        $data = cached("all:topic:{$uid}")->usingFuck()->expired(600)->serializerJSON()->fetch(function () use ($uid) {
            return \UserTopicModel::queryBase()->where('uid', $uid)->get()->map(function ($item){
                if(!is_null($item)){
                    return [
                        'id'=>$item->id,
                        'title'=>$item->title,
                    ];
                }
               return null;
            })->filter()->toArray();
        });
        return $data ? $data : null;
    }

    /**
     * 删除合集
     * @param \MemberModel $member
     * @param int $topic_id
     * @return mixed
     * @throws \Throwable
     */
    public function delete_topic(\MemberModel $member, int $topic_id)
    {
        $topic = \UserTopicModel::find($topic_id);
        test_assert($topic, "剧集不存在");
        test_assert($topic->uid == $member->uid, '无权限操作');
        return transaction(function () use ($member, $topic) {
            test_assert($topic->delete(), '操作失败，请重试1');
            if ($member->maker) {
                $member->maker->topic_count--;
                test_assert($member->maker->save(), '操作失败，请重试2');
            }
            if (0 == $topic->video_count) {
                return true;
            }
            $idAry = $topic->mv_id_ary;
            //更新视频的topid
            $itOk = \MvModel::whereIn('id', $idAry)
                ->where('topic_id', $topic->id)
                ->update(['topic_id' => 0]);
            test_assert($itOk , '操作失败，请重试3');
            //删除点赞
            if ($topic->like_count) {
                $itOk = \UserTopicLikeModel::where('topic_id', $topic->id)->delete();
                test_assert($itOk , '操作失败，请重试4');
            }

            cached(self::TOPIC_ROW_KEY . $topic->id)->clearCached();
            return true;
        });
    }

    protected function getIntvalCb()
    {
        return function ($v) {
            return intval(trim($v));
        };
    }

    /**
     * 搜索没有加入过其他合集的视频
     * @param \MemberModel $member
     * @param $kwy
     * @return array
     */
    public function listmv(\MemberModel $member, $kwy)
    {
        list($page, $limit, $last_ix) = QueryHelper::pageLimit();
        $uid = $member->uid;
        $where = [
            'uid'      => $uid,
            'topic_id' => 0
        ];

        $list = \MvModel::queryBase()
            ->forPage($page, $limit)
            ->where($where)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->when($kwy, function ($query, $val) {
                return $query->where('title', 'like', "%$val%");
            })->get();
        return (new MvService())->v2format($list);
    }

    /**
     * 热门点赞
     * @param \MemberModel $member
     * @param $limit
     * @return Collection|\UserTopicModel[]
     */
    public function popular(\MemberModel $member, $limit)
    {
        $items = cached('topic:popular:' . $limit)
            ->expired(600)
            ->serializerJSON()
            ->setSaveEmpty(true)
            ->fetch(function () use ($limit) {
                return \UserTopicModel::orderByDesc('like_count')
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get()
                    ->map(function (\UserTopicModel $item) {
                        return $item->getAttributes();
                    })
                    ->toArray();
            });
        $items = \UserTopicModel::itRelated($items,
            [
                'user:uid,nickname,thumb,aff,expired_at,vip_level,uuid,sexType'
            ])
            ->map(function (\UserTopicModel $item) use ($member) {
                return $item->watchByUser($member);
            });
        return $items;
    }

    /**
     * 将视频加入到剧集
     * @param \MemberModel $member
     * @param \UserTopicModel|null $topic
     * @param \MvModel|null $mv
     * @return mixed
     * @throws \Throwable
     */
    public function addVideo(\MemberModel $member, ?\UserTopicModel $topic, ?\MvModel $mv)
    {
        test_assert($topic, "剧集不存在");
        test_assert($mv, "视频不存在");
        test_assert($topic->uid == $member->uid, '无权限操作');
        test_assert($topic->status == \UserTopicModel::STAT_PASS, '剧集状态未通过');
        test_assert($mv->uid == $member->uid, '无权限操作');
        test_assert(empty($mv->topic_id), '视频已加入剧集');
        return transaction(function () use ($member, $topic, $mv) {
            $idStr = trim($topic->mv_id_str);
            if (empty($idStr)) {
                $topic->mv_id_str = "{$mv->id}";
            } else {
                $topic->mv_id_str = sprintf("%s,%d", $topic->mv_id_str, $mv->id);
            }
            test_assert($topic->save(), '操作失败1');
            $mv->topic_id = $topic->id;
            test_assert($mv->save(), '操作失败2');
            return true;
        });
    }

    /**
     * 将删除剧集的合集
     * @param \MemberModel $member
     * @param int $id
     * @param int $mv_id
     * @return mixed
     * @throws \Throwable
     */
    public function delVideo(\MemberModel $member, int $id, int $mv_id)
    {
        $topic = \UserTopicModel::find($id);
        test_assert($topic, "剧集不存在");
        test_assert($topic->uid == $member->uid, '剧集无权限操作');
        test_assert($topic->status == \UserTopicModel::STAT_PASS, '剧集状态未通过');
        test_assert(in_array($mv_id, $topic->mv_id_ary), '视频没有在该剧集');
        $mv = \MvModel::find($mv_id);
        test_assert($mv, "视频不存在");
        test_assert($mv->uid == $member->uid, '视频无权限操作');
        test_assert($mv->topic_id, '视频没有加入剧集');
        test_assert($mv->topic_id == $topic->id, '视频没有在该剧集');
        return transaction(function () use ($member, $topic, $mv) {
            $key = array_search($mv->id, $topic->mv_id_ary);
            test_assert($key !== false, '视频没有在该剧集');
            $ary = $topic->mv_id_ary;
            unset($ary[$key]);
            $topic->mv_id_str = join(',', $ary);
            test_assert($topic->save(), '操作失败1');
            $mv->topic_id = 0;
            test_assert($mv->save(), '操作失败2');
            return true;
        });
    }


}