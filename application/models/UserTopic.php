<?php


use Illuminate\Database\Eloquent\Model;
use service\MvNewService;

/**
 * class UserTopicModel
 *
 * @property int $id
 * @property int $uid 用户uid
 * @property int $is_top 是否置顶
 * @property int $is_hide 上下架
 * @property string $title 剧情标题
 * @property string $desp 剧情介绍
 * @property string $image 剧情图片
 * @property int $video_count 视频数量
 * @property int $like_count 点赞数量
 * @property int $play_count 播放数量
 * @property string $mv_id_str 视频id都号分隔
 * @property int $status 状态 0 待审 1 通过
 * @property string $year 年份
 * @property string $area 地区
 * @property string $type 类型
 * @property string $tags 标签
 * @property string $deny_str 剧集未通过审核的原因
 * @property string $date_at 更新时间
 * @property int[] $mv_id_ary
 * @property int $number 最近更新到多少节
 *
 * @property MemberModel $user
 * @author xiongba
 * @date 2021-08-17 12:03:30
 *
 * @mixin \Eloquent
 */
class UserTopicModel extends EloquentModel
{

    protected $table = "user_topic";

    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'is_top',
        'is_hide',
        'title',
        'desp',
        'image',
        'video_count',
        'like_count',
        'play_count',
        'mv_id_str',
        'status',
        'year',
        'area',
        'type',
        'tags',
        'date_at',
        'deny_str'
    ];

    protected $guarded = 'id';

    public $timestamps = true;
    const CREATED_AT = null;
    const UPDATED_AT = 'date_at';


    const STAT_WAIT = 0;
    const STAT_PASS = 1;
    const STAT_REJECT = 2;

    const STAT = [
        self::STAT_WAIT   => '等待审核',
        self::STAT_PASS   => '审核通过',
        self::STAT_REJECT => '审核失败',
    ];
    const IS_TOP_YES = 1;
    const IS_TOP_NO = 0;

    const IS_TOP = [
        self::IS_TOP_YES => '是',
        self::IS_TOP_NO  => '否',
    ];

    const IS_HIDE_YES = 1;
    const IS_HIDE_NO = 0;
    const IS_HIDE = [
        self::IS_HIDE_YES => '隐藏',
        self::IS_HIDE_NO  => '显示',
    ];

    /**
     * 连续剧国别
     */
    const AREA_COUNTRY = [
        0  => '美国',
        1  => '英国',
        2  => '大陆',
        3  => '中国台湾',
        4  => '日本',
        5  => '泰国',
        6  => '意大利',
        7  => '越南',
        8  => '泰国',
        9  => '瑞士',
        10 => '韩国',
        11 => '中国香港',
        12 => '巴西',
        13 => '印度尼西亚',
        14 => '芬兰',
        15 => '澳大利亚',
        16 => '其他',
    ];

    const TYPE_LIST = [
        ['name' => '青春校园', 'cover' => '/new/ads/20210914/2021091410323343628.png'],
        ['name' => '浪漫爱情', 'cover' => '/new/ads/20210914/2021091410321323444.png'],
        ['name' => '都市职场', 'cover' => '/new/ads/20210914/2021091410311989420.png'],
        ['name' => '经典剧情', 'cover' => '/new/ads/20210914/2021091410320184464.png'],
        ['name' => '偶像鲜肉', 'cover' => '/new/ads/20210914/2021091410322312501.png'],
        ['name' => '情感喜剧', 'cover' => '/new/ads/20210914/2021091410324028166.png'],
        ['name' => '家庭故事', 'cover' => '/new/ads/20210914/2021091410314865902.png'],
        ['name' => '幻想古装', 'cover' => '/new/ads/20210914/2021091410313967720.png'],
    ];

    static function getTypeInfo($type){
        return collect(self::TYPE_LIST)->where('name',trim($type))->first();
    }

    const YEAR_LIST = ['2021', '2020', '2019', '2018', '2017', '2016', '2015', '更早'];//更早格式化 存为 0 表示


    protected $hidden = ['mv_id_str'];
    protected $appends = ['image_url', 'mv_id_ary', 'is_like', 'number',
                          'is_hide_str', 'tags_ary', 'tip',
                          'status_str'

    ];

    public function getImageUrlAttribute(): string
    {
        $xCoverUrl = $this->attributes['image'] ?? null;
        return $xCoverUrl?url_cover($xCoverUrl):'';
    }

    public function getTagsAryAttribute(): array
    {
        $s = $this->attributes['tags'] ?? '';
        return $s ? explode(',', $s) : [];
    }

    public function getIsHideStrAttribute(): string
    {
        $x = $this->attributes['is_hide'] ?? -1;
        return self::IS_HIDE[$x] ?? '未知';
    }


    public function getStatusStrAttribute(): string
    {
        $x = $this->attributes['status'] ?? -1;
        return self::STAT[$x] ?? '未知';
    }

    public function getTipAttribute(): string
    {
        $idAry = $this->getMvIdAryAttribute();
        return $idAry ? sprintf("共%d集", count($idAry)) : '待上传';
    }

    public function getMvIdAryAttribute(): array
    {
        $str = $this->attributes['mv_id_str'] ?? null;
        if (empty($str)) {
            return [];
        }
        return collect(explode(',', trim($str,',')))->unique()->map(function ($v) {
            return intval($v);
        })->filter()->toArray();
    }

    public function getNumberAttribute(): int
    {
        $str = $this->attributes['mv_id_str'] ?? null;
        if (empty($str)) {
            return 0;
        }
        return collect(explode(',', trim($str,',')))->unique()->count();
    }

    public function getIsLikeAttribute(): int
    {
        $is_like = $this->attributes['is_like'] ?? null;
        if ($is_like !== null) {
            return intval($is_like);
        }
        $id = $this->attributes['id'] ?? null;
        static $likeTopicAry = null;
        if (empty($id)) {
            return 0;
        }
        if (empty($this->watchUser) || empty($this->watchUser['uid'])) {
            return 0;
        }
        $uid = $this->watchUser['uid'];
        if ($likeTopicAry === null) {
            $likeTopicAry = UserTopicLikeModel::where('uid', $uid)->pluck('topic_id')->toArray();
        }
        return in_array($id, $likeTopicAry) ? 1 : 0;
    }

    public static function queryBase(...$args)
    {
        $args = $args ?: [[]];
        return self::where('status', self::STAT_PASS)->where('video_count', '>',0)->where(...$args);
    }

    public function user()
    {
        return $this->hasOne(MemberModel::class, 'uid', 'uid');
    }


    public static function queryUser()
    {
        return self::queryBase()->with('user:uid,nickname,thumb,aff,expired_at,vip_level,uuid,sexType');
    }

    static function createByData($uid, $data)
    {
        return self::create(array_merge($data, [
            'status'     => self::STAT_WAIT,
            'like_count' => 0,
            'is_top'     => self::IS_TOP_NO,
            'is_hide'    => self::IS_HIDE_NO,
            'mv_id_str'  => '',
            'uid'        => $uid,
        ]));
    }

    /**
     * 模拟电视剧的集数
     * @return array|\Illuminate\Support\Collection
     */
    public function getItems()
    {
        $idStr = $this->attributes['mv_id_str'];
        if (empty($idStr)) {
            return [];
        }
        $idAry = collect(explode(',', $idStr))->flip()->keys()->filter();
        $ret = MvModel::whereIn('id', $idAry)->select('id', 'title', 'coins')->get()
            ->keyBy('id')
            ->map(function (MvModel $item) {
                $item->setAppends([]);
                return $item;
            });
        $items = [];
        $num = 0;
       /* $myVidAry = $this->watchUser
            ? MvPayModel::where('uid', $this->watchUser['uid'])->pluck('mv_id')->toArray()
            : [];*/
        foreach ($idAry as $id) { //调整顺序
            if (isset($ret[$id])) {
                /** @var MvModel $item */
                $item = $ret[$id];
                $tmp = $item->toArray();
                /*if ($item->coins <= 0 || in_array($item->id, $myVidAry)) {
                    $tmp['is_pay'] = 1;
                } else {
                    $tmp['is_pay'] = 0;
                }*/
                $num++;
                $tmp['std_name'] = sprintf("第%d集", $num);
                $tmp['show_name'] = sprintf("%s 第%d集", $item->title, $num);
                $items[] = $tmp;
            }
        }
        return $items;
    }

    /**
     * 模拟电视剧的集数
     * @return array|\Illuminate\Support\Collection
     */
    public function getMvList()
    {
        $idStr = $this->attributes['mv_id_str'];
        if (empty($idStr)) {
            return [];
        }
        $idAry = collect(explode(',', $idStr))->flip()->keys()->filter();
        $ret = MvModel::whereIn('id', $idAry)->with('user')->get()
            ->keyBy('id')
            ->map(function (MvModel $item) {
                return $item;
            });
        $items = [];
        foreach ($idAry as $id) { //调整顺序
            if (isset($ret[$id])) {
                $items[] = $ret[$id];
            }
        }
        $data = (new MvNewService())->v2format($items);
        return $data;
    }


}
