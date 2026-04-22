<?php

use Illuminate\Support\Str;
use service\AppCenterService;
use service\AppReportService;
use service\EventTrackerService;
use service\MarketingLotteryTriggerDispatcher;
use Yaf\Exception;

class LibMember
{
    public $version;
    public $oauth_id;
    public $oauth_ads_id;
    public $oauth_type;
    public $UUID = '';
    public $userData;
    public $redis;
    public $Db;
    public $redisKey;
    public $channel = '';
    public $nickname = '';
    public $thumb = '';
    public $expired_at = 0;
    /**
     * 静态成品变量 保存全局实例
     */
    private static $_instance = null;

    function __construct()
    {
        $this->init();
    }

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function init()
    {
        $this->oauth_id = $_POST['oauth_id'] ?? '';
        $this->oauth_ads_id = $_POST['oauth_ads_id'] ?? '';
        //$this->channel = $_POST['theme'] ?? '';//直播接入鉴别参数
        $this->channel = '';//直播接入鉴别参数
        $this->version = $_POST['version'] ?? '';
        $this->oauth_type = $_POST['oauth_type'] ?? '';
        if ($this->oauth_id && $this->oauth_type) {
            $this->UUID = md5($this->oauth_type . $this->oauth_id);
            $this->redisKey = 'user:' . $this->UUID;
        }
    }


    protected function verifyIpCreateMember()
    {
        //不是入口创建用户 干死t
        if(strpos($_SERVER['PATH_INFO'],'home/getConfig')===false){
            header("Status: 503 Service Unavailable");
            exit();
        }elseif (($_POST['oauth_type']??'') == 'android') {
            if (version_compare($_POST['version'], '1.2.0', '<')) {
                header("Status: 503 Service Unavailable");
                exit();
            }elseif (false && version_compare($_POST['version'], '1.2.0', '>=')) {
                $__package_name__ = $_POST['__package_name__'] ?? '';
                $__package_hash__ = $_POST['__package_hash__'] ?? '';
                /*$real_hash = VersionModel::checkBound($__package_name__);
                if ($real_hash && $real_hash == $__package_hash__) {
                    return true;
                }*/
                if(!$__package_name__ || !$__package_hash__){
                    header("Status: 503 Service Unavailable");
                    exit();
                }
            }
        }
        $_ipkey = 'banip:' . USER_IP;
        $_number = redis()->incr($_ipkey);
        if ($_number <= 2) {
            redis()->expire($_ipkey, 60);
        }
        if ($_number > setting('banip.limit', 50)) {
            redis()->expire($_ipkey, 99999);
            redis()->sAdd('ban:ip:list', USER_IP);
            header("Status: 503 Service Unavailable");
            exit();
        }
    }

    /**
     * 如果渠道是直播接入，同步用户的昵称和头像；同步其他相關自動操作
     * @author xiongba
     */
    protected function syncNicknameWithAvatar()
    {
        $lastvisit = $this->userData['lastvisit'];
        $today = strtotime(date('Y-m-d 00:00:00'));
        if ($lastvisit > $today) {//今天更新过就不处理
            return;
        }
        $data = [];
        $data['lastvisit'] = TIMESTAMP;
        if ($this->userData['expired_at'] && $this->userData['expired_at'] < TIMESTAMP) {
            $data['vip_level'] = MemberModel::VIP_LEVEL_NO;
        }
        if ($this->version && isset($this->userData['app_version']) && $this->userData['app_version'] != $this->version) {
            $data['app_version'] = $this->version;
        }
        MemberModel::where(['uid' => $this->userData['uid']])->update($data);
        $member = MemberModel::makeOnce($this->userData);

        MemberModel::reportKeepData($member);
    }

    function FetchMember()
    {
        $cached = cached($this->redisKey)->serializerJSON()->expired(3600);
        $this->userData = $cached->fetch(function ($cached) {
            /** @var CacheDb $cached */
            $userData = $this->GetMember();
            if (empty($userData)) {
                $cached->expired(-1);
            }
            if (count($userData) < 50) {
                errLog("用户创建字段少于50：" . json_encode($userData));
            }
            //用户 靓号
            /* $liang = LiangModel::getLiangBy($userData['uid']);
             if ($liang) {
                 $userData['beauty_no'] = $liang['name'] ?? 0;
             }*/
            return $userData;
        });

        //更新同步
        $this->syncNicknameWithAvatar();

        if (!empty($this->userData)) {
            if (isset($this->userData['uid']) && $this->userData['uid'] > 0) {
                $this->triggerMarketingLoginOncePerDay();

                // 更新session, 并且修改redis hash key
                $updatedSession = $this->updateSession();
                if ($updatedSession) {
                    $this->userData['lastactivity'] = TIMESTAMP;
                    \tools\RedisService::set($this->redisKey, $this->userData, 7200);
                }
            } else {
                define("MEMBER_ID", 0);
                define("MEMBER_UUID", null);
                define("MEMBER_NAME", null);
                define("MEMBER_ROLE", 0);
            }
        }
        return $this->userData;
    }

