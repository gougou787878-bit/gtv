<?php

// 视频模块
use service\AdService;
use service\TabService;
use service\UserTopicService;

class MvController extends BaseController
{
    use \repositories\MvRepository,
        \repositories\UsersRepository;

    /**
     * 视频标签
     * @return bool|void
     * @author xiongba
     */
    public function listOfTagAction()
    {
        $tag = request()->getPost('tag');
        $type = request()->getPost('type', 'newest');
        $service = new \service\TagMvService();
        $data = $service->videoForVideo($tag, null, $type,request()->getMember());
        return $this->showJson($data);
    }



    /**
     * @return bool
     * @author xiongba
     */
    public function detailAction()
    {
        $id = $this->post['id'] ?? 0;
        try {
            if (empty($id)) {
                throw new \Yaf\Exception('参数错误', 422);
            }
            $data = (new \service\MvService())->firstById($id, request()->getMember());
            return $this->showJson($data);
        } catch (Exception $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    /**
     * 改变置顶的状态
     * @return bool|void
     * @author xiongba
     * @date 2020-10-15 14:28:19
     */
    public function toggleTopAction()
    {
        $id = $this->post['id'] ?? 0;
        try {
            if (empty($id)) {
                throw new \Exception('参数错误', 422);
            }
            /** @var MvModel $model */
            $where = ['id' => $id, 'uid' => $this->member['uid']];
            $model = MvModel::where($where)->first();
            if (empty($model)) {
                throw new \Exception('视频不存在', 422);
            }
            $set_top = $model->is_top ? 0 : 1;
            if ($model->is_top == MvModel::IS_TOP_NO) {
                //如果视频没有置顶，标示想置顶，置顶就需要检查一下
                $count = MvModel::where(['uid' => $this->member['uid'], 'is_top' => MvModel::IS_TOP_YES])->count();
                if ($count >= 3) {
                    throw new \Exception('最多置顶3个视频，可取消后再操作', 422);
                }
            }
            $itOk = $model->update(['is_top' => $set_top]);
            /* $itOk = MvModel::toggleColumn($where , 'is_top' , array_keys(MvModel::IS_TOP));*/
            if (empty($itOk)) {
                throw new \Exception('操作失败', 422);
            }
            $model->msg = '操作成功';
            foreach (range(0, 10) as $page) {
                cached(\MvModel::REDIS_USER_VIDEOS_ITEM . $model->uid)->suffix("_$page")->clearCached();
            }
            $model->is_top = $set_top;
            return $this->showJson($model->toArray());
        } catch (Exception $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    /**
     * 获取视频完整播放链接
     * @return bool
     */
    public function longAction()
    {
        $id = $this->post['id'] ?? '';
        if (empty($id)) {
            return $this->errorJson('参数错误');
        }
        $mv = MvModel::find($id);
        if (!$mv) {
            return $this->errorJson('视频不存在');
        }
        $member = request()->getMember();
        $mv->watchByUser(request()->getMember());
        if ($mv->coins > 0 && $mv->uid != $member->uid) {
            if (!$mv->is_pay) {
                return $this->errorJson('视频您还没有购买');
            }
        }
        if (!empty($mv['full_m3u8'])) {
            $playURL = getPlayUrl($mv['full_m3u8'], false);
        } else {
            $playURL = getPlayUrl($mv['m3u8'], false);
        }
        $mv->addHidden(['m3u8', 'cover_thumb', 'full_m3u8', 'v_ext']);
        $mv = $mv->toArray();
        $mv['play_url'] = $playURL;
        $this->showJson($mv);
    }

    /**
     * 点赞
     * @return bool
     */
    public function likingAction()
    {
        $id = $this->post['id'] ?? '';
        if ($id == '') {
            return $this->errorJson('参数错误');
        }
        if ($this->member['role_id'] == MemberModel::USER_ROLE_LEVEL_BANED) {
            return $this->errorJson('您已经被禁言');
        }
        if (!frequencyLimit(10, 3, request()->getMember())) {
            return $this->errorJson('短时间内赞操作太頻繁了,稍后再试试');
        }
        try {
            $msg = (new \service\MvLikeService())->toggleLikeMv($id, request()->getMember());
            $this->showJson(['success' => true, 'msg' => $msg]);
        } catch (Exception $e) {
            return $this->errorJson('错误的请求');
        }

    }

    /**
     * 提交观看记录
     * @return bool
     */
    public function watchingAction()
    {
        $logAry = $this->post['log'] ?? [];
        $watchIdx = $this->post['id_log'] ?? '';
        $timestamp = (int)($this->post['timestamp'] ?? time());
        if (empty($timestamp)) {
            $timestamp = time();
        }
        if (empty($watchIdx)) {
            return $this->errorJson('参数错误');
        }
        if (is_string($logAry)) {
            $logAry = json_decode($logAry, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                $logAry = [];
            }
        }
        $data = $this->handleCreateWatch(request()->getMember(), $logAry, $watchIdx, $timestamp);
        $this->showJson($data);
    }


    public function pwaWatchingAction(){
        $member = request()->getMember();
        $watch_id = $this->post['mv_id']??0;
        if(!$watch_id){
            return $this->errorJson("非法参数");
        }
        if($member->is_vip ){
            $data = [
                'watched_count'=>0,
                'can_watch'=>1024,// 可用观看次数
            ];
            return $this->showJson($data);
        }
        // 当日观看记录
        $todayKey = \MvModel::REDIS_USER_TODAY_MV_LIST . $this->member['uid'];
        redis()->sAdd($todayKey, $watch_id);
        $expireTimestamp = strtotime(date('Y-m-d', strtotime('+1 days'))) - TIMESTAMP;
        redis()->expire($todayKey, $expireTimestamp);
        $watchCount = $this->getUserTodayWatchCount($this->member['uid']);
        $canWatch = (int)setting("site.can_watch_count", config('site.can_watch_count', 10));
        $data = [
            'watched_count' => $watchCount,//已经看了多少次了
            'can_watch'   => ($canWatch >= $watchCount) ? ($canWatch - $watchCount) : 0,
        ];
        return $this->showJson($data);

    }


    /**
     * 上传视频
     * @author xiongba
     * @date 2020-11-12 09:41:40
     */
    public function uploadAction()
    {
        if (empty($this->post['title']) or empty($url = $this->post['url'])) {
            return $this->errorJson('请填写完整');
        }

        $tags = $this->post['tags'] ?? '';
        $tags = collect(explode(',', $tags))->filter()->values()->toArray();
        if (empty($tags)) {
            return $this->errorJson('标签不能为空');
        } elseif (count($tags) > 5) {
            return $this->errorJson('标签不能超过5个');
        }
        $itOk = $this->post['img_url'] ?? '';
        if (empty($itOk)) {
            return $this->errorJson('请上传封面图');
        }
        $itOk = $this->post['url'] ?? '';
        if (empty($itOk)) {
            return $this->errorJson('请上传视频');
        }
        $topic_id = intval($this->post['topic_id'] ?? 0);
        if($topic_id){
            $hasTopic = UserTopicModel::queryBase('uid',$this->member['uid']);
            if(is_null($hasTopic)){
                $this->post['topic_id'] = 0;
            }
        }
        //check
        $res = MvModel::checkMemberToReleaseGoldMV($this->member['uid']);
        $is_fee = $res['can_release_fee'] ?? 0;
        $is_can = $res['can_release'] ?? 0;
        $not_can_msg = $res['msg_tips'] ?? '该用户不允许上传视频';
        if (!$is_can && false) {
            return $this->errorJson($not_can_msg);
        }
        $coins = isset($this->post['coins']) ? (int)$this->post['coins'] : 0;
        if (!$is_fee && $coins) {
            return $this->errorJson('你的付费视频额度已超比例，请先上传免费视频');
        }

        $return = $this->uploadMv();
        if ($return['success'] == true) {
            MvUploadIpInfoModel::addData(request()->getMember());
            return $this->showJson($return);
        } else {
            return $this->errorJson($return['msg']);
        }
    }

    /**
     *用户发布砖石视频 新增 配置 接口
     */
    public function preUploadAction()
    {
        $member = $this->member;
        $res = MvModel::checkMemberToReleaseGoldMV($member['uid']);
        $is_fee = $res['can_release_fee'] ?? 0;
        $tips = setting('upload.tips', '禁止上传未成年、真实强奸、吸毒、枪支、偷拍、侵害他人隐私等违规内容');
        $return = [
            'tags'         => TagsModel::getUserUpList(),
            'cat_data'         => TabService::getUploadTabList(),
            'is_fee'       => $is_fee,
            'price_max'    => abs(intval(setting('mv:coins:max', 100))),
            'rule_text'    => $tips,
            'price_text'   => '#txt#，后续可设置为付费。每日总付费视频数量不可超过免费视频数量。',
            'price_strong' => '每日前两部只可上传免费视频',
            'is_maker'     => (int)$member['auth_status'],
            'rule'     => $res,
            'topic_list'=>UserTopicService::getTopicByUidAll($member['uid'])
        ];
        $this->showJson($return);
    }

    /**
     * 我关注的人
     * @return bool|void
     */
    public function listOfFollowAction()
    {
        $uid = $this->member['uid'];
        $list = cached('tb_fl_v:' . $uid)
            ->hash($this->page)
            ->fetchPhp(function () use ($uid){
                $likeVid = UserAttentionModel::query()
                    ->join('mv', 'mv.uid', '=', 'member_attention.touid')
                    ->where('member_attention.uid', $uid)
                    ->orderByDesc('mv.id')
                    ->forPage($this->page, $this->limit)
                    ->pluck('mv.id')
                    ->toArray();

                $items = MvModel::queryBase()
                    ->with('user_topic')
                    ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff')
                    ->whereIn('id', $likeVid)
                    //->where('is_aw', MvModel::AW_NO)
                    ->get();
                return array_keep_idx($items, $likeVid);
            }, 600);
        $vlist = [];
        if (empty($list)) {
            $cached = cached('foryou:follow:' . $this->member['uid'])->hash($this->page);
            $vlist = $cached->serializerJSON()
                ->expired(600)
                ->fetch(function () {
                    $uidAry = collect(explode(',', setting('follow:foryou', '99,100,101,102,103')));
                    if ($uidAry->count() > 5) {
                        $uidAry = $uidAry->random(5);
                    }
                    $items = MvModel::queryWithUser()
                        ->with('user_topic')
                        ->select(['mv.*'])
                        ->whereIn('uid', $uidAry)
                        //->where('is_aw', MvModel::AW_NO)
                        ->forPage($this->page, $this->limit)
                        ->orderByDesc('id')
                        ->get();
                    return (new \service\MvService())->v2format($items, request()->getMember());
                });
        }
        $result = [
            'list' => (new \service\MvService())->v2format($list, request()->getMember()),
        ];
        if (!empty($vlist)) {
            $result['vlist'] = $vlist;
        }
        return $this->showJson($result);
    }

    /**
     * 修复视频时长
     * @return bool|void
     */
    public function fix_durationAction()
    {
//        $id = $this->post['id'] ?? 0;
//        $duration = $this->post['duration'] ?? 0;
//        $url = $this->post['url'] ?? '';
//        if (empty($duration) || empty($id) || empty($url)) {
//            return $this->showJson('ok');
//        }
//        MvModel::where('id', $id)->update(['duration' => $duration]);
        return $this->showJson('ok');
    }

    /**
     * 举报类型
     * @return bool|void
     */
    public function report_typeAction()
    {
        return $this->showJson(explode(',', setting('mv:report-type', '男女,收費不合理,標題黨')));
    }

    /**
     * 举报视频
     * @return bool|void
     */
    public function report_pushAction()
    {
        $mv_id = $this->post['mv_id'] ?? 0;
        $content = $this->post['content'] ?? '';
        $uuid = request()->getMember()->uuid;
        if (empty($mv_id) || empty($content) || empty($uuid)) {
            return $this->showJson('ok');
        }
        MvReportModel::createBy($mv_id, $content, $uuid,0);
        return $this->showJson('ok');
    }

    /**
     * 答题选项
     */
    public function uploadAnswerAction()
    {
        $data = [
            [
                'title' => '1、下面哪些内容是可以在GTV视频中上传的？',
                'type'  => 0,
                'item'  => [
                    [
                        'name'  => '· A 带有广告水印的视频内容',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· B 性爱自拍分享等大尺度内容',
                        'check' => 1,
                    ],
                    [
                        'name'  => '· C 真实强奸内容',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· D 幼童等大尺度内容',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· E 吸毒嗑药嗨操型内容',
                        'check' => 0,
                    ]
                ]
            ],
            [
                'title' => '2、GTV视频目前上传大小为100M，如果上传的视频超过大小限制，我应该怎么处理？（多选）',
                'type'  => 1,
                'item'  => [
                    [
                        'name'  => '· A 骂在线客服',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· B 不上传',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· C 保证视频清晰度的情况下，压缩视频上传',
                        'check' => 1,
                    ],
                    [
                        'name'  => '· D 裁剪视频，分段上传',
                        'check' => 1,
                    ]
                ]
            ],
            [
                'title' => '3、什么样的视频在GTV中最受欢迎？（多选）',
                'type'  => 1,
                'item'  => [
                    [
                        'name'  => '· A 从头到尾打飞机的视频',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· B 画质模糊不清，看不到人脸的视频',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· C 有剧情，有对白的视频',
                        'check' => 1,
                    ],
                    [
                        'name'  => '· D 画质精细，主角露脸颜值高的视频',
                        'check' => 1,
                    ]
                ]
            ],
            [
                'title' => '4、下面哪种方式可以增加自己的视频收入（多选）',
                'type'  => 1,
                'item'  => [
                    [
                        'name'  => '· A 全部上传付费视频',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· B 通过免费视频吸引粉丝，部分付费视频变现',
                        'check' => 1,
                    ],
                    [
                        'name'  => '· C 全部上传免费视频',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· D 付费视频封面精致，标题吸引',
                        'check' => 1,
                    ]
                ]
            ],
            [
                'title' => '5、以下哪项不是GTV官方认证制片人的特权？',
                'type'  => 0,
                'item'  => [
                    [
                        'name'  => '· A 最高50%的视频分成',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· B 上传视频更多，官方高速审核通道',
                        'check' => 0,
                    ],
                    [
                        'name'  => '· C 全部上传付费视频',
                        'check' => 1,
                    ],
                    [
                        'name'  => '· D 特殊身份标识，官方流量扶持',
                        'check' => 0,
                    ]
                ]
            ],

        ];
        $tips = setting('upload.tips', '禁止上传未成年、真实强奸、吸毒、枪支、偷拍、侵害他人隐私等违反国际法的内容');
        return $this->showJson(['answer' => $data, 'rule_text' => $tips]);
    }

}