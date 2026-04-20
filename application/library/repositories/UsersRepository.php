<?php


namespace repositories;
use service\AppCenterService;
use service\AppReportService;
use tools\RedisService;
use Yaf\Exception;

trait UsersRepository
{
    /**
     * 通过手机号获取用户
     * @param string $phone
     * @return \MemberModel|object|null
     */
    public function getUserByPhone(string $phone)
    {
        $user = \MemberModel::query()->where('username', $phone)->first();

        return $user;
    }

    /**
     * 通过UUID获取用户
     * @param string $uuid
     * @return array
     */
    public function getUserByUUID(string $uuid)
    {
        $hash = cached('user:uuid:' . $uuid)
            ->serializerJSON()
            ->expired(7200)
            ->fetch(function () use ($uuid) {
                $member = \MemberModel::where('uuid', $uuid)->first(['oauth_id', 'oauth_type']);
                if ($member) {
                    return $member->getDeviceHash();
                }
                return null;
            });
        if (empty($hash)) {
            return [];
        }
        return cached('user:' . $hash)->expired(86400)->serializerJSON()->fetch(function () use ($uuid) {
            return \MemberModel::where('uuid', $uuid)->first()->toArray();
        });
    }

    /**
     * 获取用户头像  不见意使用 改方法  使用    url_avatar（）代替
     * @param $thumb
     * @return string
     */
    public function fetchUserThumb(string $thumb)
    {
        return url_avatar($thumb);
        if (empty(trim($thumb))) {
            return \Yaf\Registry::get('config')->img->default_jd_thumb;
        }
        return \Yaf\Registry::get('config')->img->img_head_url . $thumb;
    }

    /**
     * 随机获取分享链接
     * @param $aff
     * @param $channel
     * @return mixed
     */
    public function getShareUrl(string $aff = '',string $channel='')
    {
        $aff_code = '';
        if ($aff) {
            $aff_code = generate_code($aff);
            return getShareLink($aff_code, $channel);
        } else {
            $aff_code = generate_code($this->member['aff']);
            $channel = $this->member['build_id'];
        }
        return getShareLink($aff_code, $channel);
    }

    /**
     * 绑定账号
     * @param string $phone
     * @param string $password
     * @return string
     * @throws Exception
     */
    public function handleRegister(string $phone, string $password): string
    {
        $password = md5($password);
        $hasPhone = $this->getUserByPhone($phone);
        if ($hasPhone) {
            throw new Exception('该账号已被注册', 422);
        }

        if ($this->member['username'] != '') {
            throw new Exception('已经注册过账号了', 422);
        }

        $data = [
            'username' => $phone,
            'phone'    => $phone,
            'password' => $password,
            'is_reg'   => 1,
        ];
        \MemberModel::query()->where('uuid', $this->member['uuid'])->update($data);
        if ($this->member['invited_by'] > 0 && $this->member['regdate'] > \UserInviteReceiveLogModel::START_TIME) {
            $inviteInfo = \UserInviteStatModel::query()->where('uid', $this->member['invited_by'])->first();
            if ($inviteInfo) {
                \UserInviteStatModel::query()->where('uid', $this->member['invited_by'])->increment("nums");
            } else {
                \UserInviteStatModel::query()->insert(['uid' => $this->member['invited_by'], "nums" => 1]);
            }
        }
        $this->member = array_merge($this->member, $data);
        \MemberModel::clearFor($this->member);
        return $this->token($this->member['uuid']);
    }

    /**
     * 切换账号
     * @param \MemberModel $user
     * @return string
     * @throws Exception
     */
    public function handleChange(\MemberModel $user): string
    {
        $member = \MemberModel::find($this->member['uid']);
        if (empty($user)) {
            throw new Exception('找不到该用户', 422);
        }
        if ($user->uid == $member->uid) {
            return $this->token($member->uuid);
            //throw new Exception('账号跟当前账号一致', 422);
        }
        $changeUUID = $user->uuid;


        \DB::beginTransaction();
        try {
            // 保存交换记录
            $log = [
                'uid'             => $member->uid,
                'oauth_type'      => $member->oauth_type,
                'oauth_id'        => $member->oauth_id,
                'old_uuid'        => $member->uuid,
                'new_uuid'        => $user->uuid,
                'old_invited_num' => $member->invited_num,
                'new_invited_num' => $user->invited_num,
                'created_at'      => TIMESTAMP
            ];
            \UuidLogModel::create($log);

            // 交换用户
            $tempOauth = ['oauth_id' => 'temp_' . \TIMESTAMP . rand(1000, 9999), 'oauth_type' => 'ios'];
            $memberTemp = ['oauth_id' => $user->oauth_id, 'oauth_type' => $user->oauth_type];
            $userTemp = ['oauth_id' => $member->oauth_id, 'oauth_type' => $member->oauth_type];
            $member->update($tempOauth); // 联合索引
            $user->update($userTemp);
            $member->update($memberTemp);

            // 如果被交换的用户也交换过，uuid 不是自己的
            \MemberModel::clearFor($memberTemp);
            \MemberModel::clearFor($userTemp);
            \MemberModel::clearFor($this->member);
            RedisService::redis()->del('user:' . $changeUUID);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw new Exception($exception->getMessage(), 422);
        }
        return $this->token($member->uuid);
    }

