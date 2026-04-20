<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class RankModel
 *
 * @property int $id 
 * @property int $type_id 编号
 * @property int $type 0 av  1 tv 剧情
 * @property int $like 喜欢
 * @property int $play 播放
 * @property int $pay 支付
 * @property int $comment 评论
 * @property string $day 日期
 * @property MvModel $mv 视频
 * @property UserTopicModel $topic 剧集
 *
 * @author xiongba
 * @date 2021-08-17 20:43:48
 *
 * @mixin \Eloquent
 */
class RankModel extends Model
{

    protected $table = "rank";

    protected $primaryKey = 'id';

    protected $fillable = ['type_id', 'type', 'like', 'play', 'pay', 'comment', 'day'];

    protected $guarded = 'id';

    public $timestamps = false;

    const TYPE_MV = 0;//av  对应 type_id 为视频编号
    const TYPE_TOPIC = 1;//剧情 对应 type_id 为剧集编号
    const TYPE_USER = 2;//用户 对应 type_id 为用户编号

    const TYPE = [
        self::TYPE_MV=>'视频',
        self::TYPE_TOPIC=>'剧集',
    ];

    //like|play|pay|comment

    const FIELD_TYPE_LIKE = 'like';
    const FIELD_TYPE_PLAY = 'play';
    const FIELD_TYPE_PAY = 'pay';
    const FIELD_TYPE_COMMENT = 'comment';


    /**
     * 视频
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mv()
    {
        return self::hasOne(MvModel::class, 'id', 'type_id');
    }

    /**
     * 剧集
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function topic()
    {
        return self::hasOne(UserTopicModel::class, 'id', 'type_id');
    }

    /**
     * @param $type
     * @param $type_id
     * @param $field
     * @param int $increase
     * @param null $day
     * @return bool
     */
    public static function addRank($type,$type_id, $field, $increase = 1, $day = null)
    {
        $increase = (int)$increase;
        if (0 == $increase) {
            return false;
        }
        ($day == null) && $day = date('Y-m-d');
        $where = ['type_id' => $type_id, 'day' => $day,'type'=>$type];
        $data = [$field => DB::raw("`{$field}`+{$increase}")];
        return (bool)self::updateOrCreate($where, $data);
    }


    static function getHomeTop($type, $field, $limit = 3, $date = null)
    {
        $cacheKey = "rank:{$type}:{$field}:{$limit}:{$date}";
        //晚上11点到凌晨1点高峰期走固定列表,90%的概率
        if (in_array((int)date('H'),[22,23,0,1])){
            $expired = $date != 'day' ? 3600 : 1800;
        }else{
            $expired = $date != 'day' ? 1200 : 360;
        }
        return cached($cacheKey)
            ->serializerJSON()
            ->usingFuck(false)
            ->expired($expired)
            ->fetch(function () use ($type, $field, $limit, $cacheKey, $date) {
                $parse = date('Y-m-d');//日
                if ($date == 'week') {//周
                    $parse = date('Y-m-d', strtotime("-7 days"));
                } elseif ($date == 'month') {//月
                    $parse = date('Y-m-d', strtotime("-60 days"));
                }
                APP_ENVIRON == 'product' && $w[] = ['day', '>=', $parse];
                $w[] = ['type', '=', $type];
                $data = self::selectRaw("`type_id`,sum(`$field`) as `rank`")
                    ->with(['mv' => function ($query) {
                        return $query->with(['user' => function ($query) {
                            return $query->selectRaw('uid,thumb,nickname,expired_at,vip_level,auth_status,person_signnatrue,aff');
                        }]);
                    }])
                    ->where($w)
                    ->groupBy('type_id')
                    ->orderByDesc('rank')
                    ->limit($limit)
                    ->get()
                    ->map(function (RankModel $item) use ($type) {
                        if ($type == self::TYPE_MV) {
                            return [
                                'id'              => $item->type_id,
                                'cover_thumb_url' => $item->mv->cover_thumb_url,
                                'title'           => $item->mv->title,
                                'rating'          => $item->mv->rating,
                                'like'            => $item->mv->like,
                                'user'            => $item->mv->user,
                            ];
                        } elseif ($type == self::TYPE_TOPIC) {
                            return $item->topic;
                            return [
                                'id'         => $item->type_id,
                                'image_url'  => $item->topic->image_url,
                                'title'      => $item->topic->title,
                                'play_count' => $item->topic->play_count,
                                'like_count' => $item->topic->like_count,
                                'number'     => $item->topic->number,
                                'type'       => $item->topic->type,
                                'user'       => $item->topic->user,
                            ];
                        }
                        return null;
                    })->toArray();
                CacheKeysModel::createOrEdit($cacheKey, '排行榜');
                return $data;
            });
    }



}
