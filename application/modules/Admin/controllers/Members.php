<?php


/**
 * Class MembersController
 * @author xiongba
 * @date 2020-02-27 17:44:18
 */
class MembersController extends BackendBaseController
{
    public $user_roles = array(
        2  => '广告推广用户',
        3  => '高级代理',
        /* 7 => '官网单独用', *///没带aff 默认一个值  区别与从别的app直接下载的app(aff为0)
        4  => '黑名单',//直接不能使用软件功能
        8  => '普通用户',
        9  => '禁言用户',
        10 => '后台资源用户',
        14 => '限时VIP',
        15 => '永久VIP'
    );

    /**
     * 列表数据过滤
     * @return Closure
     * @author xiongba
     * @date 2019-12-02 17:08:03
     */
    protected function listAjaxIteration()
    {
        return function ($item) {
            /** @var MemberModel $item */
            $item->aff_str = generate_code($item->aff);
            $item->invited_by_str = generate_code($item->invited_by);
            $item->thumb_url = url_avatar($item->thumb);
            $item->expired_at = date('Y-m-d', $item->expired_at);
            $item->coins = $item->coins;
            $item->coins_total = $item->coins_total;
            $item->score = $item->score;
            $item->score_total = $item->score_total;
            $item->votes = $item->votes;
            $item->votes_total = $item->votes_total;
            if ($item->regdate) {
                $item->regdate = date('Y-m-d H:i', $item->regdate);
            } else {
                $item->regdate = '';
            }
            if ($item->lastvisit) {
                $item->lastvisit = date('Y-m-d H:i', $item->lastvisit);
            } else {
                $item->lastvisit = '';
            }
            $item->free_date = date('Y-m-d',FreeMemberModel::where('uid' , $item->uid)->value('expired_at') ?? 0);
            return $item;
        };
    }


    public function indexInfoAction()
    {
        $where = $_GET['where'] ?? [];
        $this->assign('where', $where);
        $this->assign('roleArray', $this->user_roles);
        return $this->display('members/index-where.phtml');
    }


    public function listAjaxAction()
    {
        $this->convertAff2Num($_GET, ['aff', 'invited_by'], 'get_num');
        return parent::listAjaxAction();
    }

    public function add_freeAction()
    {
        $uid = $_POST['uid'];
        $day = $_POST['day'];
        FreeMemberModel::createInit($uid, $day);
        AdminLogModel::addOther($this->getUser()->username ,"对用户[$uid]添加了无限观看天数{$day}");
        $this->ajaxSuccessMsg('操作成功');
    }

    public function clearCachedAction()
    {
        $uid = $_POST['uid'] ?? '';
        $member = MemberModel::find($uid);
        MemberModel::clearFor($member);
        redis()->del("user:freemv:" . $uid);
        return $this->ajaxSuccessMsg('ok');
    }

    public function setUsername($val, $data, $pk)
    {
        $val = trim($val);
        if (empty($val)) {
            return '';
        }
        $member = MemberModel::firstByName($val);
        if (!empty($member) && $member->uid != $pk) {
            throw new Exception('用户名已存在');
        }
        return $val;
    }


    public function updateAfterCallback($model, $oldModel)
    {
        /** @var MemberModel $model */
        if (empty($model)) {
            return;
        }
        $id = UserProxyModel::where('aff', '=', $model->aff)->first();
        if (empty($id)) {
            UserProxyModel::insert([
                'root_aff'    => $model->aff,
                'aff'         => $model->aff,
                'proxy_level' => 1,
                'proxy_node'  => $model->aff,
                'created_at'  => time(),
            ]);
        }

        if (MemberModel::USER_ROLE_BLACK == $model->role_id) {
            //不能进入app  同时直接下掉会员 、账户妹币清零
            MemberModel::where('uid', $model->uid)->update(
                [
                    'expired_at' => strtotime("-7 days"),
                    'level'      => MemberModel::VIP_LEVEL_NO,
                    "coins"      => 0,
                    "score"      => 0,
                ]
            );
        }


        changeMemberCache($model->getDeviceHash(), $model->toArray());
    }


    public function setExpired_at($val, $data, $pk)
    {
        return strtotime($val);
    }


    public function setPassword($val)
    {
        if (empty($val)) {
            return null;
        }
        return $this->MakePasswordHash($val);
    }

    /**
     * 设置用户上推荐
     * @return bool
     */
    public function setRecommendAction()
    {
        $id = $this->post['id'] ?? 0;
        $recommend = $this->post['recommend'] ?? 0;
        if (empty($id)) {
            return $this->ajaxError('请求失败');
        }
        $recommend = $recommend ? 0 : 1;
        if (MemberModel::where('uid', $id)->update(['is_recommend' => $recommend])) {
            $member = MemberModel::find($id);
            changeMemberCache($member->getDeviceHash(), ['"is_recommend' => $recommend]);
            return $this->ajaxSuccess('设置成功');
        }
        return $this->ajaxError('设置失败');
    }

    /**
     * 过滤 post数据，
     * @param null $setPost
     * @return mixed
     */
    protected function postArray($setPost = null)
    {
        $post = parent::postArray($setPost);
        if (isset($post['phone']) && $post['phone']) {
            $post['is_reg'] = 1;
        }

        return $post;
    }

    public function setCoins($val, $data, $pk)
    {
        return null;
    }

    public function setCoins_total($val, $data, $pk)
    {
        return null;
    }

    public function setConsumption($val, $data, $pk)
    {
        return null;
    }

    public function setVotes($val, $data, $pk)
    {
        return null;
    }

    public function setVotes_total($val, $data, $pk)
    {
        return null;
    }


    public function addvipAction()
    {
        $id = $_POST['_pk'] ?? 0;
        $where = ['uid' => $id];
        $memebr = MemberModel::find($_POST['_pk'] ?? 0);
        if ($memebr) {
            $day = $_POST['expired_at'] ?? 0;
            $up = [
                'expired_at' => max($memebr->expired_at, time()) + $day * 24 * 3600,
                'vip_level'  => max($memebr->vip_level, $_POST['vip_level'] ?? 0),
            ];
            MemberModel::where($where)->update($up);
            MemberModel::clearFor($memebr);
            AdminLogModel::addFeedbackVip($this->getUser()->username,
                sprintf("对用户「{$id}」添加了「{$memebr->expired_at}」天VIP"));
        }
        $this->ajaxSuccessMsg('操作成功');
    }

    /**
     * 试图渲染
     * @return string
     * @author xiongba
     * @date 2020-02-27 17:44:18
     */
    public function indexAction()
    {
        $this->assign('roleArray', $this->user_roles);
        $this->display();
    }


    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2020-02-27 17:44:18
     */
    protected function getModelClass(): string
    {
        return MemberModel::class;
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2020-02-27 17:44:18
     */
    protected function getPkName(): string
    {
        return 'uid';
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

    public function gameAction()
    {
        $uid = $_POST['uid'] ?? 0;
        if (empty($uid)) {
            return $this->ajaxError('无效用户');
        }
        $balance = 0;
        return $this->ajaxError("当前账号: {$uid} 游戏余额：{$balance}");
    }

    public function banAction()
    {
        $uid = $_POST['uid'] ?? 0;
        if (empty($uid)) {
            return $this->ajaxError('无效用户');
        }
        MemberModel::where('uid',$uid)->update(['role_id'=>MemberModel::USER_ROLE_BLACK]);
        AdminLogModel::addOther($this->getUser()->username ,"对用户[$uid]封号拉黑处理了");
        return $this->ajaxError('操作成功');
    }
}