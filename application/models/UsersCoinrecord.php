<?php

use tools\RedisService;

/**
 * class MemberCoinrecordModel
 * 用户金币消费日志
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property string $action 收支行为
 * @property int $game_action 游戏类型
 * @property int $game_banker 庄家ID
 * @property int $giftcount 数量
 * @property int $giftid 行为对应ID
 * @property int $live_updated_at 直播时间
 * @property int $mark 标识，1表示热门礼物，2表示守护礼物
 * @property int $showid 直播标识
 * @property int $totalcoin 总价
 * @property int $touid 对方ID
 * @property string $type 收支类型
 * @property int $addtime 添加时间
 *
 * @author xiongba
 * @date 2020-03-02 12:32:23
 *
 * @mixin \Eloquent
 */
class UsersCoinrecordModel extends EloquentModel
{
    protected $table = 'member_coinrecord';

    protected $primaryKey = 'id';


    protected $guarded = [];


    protected $appends = ['add_time_str'];

    public function getAddTimeStrAttribute($key)
    {
        return date('Y-m-d H:i:s', $this->attributes['addtime'] ?? 0);
    }


    public static function createForExpendBySys(
        $action,
        $uid,
        $totalCoin,
        $showId,
        $mark = null,
        $addTime = null
    ) {
        return self::createForExpend($action, $uid, 0, $totalCoin, 0, 0, $showId, $mark, $addTime);
    }

    public static function addTopicExpend($uid, $title, $totalCoin)
    {
        $insert = [
            "type"      => 'expend',
            "action"    => 'topic',
            "uid"       => $uid,
            "touid"     => 0,
            "giftid"    => 0,
            "giftcount" => 0,
            "totalcoin" => $totalCoin,
            "showid"    => 0,
            "mark"      => 0,
            "addtime"   => time(),
            "desc"      => "创建合集[{$title}]",
        ];
        return self::create($insert);
    }


    public static function addIncome($action, $uid, $toUid, $totalCoin, $giftid, $showId, $desc = '')
    {
        $add_log = [
            "type"      => 'income',
            "action"    => $action,
            "uid"       => $uid,
            "touid"     => $toUid ?? $uid,
            "giftid"    => $giftid,
            "giftcount" => 0,
            "totalcoin" => $totalCoin,
            "showid"    => $showId,
            "addtime"   => time(),
            'desc'      => $desc
        ];
        return \UsersCoinrecordModel::create($add_log);
    }

    /**
     * @param $uid
     * @param $mv
     * @param $totalCoin
     * @return \Illuminate\Database\Eloquent\Model|UsersCoinrecordModel
     */
    public static function addAvExpend($uid, $mv, $totalCoin)
    {
        $insert = [
            "type"      => 'expend',
            "action"    => 'AV',
            "uid"       => $uid,
            "touid"     => 0,
            "giftid"    => 0,
            "giftcount" => 0,
            "totalcoin" => $totalCoin,
            "showid"    => $mv->id,
            "mark"      => 0,
            "addtime"   => time(),
            "desc"      => "购买AV[{$mv->title}]",
        ];
        return self::create($insert);
    }

    public static function addMvExpend($uid, $mv, $totalCoin)
    {
        $insert = [
            "type"      => 'expend',
            "action"    => 'buymv',
            "uid"       => $uid,
            "touid"     => $mv['uid'],
            "giftid"    => 0,
            "giftcount" => 0,
            "totalcoin" => $totalCoin,
            "showid"    => $mv['id'],
            "mark"      => 0,
            "addtime"   => time(),
            "desc"      => "购买视频[{$mv['title']}]",
        ];
        return self::create($insert);
    }

    public static function createForExpend(
        $action,
        $uid,
        $toUid,
        $totalCoin,
        $giftId,
        $giftCount,
        $showId,
        $mark = null,
        $addTime = null,
        $desc = null
    ) {
        $insert = [
            "type"      => 'expend',
            "action"    => $action,
            "uid"       => $uid,
            "touid"     => $toUid,
            "giftid"    => $giftId,
            "giftcount" => $giftCount,
            "totalcoin" => $totalCoin,
            "showid"    => $showId,
            "mark"      => $mark,
            "addtime"   => $addTime ?? time(),
            "desc"      => $desc ? $desc : $action,
        ];

        foreach ($insert as $k => $v) {
            if ($v === null) {
                unset($insert[$k]);
            }
        }

        $model = self::create($insert);
        if ($model) {
            //redis()->del(self::getContribute($toUid, $uid, 'day'));
            //redis()->del(self::getContribute($toUid, $uid, 'week'));
            //redis()->del(self::getContribute($toUid, $uid, 'moon'));
        }
        return $model;
    }

    public function withMember()
    {
        return $this->hasOne(MemberModel::class, 'uid', 'uid');
    }

    public function withUser()
    {
        return $this->withMember();
    }

    public function withMv()
    {
        return $this->hasOne(MvModel::class, 'id', 'showid');
    }


    public function toUser()
    {
        return $this->hasOne(MemberModel::class, 'uid', 'touid');
    }


    static function getTopProfit($limit = 3, $date = null)
    {
        $time = strtotime("-24 hours");
        if(APP_ENVIRON == 'test'){
            $time = strtotime("-1 month");
        }
        $expired = 300;
        if ($date == 'week') {
            $time = strtotime("-7 days");
            $expired = 2000;
        } elseif ($date == 'month') {
            $time = strtotime("-1 month");
            $expired = 86400;
        }
        $key = "profit:{$limit}:{$date}";
        return cached($key)
            ->expired($expired)
            ->usingFuck()
            ->serializerJSON()
            ->fetch(function () use ($time, $limit) {
                return UsersCoinrecordModel::where(['type' => 'expend', 'action' => 'buymv'])
                    ->where('addtime', '>=', $time)
                    ->select(["touid", DB::raw('SUM(totalcoin) as total')])
                    ->groupBy('touid')
                    ->orderByDesc('total')
                    ->limit($limit)
                    ->get()->map(function ($item) {
                        return [
                            'uid'          => $item->touid,
                            'nickname'     => $item->touser->nickname,
                            'coins_total'  => $item->total,
                            'avatar_url'   => $item->touser->avatar_url,
                            'videos_count' => $item->touser->videos_count,
                        ];
                    })->filter()->toArray();
            });

    }


    static function getTodayProfit($touid)
    {
        $where = [
            ['touid', '=', $touid],
            ['action', '=', 'buymv'],
            ['type', '=', 'expend'],
            ['addtime', '>=', strtotime(date('Y-m-d 00:00:00', TIMESTAMP))],
        ];
        return cached("video:profit:{$touid}")->expired(600)->serializerPHP()->fetch(function () use ($where) {
            return UsersCoinrecordModel::where($where)->sum('totalcoin');
        });
    }
}