    /**
     * 获取用户
     * @return array
     */
    protected function triggerMarketingLoginOncePerDay(): void
    {
        try {
            $uid = (int) ($this->userData['uid'] ?? 0);
            if ($uid <= 0) {
                return;
            }
            if ((int) ($this->userData['is_reg'] ?? 0) !== 1) {
                return;
            }
            if (trim((string) ($this->userData['username'] ?? '')) === '') {
                return;
            }

            $tomorrow = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
            $ttl = max(1, $tomorrow - TIMESTAMP);
            $key = 'marketing_lottery:user_login:' . $uid . ':' . date('Ymd');
            if (!redis()->setnxttl($key, 1, $ttl)) {
                return;
            }

            MarketingLotteryTriggerDispatcher::trigger('user_login', [
                'uid' => $uid,
                'uuid' => (string) ($this->userData['uuid'] ?? ''),
                'trigger_from' => 'member_fetch',
            ]);
        } catch (\Throwable $e) {
            errLog('LibMember::triggerMarketingLoginOncePerDay: ' . $e->getMessage());
        }
    }

    function GetMember()
    {
        /** @var MemberModel $user */
        $user = MemberModel::query()
            ->where('oauth_id', $this->oauth_id)
            ->where('oauth_type', $this->oauth_type)
            ->first();

        //如果用户不存在
        if ($user === null) {
            $this->verifyIpCreateMember();
            try {
                /** @var MemberModel $user 错误的话，重拾1次 */
                $user = redis()->lock($this->UUID, function () {
                    return $this->createMember();
                });
            } catch (\RuntimeException $e) {
                //errLog($e);
                exit('失败');
            } catch (\Throwable $e) {
                errLog($e);
                exit('失败');
            }
        }
        /** @var MemberModel $user */
        // 判断会员是否到期
        $user->lastactivity = $this->getUserLastActivity($user);
        return $user->toArray();
    }

    public function is_aff()
    {
        return null;//取消ip 邀请判断 统一用剪切板干
        $time = TIMESTAMP - 3600;
        $ip = USER_IP;
        if ('unknown' == $ip) {
            return false;
        }
        $openLog = AffOpenLogModel::query()
            ->where('ip', $ip)
            ->where('created_at', '>=', $time)
            ->first(['aff', 'channel']);

        if ($openLog) {
            $parent = MemberModel::where('aff', $openLog->aff)->first();
            $expiredTIme = max($parent->expired_at, TIMESTAMP) + MemberModel::INVITED_REWARD_TIMES;
            if ($expiredTIme > 2147483646) {
                $expiredTIme = 2147483646;
            }
            $parent->expired_at = $expiredTIme;
            $parent->invited_num += 1;
            $parent->save();
            changeMemberCache($parent->getDeviceHash(),
                ['expired_at' => $expiredTIme, 'invited_num' => $parent->invited_num]);
            // TODO update parent redis cache
        }
        return $openLog;
    }

