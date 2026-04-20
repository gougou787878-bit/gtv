<?php


/**
 * Class MvController
 * @author xiongba
 * @date 2020-03-03 18:21:06
 */
class MvController extends BackendBaseController
{
    /**
     * 列表数据过滤
     * @return Closure
     * @author xiongba
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        /**
         * @param MvModel $item
         * @return mixed
         * @author xiongba
         * @date 2020-03-03 17:23:38
         */
        return function ($item) {

            $item->href = '';
            if($item->m3u8){
                $item->href = getAdminPlayM3u8($item->m3u8,1);
            }
            $item->full_href = '';
            if($item->full_m3u8){
                $item->full_href = getAdminPlayM3u8($item->full_m3u8,1);
            }
            $item->coins = $item->coins;
            $item->title = htmlspecialchars($item->title);
            $item->statusname = MvModel::STAT[$item->status];
            $item->created_at = date('Y-m-d H:i', $item->created_at);
            $item->refresh_at && $item->refresh_at = date('Y-m-d H:i', $item->refresh_at);
            $item->thumb = $item->cover_thumb;
            $item->img_thumb = $item->cover_thumb;
            $item->img_gif_thumb = $item->gif_thumb;
            $item->tagsname = htmlspecialchars($item->tags);
            $item->buy_num = 0;
            /*if ($item->coins > 0) {
                $item->buy_num = MvPayModel::getBuyMvNum($item->id);
            }*/
            $item->nickname = $item->user->nickname;

            $constructName = '';
            if ($item->construct_id > 0) {
                /** @var ConstructModel $construct */
                $construct = ConstructModel::where('id', $item->construct_id)->first();
                $constructName = $construct ? $construct->title : '';
            }
            $item->construct_name = $constructName;

            return $item;
        };
    }

    public function tagsListAction()
    {
        return $this->ajaxSuccess(TagsModel::orderBy('id', 'DESC')->pluck('name'));
    }


    public function setTags($val, $data, $pk)
    {
        return join(',', array_map('trim', $val));
    }


    /**
     * 拒绝用户请求
     * @return bool
     * refused: 您上传的视频质量、清晰度还不够好，建议在丰富一下内容再次上传
     * _pk: 93777
     * status: 2
     */
    public function refuseUserUploadAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $model = MvModel::find($id);
        if (empty($model) || $model->status != 0) {
            return $this->ajaxError('当前状态不可拒绝');
        }
        $curl_data = [
            'timestamp' => TIMESTAMP,
            'playUrl'   => $model->m3u8,
            'sign'      => md5(TIMESTAMP . (config('mp4.slice_key')) . $model->m3u8)
        ];
        $curl = new \tools\CurlService();
        $re = $curl->request(config('mp4.destroy'), $curl_data);
        if ($re == 'success' || $re == '文件不存在') {
            \MvModel::where('id', $id)->update([
                'status'  => MvModel::STAT_REFUSE,
                'is_hide' => MvModel::IS_HIDE_YES
            ]);
            //messageCenter
            $member = MemberModel::where(['uid' => $model->uid])->first();
            $member && MessageModel::createSystemMessage($member->uuid, MessageModel::SYSTEM_MSG_TPL_MV_REFUSE,
                ['title' => $model->title, 'reason' => trim($this->post['refused'], '"\'') ?? '视频模糊有水印']);
            return $this->ajaxSuccess('操作成功');
        } else {
            return $this->ajaxError('操作失败', -9999, $re);
        }
    }

    /**
     * 切换推荐状态
     * @return bool
     */
    public function switchRecommendAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $model = MvModel::find($id);
        if (empty($model)) {
            return $this->ajaxError('当前状态不能切换推荐状态');
        }
        try {
            $is_recommend = $model->is_recommend == MvModel::RECOMMEND_YES ? MvModel::RECOMMEND_NO : MvModel::RECOMMEND_YES;
            list($status, $re) = $this->status2Success($id, ['is_recommend' => $is_recommend]);
            if ($status) {
                $this->ajaxSuccess('审核成功');
            } else {
                $this->ajaxError('审核失败#' . var_export($re, true), -9999, $re);
            }
        } catch (Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    public function refreshAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $model = MvModel::where('id', '=', $id)->update(['refresh_at' => TIMESTAMP]);
        $this->ajaxSuccess('涮新成功~');
    }

    /**
     * 同步水蜜桃
     */
    public function syncAvAction()
    {
        return $this->ajaxSuccess('暂不支持的功能~');
        $id = $this->post['_pk'] ?? 0;
        $model = MvModel::where('id', '=', $id)->first();
        if(is_null($model)){
            return $this->ajaxSuccess('查无数据~');
        }
        try{
            $data = $model->getAttributes();

            $curl = new \tools\CurlService();
            $return = $curl->request(SYNC_SMT_AV_URL, $data);
            //errLog("sync req:".var_export([$data,$return],true));
            $returnArr = json_decode($return,true);
            if($returnArr['status'] == 0){
                $model->increment('music_id',1);// music_id 已经弃用  作为同步标识
                return $this->ajaxSuccess('同步成功#'.$returnArr['msg']);
            }
            //{"status":0,"msg":"ok","data":[]}
            return $this->ajaxSuccess('同步失败#'.$returnArr['msg']);
            //return $return;
        }catch (\Yaf\Exception $e){
            return $this->ajaxSuccess('同步失败'.$e->getMessage());
        }
        return $this->ajaxSuccess('同步成功~');
    }

    /**
     * 用户上传的视频通过审核
     */
    public function upFeatureAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $model = MvModel::find($id);
        if (empty($model)) {
            return $this->ajaxError('当前状态不能切换推荐状态');
        }
        try {
            $is_recommend = $model->is_feature == MvModel::IS_FEATURE_YES ? MvModel::IS_FEATURE_NO : MvModel::IS_FEATURE_YES;
            list($status, $re) = $this->status2Success($id, ['is_feature' => $is_recommend]);
            if ($status) {
                $this->ajaxSuccess('审核成功');
            } else {
                $this->ajaxError('审核失败#' . var_export($re, true), -9999, $re);
            }
        } catch (Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }


    public function createBeforeCallback($model)
    {
        /** @var MvModel $model */
        $model->uid = getOfficialUID();
        $model->via = MvModel::VIA_OFFICAL;
        $model->created_at = time();
    }

    public function saveAfterCallback($model)
    {
        /** @var MvModel $model */
        if (empty($model)) {
            return;
        }
        $tags = $model->tags;
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        MvTagModel::deleteMvNoTag($model->id, $tags);
        if ($model->status == MvModel::STAT_CALLBACK_DONE) {
            MvTagModel::createByAll($model->id, $tags);
        }


    }

    /**
     * 用户上传的视频通过审核
     */
    public function approvedUserUploadAction()
    {
        $id = $this->post['_pk'] ?? 0;
        try {
            $row = \MvModel::find($id);
            if (empty($row)) {
                throw new \Exception('视频不存在');
            }
            if ($row->status != MvModel::STAT_UNREVIEWED) {
                return $this->ajaxSuccess('当前状态不可操作');
            }
            list($status, $re) = $this->status2Success($id);
            if ($status) {
                $this->ajaxSuccess('审核成功');
            } else {
                $this->ajaxError('审核失败#' . var_export($re, true), -9999, $re);
            }
        } catch (Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    protected function status2Success($id, $values = [])
    {
        $row = \MvModel::where('id', $id)->first();
        if (empty($row)) {
            throw new \Exception('视频不存在');
        }
        if ($row->status == MvModel::STAT_UNREVIEWED) {
            $re = $this->approvedMv($row);
            if ($re == setting('approvedUserUpload', 'success')) {
                $row->update(array_merge(['status' => MvModel::STAT_CALLBACK_ING], $values));
                return [true, '审核成功'];
            } else {
                return [false, $re];
            }
        } else {
            $row->update($values);
            return [true, '审核成功'];
        }
    }


    /**
     * 重新切片申请回调
     */
    public function retrysliceAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $row = \MvModel::query()->where(['id' => $id, 'status' => MvModel::STAT_CALLBACK_ING])->first();
        if (empty($row)) {
            return $this->ajaxError('切片回调已处理');
        }

        $re = $this->approvedMv($row);
        if ($re == 'success') {
            $this->ajaxSuccess('切片回调已处理成功');
        } else {
            $this->ajaxError('切片回调处理失败');
        }
    }


    /**
     * 重新切片申请回调
     */
    public function avsliceAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $row = \MvModel::query()->where(['id' => $id])->first();
        if (empty($row)) {
            return $this->ajaxError('切片回调已处理');
        }
        $re = $this->approvedMv($row);
        if ($re == 'success') {
            $this->ajaxSuccess('切片回调已处理成功');
        } else {
            $this->ajaxError('切片回调处理失败');
        }
    }


    /**
     * @param MvModel|object $model
     * @return bool|string
     * @author xiongba
     * @date 2020-03-03 19:53:48
     */
    protected function approvedMv($model)
    {
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
        $data['notifyUrl'] = SYSTEM_NOTIFY_SLICE_URL;
        $curl = new \tools\CurlService();
        $return = $curl->request(config('mp4.accept'), $data);
        //errLog("reslice req:".var_export([$data,$return],true));
        return $return;
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-03-03 18:21:06
     */
    public function indexAction()
    {
        $constructs = [];
        ConstructModel::queryBase()->with('navigation')
            ->where('type', ConstructModel::TYPE_COMMON)
            ->get()
            ->each(function (ConstructModel $item) use (&$constructs){
                if (empty($item->navigation)){
                    return null;
                }
                $name = $item->navigation->title . '-' . $item->title;
                $constructs[$item->id] = $name;
            });

        $this->assign('constructs', $constructs);

        $this->display();
    }

    public function avAction()
    {
        $this->display('mv/av');
    }

    protected function listAjaxWhere()
    {
        $where = [];
        if (isset($_GET['is_gov'])) {
            //$where[] = ['uid', '=', getOfficialUID()];
            $where[] = ['coins', '>', 0];
        } else {
            //小视频
            //$where[] = ['coins', '=', 0];
        }
        $isSetFree = isset($_GET['_is_free']);
        if ($isSetFree && $_GET['_is_free'] != '__undefined__') {
            $ifFree = intval($_GET['_is_free']);
            if ($ifFree == '0') {
                $where[] = ['coins', '!=', 0];
            } elseif ($ifFree == '1') {
                $where[] = ['coins', '=', 0];
            }
        }

        return $where;
    }

    protected function getSearchWhereParam() {
        $get = $this->getRequest()->getQuery();
        $get['where'] = $get['where'] ?? [];
        $where = [];
        foreach ($get['where'] as $key => $value) {
            if ($value ==='__undefined__'){
                continue;
            }
            $key = $this->formatKey($key);
            if (empty($key)){
                continue;
            }
            $value = $this->formatSearchVal($key, $value);
            if ($value !=='') {
                if ($key == 'construct_id' && $value == '-1'){
                    $value = 0;
                }
                $where[] = [$key, '=', $value];
            }
        }
        return $where;
    }

    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2020-03-03 18:21:06
     */
    protected function getModelClass(): string
    {
        return MvModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2020-03-03 18:21:06
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

    /**
     * 每次編輯更新片源時間
     * @param null $setPost
     * @return mixed
     */
    protected function postArray($setPost = null)
    {
        $post = parent::postArray();
        $post['refresh_at'] = TIMESTAMP;
        return $post;
    }

    /**
     * 加入合集
     * @return bool
     * refused: 您上传的视频质量、清晰度还不够好，建议在丰富一下内容再次上传
     * _pk: 93777
     * status: 2
     */
    public function addTopicAction()
    {
        $id = $this->post['_pk'] ?? 0;
        $model = MvModel::find($id);
        if (empty($model) || $model->status != MvModel::STAT_CALLBACK_DONE) {
            return $this->ajaxError('当前状态不能加入合集');
        }
        if (empty($this->post['topic_id']) || !is_array($this->post['topic_id'])) {
            return $this->ajaxError('加入合集不能为空');
        }
        $insertData = [];

        return $this->ajaxError('操作失败#' );
    }

    public function batchAddConstructAction()
    {
        $id = $this->post['mv_ids'] ?? null;
        $construct_id = $this->post['construct_id'] ?? null;
        if (!$id || !$construct_id) {
            return $this->ajaxError('参数不能为空');
        }
        $id = explode(',', $id);

        try {
            $list = MvModel::selectRaw('id, construct_id')->whereIn('id', $id)->get();
            $construct_id = (int)$construct_id;
            $list->each(function ($item) use ($construct_id) {
                $item->construct_id = $construct_id;
                $item->saveOrFail();
            });
            return $this->ajaxSuccessMsg('操作成功');
        } catch (\Throwable $e) {
            return $this->ajaxError($e->getMessage());
        }
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
        $query = MvModel::query()->where($where);
        $totalGoldMVQuery = clone $query;
        $totalGoldXiaoQuery = clone $query;
        $totalPassedQuery = clone $query;
        $totalMV = $query->count('id');
        $totalGoldMV = $totalGoldMVQuery->where('coins', '>', 0)->count('id');
        $totalXiao = $totalGoldXiaoQuery->where('coins', '=', 0)->count('id');
        $totalPassed = $totalPassedQuery->whereIn('status',
            [MvModel::STAT_CALLBACK_DONE, MvModel::STAT_CALLBACK_ING])->count('id');

        $data = [
            'totalMV'     => $totalMV,
            'totalGoldMV' => $totalGoldMV,
            'totalXiao'   => $totalXiao,
            'totalPassed' => $totalPassed,
        ];
        return $this->ajaxSuccess($data);
    }

}