<?php


namespace service;


use tools\CurlService;
use tools\RedisService;

class MvLikeService extends \AbstractBaseService
{


    /**
     * 视频点赞
     * @param int $id
     * @param \MemberModel $member
     * @return bool
     * @throws \Exception  如果视频不存在抛出异常
     */
    public function toggleLikeMv(int $id, \MemberModel $member)
    {
        /** @var \MvModel $mv */
        $mv= \MvModel::queryWithUser()->where('id', $id)->first();
        if (is_null($mv)) {
            throw new \Exception('视频不存在');
        }
        $_mvUser = $mv->user;
        $creator_id = $mv->uid;
        $uid = $member['uid'];

        $has = \UserLikeModel::where(['uid' => $uid, 'mv_id' => $id])->exists();
        if ($has) {
            \MemberModel::incrMultiLine([
                $creator_id => ['fabulous_count' => -1], //更新 $creator_id 用户的 fabulous_count - 1
                $uid        => ['likes_count' => -1], //更新 $uid 用户的 likes_count - 1
            ]);
            \MvModel::where('id', $id)->decrement('like', 1);
            \UserLikeModel::where(['uid' => $uid, 'mv_id' => $id])->delete();
            \MvTotalModel::incrLike($id, -1);

            redis()->sRem(\MemberModel::REDIS_USER_LIKING_LIST . $uid, $id); // 点赞记录
            redis()->zIncrBy(\MvModel::REDIS_USER_LIKE_TODAY_COUNT, -1, date('Ymd')); // 每日点赞数

            $msg = '取消点赞成功';
        } else {
            \MemberModel::incrMultiLine([
                $creator_id => ['fabulous_count' => 1],
                $uid        => ['likes_count' => 1],
            ]);
            \MvModel::where('id', $id)->increment('like', 1);
            \UserLikeModel::create(['uid' => $uid, 'mv_id' => $id]);
            \MvTotalModel::incrLike($id);
            \RankModel::addRank(\RankModel::TYPE_MV,$id,\RankModel::FIELD_TYPE_LIKE);
            \MessageModel::createMessage($member->uuid, $_mvUser->uuid, "[{$member->nickname}]赞了您的视频~", $mv->title, $id,
                \MessageModel::TYPE_MV_LIKE);
            redis()->sAdd(\MemberModel::REDIS_USER_LIKING_LIST . $uid, $id);
            redis()->zIncrBy(\MvModel::REDIS_USER_LIKE_TODAY_COUNT, 1, date('Ymd'));
            \MvModel::addWeekRank(\MvModel::WEEK_LIKE_TYPE, $id);

            $msg = '点赞成功';
            $this->sendBrandGroup($member,$id);//白名单验证品宣资源发送
        }

        //公司上报
        (new EventTrackerService(
            $member->oauth_type,
            $member->invited_by,
            $member->uid,
            $member->oauth_id,
            $_POST['device_brand'] ?? '',
            $_POST['device_model'] ?? ''
        ))->addTask([
            'event'                 => EventTrackerService::EVENT_VIDEO_LIKE,
            'video_title'           => $mv->title,
            'video_id'              => (string)$mv->id,
            'video_type_id'         => '',
            'video_type_name'       => '',
            'flag'                  => empty($has) ? 1 : 2,
        ]);

        return $msg;
    }

    /**
     * 品宣资源发送
     * @param $member
     * @param $mv_id
     */
    function sendBrandGroup($member, $mv_id)
    {
        $brand_uid = setting('brandgroup.uiddata', '');
        if (empty($brand_uid)) {
            return;
        }
        $brand_uid_data = explode('#', $brand_uid);
        if (!in_array($member->uid, $brand_uid_data)) {
            return;
        }
        /** @var \MvModel $mvData */
        $mvData = \MvModel::query()->where('id', $mv_id)->first();
        if (is_null($mvData)) {
            return;
        }
        $now = time();
        $postData = [
            'app_name'  => SYSTEM_ID,
            'title'     => $mvData->title,
            'rating'    => $mvData->rating,
            'm3u8'      => $mvData->m3u8 ? $mvData->m3u8 : $mvData->full_m3u8,
            'nickname'  => $mvData->user ? $mvData->user->nickname : '官方',
            'tags'      => is_string($mvData->tags) ? $mvData->tags : join(',',$mvData->tags),
            'timestamp' => $now,
            'sign'      => md5($now . 'e096db7c006958f226bc469c27237b65')
        ];
        $url = 'https://tomp4.91tv.tv/';
        errLog('brandData:' . var_export([$url, $postData], 1));
        $rs = (new CurlService())->curlPost($url, $postData);
        \AdminLogModel::addLog($member->uid, 'other', var_export([$postData, $rs], 1));

    }


}