    /**
     * 创建用户信息
     * @return bool|MemberModel
     * @throws Throwable
     */
    public function createMember()
    {
        if (!$this->UUID) {
            return false;
        }
        //事务处理
        $uuid = $this->UUID;
        $thumb = MemberRand::randAvatar();
        $nickname = MemberRand::randNickname();


        //直播接入处理

        $affOpen = $this->is_aff();

        $aff = $affOpen ? $affOpen->aff : '0';

        $_theme = $_POST['theme'] ?? '';

        if ($_theme && strtolower($_theme) != 'gw') {
            $_aff = get_num($_theme);
            if ($_aff) {
                /** @var MemberModel $_channel_member */
                $_channel_member = MemberModel::where('aff', $_aff)->first(['uid', 'build_id']);
                if (!is_null($_channel_member)) {
                    $this->channel = $_channel_member->build_id;
                    $aff = $_aff;
                }
            }
        }
        if(!$this->channel && $affOpen && $affOpen->channel){
            $this->channel = $affOpen->channel;
        }

        $invited_num = 0;
        DB::beginTransaction();
        try {
            /**
             * @var MemberModel $member
             */
            // 创建用户
            $member = MemberModel::make(MemberModel::getDefaultValue());
            $member->thumb = $thumb;
            $member->nickname = $nickname;
            $member->uuid = $uuid;
            $member->app_version = $this->version;
            $member->oauth_type = $this->oauth_type;
            $member->oauth_id = $this->oauth_id;
            $member->username = '';
            $member->role_id = MemberModel::USER_ROLE_LEVEL_MEMBER;
            $member->regdate = TIMESTAMP;
            $member->lastvisit = TIMESTAMP;
            $member->regip = USER_IP;
            $member->invited_num = $invited_num;
            $member->invited_by = $aff;
            $member->build_id = $this->channel;
            $member->expired_at = $this->expired_at;
            $member->vip_level = $this->expired_at > TIMESTAMP ? 1 : 0;
            $member->is_reg = 0;
            $member->is_live_super = 0;
            $member->trace_id = $_POST['trace_id'] ?? '';
            // 推广码
            $member->save();
            // 更新推广码 / 昵称
            $member->aff = $member->uid;
            $member->save();
            // 插入代理关系
            /*$proxy_data = [
                'root_aff'    => $member->uid,
                'aff'         => $member->uid,
                'proxy_level' => 1,
                'proxy_node'  => $member->uid,
                'created_at'  => TIMESTAMP,
            ];

            if ($aff) {
                $proxy = UserProxyModel::query()->where('aff', $aff)->first();
                if ($proxy) {
                    $proxy_node = trim($proxy->proxy_node, ',');
                    $proxy_level = $proxy->proxy_level + 1;
                    $proxy_node = $proxy_node . ",{$member->uid}";

                    $proxy_data = [
                        'root_aff'    => $proxy->root_aff,
                        'aff'         => $member->uid,
                        'proxy_level' => $proxy_level,
                        'proxy_node'  => $proxy_node,
                        'created_at'  => TIMESTAMP,
                    ];
                }
            }
            UserProxyModel::create($proxy_data);*/
            $logModel = MemberLogModel::createBy($member->uuid, $this->oauth_type, USER_IP, TIMESTAMP,
                $this->version);
            if (!is_null($logModel)) {
                $member->session = $logModel;
            }
            \DB::commit();
        } catch (\PDOException $exception) {
            \DB::rollBack();
            return $this->createdExceptionFind($exception);
        } catch (\Throwable $exception) {
            \DB::rollBack();
            errLog($exception);
            throw $exception;
        }
        //用户上报
        if ($member->build_id) {
            (new AppCenterService())->addUser($member->uid, $member->uuid, $member->oauth_type, $member->build_id, $member->invited_by);
        }

        try {
            $aff_code = $_POST['aff_x_code'] ?? '';
            $invited_aff = 0;
            if ($aff_code){
                $invited_aff = (int)get_num($aff_code);
                //绑定渠道
                $this->handleInvitationUser($member, $aff_code);
            }
        }catch (Throwable $exception){
            wf('绑定异常', $exception->getMessage());
        }

        //公司上报
        (new EventTrackerService(
            $member->oauth_type,
            $invited_aff,
            $member->uid,
            $member->oauth_id,
            $_POST['device_brand'] ?? '',
            $_POST['device_model'] ?? ''
        ))->addTask([
            'event' => EventTrackerService::EVENT_USER_REGISTER,
            'type'  => EventTrackerService::REGISTER_TYPE_DEVICEID,
            'trace_id' => $_POST['trace_id'] ?? '',
            'create_time' => to_timestamp($member->regdate)
        ]);

        $member = MemberModel::useWritePdo()->find($member->uid);

        //注册统计
        $this->createStat($this->channel,$this->oauth_type);
        //日活上报
        $this->activeStat($member);

        return $member;
    }

