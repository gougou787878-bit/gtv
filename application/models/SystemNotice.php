<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class SystemNoticeModel
 *
 * @property int $id 
 * @property string $type 类型
 * @property int $status 0 默认 1 标记
 * @property string $uuid 
 * @property string $description 信息
 * @property string $date_at 
 *
 * @author xiongba
 * @date 2021-07-19 22:12:50
 *
 * @mixin \Eloquent
 */
class SystemNoticeModel extends Model
{

    protected $table = "system_notice";

    protected $primaryKey = 'id';

    protected $fillable = ['type', 'status', 'uuid', 'description', 'date_at'];

    protected $guarded = 'id';


    public $timestamps = false;


    const TYPE_VIDEO = 'video';
    const TYPE_ORDER = 'order';
    const TYPE_DRAW = 'withdraw';
    const TYPE_GAME = 'game';

    const TYPE = [
        self::TYPE_VIDEO => '视频',
        self::TYPE_ORDER => '订单',
        self::TYPE_DRAW  => '提现',
        self::TYPE_GAME  => '游戏单',
    ];

    const STAT_OK = 1;
    const STAT_NO = 0;//默认
    const STAT = [
        self::STAT_NO => '默认',
        self::STAT_OK => '标记',
    ];

    /**
     * @param $type
     * @param $uuid
     * @param $desc
     * @return bool
     */
    static function addNotice($type, $uuid, $desc)
    {
        return self::insert([
            'type'        => $type,
            'uuid'        => $uuid,
            'description' => $desc,
            'status'      => self::STAT_NO,
            'date_at'     => date('Y-m-d H:i:s'),
        ]);
    }





}
