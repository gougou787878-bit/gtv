<?php

/**
 * class PostTopicCategoryModel
 *
 *
 * @property int $id
 * @property string $name 类型名称
 * @property int $sort 排序 越大越前
 * @property int $status 是否显示 0不显示 1显示
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 *
 *
 * @mixin \Eloquent
 */
class PostTopicCategoryModel extends EloquentModel
{
    protected $table = 'post_topic_category';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'created_at',
        'name',
        'sort',
        'status',
        'updated_at',
    ];

    protected $appends = ['api', 'params', 'status_str'];
    const POST_TOPIC_CATEGORY_KEY = 'post:topic:category:list';
    const STATUS_HIDE = 0;
    const STATUS_NORMAL = 1;
    const STATUS_TIPS = [
        self::STATUS_HIDE => '屏蔽',
        self::STATUS_NORMAL => '正常'
    ];

    const FIND_NO = 0;
    const FIND_YES = 1;
    const FIND_TIPS = [
        self::FIND_NO => '否',
        self::FIND_YES => '是'
    ];

    const CK_NAV_LIST = 'ck:nav:list';
    const GP_NAV_LIST = 'gp:nav:list';
    const GP_NAV_DETAIL = 'gp:nav:detail:%s';
    const GP_NAV_GROUP = 'gp:nav:detail:group';

    public function getApiAttribute(): string
    {
        return '/api/community/construct';
    }

    public function getParamsAttribute(): array
    {
        return ['id' => $this->attributes['id'] ?? 0];
    }

    public function getStatusStrAttribute(): string
    {
        return self::STATUS_TIPS[$this->attributes['status']] ?? '';
    }

    public static function listNavs()
    {
        return cached(self::CK_NAV_LIST)
            ->group(self::GP_NAV_LIST)
            ->fetchPhp(function () {
                return self::where('status', self::STATUS_NORMAL)
                    ->selectRaw('id,name,status')
                    ->orderByDesc('sort')
                    ->get();
            });
    }

    public static function findById($id)
    {
        return cached(sprintf(self::GP_NAV_DETAIL,$id))
            ->group(self::GP_NAV_GROUP)
            ->fetchPhp(function () use($id) {
                return self::where('status', self::STATUS_NORMAL)->where('id',$id)->first();
            },10);
    }

    public static function clearCache()
    {
        cached(self::POST_TOPIC_CATEGORY_KEY)->clearCached();
    }
}