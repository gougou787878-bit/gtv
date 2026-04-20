<?php


namespace service;


use DB;
use helper\QueryHelper;
use MvModel;
use repositories\MvRepository;
use repositories\UsersRepository;
use UserAttentionModel;
use const Yaf\ENVIRON;

class MvService extends \AbstractBaseService
{
    use MvRepository;
    use UsersRepository;


    public function formatItem($datum, $watchByMember = null,$hasBuy = false)
    {
        if(is_null($datum) || empty($datum)){
            return [];
        }

        /** @var MvModel $datum */
        $datum->addHidden([
            'cover_thumb',
            'm3u8',
            'full_m3u8',
             // 'is_hide',
            'y_cover',
            'tags',
            'actors',
            'category',
            'via',
            'onshelf_tm',
            'music_id'
        ]);
        if ($watchByMember !== false) {
            $datum->watchByUser($watchByMember);
            if ($datum->user) {
                $datum->user->addHidden([
                    'phone',
                    'birthday',
                    'thumb',
                    'app_version',
                    'regip',
                    'lastvisit',
                    'lastip',
                    'username',
                    'password',
                    'oauth_id',
                    'build_id',
                    'oauth_type',
                    'uuid',
                    'gender',
                    'regdate',
                    'invited_by',
                    'invited_num',
                    'login_count',
                    'chat_uid',
                    'live_supper',
                    'is_live_super',
                ]);
                $datum->user->watchByUser($watchByMember);
            } else {
                $datum->user = \MemberModel::virtualByForDelele();
            }
        }
        $datum->is_free = $datum->coins <= 0 ? 1 : 0;
        $attributes = $datum->getAttributes();
        $m3u8 = $attributes['full_m3u8'] ?? null;
        if (empty($m3u8)) {
            $m3u8 = $attributes['m3u8'] ?? '';
        }
        $datum->play_url = getPlayUrl($m3u8, true);
        if($hasBuy){
            $datum->play_url = getPlayUrl($m3u8);
        }
        $datum->hasLongVideo = $datum->duration > 30;
        $datum->hotAds = [];
        $datum->is_ad = 0;//是否视频广告
        $datum->ad_url = '';//是视频广告的跳转地址
        if (IS_FAKE_CLIENT) {
            $datum->play_url = getPlayUrl(ILLEGAL_ORG_VIDEO);
        }
        return $datum;
    }

    public function formatList($items, $watchByMember = null)
    {
        return $this->v2format($items, $watchByMember);
    }


    public function v2format($items, $watchByMember = null, $needFeatureAds = false)
    {
        if (empty($items)) {
            return [];
        }
        $lists = [];
        foreach ($items as $datum) {
            $lists[] = $this->formatItem($datum, $watchByMember);
        }

        return $lists;
    }




    /**
     * 使用指定的id获取视频。并保障和id排序一样
     * @param array $ids
     * @return array
     * @author xiongba
     * @date 2020-03-16 20:12:52
     */
    public function getByIdsKeepSort(array $ids)
    {
        $member = request()->getMember();
        $all = \MvModel::queryBase()->whereIn('id', $ids)->get();
        //$all = $this->v2format($all, $member);
        $all = $this->v2format($all);
        $ary = array_reindex($all, 'id');
        $result = [];
        foreach ($ids as $id) {
            if (isset($ary[$id])) {
                $result[] = $ary[$id];
            }
        }
        return $result;
    }

