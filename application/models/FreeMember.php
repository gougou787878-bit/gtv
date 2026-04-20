<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class FreeMemberModel
 * @property int $id null
 * @property int $uid 账号
 * @property int $created_at
 * @property int $expired_at
 * @package App\models;
 * @mixin \Eloquent
 */
class FreeMemberModel extends Model
{

    protected $table = "free_member";

    protected $primaryKey = 'id';

    protected $fillable = ['uid', 'created_at', 'expired_at'];

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * 是否是免费用户
     * @param $uid
     * @return bool
     */
    public static function isFreeMember($uid)
    {
        static $isFreeMember = null;
        if (!isset($isFreeMember)) {
            $s = 'user:freemv:' . $uid;
            $expired_at = redis()->get($s);
            if ($expired_at === false || $expired_at === null) {
                /** @var self $model */
                $model = self::where('uid', $uid)->first();
                if (is_null($model) || $model->expired_at < time()) {
                    $expired_at = 0;
                    $ttl = 86400;
                } else {
                    $expired_at = $model->expired_at;
                    $ttl = $model->expired_at - $model->created_at;
                }
                redis()->set($s, $expired_at, $ttl);
            }
            if ($expired_at < time()) {
                $isFreeMember = false;
            } else {
                $isFreeMember = true;
            }
        }
        return $isFreeMember;
    }

    public static function createInit($uid, $day)
    {
        /** @var self $model */
        $model = self::where('uid', $uid)->first();
        $ttl = max($day, 7) * 86400;
        if (empty($model)) {
            $model = self::create([
                'uid'        => $uid,
                'created_at' => time(),
                'expired_at' => time() + $ttl
            ]);
        } else {
            $model->created_at = time();
            $model->expired_at = max($model->expired_at , time()) + $ttl;
            $model->save();
        }
        redis()->set('user:freemv:' . $uid, $model->expired_at, $ttl);
        return true;
    }


}
