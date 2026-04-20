<?php

/**
 * 评论
 * Class UserController
 */
class CommentController extends AdminController
{
    public function indexAction()
    {

        $query_link = 'd.php?mod=comment&code=index';
        $query = CommentModel::query()->offset($this->pageStart)->limit($this->perPageNum)->orderBy('created_at',
            'desc');
        $status = $this->get['status'] ?? '';
        $uid = $this->get['uid'] ?? '';
        $mv_id = $this->get['mv_id'] ?? '';
        $start = $this->get['start_time'] ?? date('Y-m-d');
        $end = $this->get['end_time'] ?? date('Y-m-d');

        $query_link .= '&status=' . $status;
        $get['status'] = $status;
        ($status == '0' || $status == '1' || $status == '2') && $query->where('status', $status);

        $query_link .= '&start_time=' . $start;
        $get['start_time'] = $start;
        $start && $query->where('created_at', '>=', "{$start} 00:00:00");

        $query_link .= '&end_time=' . $end;
        $get['end_time'] = $end;
        $end && $query->where('created_at', '<', "{$end} 23:59:59");

        $query_link .= '&uid=' . $uid;
        $get['uid'] = $uid;
        $uid && $query->where('uid', $uid);
        $query_link .= '&mv_id=' . $mv_id;
        $get['mv_id'] = $mv_id;
        $mv_id && $query->where('mv_id', $mv_id);
        $query = $query->get();
        if (count($query) < $this->perPageNum) {
            $page_arr['html'] = sitePage($query_link, 1);
        } else {
            $page_arr['html'] = sitePage($query_link);
        }
        $this->getView()
            ->assign('data', $query)
            ->assign('page_arr', $page_arr)
            ->assign('formget', $get)
            ->display('comment/index.phtml');
    }

    public function setstatusAction()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $data['status'] = 0;
            $data['updated_at'] = date('Y-m-d H:i:s');
            $result = CommentModel::where("id", $id)->update($data);
            if ($result) {
                $rs = CommentModel::find($id);
                $this->clearCommet($rs->mv_id);
                $this->showJson('隐藏评论成功#' . $id);
            } else {
                $this->showJson('隐藏评论失败', 0);
            }
        } else {
            $this->showJson('数据传入失败！', 0);
        }
    }


    /**
     * 设置状态或删除时 同时清楚1-2评论缓冲；
     *
     * @param $mvid
     */
    public function clearCommet($mvid)
    {
        $key = \CommentModel::REDIS_COMMENT_LIST . $mvid . '_' . 1;
        \tools\RedisService::del($key);
        $key = \CommentModel::REDIS_COMMENT_LIST . $mvid . '_' . 2;
        \tools\RedisService::del($key);
        MvModel::where(['id' => $mvid])->decrement('comment');
    }


    public function delAction()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $result = CommentModel::find($id);
            if ($result) {
                $this->clearCommet($result->mv_id);
                $result->delete();
                $this->showJson('删除成功#' . $id);
            } else {
                $this->showJson('删除失败', 0);
            }
        } else {
            $this->showJson('数据传入失败！', 0);
        }
    }

    public function setbanAction()
    {
        $id = intval($_GET['id']);
        $action = $_REQUEST['action'];
        if ($id && in_array($action, ['forever', 'ban724'])) {
            $result = CommentModel::find($id);
            if ($result) {
                $key = CommentModel::REDIS_COMMENT_BAN . $result->uid;
                ($action == 'ban724') && \tools\RedisService::set($key, $action);
                if ($action == 'forever') {
                    CommentModel::where('uid', $result->uid)->delete();
                    $this->clearCommet($result->mv_id);
                    $user = MemberModel::query()->where('uid', $result->uid)->first();
                    if ($user) {
                        $update = MemberModel::where('uid', $result->uid)->update([
                            'role_id' => MemberModel::USER_ROLE_LEVEL_BANED,
                        ]);
                        $update && changeMemberCache($user->getDeviceHash(), [
                            'role_id' => MemberModel::USER_ROLE_LEVEL_BANED,
                        ]);
                    }
                }


                $content = $result->comment;
                $s_content = AdsampleModel::getCommentTextSmilarity($content);
                $s_content && AdsampleModel::addAdsample([
                    'content' => $s_content
                ]);

                $this->showJson('禁言成功#' . $result->uid);
            } else {
                $this->showJson('禁言失败', 0);
            }
        } else {
            $this->showJson('数据传入失败！', 0);
        }
    }


}
