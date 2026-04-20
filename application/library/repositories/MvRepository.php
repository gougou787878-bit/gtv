<?php
namespace repositories;


use service\VisitHistoryService;

trait MvRepository
{


    /**
     * 提交观看记录
     * @param \MemberModel $member
     * @param array $log 预期数据应该是 [视频id=>用户观看的时长]
     * @param $watchIdx
     * @param $timestamp
     * @return array
     */
    public function handleCreateWatch(\MemberModel $member , array $log,$watchIdx, $timestamp)
    {
        $todayFirstTime = strtotime(date('Y-m-d'));
        $watchTime = strtotime(date('Y-m-d', $timestamp));
        $expireTimestamp = strtotime(date('Y-m-d', strtotime('+1 days'))) - TIMESTAMP;
        $allExpire = strtotime(date('Y-m-d', strtotime('+ 30days'))) - TIMESTAMP;


        $isVIP = $member->is_vip;
        $watchCount = 0;
        $canWatch = 1024;
        $ids = explode(',',$watchIdx);
        if ($todayFirstTime >= $watchTime) {
            $items = $ids;
            $coinsIds = [];
            if (boolval(setting('config:fee-review:count', 1))) {
                $list = \MvModel::whereIn('id', $ids)->get(['coins', 'id']);
                $items = $list->pluck('id')->toArray();
                $coinsIds = $list->where('coins', '!=', 0)->pluck('id')->toArray();
            }

            // 当日观看记录
            $todayKey = \MvModel::REDIS_USER_TODAY_MV_LIST . $this->member['uid'];
            redis()->sAddArray($todayKey, $items);
            redis()->expire($todayKey, $expireTimestamp);
            //当日收费视频观看预览观看次数
            $feeKey = \MvModel::REDIS_WATCH_COUNT . ':fee:' . date('Ymd');
            $feeNum = redis()->incrBy($feeKey, count($coinsIds));
            if ($feeNum <= count($coinsIds) * 2) {
                redis()->expire($feeKey, $expireTimestamp);
            }

            // 每日观看总数
            redis()->zIncrBy(\MvModel::REDIS_WATCH_COUNT, count($items), date('Ymd'));
            if (!$isVIP) {
                $watchCount = $this->getUserTodayWatchCount($this->member['uid']);
                $canWatch = (int)setting("site.can_watch_count", config('site.can_watch_count', 10));
            }
        } elseif (!$isVIP) {
            $canWatch = (int)setting("site.can_watch_count", config('site.can_watch_count', 10));
        }
        \MvModel::whereIn('id' , $ids)->increment('rating');

        // 历史观看记录
        $history = new VisitHistoryService($member->uid);
        $history->addPlay(...$ids);

        //统计视频播放数
        $ary = array_count_values($ids);
        foreach ($ary as $vid => $val) {
            \MvTotalModel::incrView((int)$vid, $val, date('Y-m-d', $timestamp));
            \RankModel::addRank(\RankModel::TYPE_MV,$vid,\RankModel::FIELD_TYPE_PLAY);
        }
        $uid = $member->uid;
        async_task_cgi(function () use ( $log, $uid) {
            $ids = [];
            foreach ($log as $id => $time) {
                if ($time > 1) {
                    $ids[] = $id;
                }
            }
            if (empty($ids)) {
                return;
            }
            $list = \MvModel::whereIn('id', $ids)->get(['id', 'duration'])->keyBy('id');
            foreach ($ids as $id) {
                $video_time = $list[$id] ? $list[$id]->duration : 0;
                $watchTime = $log[$id];

            }
        });
        $_canWatch = $canWatch - $watchCount;

        return [
            'watched'        => $watchCount,
            'todayTimestamp' => $watchTime,
            'canWatchCount'  => $canWatch,
            'left'           => $_canWatch > 0 ? $_canWatch : 0,
        ];
    }

    /**
     * 获取当日观看次数
     * @param string $uid
     * @return int
     */
    public function getUserTodayWatchCount($uid): int
    {
        return (new \MemberModel())->getTodayWatchCount($uid);
    }

    public function fetchMvList($items)
    {
        $data = $allLiveRoom = [];
        $likeids = redis()->sMembers(\MemberModel::REDIS_USER_LIKING_LIST . $this->member['uid']);
        $followedUids = redis()->sMembers(\UserAttentionModel::REDIS_USER_FOLLOWED_LIST . $this->member['uid']);
        foreach ($items as $key => $item) {
            if (empty($item['id'])){
                continue;
            }
            if (empty($item['user'])) {
                $item['user'] = \MemberModel::getOfficial();
            }
            $item['isLiked'] = in_array($item['id'],$likeids) ? 1 : 0;
            $item['isFollowed'] = in_array($item['uid'],$followedUids) ? 1 : 0;
            $item['islive'] = false;
            if (isset($item['user']['uid']) && in_array($item['user']['uid'],$allLiveRoom)){
                $item['islive'] = true;
            }
            $data[] = $this->fetchMv($item);
        }
        return $data ?? [];
    }