    /**
     * 使用指定标签获取收费视频id，并将结果缓存10分钟
     * @param string $tag 视频标签
     * @param null $official
     * @return array
     * @author xiongba
     */
    public function getChargeMvIdByTagName(string $tag, $official = null)
    {
        $key = 'charge:tag:' . $tag;
        if ($official === true) {
            $key .= '-official';
        } elseif ($official === false) {
            $key .= '-user';
        }
        return cached($key)
            ->setSaveEmpty(true)
            ->expired(600)
            ->serializerPHP()
            ->fetch(function ($cached) use ($tag, $official) {
//                $idInTags = \MvTagModel::distinct()
//                    ->where('tag' , '=' , $tag)
//                    ->get(['mv_id'])
//                    ->map(function ($v){
//                        return $v->mv_id;
//                    })->toArray();
//                $ids = \MvModel::queryBase()
//                    ->where('coins' ,'>' , 0)
//                    ->whereIn('id' , $idInTags)
//                    ->get(['id'])
//                    ->map(function ($v){
//                        return $v->id;
//                    });
                if (ENVIRON == 'test') {
                    $query = MvModel::queryFee()->where('tags', 'like', "%$tag%");
                } else {
                    $query = MvModel::queryFee()->whereRaw("match(tags) against(?)", [$tag]);
                }
//                if ($official === true) {
//                    $query->where('uid', (int)getOfficialUID());
//                } elseif ($official === false) {
//                    $query->where('uid', '!=', (int)getOfficialUID());
//                }
                $ids = $query->orderByDesc('id')->pluck('id');

                if ($ids->isEmpty()) {
                    /** @var \CacheDb $cached */
                    $cached->expired(200);
                }
                return $ids->toArray();
            });
    }

    /**
     * 使用指定标签获取收费视频
     * @param \MemberModel $member
     * @param int $lastId
     * @param string $tag
     * @param null $official 是否值读取官方账号的视频 ，null全部，true=只读官方的。false=只读用户的
     * @return array
     * @author xiongba
     */
    public function getChargeMvByCached(\MemberModel $member, int $lastId, string $tag, $official = null)
    {

        list($limit, $offset) = QueryHelper::restLimitOffset();
        //剔除用户买过的视频
        $boughtVidArray = \MvPayModel::getVid($member->uid);
        $ids = collect(array_diff($this->getChargeMvIdByTagName($tag, $official), $boughtVidArray));
        if ($lastId) {
            $ids->filter(function ($v) use ($lastId) {
                return $v < $lastId;
            });
            $offset = 0;
        }
        $ids = $ids->slice($offset, $limit)->toArray();
        $list = MvModel::queryWithUser()->with('user_topic')->whereIn('id', $ids)->orderBy('refresh_at', 'desc')->get();
        $list = $this->v2format($list, $member);
        return [
            'total'     => count($ids),
            'list'      => $list,
            'lastIndex' => 0
        ];
    }

    /**
     * 在缓存中获取用户的关注作者的视频id，如果缓存没有，从数据库获取
     * @param \MemberModel $member
     * @return mixed
     * @author xiongba
     */
    public function getChargeVidByFollow(\MemberModel $member)
    {
        return $this->getVidByFollow($member, true);
    }

    /**
     * 在缓存中获取用户的关注作者的视频id，如果缓存没有，从数据库获取
     * @param \MemberModel $member
     * @param null $isFee
     * @return mixed
     * @author xiongba
     */
    public function getVidByFollow(\MemberModel $member, $isFee = null)
    {
        $uidArt = UserAttentionModel::getList($member)->pluck('touid');
        return cached('follow:vid:' . $isFee ?? 'null')
            ->suffix($member->uid)
            ->serializerPHP()
            ->expired(7200)
            ->fetch(function () use ($uidArt, $isFee) {
                $query = \MvModel::query()->whereIn('uid', $uidArt)
                    ->where('status', '=', MvModel::STAT_CALLBACK_DONE)
                    ->where('is_hide', '=', 0);
                if ($isFee === false) {
                    $query->where('coins', '=', 0);
                } elseif ($isFee === true) {
                    $query->where('coins', '>', 0);
                }
                //获取用户关注者的视频列表
                return $query->pluck('id')->toArray();
            });
    }

    /**
     * 获取用户关注的作者发布的收费视频
     * @param \MemberModel $member
     * @return array
     * @author xiongba
     * @date 2020-03-17 19:14:25
     */
    public function getChargeMvByFollow(\MemberModel $member)
    {
        list($limit, $offset, $page) = QueryHelper::restLimitOffset();
        $uidArt = UserAttentionModel::getList($member)->pluck('touid');
        $query = MvModel::queryWithUser()
            ->with('user_topic')
            ->whereIn('uid', $uidArt)
            ->where('coins', '>', 0);
        $totalQuery = clone $query;
        $mvItems = $query->offset($offset)
            ->limit($limit)
            ->orderByDesc('refresh_at')
            ->get();

        return [
            'total'     => $totalQuery->count(),
            'list'      => (new MvService())->v2format($mvItems, $member),
            'lastIndex' => 0,
        ];
    }