    /**
     * 填写邀请码
     * @param string $aff
     * @throws Exception
     */
    public function handleInvitationUser(string $aff,&$inviteUser)
    {
        //$msg = 'handleInvitationUser:'.date('Y-m-d').'#'.$aff.'#'.PHP_EOL;
        $aff_uid = (int)get_num($aff);
        wf('邀请码3', $aff_uid, false, '/storage/logs/invitation.log');
        if ($aff_uid >= $this->member['uid']) {
            wf('邀请码无效', $aff_uid, false, '/storage/logs/invitation.log');
            throw new Exception('邀请码无效', 422);
        }
        $regTime = $this->member['regdate']??0;
        $now = time();
        $gap = 48 * 3600;
        if(($now-$regTime)>$gap){
            wf('已超过48小时,你不能被邀请~', $aff_uid, false, '/storage/logs/invitation.log');
            throw new Exception('已超过48小时,你不能被邀请~', 422);
        }
        /** @var \MemberModel $user */
        $user = \MemberModel::query()->where('aff', $aff_uid)->first();
        wf('邀请码4', $user->toArray(), false, '/storage/logs/invitation.log');

        if (empty($user)) {
            wf('邀请码不正确', $aff, false, '/storage/logs/invitation.log');
            throw new Exception('邀请码不正确', 422);
        }
        //过滤非法渠道
        if ($user->build_id && stripos($user->build_id, 'xl') !== false) {
            $user->build_id = '';
        }
        if ($user->build_id == 'xl') {
            $user->build_id = '';
        }

        /*if(BaipiaoService::checkJoin(['uid'=>$aff_uid])){
            $inviteUser = $user;
        }*/
        if ($this->member['invited_by'] != 0) {
            wf('已经填写过邀请码了', $this->member['invited_by'], false, '/storage/logs/invitation.log');
            return ['code'=>422,'msg'=>'已经填写过邀请码了'];
            //throw new Exception('已经填写过邀请码了', 422);
        }
        //$msg .='user:'.var_export($user->toArray(),true);
        //$msg .='nowU:'.var_export($this->member,true);
        \DB::beginTransaction();
        try {
            $reward = \MemberModel::INVITED_REWARD_TIMES;
            $expired = max($user->expired_at , TIMESTAMP) + $reward;
            // 更新上级信息
            $user->expired_at = $expired;
            $user->invited_num = $user->invited_num + 1;
            $user->save();
            changeMemberCache($user->getDeviceHash(), ['expired_at' => $expired, 'invited_num' => $user->invited_num]);

            // 更新邀请信息
            \MemberModel::query()->where('uuid', $this->member['uuid'])->update([
                'invited_by' => $user->aff,
                'build_id'   => $user->build_id,
            ]);
            wf('邀请信息', [$user->aff, $user->build_id], false, '/storage/logs/invitation.log');

            //上报更新
            if ($user->build_id) {
                (new AppCenterService())->addUser($this->member['uid'], $this->member['uuid'], $this->member['oauth_type'], $user->build_id,
                    $user->aff);
                \SysTotalModel::incrBy('member:create:invite');
            }
            //数据中心 邀请上报
            (new AppReportService())->updateUser([
                'uid'        => $this->member['uid'],
                'invited_by' => $user->aff,
                'channel'    => $user->build_id ? $user->build_id : '',
            ]);

            \MemberModel::clearFor($this->member);
            \MemberModel::clearFor($user);
            // 更新代理表

            \DB::commit();
            $this->member['invited_by'] = $user->aff;
            $this->member['build_id'] = $user->build_id;
        } catch (\Exception $exception) {
            \DB::rollBack();
            errLog('invite:'.$exception->getMessage());
            throw new Exception('填写失败！', 422);
        }
        //errLog($msg);
        return ['code'=>200,'msg'=>'yes'];

    }


    /**
     * 用户关注列表
     * @param string $uid
     * @param $member
     * @return array|bool|mixed|string
     */
    public function getUserFollowedList(string $uid, $member)
    {
        $key = \UserAttentionModel::REDIS_USER_FOLLOWED_ITEM . $uid;
        return cached($key)
            ->expired(7200)
            ->hash($this->page)
            ->serializerPHP()
            ->fetch(function () use ($uid, $member) {
                $members = \UserAttentionModel::query()
                    ->where('uid', $uid)
                    ->with('followed:uid,aff,nickname,thumb,person_signnatrue,vip_level,expired_at')
                    ->offset($this->offset)
                    ->limit($this->limit)
                    ->get()
                    ->pluck('followed');
                $results = [];
                foreach ($members as $key => $item) {
                    if ($item === null){
                        $member = \MemberModel::virtualByForDelele();
                    }else{
                        /** @var \MemberModel $item */
                        $item->watchByUser($member);
                    }
                    $results[$key] = $item;
                }

                return $results;
            });
    }


    public function clearUserInfo($uuid = '')
    {
        if (empty($uuid) || $uuid == $this->member['uuid']) {
            \MemberModel::clearFor($this->member);
        } else {
            $member = \MemberModel::where('uuid', $uuid)->first();
            \MemberModel::clearFor($member);
        }
    }

    /* 判断是否关注(新) */
    function isAttentionNew($uid, $toUid)
    {
        static $array = null;
        if ($array === null) {
            $tmp = redis()->sMembers(\UserAttentionModel::REDIS_USER_FOLLOWED_LIST . $uid);
            $array = array_flip($tmp);
        }
        //不要修改类型，否则app可能会崩溃
        return isset($array[$toUid]) ? 1 : 0;
    }

    /**
     * 得到关注人是否有开播
     * @param $uid
     * @return bool
     */
    function getFollowLive($uid)
    {
        return false;
    }
}