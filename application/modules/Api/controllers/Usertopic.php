<?php

use service\TabService;
use service\UserTopicService;

/**
 * Class TopicController
 */
class UsertopicController extends BaseController
{

    /**
     * 合集列表
     * @return bool|void
     */
    public function list_topicAction()
    {
        $uid = intval($this->post['uid'] ?? 0);
        $status = 1;
        if (empty($uid)) {
            $uid = request()->getMember()->uid;
            $status = intval($this->post['status'] ?? 1);
        }
        $data = (new UserTopicService())->listOfTopic($uid,$status);
        return $this->showJson(['list' => $data]);
    }

    /**
     * 作品管理 我的剧集视频
     * @return bool|void
     */
    public function detailAction()
    {
        $topic_id = intval($this->post['topic_id'] ?? 0);
        if (!$topic_id) {
            return $this->errorJson('参数错误');
        }
        try {
            $member = request()->getMember();
            $model = UserTopicService::getTopicInfo($topic_id, request()->getMember());
            test_assert($model, '剧集不存在');
            $this->showJson([
                'info'  => $model,
                'list'  => $model->getMvList()
            ]);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    /**
     * 点赞视频
     * @return bool|void
     */
    public function toggle_likeAction()
    {
        $topic_id = intval($this->post['topic_id'] ?? 0);
        if (empty($topic_id)) {
            return $this->errorJson('数据错误');
        }

        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyMemberSayRole();
            $status = $service->toggle_like($member, $topic_id);
            \RankModel::addRank(\RankModel::TYPE_TOPIC,$topic_id,\RankModel::FIELD_TYPE_LIKE);
            return $this->showJson(['status' => $status, 'msg' => '操作成功']);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    /**
     * 点赞视频
     * @return bool|void
     */
    public function toggle_hideAction()
    {
        $topic_id = intval($this->post['topic_id'] ?? 0);
        if (empty($topic_id)) {
            return $this->errorJson('数据错误');
        }

        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $status = $service->toggle_hide($member, $topic_id);
            return $this->showJson(['status' => $status, 'msg' => '操作成功']);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    /**
     * 点赞的合集
     * @return bool|void
     */
    public function like_topicAction()
    {

        $uid = intval($this->post['uid'] ?? 0);
        if($uid){
            $member = MemberModel::find($uid);
        }else{
            $member = request()->getMember();
        }
        if(is_null($member)){
            return $this->showJson(null);
        }

        $service = new UserTopicService();
        try {
            $objectArray = $service->listOfLike($member);
            return $this->showJson($objectArray);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    /**
     * 置顶合集
     * @return bool|void
     */
    public function toggle_topAction()
    {
        $topic_id = intval($this->post['topic_id'] ?? 0);
        if (empty($topic_id)) {
            return $this->errorJson('数据错误');
        }

        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $model = $service->toggle_top($member, $topic_id);
            return $this->showJson($model);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }



    /**
     * 创建剧集
     */
    public function createAction()
    {
        $Validator = \helper\Validator::make($this->post, [
            'title' => 'required|max:100',
            'desp'  => 'required|max:1200',
            'type'  => 'required|max:40',
            'area'  => ['required', ['in' => UserTopicModel::AREA_COUNTRY]],
            'tags'  => 'required|max:40',
            'image' => ['required|max:255', ['regx' => '^([\w\./]){10,}$']],
            'year'  => ['required', ['in' => UserTopicModel::YEAR_LIST]],
        ]);
        $Validator->msgFail([
            'year'  => ['in' => '错误的年份选择'],
            'image' => ['regx' => '图片路径有误'],
        ]);

        $service = new UserTopicService();
        try {
            $data = $Validator->resultOrFail();
            $member = request()->getMember();
            $this->verifyMemberSayRole();
            $this->verifyAuth();
            $model = $service->create_episodes($member, $data);
            return $this->showJson($model);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    /**
     * 修改合集的视频
     * @return bool|void
     */
    public function updateAction()
    {
        $topic_id = intval($this->post['topic_id'] ?? 0);
        $idStr = htmlspecialchars($this->post['mv_id'] ?? ''); //视频id

        if (empty($topic_id) || empty($idStr)) {
            return $this->errorJson('数据不能为空');
        }
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $model = $service->update_topic($member, $topic_id, $idStr);
            return $this->showJson($model);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    public function add_videoAction(){
        $id = intval($this->post['topic_id'] ?? 0);
        $mv_id = intval($this->post['mv_id'] ?? 0);
        if (empty($id) || empty($mv_id)) {
            return $this->errorJson('参数不能为空');
        }
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $topic = UserTopicModel::find($id);
            $mv = MvModel::find($mv_id);
            $service->addVideo($member , $topic , $mv);
            return $this->showJson('操作成功');
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    public function clear_videoAction(){
        $id = intval($this->post['topic_id'] ?? 0);
        if (empty($id)) {
            return $this->errorJson('参数不能为空');
        }
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $service->clearVideo($member , $id);
            return $this->showJson('操作成功');
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    public function del_videoAction()
    {
        $id = intval($this->post['topic_id'] ?? 0);
        $mv_id = intval($this->post['mv_id'] ?? 0);
        if (empty($id) || empty($mv_id)) {
            return $this->errorJson('参数不能为空');
        }
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $service->delVideo($member, $id, $mv_id);
            return $this->showJson('操作成功');
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    /**
     * 删除合集
     * @return bool|void
     */
    public function deleteAction()
    {
        $topic_id = intval($this->post['topic_id'] ?? 0);

        if (empty($topic_id)) {
            return $this->errorJson('数据不能为空');
        }
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $this->verifyAuth();
            $this->verifyMemberSayRole();
            $service->delete_topic($member, $topic_id);
            return $this->showJson('删除成功');
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    public function listmvAction()
    {
        $kwy = $this->post['kwy'] ?? '';
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $list = $service->listmv($member, $kwy);
            return $this->showJson(['list' => $list]);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    /**
     * 热门合集
     * @return bool|void
     */
    public function popularAction(){
        $service = new UserTopicService();
        try {
            $member = request()->getMember();
            $list = $service->popular($member , 30);
            return $this->showJson(['list' => $list]);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }

    public function pre_configAction()
    {
        $data = [
            'type'     => UserTopicModel::TYPE_LIST,
            'cat_data' => TabService::getUploadTabList(),
            'year'     => UserTopicModel::YEAR_LIST,
            'area'     => UserTopicModel::AREA_COUNTRY
        ];
        return $this->showJson($data);
    }

}