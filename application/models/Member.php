<?php

use helper\OperateHelper;


/**
 * class MemberModel
 * @property int $uid
 * @property string $oauth_type 设备'ios','android'
 * @property string $oauth_id
 * @property string $uuid
 * @property string $username
 * @property string $password
 * @property int $is_reg 是否注册， 0为游客  1为注册用户
 * @property int $role_id
 * @property string $regip
 * @property int $regdate
 * @property string $lastip
 * @property int $lastvisit
 * @property int $expired_at 会员到期时间
 * @property int $aff 邀请码md5( md5(uuid) )
 * @property int $invited_by 被谁 aff 邀请
 * @property int $invited_num 已邀请安装个数
 * @property string $app_version app版本号
 * @property string $nickname 用户昵称
 * @property string $thumb 用户头像
 * @property int $coins 用户金币余额
 * @property int $coins_total 累计金币
 * @property int $score 用户视频收益
 * @property int $score_total 累计视频收益
 * @property int $votes 直播收益余额
 * @property int $votes_total 直播收益总额
 * @property int $post_coins 社区收益
 * @property int $total_post_coins 社区总收益
 * @property int $tui_coins
 * @property int $total_tui_coins
 * @property int $fans_count 用户粉丝数
 * @property int $followed_count 关注数
 * @property int $videos_count 作品数
 * @property int $fabulous_count 获赞数
 * @property int $likes_count 喜欢数
 * @property int $live_count 直播次数
 * @property int $sexType 0未设置1男2女
 * @property int $vip_level 0 非会员/过期 1月卡 2季卡 3 年卡
 * @property string $person_signnatrue 个人签名
 * @property string $build_id build_id 超级签名标识
 * @property int $auth_status 0 未认证 1 认证通过
 * @property string $birthday 生日
 * @property int $live_supper 直播超管  0 1
 * @property int $is_live_super 直播角色 30 普通，50 房间管理员 60 超管
 * @property string $phone
 * @property MemberLogModel $session
 * @property MvModel[] $mvList
 * @property string $trace_id trace_id
 *
 * @property int $is_vip 是否是vip
 * @property string $avatar_url
 * @property string $expired_str
 * @property int $is_attention
 * @property int $doubleFollowed
 * @property boolean $isVV
 * @property boolean $vvLevel
 *
 * @property MemberMakerModel $maker
 *
 *
 * @author xiongba
 * @date 2020-02-26 12:07:25
 *
 * @mixin \Eloquent
 */
class MemberModel extends EloquentModel
{
    // const
    const USER_ROLE_LEVEL_MEMBER = 8; // 普通用户
    const USER_ROLE_LEVEL_BANED = 9; // 禁言用户
    const USER_ROLE_LEVEL_ADS = 2; // 广告推广用户
    const USER_ROLE_BLACK = 4;//黑名单

    const REDIS_USER_LIKING_LIST = 'user_like_list:'; // 用户喜欢列表

    const REDIS_USER_UID = 'user_uid:'; // 用户缓存（根据uid查找）

    const REDIS_USER_UID_OTHER = 'other_user_uid:'; // 他人用户缓存

    const USER_DEFAULT_NICKNAME_PREFIX = '账号_';

    const INVITED_REWARD_TIMES = 172800; // 邀请奖励天数
    const USER_WATCH_COUNT_DEFAULT = 10; // 默认用户可观看次数

    const POSTS_PAID = "member_posts_paied:member_aff:%s"; //用户购买的帖子


    const VIP_LEVEL_NO = 0,
        VIP_LEVEL_MOON = 1,
        VIP_LEVEL_JIKA = 2,
        VIP_LEVEL_YEAR = 3,
        VIP_LEVEL_LONG = 4,
        VIP_LEVEL_BN = 5;
    const USER_VIP_TYPE = [
        self::VIP_LEVEL_NO   => '非VIP',
        self::VIP_LEVEL_MOON => '月卡',
        self::VIP_LEVEL_JIKA => '季卡',
        self::VIP_LEVEL_YEAR => '年卡',
        self::VIP_LEVEL_LONG => '永久',
        self::VIP_LEVEL_BN   => '半年',
    ];

    const LIVE_SUPER_GUEST = 0;
    const LIVE_SUPER_ORDINARY = 30;
    const LIVE_SUPER_ADMIN = 50;
    const LIVE_SUPER_SUPER = 60;
    const LIVE_SUPER = [
        self::LIVE_SUPER_GUEST    => '游客',
        self::LIVE_SUPER_ORDINARY => '普通',
        self::LIVE_SUPER_ADMIN    => '房间管理',
        self::LIVE_SUPER_SUPER    => '超管',
    ];

