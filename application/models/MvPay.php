<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class MvPayModel
 *
 * @property int $id
 * @property int $mv_id 视频id
 * @property int $uid 用户id
 * @property int $mv_uid 作者的用户id
 * @property int $date_at  日期 20201010 2020年10月10日
 * @property string $type 类型
 * @property int $coins 金币
 * @property int $created_at 金币
 * @property int $is_refund 是否已退款
 *
 * @property MvModel $mv
 * @property MemberModel $creator
 * @property MemberModel $user
 *
 * @author xiongba
 * @date 2020-01-08 12:36:38
 *
 * @mixin \Eloquent
 */
class MvPayModel extends Model
{

    protected $table = "mv_pay";

    protected $primaryKey = 'id';

    protected $fillable = ['mv_id', 'uid', 'mv_uid', 'type', 'coins', 'created_at','is_refund'];

    protected $guarded = 'id';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = null;

    public function mv()
    {
        return self::hasOne(MvModel::class, 'id', 'mv_id');
    }

    public function user(){
        return self::hasOne(MemberModel::class , 'uid' , 'uid');
    }

    public function creator(){
        return self::hasOne(MemberModel::class , 'uid' , 'mv_uid');
    }


    const TYPE_PAY = 1;
    const TYPE_REWARD = 2;
    const TYPE_TICKET = 3;
    const TYPE = [
        self::TYPE_PAY    => '购买',
        self::TYPE_REWARD => '打赏',
        self::TYPE_TICKET => '观影券',
    ];


    const IS_REFUND_NO = 0;
    const IS_REFUND_YES = 1;
    const IS_REFUND = [
        self::IS_REFUND_NO  => '否',
        self::IS_REFUND_YES => '是',
    ];


    public static function createLog($uid, $mv_uid, $mvId, $type, $coins)
    {
        $data = [
            'uid'     => $uid,
            'mv_uid'  => $mv_uid,
            'type'    => $type,
            'coins'   => $coins,
            'date_at' => date('Ymd'),
            'mv_id'   => $mvId
        ];
        return self::create($data);
    }

    public static function createBuyLog($uid, $mv_uid, $mvId, $coins)
    {
        $model = self::createLog($uid, $mv_uid, $mvId, self::TYPE_PAY, $coins);
        if(!is_null($model)){
            self::addVidArr($uid,$mv_uid);
        }
        return $model;
    }
    public static function createTicketLog($uid, $mv_uid, $mvId, $coins=0)
    {
        return self::createLog($uid, $mv_uid, $mvId, self::TYPE_TICKET, $coins);
    }
    public static function createRewardLog($uid, $mv_uid, $mvId, $coins)
    {
        return self::createLog($uid, $mv_uid, $mvId, self::TYPE_REWARD, $coins);
    }

    static function checkPay($uid,$av_id)
    {
        redis()->sIsMember("mv_pay:list:" . $uid,$av_id);
    }

    public static function getVidArrByUser($uid)
    {
        return redis()->sMembers("mv_pay:list:" . $uid);
    }

    public static function addVidArr($uid, $vidArr)
    {
        $vidArr = (array)$vidArr;
        return redis()->sAddArray("mv_pay:list:" . $uid, $vidArr);
    }


    /**
     * 有没有支付
     * @param int $uid
     * @param int $mvId
     * @return bool
     * @author xiongba
     * @date 2020-01-08 12:18:36
     */
    public static function hasPay($uid, $mvId)
    {
        static $array = null;
        if ($array === null) {
            $ary = self::getVid($uid);
            $array = array_flip($ary);
        }
        return isset($array[$mvId]);
    }

    /**
     * 有没有打赏
     * @param $uid
     * @param $mvId
     * @return bool
     * @author xiongba
     */
    public static function hasReward($uid, $mvId)
    {
        $data = [
            'uid'   => $uid,
            'mv_id' => $mvId,
            'type'  => self::TYPE_REWARD,
        ];
        return self::where($data)->count('id') > 0;
    }

    /**
     * 获取用户购买视频的次数
     * @param $uid
     * @return int
     * @author xiongba
     */
    public static function buyCount($uid)
    {
        return self::query()->where(['uid' => $uid, 'type' => self::TYPE_PAY,])->count('id');
    }


    /**
     * 获取视频id
     * @param $uid
     * @return array
     * @author xiongba
     */
    public static function getVid($uid)
    {
        //强制重主库读，避免从库同步数据延迟是，用户被迫重复购买
        return self::getBought($uid)->useWritePdo()->pluck('mv_id')->toArray();
    }

    /**
     * 获取视频id
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Builder
     * @author xiongba
     */
    public static function getBought($uid)
    {
        return self::where('uid', $uid);
    }

    /**
     *  根据视频id 获取购买次数  5分钟缓存 | 无 缓存60s 后台有使用
     *
     * @param $mv_id
     * @return int|mixed
     */
    static function getBuyMvNum($mv_id)
    {
        if (!$mv_id) {
            return 0;
        }
        $key = 'buy:' . $mv_id;
        return cached($key)
            ->serializerPHP()
            ->expired(300)
            ->fetch(function ($cache) use ($mv_id) {
                $num = self::where('mv_id', '=', $mv_id)->count('id');
                ($num == 0) && $cache->expired(60);
                return $num;
            });
    }


}
