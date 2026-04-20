<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class MemberMakerStatModel
 *
 * @property int $id 
 * @property string $uuid 
 * @property string $tui_total 当月推广业绩元
 * @property int $mv_coins 视频收益统计
 * @property int $mv_number 视频数量
 * @property int $date_month 月度日期
 * @property int $level_num 创作者等级
 * @property int $before_level_num 保级
 *
 * @mixin \Eloquent
 */
class MemberMakerStatModel extends Model
{

    protected $table = "member_maker_stat";

    protected $primaryKey = 'id';

    protected $fillable = ['uuid', 'tui_total', 'mv_coins', 'mv_number', 'date_month', 'level_num', 'before_level_num'];

    protected $guarded = 'id';

    public $timestamps = false;

    const FIELD_TUI = 'tui_total';//月推广
    const FIELD_MV_PORFIT = 'mv_coins';//月视频收益
    const FIELD_MV_NUMBER = 'mv_number';//月视频数量 按通过回调计入统计


    /**
     * @param $uuid
     * @param $field
     * @param int $increase
     * @param null $month
     * @return bool
     */
    public static function addStat($uuid, $field, $increase = 1,$month = null)
    {
        ($month == null) && $month = date('Ym');
        $where = ['uuid' => $uuid, 'date_month' => $month];
        $data = [$field => DB::raw("`{$field}`+{$increase}")];
        return (bool)self::updateOrCreate($where, $data);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOnes
     */
    public function maker()
    {
        return $this->hasOne(MemberMakerModel::class, 'uuid', 'uuid');
    }

}