    const TYPE_ANDROID = 'android';
    const TYPE_IOS = 'ios';
    const TYPE_PWA = 'pwa';
    const TYPE = [
        self::TYPE_ANDROID => '安卓',
        self::TYPE_IOS     => '苹果',
        self::TYPE_PWA     => 'pwa'
    ];

    const CHANGE_COLUMN = [
        'uuid',
        'username',
        'password',
        'role_id',
        'role_type',
        'gender',
    ];

    // 数据表
    const AUTH_STATUS_NO = 0;
    const AUTH_STATUS_YES = 1;
    const AUTH_STATUS = [
        self::AUTH_STATUS_NO  => '未认证',
        self::AUTH_STATUS_YES => '已认证',
    ];
    protected $table = 'members';

    protected $primaryKey = 'uid';

    protected $hidden = ['session', 'password'];
    // 可填充字段
    protected $fillable = [
        'oauth_type',
        'oauth_id',
        'uuid',
        'username',
        'password',
        'is_reg',
        'role_id',
        'regip',
        'regdate',
        'lastip',
        'lastvisit',
        'expired_at',
        'aff',
        'invited_by',
        'invited_num',
        'app_version',
        'nickname',
        'thumb',
        'coins',
        'coins_total',
        'score',
        'score_total',
        'votes',
        'votes_total',
        'tui_coins',
        'total_tui_coins',
        'fans_count',
        'followed_count',
        'videos_count',
        'fabulous_count',
        'likes_count',
        'live_count',
        'sexType',
        'vip_level',
        'person_signnatrue',
        'build_id',
        'auth_status',
        'birthday',
        'live_supper',
        'is_live_super',
        'phone',
        'post_coins',
        'total_post_coins',
        'trace_id',
    ];
    public $timestamps = false;

    protected $appends = [
        'avatar_url',
        'expired_str',
        'is_vip',
        'is_attention',
    ];

    public static function virtualByForDelele()
    {
        $member = self::make();
        $member->nickname = "用户已注销";
        $member->is_null = true;
        return $member;
    }


    public function getLevelAnchorAttribute()
    {
        return 0; //return getLevel($this->attributes['votes_total']);
    }

    public function getLevelAttribute()
    {
        return 0;  //return getLevel($this->attributes['consumption']);
    }

    public function getLiveLogAttribute()
    {
        return [];  //UserLiveLogModel::getTopLog($uid);
    }

    public function getIsLiveAttribute()
    {
        return 0; // LiveModel::instance()->getRoomInfo($this->post['to_uid']) ? 1 : 0;
    }

    public function getAvatarUrlAttribute()
    {
        return url_avatar($this->attributes['thumb'] ?? '');
    }

    public function getAffCodeAttribute()
    {
        return generate_code($this->attributes['aff'] ?? 0);
    }

    public function getIsVipAttribute()
    {
        if (!isset($this->attributes['expired_at']) || $this->attributes['expired_at'] < TIMESTAMP) {
            return 0;
        }
        return 1;
    }

    public function getIsVVAttribute()
    {
        if (!isset($this->attributes['expired_at'])) {
            return false;
        }
        return $this->attributes['expired_at'] > time();
    }

    public function getVvLevelAttribute()
    {
        if (!isset($this->attributes['vip_level'])) {
            return 0;
        }
        return $this->attributes['vip_level'];
    }

    public function getExpiredStrAttribute()
    {
        $expired_at = $this->attributes['expired_at'] ?? 0;
        if ($expired_at && is_numeric($expired_at)) {
            return date('Y-m-d H:i:s', $expired_at);
        }
        return '';
    }

    public function getIsAttentionAttribute()
    {
        if (!isset($this->attributes['aff']) || empty($this->watchUser)) {
            return 0;
        }
        if (!isset($this->watchUser['aff'])) {
            return 0;
        }
        static $ids = null;
        if (null === $ids) {
            $key = UserAttentionModel::REDIS_USER_FOLLOWED_LIST . $this->watchUser['aff'];
            $ids = redis()->sMembers($key);
        }
        return in_array($this->attributes['aff'], $ids) ? 1 : 0;
    }

    public function getDoubleFollowedAttribute()
    {
        if (!isset($this->attributes['aff']) || empty($this->watchUser)) {
            return 0;
        }
        if (!isset($this->watchUser['aff'])) {
            return 0;
        }
        if (!$this->getIsAttentionAttribute()) {
            return 0;
        }
        static $ids = null;
        if (null === $ids) {
            $key = UserAttentionModel::REDIS_USER_FANS_LIST . $this->watchUser['aff'];
            $ids = redis()->sMembers($key);
        }
        return in_array($this->attributes['aff'], $ids) ? 1 : 0;
    }

