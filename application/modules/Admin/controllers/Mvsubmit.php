<?php

/**
 * Class MvsubmitController
 * @author xiongba
 * @date 2020-11-12 10:56:40
 */
class MvsubmitController extends BackendBaseController
{
    /**
     * 列表数据过滤
     * @return Closure
     * @author xiongba
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        return function (MvSubmitModel $item) {
            $item->href = $item->m3u8?url_videoMP4($item->m3u8):'';
            $item->href_full = $item->full_m3u8?url_videoMP4($item->full_m3u8):'';
            if ($item->user) {
                $item->nickname = $item->user->nickname;
            } else {
                $item->nickname = '注销户';
            }

            return $item;
        };
    }

    /**
     * @param MvModel|MvSubmitModel|object $model
     * @return bool|string
     * @author xiongba
     * @date 2020-03-03 19:53:48
     */
    protected function approvedMv($model)
    {
        if(stripos($model->m3u8, 'mp4') !== false ){
            $data = [
                'uuid'    => 'fasdfddfasdfdjfajkodfs09ds0r23089df',
                'm_id'    => $model->id,
                'needMp3' => 0,
                'needImg' => empty($model->cover_thumb) ? 1 : 0,
                'playUrl' => $model->m3u8,
            ];
            $crypt = new \tools\CryptService();
            $sign = $crypt->make_sign($data);
            $data['sign'] = $sign;
            $data['notifyUrl'] =SYSTEM_NOTIFY_SLICE_URL_PRE;
            $curl = new \tools\CurlService();
            $return = $curl->request(config('mp4.accept'), $data);
            errLog("pre slice req:" . var_export([$data, $return], true));
        }

        $return = $this->approvedMvFull($model);

        return $return;
    }
    protected function approvedMvFull($model)
    {
        $data = [
            'uuid'    => 'fasdfddfasdfdjfajkodfs09ds0r23089df',
            'm_id'    => $model->id,
            'needMp3' => 0,
            'needImg' => empty($model->cover_thumb) ? 1 : 0,
            'playUrl' => $model->full_m3u8,
        ];
        $crypt = new \tools\CryptService();
        $sign = $crypt->make_sign($data);
        $data['sign'] = $sign;
        $data['notifyUrl'] =SYSTEM_NOTIFY_SLICE_URL;
        $curl = new \tools\CurlService();
        $return = $curl->request(config('mp4.accept'), $data);
        errLog("full slice req:" . var_export([$data, $return], true));


        return $return;
    }

    public function passAction()
    {
        $pk = $_POST['_pk'] ?? 0;
        $model = MvSubmitModel::find($pk);
        if ($model->status != MvSubmitModel::STAT_UNREVIEWED) {
            return $this->ajaxError('当前状态不可操作');
        }
        $model->status = MvSubmitModel::STAT_CALLBACK_ING;
        if ($model->save()) {
            $re = $this->approvedMv($model);
            if ($re == setting('approvedUserUpload', 'success')) {
                $this->addReviewMvLog($model);
                return $this->ajaxSuccess('审核成功');
            } else {
                return $this->ajaxError($re);
            }
        }
        return $this->ajaxSuccess('操作失败');
    }

    public function refuseUserUploadAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $model = MvSubmitModel::find($id);
        if (empty($model)) {
            return $this->ajaxError('当前状态不可拒绝');
        }
        if ($model->status != MvSubmitModel::STAT_UNREVIEWED) {
            return $this->ajaxError('当前状态不可操作');
        }
        if ($model->update(['status' => MvSubmitModel::STAT_REFUSE])) {
            if ($model->user) {
                $memo = $this->post['refused'] ?? '视频模糊有水印';
                MessageModel::createSystemMessage($model->user->uuid, MessageModel::SYSTEM_MSG_TPL_MV_REFUSE,
                    ['title' => $model->title, 'reason' => $memo]);
            }
            $this->addReviewMvLog($model);
            return $this->ajaxSuccess('操作成功');
        }
        return $this->ajaxError('操作失败');

        return ;//切片的mp4定期清理
        $curl_data = [
            'timestamp' => TIMESTAMP,
            'playUrl'   => $model->m3u8,
            'sign'      => md5(TIMESTAMP . (config('mp4.slice_key')) . $model->m3u8)
        ];
        $curl = new \tools\CurlService();
        $re = $curl->request(config('mp4.destroy') , $curl_data);
        errLog("refuseUserUploadAction:".var_export($re,1));
        if ($re == 'success' || $re == '文件不存在') {
            if ($model->update(['status' => MvSubmitModel::STAT_REFUSE])) {
                if ($model->user) {
                    $memo = $this->post['refused'] ?? '视频模糊有水印';
                    MessageModel::createSystemMessage($model->user->uuid, MessageModel::SYSTEM_MSG_TPL_MV_REFUSE,
                        ['title' => $model->title, 'reason' => $memo]);
                }
                $this->addReviewMvLog($model);
                $key = "mymsg:{$model->user->uuid}:0";//清楚第一页消息
                redis()->del($key);
                return $this->ajaxSuccess('操作成功');
            }
        } else {
            return $this->ajaxError('操作失败');
        }
    }

    private function addReviewMvLog(MvSubmitModel $model)
    {
        AdminLogModel::addReviewMv($this->getUser()->username, sprintf('审视频[%d]#(%d)%s', $model->uid, $model->id, $model->title));
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-11-12 10:56:40
     */
    public function indexAction()
    {
        $this->display();
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-11-12 10:56:40
     */
    public function waitAction()
    {
        $list = MvSubmitModel::with('user:uid,nickname')
            ->where('status', MvModel::STAT_UNREVIEWED)
            ->where('task_at', '<', time())
            ->limit(10)
            ->get()
            ->map($this->listAjaxIteration());
        $ids = $list->pluck('id');
        MvSubmitModel::whereIn('id', $ids)->update(['task_at' => time() + 2800]);

        $this->assign('list', $list->toArray());
        $this->assign('admin_css', true);
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2020-11-12 10:56:40
     */
    protected function getModelClass(): string
    {
        return MvSubmitModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2020-11-12 10:56:40
     */
    protected function getPkName(): string
    {
        return 'id';
    }

    /**
     * 定义数据操作日志
     * @return string
     * @author xiongba
     * @date 2019-11-04 17:19:41
     */
    protected function getLogDesc(): string
    {
        // TODO: Implement getLogDesc() method.
        return '';
    }
    public function setTags($val, $data, $pk)
    {
        return join(',', array_map('trim', $val));
    }
    /**
     *
     * 视频后台统计
     *
     * @return bool
     */
    public function totalAction()
    {
        /*$data = [
            'totalMV' => 100,
            'totalGoldMV'  => 100,
            'totalXiao' => 100,
            'totalPassed' => 100,
        ];
        return $this->ajaxSuccess($data);*/
        $where = array_merge(
            $this->getSearchLikeParam(),
            $this->getSearchWhereParam(),
            $this->getSearchBetweenParam()
        );

        $totalToCheck = MvSubmitModel::where('status','=',MvSubmitModel::STAT_UNREVIEWED)->count(['id']);
        $totalToBack = MvSubmitModel::where('status','=',MvSubmitModel::STAT_CALLBACK_ING)->count(['id']);

        $data = [
            'totalToCheck' => $totalToCheck,
            'totalToBack'  => $totalToBack
        ];
        return $this->ajaxSuccess($data);
    }
}