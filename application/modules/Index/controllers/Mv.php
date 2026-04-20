<?php


/**
 * 回调
 */
class MvController extends SiteController
{
    use \repositories\MvRepository,
        \repositories\UsersRepository;

    public function init()
    {
        parent::init();
    }

    /**
     * 用户上传的完整视频  m3u8切片完成后同步
     */
    public function indexAction()
    {
        $this->processCallBackM3u8(false);
    }

    /**
     * 用户上传的预览视频 m3u8切片完成后同步
     */
    public function prem3u8Action()
    {
        $this->processCallBackM3u8(true);
    }


    /**
     * @param $is_pre_m3u8 true|false
     * @throws Exception
     */
    protected function processCallBackM3u8($is_pre_m3u8)
    {
        $data = $this->post ?? array();
        $data['pre_m3u8'] = $is_pre_m3u8;
        error_log('视频回调' . print_r($data, true), 3, $this->logFile);
        /** @var MvModel $videoModel */
        if (!$data || !isset($data['mv_id'])) {
            return;
        }

        NotifyLogModel::addByM3u8($data['mv_id'], json_encode($data));
        /** @var MvSubmitModel $object */
        $object = MvSubmitModel::useWritePdo()->where('id', $data['mv_id'])->first();
        if (is_null($object)) {
            errLog('未找到对应视频');
            echo 'success';
            return;
        }
        if ($is_pre_m3u8) {
            MvSubmitModel::where('id', $data['mv_id'])->update([
                'm3u8' => $data['source']
            ]);
            echo 'success';
            return;
        }
        if ($object->status == MvSubmitModel::STAT_CALLBACK_DONE) {
            if (stripos($object->full_m3u8, 'mp4') === false) {
                echo 'success';
                return;
            }
        }

        $videoModel = $object->toArray();
        $updateData = [];
        if (stripos($videoModel['m3u8'], 'mp4') !== false) {
            $updateData['m3u8'] = '';//预览视频 或小视频
        }
        $updateData['full_m3u8'] = $data['source'];//完整视频切片
        $updateData['status'] = MvModel::STAT_CALLBACK_DONE;
        $updateData['refresh_at'] = TIMESTAMP;
        $updateData['music_id'] = 0;
        (!$videoModel['thumb_width']) && $updateData['thumb_width'] = $data['thumb_width'];
        (!$videoModel['thumb_height']) && $updateData['thumb_height'] = $data['thumb_height'];
        $data['cover_thumb'] && $updateData['cover_thumb'] = $data['cover_thumb'];
        (!$videoModel['duration']) && $updateData['duration'] = $data['duration'];

        $updateData['duration'] = $data['duration'];


        /** @var MemberModel $member */
        $member = \MemberModel::useWritePdo()->where('uid', $videoModel['uid'])->first();
        //如果作者不存在了
        if (is_null($member)) {
            $official_url = getOfficialUID();
            $member = MemberModel::useWritePdo()->where('uid', $official_url)->first();
        }
        if (is_null($member)) {
            $object->delete();
            echo 'success';
            errLog("no fund user \r\n");
            return;
        }

        try {
            \DB::beginTransaction();
            //更新用户视频统计
            $member->increment('videos_count', 1);
            $insertDat = $object->getAttributes();
            $insertDat = array_merge($insertDat, $updateData);
            unset($insertDat['id']);
            /** @var MvModel $releaseMv */
            if ($insertDat['coins'] <= 0) {
                $insertDat['coins'] = 0;
                $insertDat['is_free'] = MvModel::IS_FREE_YES;
            } else {
                $insertDat['is_free'] = MvModel::IS_FREE_NO;
            }
            $releaseMv = \MvModel::create($insertDat);
            if (is_null($releaseMv) || (!$releaseMv->id)) {
                throw new \Exception('发布库新增视频异常，操作失败');
            }
            //如果是剧集视频 就关联  内部直接关联
            $object->topic_id && UserTopicModel::where('id', $object->topic_id)
                ->update([
                    'mv_id_str'   => \DB::raw("IF(`video_count`=0,{$releaseMv->id},CONCAT_WS(',',`mv_id_str`,{$releaseMv->id}))"),
                    'video_count' => \DB::raw("`video_count`+1")
                ]);
            \DB::commit();
            //销毁
            $object->delete();
            //统计
            MemberMakerStatModel::addStat($member->uuid, MemberMakerStatModel::FIELD_MV_NUMBER);
            //messageCenter
            MessageModel::createSystemMessage($member->uuid, MessageModel::SYSTEM_MSG_TPL_MV_PASS,
                ['title' => $releaseMv->title]);
            //处理视频标签关联
            $videoModel['tags'] && MvTagModel::createByAll($releaseMv->id, $releaseMv->tags);
            MvWordsModel::createForTitle($releaseMv->id, $releaseMv->title);
            redis()->del(\MvModel::REDIS_USER_VIDEOS_ITEM . $releaseMv->uid . '_1');
            MemberModel::clearFor($member);
            echo 'success';
        } catch (Exception $exception) {
            \DB::rollBack();
            errLog("\r\n 回调进入发布库失败:" . $exception->getMessage());
            return;
        }
    }