    /**
     * 购买视频
     * @param \MemberModel $member
     * @param $vid
     * @return MvModel
     * @throws \Throwable
     * @author xiongba
     */
    public function buyMv(\MemberModel $member, $vid)
    {
        //1 查询视频
        /** @var MvModel $videoModel */
        $videoModel = MvModel::query()->with('user:uid,nickname,thumb,vip_level,auth_status,sexType,expired_at,uuid,oauth_type,oauth_id,votes,votes_total,score,score_total')->find($vid);
        if (empty($videoModel)) {
            throw new \Exception('视频不存在');
        }
        if (empty($videoModel->coins)) {
            throw new \Exception('该视频不支持购买');
        }
        /**
         * @var \MemberModel $authorModel
         */
        $authorModel = $videoModel->user;
        //$transfer2user = boolval(setting('charge.video.transfer', 0));
        $transfer2user = 1;
        if ($authorModel->uid == $member->uid) {
            return $this->formatItem($videoModel, $member,true);
        }
        // 不允许重复购买视频购买
        if (\MvPayModel::hasPay($member->uid, $vid)) {
            //cached('v2:user:idolVideo:')->suffix($member->uid)->clearCached();
            return $this->formatItem($videoModel, $member,true);
        }
        $total = $videoModel->coinsAfterDiscount($member);

        try {
            DB::beginTransaction();
            //扣款
            do {
                //价格小于等于0 不需要影响用户的日志和首款日志
                if ($total <= 0) {
                    break;
                }
                $itOk = $member->incrMustGE_raw(['coins' => -$total, 'consumption' => $total]);
                if (empty($itOk)) {
                    throw new \Exception('扣款失败,请确认您的金币是否足够', 1008);
                }
                //记录日志
                $action = 'buymv';
                $rs3 = \UsersCoinrecordModel::addMvExpend($member->uid, $videoModel, $total);
                if (empty($rs3)) {
                    throw new \Exception('操作失败，请重试');
                }
                //不需要转账或者作者数据不存在，不转收益
                if (!$transfer2user || empty($authorModel)) {
                    break;
                }
                $itOk = $authorModel->incrMustGE_raw(['score' => $total, 'score_total' => $total]);
                if (empty($itOk)) {
                    throw new \Exception('转账失败');
                }

            } while (false);

            $model = \MvPayModel::createBuyLog($member->uid, $videoModel->uid, $videoModel->id, $total);
            if (empty($model)) {
                throw new \Exception('操作失败，请重试');
            }
            $itOk = \MvTotalModel::incrBuy($videoModel->id, 1);
            if (empty($itOk)) {
                throw new \Exception('操作失败，请重试');
            }
            $itOk = $videoModel->increment('count_pay', 1);
            if (empty($itOk)) {
                throw new \Exception('操作失败，请重试');
            }
            DB::commit();

            \RankModel::addRank(\RankModel::TYPE_MV,$videoModel->id,\RankModel::FIELD_TYPE_PAY);

            if ($authorModel->auth_status) {
                \MemberMakerModel::where(['uuid' => $authorModel->uuid])->increment('total_coins', $total);
                \MemberMakerStatModel::addStat($authorModel->uuid,\MemberMakerStatModel::FIELD_MV_PORFIT,$total);
            }
            //更新用户缓存
            \MemberModel::clearFor($member);
            \MemberModel::clearFor($authorModel);

            //cached('v2:user:idolVideo:')->suffix($member->uid)->clearCached();
            for ($i = 0; $i <= $member->likes_count / 20; $i++) {
                cached(\MvModel::REDIS_USER_LIKE_VIDEOS_ITEM . $member->uid . '_')->suffix($i)->clearCached();
            }
            $videoModel->emitChange(false);
            \MvPayModel::addVidArr($member->uid, $vid);

            //金币消耗上报
            (new EventTrackerService(
                $member->oauth_type,
                $member->invited_by,
                $member->uid,
                $member->oauth_id,
                $_POST['device_brand'] ?? '',
                $_POST['device_model'] ?? ''
            ))->addTask([
                'event'                 => EventTrackerService::EVENT_COIN_CONSUME,
                'product_id'            => (string)$videoModel->id,
                'product_name'          => $videoModel->title,
                'coin_consume_amount'   => (int)$videoModel->coins,
                'coin_balance_before'   => (int)$member->coins,
                'coin_balance_after'    => (int)($member->coins - $total),
                'consume_reason_key'    => 'video_unlock',
                'consume_reason_name'   => '视频解锁',
                'order_id'              => (string)$rs3->id,
                'create_time'           => to_timestamp($rs3->addtime),
            ]);

            //视频购买上报
            (new EventTrackerService(
                $member->oauth_type,
                $member->invited_by,
                $member->uid,
                $member->oauth_id,
                $_POST['device_brand'] ?? '',
                $_POST['device_model'] ?? ''
            ))->addTask([
                'event'                 => EventTrackerService::EVENT_VIDEO_PURCHASE,
                'video_id'              => (string)$videoModel->id,
                'video_title'           => $videoModel->title,
                'video_type_id'         => '',
                'video_type_name'       => '',
                'coin_quantity'         => (int)$videoModel->coins,
                'order_id'              => (string)$model->id,
            ]);

            return $this->formatItem($videoModel, $member,true);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * 获取用户购买的视频
     * @param \MemberModel $member 用户
     * @param int $lastIndex 缩影
     * @return array
     */
    public function getBought(\MemberModel $member, $lastIndex)
    {
        list($limit, $offset) = QueryHelper::restLimitOffset();
        $query = \MvPayModel::getBought($member->uid)->with([
            'mv' => function ($query) {
                return $query->with('user:uid,nickname,thumb,uid,expired_at,vip_level,uuid,sexType');
            }
        ])->orderBy('created_at', 'desc');
        $total = $query->count('id');
        if ($lastIndex) {
            $query->where('id', '<', $lastIndex);
            $offset = 0;
        }
        $list = $query->limit($limit)->offset($offset)->get()->map(function ($item) use (&$lastIndex) {
            /** @var \MvPayModel $item */
            $lastIndex = $item->id;
            return $item->mv;
        });
        $vids = $list->pluck('id')->toArray();
        \MvPayModel::addVidArr($member->uid, $vids);
        $list = $list->filter();

        return [
            'total'     => $total,
            'list'      => $this->v2format($list, $member),
            'lastIndex' => $lastIndex
        ];

    }

    /**
     * @param $id
     * @param null $member
     * @return array
     * @throws \Exception
     * @author xiongba
     */
    public function firstById($id, $member = null)
    {
        $data = \MvModel::where('id',$id)
            ->with('user_topic')
            ->with('user:uid,nickname,thumb,aff,expired_at,vip_level,uuid,sexType')->first();
        if (empty($data)) {
            throw new \Exception('视频不存在');
        }
        /** @var MvModel $data */
        $data->watchByUser($member);
        if ($data->user) {
            $data->user->watchByUser($member);
        }
       /* if ($data->is_free || $data->is_pay) {
            if (!empty($data['full_m3u8'])) {
                $playURL = getPlayUrl($data['full_m3u8'], false);
            } else {
                $playURL = getPlayUrl($data['m3u8'], false);
            }
            $playURL = getPlayUrl($data['m3u8'], false);
            $data->addHidden(['m3u8', 'cover_thumb', 'full_m3u8', 'v_ext']);
            $data->play_url = $playURL;
        } else {
            $playURL = getPlayUrl($data['m3u8'], false);
            $data->addHidden(['m3u8', 'cover_thumb', 'full_m3u8', 'v_ext']);
            $data->play_url = $playURL;
        }*/
        $playURL = getPlayUrl($data['m3u8'], true);
        $data->addHidden(['m3u8', 'cover_thumb', 'full_m3u8', 'v_ext']);
        $data->play_url = $playURL;
        if (IS_FAKE_CLIENT) {
            $data->play_url = getPlayUrl(ILLEGAL_ORG_VIDEO);
        }

        return $data->toArray();
    }

    /**
     * 获取推荐视频 新 -按照最新获取 因为之前的redis 队列没有更新 才1600部左右
     * @param int $lastIndex
     * @param $memberId
     * @param null $official
     * @return array
     */
    public function getChargeRecommendNew(int $lastIndex, $memberId, $official = null)
    {
        list($limit, $offset, $page) = QueryHelper::restLimitOffset();
        $query = MvModel::queryFeeRecommend();
//        if ($official || $official === null) {
//            $query->where('uid', getOfficialUID());
//        } else {
//            $query->where('uid', '!=', getOfficialUID());
//        }
        $total = (clone $query)->count();

        $all = $query
            ->with('user_topic')
            ->orderByDesc('refresh_at')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return [
            'total'     => $total,
            'list'      => $this->v2format($all, request()->getMember()),
            'lastIndex' => $lastIndex
        ];
    }

    /**
     * 作品 管理 相关 服务逻辑 查询
     * @param \MemberModel $member
     * @param array $where
     * @param string $order
     * @return MvModel[]
     */
    public function getUserWorks(\MemberModel $member, $where = [], $order = 'id')
    {
        $where[] = ['uid', '=', $member->uid];
        list($limit, $offset, $page) = QueryHelper::restLimitOffset();
        $data = MvModel::where($where)
            ->with('user_topic')
            ->limit($limit)
            ->offset($offset)
            ->orderByDesc('is_top')
            ->orderByDesc($order)
            ->with('user:uid,nickname,thumb,aff,expired_at,vip_level,sexType')
            ->get();
        return $this->v2format($data);
    }

    /**
     * 作品 管理 相关 服务逻辑 设置
     * @param array $where
     * @param array $data
     */
    public function setUserWorks($where = [], $data = [])
    {

        return MvModel::where($where)->update($data);

    }


    /**
     * 使用观影卷购买视频
     * @param int $mv_id
     * @param int $ticket_id
     * @param MemberModel $member
     * @return array
     * @throws \Throwable
     */
    public  function checkByTicket($mv_id, \MemberModel $member)
    {

        /** @var \MvTicketModel $model */
        $model = \MvTicketModel::myLatestMvTicketRow($member);
        if (empty($model)) {
            throw new \Exception('没有观影券~');
        }

        /** @var \MvModel $vModel */
        $vModel = \MvModel::queryBase()->where('id', $mv_id)->first();
        if (is_null($vModel)) {
            throw new \Exception('视频不存在');
        }

        if (\MvPayModel::hasPay($member->uid, $mv_id)) {
            throw new \Exception('您已购买过该视频');
        }
        //print_r($vModel->toArray());die;
        try {
            DB::beginTransaction();
            $itOk = \MvPayModel::createTicketLog($member->uid, $vModel->uid, $vModel->id, $vModel->coins);
            if (empty($itOk)) {
                throw new \Exception('使用观影卷失败1');
            }
            $itOk = $model->where([
                'status' => \MvTicketModel::STATUS_INIT,
                'id'     => $model->id
            ])->update([
                'used_at' => date("Ymd"),
                'mv_id'   => $vModel->id,
                'mv_uid'  => $vModel->uid,
                'status'  => \MvTicketModel::STATUS_USED,
                'used_style'=>\MvTicketModel::STYLE_POLO
            ]);
            if (empty($itOk)) {
                throw new \Exception('使用观影卷失败2');
            }
            DB::commit();
            \MvTotalModel::incrBuy($vModel->id, 1);
            $vModel->increment('count_pay', 1);
            \MvPayModel::addVidArr($member->uid, $vModel->id);
            return ['play_url'=>getPlayUrl($vModel->full_m3u8?$vModel->full_m3u8:$vModel->m3u8)];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}