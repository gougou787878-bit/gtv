<?php

/**
 * class NagConfModel
 *
 * @property string $api 结构API
 * @property string $icon 图标
 * @property int $id
 * @property int $mid_style 中部样式
 * @property int $nag_id 导航ID
 * @property int $show_num 数量
 * @property int $sort 排序
 * @property int $status 状态 1:正常   0：禁用
 * @property string $title 文案
 * @property int $type 配置类型
 * @property int $up_catid Up主分类ID
 *
 * @date 2024-10-15 15:31:34
 *
 * @mixin \Eloquent
 */
class NagConfModel extends EloquentModel
{

    protected $table = "nag_conf";

    protected $primaryKey = 'id';

    protected $fillable = [
        'api',
        'icon',
        'mid_style',
        'nag_id',
        'show_num',
        'sort',
        'status',
        'title',
        'type',
        'up_catid'
    ];

    protected $guarded = 'id';

    public $timestamps = false;

    const RECOMMEND_RANK = 1;
    const RECOMMEND_LIVE = 2;
    const RECOMMEND_AI_FACE = 3;
    const RECOMMEND_ZY = 4;
    const RECOMMEND_HOT_CATEGORY = 5;

    const STATUS_YES = 1;
    const STATUS_NO = 0;
    const STATUS = [
        self::STATUS_NO  => '否',
        self::STATUS_YES => '是',
    ];


    const CONF_TIPS = [
        self::RECOMMEND_RANK            => "排行榜类型",
        self::RECOMMEND_LIVE            => "直播",
        self::RECOMMEND_AI_FACE         => "AI",
        self::RECOMMEND_ZY              => "资源",
        self::RECOMMEND_HOT_CATEGORY    => "热门分类",
    ];

    const NAG_CONF_LIST = "nag:conf:%s:%s:%s";
    const NAG_CONF_LIST_ONE = "nag:conf:%s:%s";

    public function navigation()
    {
        return $this->hasOne(NavigationModel::class, 'id', 'nag_id');
    }

    public function getIconAttribute()
    {
        if (MODULE_NAME == 'admin'){
            return url_cover($this->attributes['icon']);
        }
        return url_cover($this->attributes['icon']);
    }

    public function setIconAttribute($value)
    {
        if (strpos($value, '://') !== false){
            $value = parse_url($value,PHP_URL_PATH);
        }
        $this->attributes['icon'] = $value;
    }

    public static function getConf($nag_id,$mid_style,$limit = 10){
        $rKey = sprintf(self::NAG_CONF_LIST,$nag_id,$mid_style,$limit);
        return cached($rKey)
            ->group('nag:conf:list')
            ->chinese('导航-配置列表')
            ->fetchPhp(function () use ($nag_id,$mid_style,$limit){
                return self::where('nag_id',$nag_id)
                    ->where('mid_style',$mid_style)
                    ->where('status',self::STATUS_YES)
                    ->orderByDesc('sort')
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get();
            });
    }

    public static function getConfFirst($nag_id,$mid_style){
        $rKey = sprintf(self::NAG_CONF_LIST_ONE,$nag_id,$mid_style);
        return cached($rKey)
            ->group('nag:conf:middle')
            ->chinese('导航-中部配置')
            ->fetchPhp(function () use ($nag_id,$mid_style){
                return self::where('nag_id',$nag_id)
                    ->where('mid_style',$mid_style)
                    ->where('status',self::STATUS_YES)
                    ->orderByDesc('sort')
                    ->orderByDesc('id')
                    ->first();
            });
    }
}
