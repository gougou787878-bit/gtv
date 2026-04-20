<?php


/**
 * class MvModel
 *
 * @property int $id
 * @property int $mv_id
 * @property string $content
 * @property string $uuid
 * @property int $status
 * @property int $created_at
 * @property int $type
 *
 * @property MvModel $mv
 *
 * @author xiongba
 *
 * @mixin \Eloquent
 */
class MvReportModel extends EloquentModel
{

    protected $table = "mv_report";

    protected $primaryKey = 'id';

    protected $fillable = [
        'mv_id',
        'content',
        'uuid',
        'status',
        'created_at',
        'type',
    ];

    protected $guarded = 'id';


    public $timestamps = false;

    const STATUS_INIT = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;
    const STATUS = [
        self::STATUS_INIT    => '待处理',
        self::STATUS_SUCCESS => '成功',
        self::STATUS_FAIL    => '失败',
    ];

    public function mv()
    {
        if ($this->type != 1) {
            return self::hasOne(MvModel::class, 'id', 'mv_id');
        }
        return null;
    }

    public static function createBy($mvId, $content, $uuid, $type = 0)
    {
        return self::create([
            'mv_id'      => $mvId,
            'content'    => $content,
            'uuid'       => $uuid,
            'status'     => self::STATUS_INIT,
            'created_at' => time(),
            'type'       => $type
        ]);
    }


}
