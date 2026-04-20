<?php


/**
 * class TabModel
 *
 * @property int $tab_id
 * @property string $tab_name 导航蓝标签组
 * @property string $tags_str 标签id
 * @property int $sort_num 排序
 * @property int $is_search 排序
 * @property int $is_tab 排序
 * @property int $is_category 排序
 * @property int $is_up
 *
 *
 * @author xiongba
 * @date 2019-12-16 13:02:49
 *
 * @mixin \Eloquent
 */
class TabModel extends EloquentModel
{

    const STATUS_YES = 1;
    const STATUS_NO = 0;
    const STATUS = [
        self::STATUS_NO  => '否',
        self::STATUS_YES => '是',
    ];


    protected $table = "tab";

    protected $primaryKey = 'tab_id';

    protected $fillable = [
        'tab_name',
        'tags_str',
        'sort_num',
        'status',
        'is_tab',
        'is_category',
        'is_search',
        'is_up',
    ];

    protected $guarded = 'tab_id';

    public $timestamps = false;


    protected $appends = ['tags_ary'];


    public function getTagsAryAttribute()
    {
        if (!isset($this->attributes['tags_str'])) {
            return [];
        }
        return explode(',', $this->attributes['tags_str']);
    }


    public static function queryBase($where = [])
    {

        $query = self::where('status', '=', self::STATUS_YES);
        if ($where) {
            $query->where($where);
        }
        return $query;
    }

    static function querySearch()
    {
        return self::queryBase(['is_search' => 1]);//搜索分类
    }

    static function queryUp()
    {
        return self::queryBase(['is_up' => 1]);//上传分类
    }

    static function queryCategory()
    {
        return self::queryBase(['is_category' => 1]);//首页展示分类
    }

    static function queryTab()
    {
        return self::queryBase(['is_tab' => 1]);//tab 展示
    }

    /**
     * 根据tab_id 获取tab 搜索标签信息
     * @param $tab_id
     * @return mixed
     */
    static function getMatchString($tab_id)
    {
        $key = 't:m' . $tab_id;
        return cached($key)->usingFuck(false)->expired(900)->fetch(function () use ($tab_id) {
            $tagStr = TabModel::where('tab_id', $tab_id)->value('tags_str');
            $tagStr = str_replace(',', ' ', $tagStr);
            return $tagStr;
        });

    }


}