    public function fetchMv($mv)
    {
        $user = [
            'isVV' => $mv['user']['expired_at'] > TIMESTAMP,
            'vvLevel' => $mv['user']['vip_level'] ?? 0,
            'uuid' => $mv['user']['uuid'] ?? '',
            'sexType' => $mv['user']['sexType'] ?? 0,
            'uid' => $mv['uid'] ?? 0,
            'thumb' => $mv['user']['thumb']?url_avatar($mv['user']['thumb']) : '',
            'nickname' => !empty($mv['user']['nickname']) ? $mv['user']['nickname'] : '',
        ];

        $hasBuy = $mv['hasBuy'] ?? true;
        if ($mv['coins'] > 0) {
            $myUid = request()->getMember()->uid;
            if ($mv['uid'] != $myUid) {
                $hasBuy = \MvPayModel::hasPay($this->member['uid'], $mv['id']);
            }
        }
        $hotAds = [];

        $data = [
            'id'           => $mv['id'],
            'uid'          => $mv['uid'],
            'title'        => $mv['title'],
            'coins'        => $mv['coins'],
            'vipCoins'     => $mv['vip_coins'] == -1 ? $mv['coins'] : $mv['vip_coins'],
            'hasBuy'       => $hasBuy,
            'duration'     => $mv['duration'],
            'durationStr'  => durationToString($mv['duration']),
            'like'         => $mv['like'],
            'comment'      => $mv['comment'],
            'tags'         => $mv['tags'],
            'isRec'        => $mv['is_recommend'] ?? 0,
            'thumbImg'     => $mv['cover_thumb'],
            'yCoverUrl'    => $mv['y_cover_url'] ?? '',
            'thumbWidth'   => $mv['thumb_width'] ?? 0,
            'thumbHeight'  => $mv['thumb_height'] ?? 0,
            'gifImg'       => $mv['gif_thumb'] ?? '',
            'gifWidth'     => $mv['gif_width'] ?? 0,
            'gifHeight'    => $mv['gif_height'] ?? 0,
            'playURL'      => $mv['status'] == 1 ? getPlayUrl($mv['m3u8'], true) : '',
            'shareUrl'     => $this->getShareUrl(),
            'hasLongVideo' => $this->hasLongVideo($mv['duration']),
            'status'       => $mv['status'],
            'is_top'       => $mv['is_top'] ?? 0,
            'isLiked'      => $mv['isLiked'] ?? 0,
            'isFollowed'   => $mv['isFollowed'] ?? 0,
            'isLive'       => isset($mv['islive']) ? $mv['islive'] : false,
            'musicID'      => $mv['music_id'] ?? 0,
            'user'         => $user ?? [],
            'rating'       => $mv['rating'] ?? 0,
            'hotAds'       => $hotAds,
            'is_ad'        => 0,//是否视频广告
            'ad_url'       => '',//是视频广告的跳转地址
        ];

        if (isset($mv['score'])){
            $data['score'] = $mv['score'];
        }else{
            $data['score'] = 0;
        }

        return $data;
    }

    /**
     * 是否有长视频
     * @param string $duration
     * @return bool
     */
    private function hasLongVideo($duration)
    {
        $duration = intval($duration);
        return $duration > config('site.long_video_duration', 30);
    }

    public function uploadMv()
    {
        $tags = $this->post['tags'] ?? '';
        $tags = trim($tags,',');
        if (stripos($tags, ',') !== false) {
            $tags = implode(',', explode(',', $tags));
        }
        $coins = intval($this->post['coins'] ?? 0);
        $topic_id = intval($this->post['topic_id'] ?? 0);
        $thumb_height = intval($this->post['thumb_height'] ?? 0);
        $thumb_width = intval($this->post['thumb_width'] ?? 0);
        $cover = $this->post['img_url'] ?? '';
        $title = $this->post['title'] ?? '';
        $m3u8 = $this->post['url'] ?? '';
        $pre_m3u8 = $this->post['pre_url'] ?? '';

        try {
            \DB::beginTransaction();

            $data = [
                'uid'          => $this->member['uid'],
                'title'        => strip_tags($title),
                'm3u8'         => $pre_m3u8,
                'full_m3u8'    => $m3u8,
                'cover_thumb'  => $cover,
                'thumb_height' => $thumb_height,
                'thumb_width'  => $thumb_width,
                'tags'         => strip_tags($tags),
                'via'          => \MvSubmitModel::VIA_USER,
                'coins'        => $coins <= 0 ? 0 : $coins,
                'is_free'      => $coins <= 0 ? \MvSubmitModel::IS_FREE_YES : \MvSubmitModel::IS_FREE_NO,
                'created_at'   => TIMESTAMP,
                'music_id'     => 0,
                'topic_id'     => $topic_id,
            ];

            $itOK = \MvSubmitModel::query()->insert($data);
            if (empty($itOK)) {
                throw new \Exception('创建失败');
            }
            \DB::commit();
            redis()->zIncrBy(\MvModel::STAT_UPLOAD_NUMBER, 1, date('Ymd'));
            return ['success' => true, 'msg' => '上传成功，请等待审核'];
        } catch (\Throwable $e) {
            trigger_log($e);
            \DB::rollBack();
            return ['success' => false, 'msg' => '上传失败，请稍后重试'];
        }
    }

}