    /**
     * @param $val
     * @return static
     * @author xiongba
     * @date 2020-02-27 20:35:26
     */
    public static function firstByName($val)
    {
        return self::where('username', $val)->first();
    }

    /**
     * 使用uid查找用户的uuid
     * @param $uid
     * @return string|null
     * @author xiongba
     * @date 2020-04-28 17:21:19
     */
    public static function getUuidByUid($uid)
    {
        return cached('member:live:uuid:')
            ->suffix($uid)
            ->expired(1800)
            ->fetch(function () use ($uid) {
                $member = MemberModel::find($uid);
                if (empty($member)) {
                    return false;
                }
                return $member->uuid;
            });
    }

    public function mvList()
    {
        return self::hasMany(MvModel::class, 'uid', 'uid');
    }



    // 追加一列 user_thumb 用户头像全路径
    // protected $appends = ['user_thumb'];

    /**
     * 用户对应的log members_log
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function session()
    {
        return $this->hasOne(MemberLogModel::class, 'uuid', 'uuid');
    }

    public static function getOfficial()
    {
        static $member = null;
        if ($member === null) {
            $member = \MemberModel::where('uid', setting('official.uid', 4888000))->first();
        }
        return $member;
    }


    public static function getDefaultValue()
    {
        return [
            "validate"          => 0,
            "share"             => 0,
            "fans_count"        => 0,
            "followed_count"    => 0,
            "videos_count"      => 0,
            "live_count"        => 0,
            "fabulous_count"    => 0,
            "likes_count"       => 0,
            "sexType"           => 0,
            "auth_status"       => 0,
            "exp"               => 0,
            "live_supper"       => 0,
            "consumption"       => 0,
            "level_anchor"      => 1,
            "coins_total"       => 0,
            "new_topic_reply"   => 0,
            "coins"             => (int)setting('register.coins', 0),
            "vip_level"         => (int)setting('register.vip_level', 0),
            "level"             => (int)setting('register.level', 1),
            "is_recommend"      => (int)setting('register.is_recommend', 0),
            "login_count"       => (int)setting('register.login_count', 1),
            "score"             => (int)setting('register.score', 0),
            "gender"            => (int)setting('register.gender', 1),
            "thumb"             => setting('register.thumb', "91_ads_20200111FeTEqY.png"),
            "role_type"         => setting('register.role_type', 'normal'),
            "person_signnatrue" => setting('register.person_signature', ""),
            "build_id"          => setting('register.build_id', ''),
            "chat_uid"          => "",
            "birthday"          => "",
            "votes"             => "0.00",
            "votes_total"       => "0.00",
            "is_live_super"     => self::LIVE_SUPER_ORDINARY,
            "phone"             => null,
            "lastip"            => USER_IP,
        ];
    }


    /**
     * 获取登录token
     * @return string
     */
    public function token(): string
    {
        $signKey = config('token.login', '');
        return md5($signKey . $this->uuid . $signKey);
    }

    public static function dynamicToken($uid = null)
    {
        if (empty($uid)) {
            return null;
        }
        $token = substr(md5(microtime(true) . '-' . $uid), 16);
        redis()->set('g-tok:' . $token, $uid, 7200);
        return $token;
    }

    /**
     * 获取设备hash
     * @return string
     * @author xiongba
     * @date 2020-03-14 17:33:31
     */
    public function getDeviceHash()
    {
        return self::hashByAry($this);
    }


    /**
     * @param $member
     * @return string
     * @author xiongba
     * @date 2020-03-15 15:22:35
     */
    public static function hashByAry($member)
    {
        return md5(($member['oauth_type'] ?? '') . ($member['oauth_id'] ?? ''));
    }

    /**
     * @param $member
     * @author xiongba
     * @date 2020-03-15 15:22:39
     */
    public static function clearFor($member)
    {
        if (empty($member)) {
            return;
        }
        $member = is_object($member) ? $member->getAttributes() : $member;
        $hash = self::hashByAry($member);
        $uuid = $member['uuid'] ?? '';
        redis()->del('user:' . $hash);
        if ($uuid != $hash) {
            redis()->del('user:' . $uuid);
        }
        \MemberModel::unbindDevice($member);
    }

    /**
     * 绑定uuid和设备关系
     * @param $member
     * @author xiongba
     * @date 2020-03-16 10:28:29
     */
    public static function bindUuidWithDevice($member)
    {
        if (empty($member)) {
            return;
        }
        if (isset($member['uuid']) && !empty($member['uuid'])) {
            redis()->set('user:uuid:' . $member['uuid'], MemberModel::hashByAry($member), 3600);
        }
    }