    public function syncAction()
    {
        $msg = '小蓝-同步数据 #' . date('Y-m-d H:i:s', TIMESTAMP) . PHP_EOL;
        if (!$this->getRequest()->isPost()) {
            $msg .= '非法请求:' . PHP_EOL . var_export($_REQUEST, true);
            errLog($msg);
            return;
        }
        $msg .= var_export($this->post, true);
        errLog($msg);
        //die;
        //echo json_encode(['status'=>0,'msg'=>'ok','data'=>[]]);
        /*

        array (
          'id' => '211276',
          'uid' => '2328179',
          'music_id' => '0',
          'coins' => '4',
          'vip_coins' => '-1',
          'title' => '国产大屌肌肉体育生小哥哥酒店约啪戴屌环的骚受，激情群啪前后夹击开火车，刺激轮插射精爽歪歪（下）',
          'm3u8' => '/watch5/54f6542a7e2bc780a48419d758e6db06/54f6542a7e2bc780a48419d758e6db06.m3u8',
          'full_m3u8' => '',
          'v_ext' => 'm3u8',
          'duration' => '1234',
          'cover_thumb' => '/new/xiao/20210913/2021091314081892060.jpeg',
          'thumb_width' => '1620',
          'thumb_height' => '910',
          'gif_thumb' => '',
          'gif_width' => '0',
          'gif_height' => '0',
          'directors' => '',
          'actors' => '',
          'category' => '',
          'tags' => '肌肉男,剧情,小鲜肉,原味,打桩机',
          'via' => 'user',
          'onshelf_tm' => '0',
          'rating' => '2',
          'refresh_at' => '1631514035',
          'is_free' => '0',
          'like' => '8',
          'comment' => '0',
          'status' => '1',
          'thumb_start_time' => '0',
          'thumb_duration' => '30',
          'is_hide' => '0',
          'created_at' => '1631513315',
          'is_recommend' => '0',
          'is_feature' => '0',
          'y_cover' => '',
          'is_top' => '0',
          'count_pay' => '3',
          'topic_id' => '714',
        )

        */

        $uidArray = [1, 2, 3, 4];//内部测试机用户随机绑定
        $uid = $uidArray[rand(0, 3)];
        $uid = ($uid >= 1 && $uid <= 4) ? $uid : 3;


        $mvData = $this->post;
        $m3u8 = $mvData['m3u8'];
        $fan_id = $mvData['id'];
        $image = $mvData['cover_thumb'];
        $has = MvModel::where('music_id', '=', $fan_id)->exists();
        if ($has) {
            errLog(PHP_EOL . "################# has fan_id: {$fan_id} #################" . PHP_EOL);
            return;
        }

        unset($mvData['id']);
        $insertData = $mvData;
        //绑定用户
        $insertData['uid'] = $uid;
        $insertData['full_m3u8'] = $insertData['m3u8'];
        $insertData['music_id'] = $fan_id;
        $insertData['rating'] = 0;
        $insertData['like'] = 0;
        $insertData['comment'] = 0;
        $insertData['status'] = 1;
        $insertData['is_hide'] = 1;
        $insertData['count_pay'] = 0;
        $insertData['topic_id'] = 0;
        $insertData['via'] = 'xlan';
        $insertData['refresh_at'] = time();
        $insertData['created_at'] = time();
        $releaseSyncMv = MvModel::create($insertData);
        $member = MemberModel::where('uid', $uid)->first();
        if (!is_null($member)) {
            //errLog("{$flag} insert ok \r\n");
            MemberMakerStatModel::addStat($member->uuid, MemberMakerStatModel::FIELD_MV_NUMBER);
            MvWordsModel::createForTitle($releaseSyncMv->id, $releaseSyncMv->title);
            $member->increment('videos_count', 1);
        }

        echo json_encode(['status' => 0, 'msg' => "sync#{$fan_id} ok", 'data' => []]);

    }

}