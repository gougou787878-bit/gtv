<?php


namespace service;


use DB;
use Exception;
use MemberModel;
use tools\RedisService;
use UserAttentionModel;

class FollowedService extends \AbstractBaseService
{


    /**
     * @param $uid
     * @param $toUid
     * @return array
     * @throws \Throwable
     * @author xiongba
     * @date 2020-03-02 20:02:53
     */
    public function handleFollowingUser($uid, $toUid)
    {
        //1 检查关注用户或者被关注者存在
        $users = MemberModel::whereIn('uid', [$uid, $toUid])->get([
            'uuid',
            'uid',
            'nickname',
            'oauth_type',
            'oauth_id',
            'aff',
            'auth_status'
        ])->map->getAttributes();
        $users = array_reindex($users, 'uid');
        $toMember = $users[$toUid] ?? null;
        $member = $users[$uid] ?? null;
        if (empty($member) || empty($toMember)) {
            return ['is_attention' => 0, 'msg' => '取消关注'];
        }
        $aff = $member['aff'];
        $toAff = $toMember['aff'];

        //2 检查redis中是否存在关注关系
        $fensKey = UserAttentionModel::REDIS_USER_FANS_LIST . $toAff;
        $followedKey = UserAttentionModel::REDIS_USER_FOLLOWED_LIST . $aff;
        $redis = redis();

        //限制关注数量
        $member['followed_count'] = $member['followed_count'] ?? 0;
        if ($member['followed_count'] > setting('followed.maxLimit', 200)) {
            return ['is_attention' => 0, 'msg' => '已经达到关注上限'];
        }
        $followed = UserAttentionModel::where(['uid' => $aff, 'touid' => $toAff])->exists();
        DB::beginTransaction();
        try {
            //4 关注过就取消关注
            if ($followed) {
                $itOk1 = UserAttentionModel::where(['uid' => $aff, 'touid' => $toAff])->delete();
                $itOk3 = $itOk2 = true;
                if ($itOk1) {
                    $itOk2 = MemberModel::incrPkLeast2Zero($toUid, ['fans_count' => -1]);
                    $itOk3 = MemberModel::incrPkLeast2Zero($uid, ['followed_count' => -1]);
                }
            } else {
                //5 没关注过
                $itOk1 = UserAttentionModel::create(['uid' => $aff, 'touid' => $toAff, 'created_at' => TIMESTAMP]);
                if (empty($itOk1)) {
                    throw new Exception('关注,操作数据失败' . intval($followed), 422);
                }
                $itOk2 = MemberModel::incrPk($toUid, ['fans_count' => 1]);
                $itOk3 = MemberModel::incrPk($uid, ['followed_count' => 1]);
                \MessageModel::createMessage($member['uuid'], $toMember['uuid'], "[{$member['nickname']}]关注了您~", '新粉丝', 0,
                    \MessageModel::TYPE_ATTENTION);
            }
            //6 只要其中一个操作失败，数据回滚
            DB::commit();
            //7 更新缓存
            UserAttentionModel::clearFollowUid($aff);
            //cached('user:idolVideo:')->suffix($uid)->clearCached();
            //cached('v2:user:idolVideo:')->suffix($uid)->clearCached();
            cached('follow:chargeVideo:')->suffix($uid)->clearCached();
            cached('follow:chargeVideo:index')->suffix($uid)->clearCached();
            cached('tb_fl_v:' . $this->member['uid'])->clearCached();
            cached('follow:mv:' . $this->member['uid'])->clearCached();
            cached(\UserAttentionModel::REDIS_USER_FANS_ITEM . $toUid)->clearCached();
            cached(\UserAttentionModel::REDIS_USER_FOLLOWED_ITEM . $uid)->clearCached();
            MemberModel::clearFor($toMember);
            MemberModel::clearFor($member);
            if ($followed) {
                $redis->sRem($fensKey, $aff);
                $redis->sRem($followedKey, $toAff);
                return ['is_attention' => 0, 'msg' => '取消关注'];
            } else {
                $redis->sAdd($fensKey, $aff);
                $redis->sAdd($followedKey, $toAff);
                return ['is_attention' => 1, 'msg' => '关注成功'];
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            //errLog($e);
            throw new Exception('操作失败');
        }
    }

    public function getFollowedBaseData(MemberModel $member)
    {
        return \UserAttentionModel::getList($member);
    }


    /**
     * 用户粉丝列表
     * @param string $uid
     * @param $member
     * @return array|bool|mixed|string
     */
    public function getUserFansList(string $uid, $member)
    {
        return cached(\UserAttentionModel::REDIS_USER_FANS_ITEM . $uid)
            ->setSaveEmpty(true)
            ->hash($this->page)
            ->expired(7200)
            ->serializerJSON()
            ->fetch(function () use ($uid, $member) {
                $members = \UserAttentionModel::where('touid', $uid)
                    ->with('fans:uid,aff,nickname,thumb,person_signnatrue,vip_level,expired_at')
                    ->limit($this->limit)
                    ->offset($this->offset)
                    ->get()
                    ->pluck('fans');
                $results = [];
                /** @var \MemberModel $item */
                foreach ($members as $item) {
                    if ($item === null) {
                        $results[] = \MemberModel::virtualByForDelele();
                    } else {
                        $results[] = $item->watchByUser($member);
                    }
                }
                return $results;
            });
    }

    public function formatMemberItem(\MemberModel $item, \MemberModel $member, $createAt)
    {
        $data = $item->watchByUser($member)
            ->makeHidden(['vip_level', 'expired_at'])
            ->toArray();
        $data['createdAtStr'] = formatTimestamp($createAt);
        return $data;
    }

    /**
     * 获取关注的主播id
     * @param MemberModel $member
     * @return array|\Illuminate\Support\Collection
     * @author xiongba
     * @date 2020-04-04 15:34:49
     */
    public function getFollowAnchorUid(MemberModel $member){
        $res = \UserAttentionModel::getList($member)->toArray();
        $uids = array_column($res , 'touid');
        $ids = MemberModel::whereIn('uid' , $uids)->where('auth_status' , MemberModel::AUTH_STATUS_YES)->get(['uid']);
        if (empty($ids->isEmpty())){
            return $ids;
        }else{
            return array_column($ids->toArray() , 'uid');
        }

    }

    /**
     * 判断是否关注(新)
     * @param $aff
     * @param $toAff
     * @return int
     * @author xiongba
     * @date 2020-02-26 20:22:36
     */
    public function isAttentionNew($aff, $toAff)
    {
        static $data = [];
        if (!isset($data[$aff])){
            $data[$aff] = redis()->sMembers(\UserAttentionModel::REDIS_USER_FOLLOWED_LIST . $aff);
        }
        if (in_array($toAff , $data[$aff])) {
            return 1;
        }
        return 0;
    }


    public function getCount($uid)
    {
        return redis()->sCard(\UserAttentionModel::REDIS_USER_FOLLOWED_LIST . $uid) ?: 0;
    }

}