    /**
     * 使用指定的uuid获取设备
     * @param $uuid
     * @return bool|string
     * @author xiongba
     * @date 2020-03-16 10:28:43
     */
    public static function getDeviceByUuid($uuid)
    {
        return redis()->get('user:uuid:' . $uuid);
    }

    /**
     * 解除uuid和设备的关系
     * @param $member
     * @author xiongba
     * @date 2020-03-16 10:29:04
     */
    public static function unbindDevice($member)
    {
        if (empty($member)) {
            return;
        }
        redis()->del('user:uuid:' . ($member['uuid'] ?? ''));
    }


    public static function queryVideoCount($videoCount)
    {
        return self::where('videos_count', '>=', $videoCount);
    }


    public function formatDiamond()
    {
        $coins = $this->coins;
        if ($coins >= 10000) {
            $str = sprintf('%.2fw钻', $coins / 10000);
        } elseif ($coins >= 1000) {
            $str = sprintf('%.2fk钻', $coins / 1000);
        } else {
            $str = $coins . '钻';
        }
        if (strpos($str, '.00') !== false) {
            $str = str_replace('.00', '', $str);
        } elseif (strpos($str, '.') !== false && mb_strpos($str, '0钻') !== false) {
            $str = str_replace('0钻', '钻', $str);
        }

        return $str;
    }

    public function getMessageCount()
    {
        return MessageModel::getMessageCount($this->uuid);
    }

    public function maker()
    {
        return $this->hasOne(MemberMakerModel::class, 'uuid', 'uuid');
    }

    public function getTodayWatchCount($uid = null): int
    {
        $uid = $uid ?? $this->uid;
        if ($this->expired_at > time()) {
            return 0;
        }
        return redis()->sCard(\MvModel::REDIS_USER_TODAY_MV_LIST . $uid);
    }

    public function getTodayCanWatchCount($uid = null): int
    {
        if ($this->expired_at > time()) {
            return 999;
        }
        $canWatch = (int)setting("site.can_watch_count", config('site.can_watch_count', 10));
        $c = $this->getTodayWatchCount($uid);
        return $canWatch <= $c ? 0 : $c - $canWatch;
    }

    public function isBan(): bool
    {
        $role_id = $this->attributes['role_id'] ?? 8;
        return in_array($role_id, [
            self::USER_ROLE_BLACK,
            self::USER_ROLE_LEVEL_BANED,
        ]);
    }

    /**
     * 是否是付费vip
     * @return bool
     */
    public function isFeeVip(): bool
    {
        $expired_at = $this->attributes['expired_at'] ?? 0;
        $vip_level = $this->attributes['vip_level'] ?? 0;
        if ($expired_at < time()) {
            return false;
        }
        if (in_array($vip_level, [self::VIP_LEVEL_NO])) {
            return false;
        }
        return true;
    }

    public function isAuthStatus(): bool
    {
        $auth_status = $this->attributes['auth_status'] ?? 0;
        return $auth_status == self::AUTH_STATUS_YES;
    }

    /**
     * 活跃留存数据上报
     * @param MemberModel $memberModel
     * @return array|null
     */
    static function reportKeepData(MemberModel $memberModel)
    {
        if (!$memberModel->build_id) {
            return;
        }
        $extend = [];
        //尝试找到用户关联联盟渠道信息上报 方便定位关系
        /** @var \AgentsUserModel $agentUser */
        $agentUser = \AgentsUserModel::where(['channel' => $memberModel->build_id, 'aff' => $memberModel->invited_by])->first();
        if (is_null($agentUser)) {
            $agentUser = \AgentsUserModel::where(['channel' => $memberModel->build_id])->first();
        }
        if (!is_null($agentUser)) {
            $extend['agent_id'] = $agentUser->root_id;
        }
        return (new \service\AppCenterService())->keepData($memberModel->uid, $memberModel->build_id, $memberModel->invited_by, date('Y-m-d', $memberModel->regdate), date('Y-m-d'), $extend);
    }

    public static function findByUuid($uuid)
    {
        return self::where('uuid', $uuid)->first();
    }

    /**
     * @param string $aff
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|MemberModel|object
     * @author xiongba
     * @date 2020-06-13 20:56:44
     */
    public static function firstAff(string $aff):MemberModel
    {
        return self::where('aff', '=', $aff)->first();
    }

    //查找两层就好了，不搞递归
    public static function info($uid){
        return cached('channel:member:info:' . $uid)
            ->fetchJson(function () use ($uid){
                /** @var AgentsUserModel $channel */
                $channel = AgentsUserModel::where('aff')->first();
                if (!empty($channel)){
                    return $channel->username;
                }
                return '';
            });
    }
}