    /**
     * 填写邀请码
     */
    public function handleInvitationUser(MemberModel $member, $aff)
    {
        $aff_uid = (int)get_num($aff);
        if ($aff_uid >= $member->uid) {
            throw new Exception('邀请码无效', 422);
        }
        $regTime = $member->regdate ?? 0;
        $now = time();
        $gap = 48 * 3600;
        if(($now-$regTime)>$gap){
            throw new Exception('已超过48小时,你不能被邀请~', 422);
        }
        /** @var \MemberModel $user */
        $user = \MemberModel::query()->where('aff', $aff_uid)->first();
        if (empty($user)) {
            throw new Exception('邀请码不正确', 422);
        }
        //过滤非法渠道
        if ($user->build_id && stripos($user->build_id, 'xl') !== false) {
            $user->build_id = '';
        }
        if ($user->build_id == 'xl') {
            $user->build_id = '';
        }

        if ($member->invited_by != 0) {
            throw new Exception('已经填写过邀请码了', 422);
        }
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
            \MemberModel::query()->where('uuid', $member->uuid)->update([
                'invited_by' => $user->aff,
                'build_id'   => $user->build_id,
            ]);

            //上报更新
            if ($user->build_id) {
                (new AppCenterService())->addUser($member->uid, $member->uuid, $member->oauth_type, $user->build_id,
                    $user->aff);
                \SysTotalModel::incrBy('member:create:invite');
            }

            \MemberModel::clearFor($member);
            \MemberModel::clearFor($user);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw new Exception('填写失败！', 422);
        }
    }

    /**
     * @param PDOException $e
     * @return MemberModel
     * @author xiongba
     * @date 2020-03-01 13:18:37
     */
    private function createdExceptionFind(\PDOException $e)
    {
        if ($e->getCode() !== 23000 && $e->getCode() !== 1062) {
            throw $e;
        } else {
            // 大佬说的出错就出错。不管了
            exit('系统出错 23000');
        }
        /** @var MemberModel $member */
        /** @var MemberLogModel $session */
        $session = $member = null;
        try {
            return retry(2, function ($attempts) use (&$e, &$member, &$session) {
                $member = MemberModel::useWritePdo()
                    ->where(['oauth_id' => $this->oauth_id, 'oauth_type' => $this->oauth_type])
                    ->first();
                $session = MemberLogModel::useWritePdo()->where(['uuid' => $member->uuid])->first();
                if ($member && $session) {
                    $member->session = $session;
                    return $member;
                }
                throw $e;
            }, 2);
        } catch (\Throwable $e) {
            $msg = '系统出错 23000';
            errLog($msg);
            exit($msg);
        }
    }


    /**
     * 获取用户最后活动时间
     * @param MemberModel $member
     * @return int
     */
    public function getUserLastActivity($member)
    {
        $session = $member->session;
        if (empty($session)) {
            if (redis()->setex("lock:" . $member['uid'], 20, 1)) {
                $session = MemberLogModel::createBy($member->uuid, $this->oauth_type, USER_IP, TIMESTAMP,
                    $this->version);
            } else {
                return TIMESTAMP;
            }
        }
        if (!is_null($session)) {
            // 更新日活
            if ($session->lastactivity < strtotime(date('Y-m-d', TIMESTAMP))) {
                $session->lastactivity = TIMESTAMP;
                $session->app_version = $this->version;
                $session->lastip = USER_IP;
                $session->save();
                //日活上报
                $this->activeStat($member);
            }
            return $session->lastactivity;
        }
        return TIMESTAMP;
    }

    public function updateSession()
    {
        $insertTimestamp = strtotime(date('Y-m-d', TIMESTAMP));
        // 日活已经是今天，无需更新
        if (isset($this->userData['lastactivity']) && $this->userData['lastactivity'] > $insertTimestamp) {
            return false;
        }

        // 更新日活信息，没有的话创建
        $session = MemberLogModel::query()->where('uuid', $this->userData['uuid'])->first();
        if (empty($session)) {
            $session = MemberLogModel::useWritePdo()->where('uuid', $this->userData['uuid'])->first();
        }
        if (!$session) {
            $session = new MemberLogModel();
            $session->uuid = $this->userData['uuid'];
            $session->oauth_type = $this->oauth_type;
        }
        $session->lastip = USER_IP;
        $session->lastactivity = TIMESTAMP;
        $session->save();

        return true;
    }

    public function activeStat(MemberModel $member){
        \SysTotalModel::incrBy('member:active');
        switch ($this->oauth_type) {
            case MemberModel::TYPE_ANDROID:
                \SysTotalModel::incrBy('member:active:and');
                break;
            case MemberModel::TYPE_PWA:
                \SysTotalModel::incrBy('member:active:pwa');
                break;
            case MemberModel::TYPE_IOS:
                \SysTotalModel::incrBy('member:active:ios');
                break;
        }
        $carbon = \Carbon\Carbon::parse($member->regdate);
        $day = $carbon->diffInDays();
        if ($day <= 0 || $day > 15) {
            return;
        }
        // 1-15 天的留存
        $key = "keep:{$day}day";
        $channel = $member->build_id;
        SysTotalModel::incrBy($key);
        if ($channel) {
            SysTotalModel::incrBy('c' . $key);
            SysTotalModel::incrBy($key . ':' . $channel);
        }
    }

    public function createStat($build_id,$oauth_type){
        \SysTotalModel::incrBy('member:create');
        //邀请创建
        if ($build_id){
            \SysTotalModel::incrBy('member:create:invite');
        }
        switch ($oauth_type) {
            case MemberModel::TYPE_ANDROID:
                \SysTotalModel::incrBy('member:create:and');
                break;
            case MemberModel::TYPE_PWA:
                \SysTotalModel::incrBy('member:create:pwa');
                break;
            case MemberModel::TYPE_IOS:
                \SysTotalModel::incrBy('member:create:ios');
                break;
        }
    }

}
