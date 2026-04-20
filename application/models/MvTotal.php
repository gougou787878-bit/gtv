<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class MvTotalModel
 *
 * @property int $id
 * @property int $vid 视频id
 * @property string $date_at 日期
 * @property int $view_num 观看量
 * @property int $like_num 点赞数
 * @property int $sale_num 销量
 *
 * @property MvModel $mv
 *
 * @author xiongba
 * @date 2020-03-16 17:04:41
 *
 * @mixin \Eloquent
 */
class MvTotalModel extends EloquentModel
{

    protected $table = "mv_total";

    protected $primaryKey = 'id';

    protected $fillable = ['vid', 'date_at', 'view_num', 'like_num', 'sale_num'];

    protected $guarded = 'id';


    public $timestamps = false;


    public function mv()
    {
        return self::hasOne(MvModel::class, 'id', 'vid');
    }

    public static function incrView(int $vid, int $view = 1, $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        //效率比updateOrCreate更高
        return self::_insertOrUpdate($vid , $date , 'view_num' , $view);
    }

    public static function incrBuy(int $vid, int $buy = 1, $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        //效率比updateOrCreate更高
        $itOk =  self::_insertOrUpdate($vid , $date , 'sale_num' , $buy);
        if ($itOk){
            cached('search:hot:sale')->clearCached();
        }
        return $itOk;
    }

    public static function incrLike(int $vid, int $like = 1, $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        return self::_insertOrUpdate($vid , $date , 'like_num' , $like);
    }

    private static function _insertOrUpdate($vid, $date, $field, $score)
    {
        if ($score >= 0) {
            $itOk = DB::update("insert into ks_mv_total (vid, date_at, $field) values ($vid,'$date',$score) on duplicate key update  $field=$field+$score");
        } else {
            $abs = abs($score);
            $itOk = DB::update("insert into ks_mv_total (vid, date_at, $field) values ($vid,'$date',0) on duplicate key update  $field=if($field > $abs, $field - $abs , 0)");
        }
        return $itOk;
    }


    public static function getViewScoreMv($start, $end = 0)
    {
        return self::getScoreMv('view_num', $start, $end);
    }

    public static function getLikeScoreMv($start, $end = 0)
    {
        return self::getScoreMv('like_num', $start, $end);
    }

    public static function getSaleScoreMv($start, $end = 0)
    {
        return self::getScoreMv('sale_num', $start, $end);
    }

    public static function getVideoId($sortField, $start, $end = 0)
    {
        if ($end <= 0) {
            list($start, $end) = [0, $start];
        }
        $dateArr = [date('Y-m-d'), \Carbon\Carbon::yesterday()->format('Y-m-d')];
        'test' == APP_ENVIRON && $dateArr = ['2021-01-18', '2020-11-25', '2020-11-23'];//测试环境
        return self::whereIn('date_at', $dateArr)
            ->orderByDesc('date_at')
            ->orderByDesc($sortField)
            ->orderByDesc('id')
            ->offset($start)
            ->limit($end)
            ->pluck('vid')
            ->toArray();
    }



    /**
     * @param $totalField
     * @param $start
     * @param int $end
     * @return \Illuminate\Support\Collection
     * @author xiongba
     * @date 2020-03-16 19:39:05
     */
    private static function getScoreMv($totalField, $start, $end = 0)
    {
        if ($end <= 0) {
            list($start, $end) = [0, $start];
        }
        return self::query()
            ->with([
                'mv' => function ($query) {
                    return $query
                        ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid')
                        ->with('user_topic');
                }
            ])
            ->whereIn('date_at', [date('Y-m-d'), \Carbon\Carbon::yesterday()->format('Y-m-d')])
            ->orderByDesc('date_at')
            ->orderByDesc($totalField)
            ->orderByDesc('id')
            ->offset($start)
            ->limit($end)
            ->get([$totalField, 'vid', 'date_at'])
            ->map(function ($item)use($totalField) {
                if (empty($item->mv)) {
                    return null;
                }
                $item->mv->score = $item->{$totalField};
                return $item->mv;
            })->filter();
    }

}

