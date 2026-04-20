<?php

/**
 * class FeedbackRewardModel
 *
 * @property int $id
 * @property int $aff 用户aff
 * @property int $type 反馈类型
 * @property string $content 内容
 * @property string $images 多图片
 * @property string $replay 回复内容
 * @property int $status 状态
 * @property string $created_at
 * @property string $updated_at
 *
 *
 * @date 2024-08-30 20:18:12
 *
 * @mixin \Eloquent
 */
class FeedbackRewardModel extends EloquentModel
{
    protected $table = "feedback_reward";
    protected $primaryKey = 'id';
    protected $fillable = [
        'aff',
        'type',
        'content',
        'images',
        'replay',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $guarded = 'id';
    public $timestamps = true;

    const STATUS_NO = 0;
    const STATUS_YES = 1;
    const STATUS_END = 2;
    const STATUS_ALL = 10;
    const STATUS_TIPS = [
        self::STATUS_NO => '待回复',
        self::STATUS_YES => '已回复',
        self::STATUS_END => '完结',
    ];

    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_5 = 5;
    const TYPE_6 = 6;
    const TYPE_7 = 7;

    const TYPE_TIPS = [
        self::TYPE_1 => '账号问题',
        self::TYPE_2 => '没找到资源',
        self::TYPE_3 => 'app不好用',
        self::TYPE_4 => '视频无法播放',
        self::TYPE_5 => '视频播放卡顿',
        self::TYPE_6 => 'bug反馈',
        self::TYPE_7 => '其他',
    ];

    public static function typeList()
    {
        return collect(self::TYPE_TIPS)->map(function ($value, $key) {
            return [
                'id' => $key,
                'name' => $value,
            ];
        })->values();
    }
}
