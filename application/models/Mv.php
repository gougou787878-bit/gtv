<?php

use Illuminate\Database\Eloquent\Builder;

/**
 * class MvModel
 *
 * @property string $actors 演员
 * @property string $category 分类
 * @property int $coins 定价
 * @property int $vip_coins 会员购买价格，-1表示没有设置会员价格
 * @property string $gif_thumb 视频动图
 * @property int $gif_height 视频动图宽
 * @property int $gif_width 视频动图高
 * @property int $comment 评论数
 * @property string $cover_thumb 封面小图
 * @property int $created_at 创建时间
 * @property string $directors 导演
 * @property int $duration 时长，秒
 * @property int $id
 * @property int $is_free 是否限免 0 收费 1 限免
 * @property int $is_hide 0显示1隐藏
 * @property int $like 喜欢点击数
 * @property string $m3u8 影片资源1
 * @property string $full_m3u8 影片资源1
 * @property int $music_id 音乐id
 * @property int $onshelf_tm 影片上映时间
 * @property int $rating 总历史点击数
 * @property int $refresh_at 刷新时间
 * @property int $status 0未审核1审核通过
 * @property array|string $tags 影片标签
 * @property int $thumb_duration 精彩时长：秒
 * @property int $thumb_height 封面高
 * @property int $thumb_start_time 精彩片段开始时间
 * @property int $thumb_width 封面宽
 * @property string $title 影片标题
 * @property int $uid 用户UUID
 * @property string $v_ext 视频格式类型
 * @property string $via 来源
 * @property int $is_recommend 来源
 * @property int $is_feature 是否是精选
 * @property string $y_cover
 * @property string $y_cover_url
 * @property int $is_top
 * @property int $count_pay
 * @property int $topic_id 合集id
 * @property int $construct_id 结构ID
 *
 * @property string $play_url
 * @property int is_pay
 *
 * @property MemberModel $user
 * @property UserTopicModel $user_topic
 *
 * @author xiongba
 * @date 2020-03-03 18:25:48
 *
 * @mixin \Eloquent
 */
class MvModel extends EloquentModel
{

    const COIN_DEFAULT = 88;//91撸av 金币视频同步后的默认价格 如果那边设置为0的话

    const REDIS_USER_TODAY_MV_LIST = 'user_today_list:'; // 用户当天看过的视频
    const REDIS_WATCH_COUNT = 'mv_watch_count'; // 视频总观看数
    const REDIS_USER_VIDEOS_ITEM = 'user_video_itemss:'; // 用户的视频列表
    const REDIS_USER_LIKE_VIDEOS_ITEM = 'user_like_video_item:'; // 用户点赞的视频列表

    const REDIS_USER_LIKE_TODAY_COUNT = 'stat_like_number'; // 点赞排行

    const STAT_UPLOAD_NUMBER = 'stat_upload_number'; // 用户每天上传视频总数

    const RECOMMEND_FEE_KEY = 'index:mv:recommend:fee';
    const RECOMMEND_FREE_KEY = 'index:mv:recommend:free';

    const REDIS_MV_DETAIL_KEY = 'mv:detail:key:%d';//视频详情

    //推荐
    const RK_RECOMMEND_CONSTRUCT = 'rk:recommend:construct:%s:%d';
    const RK_RECOMMEND_NAVIGATION = 'rk:recommend:navigation:%s:%d';
    const RK_RECOMMEND_SET = 'rk:recommend:%s:%s';

    //正在看
    const RK_SEE_CONSTRUCT = 'rk:see:construct:%d';
    const RK_SEE_NAVIGATION = 'rk:see:navigation:%d';

    const REDIS_NAG_TAB_MV_KEY = 'redis:nag:tab:mv:key:%s:%s:%s:%s';// 分类key
    const REDIS_NAG_TAB_MV_GROUP = 'redis:nag:tab:mv:group';//分类group
    const REDIS_NAG_TAB_MV_CN = '导航-视频列表';

    const CK_TAG_LIST_MV = 'redis:tag:list:mv:key:%s:%s:%s:%s:%s';
    const GP_TAG_LIST_MV_GP = 'redis:tag:list:mv:group';
    const CN_TAG_LIST_MV_CN = '标签视频列表';

    const REDIS_FIND_LIST_MV_KEY = 'redis:find:list:mv:key:%s:%s:%s:%s:%s';//发现
    const REDIS_FIND_LIST_MV_GP = 'redis:find:list:mv:group';
    const REDIS_FIND_LIST_MV_CN = '发现-金币/VIP列表';

    const STAT_UNREVIEWED = 0;
    const STAT_CALLBACK_DONE = 1;
    const STAT_REFUSE = 2;
    const STAT_CALLBACK_ING = 3;
    const STAT_REMOVE = 4;
    const STAT = [
        self::STAT_UNREVIEWED    => '未审核',
        self::STAT_CALLBACK_DONE => '回调完成',
        self::STAT_REFUSE        => '未通过',
        self::STAT_CALLBACK_ING  => '回调中',
        self::STAT_REMOVE        => '逻辑删除',
    ];
    const IS_HIDE_YES = 1;
    const IS_HIDE_NO = 0;
    const IS_HIDE = [
        self::IS_HIDE_YES => '隐藏',
        self::IS_HIDE_NO  => '显示',
    ];

    const IS_FREE_YES = 1;
    const IS_FREE_NO = 0;
    const IS_FREE = [
        self::IS_FREE_YES => '免费',
        self::IS_FREE_NO  => '收费',
    ];
    const IS_FEATURE_YES = 1;
    const IS_FEATURE_NO = 0;
    const IS_FEATURE = [
        self::IS_FEATURE_YES => '是',
        self::IS_FEATURE_NO  => '否',
    ];
    const RECOMMEND_YES = 1;
    const RECOMMEND_NO = 0;
    const RECOMMEND = [
        self::RECOMMEND_NO  => '否',
        self::RECOMMEND_YES => '是',
    ];

    const IS_TOP_YES = 1;
    const IS_TOP_NO = 0;
    const IS_TOP = [
        self::IS_TOP_NO  => '否',
        self::IS_TOP_YES => '是',
    ];

    const VIA_USER = 'user';
    const VIA_OFFICAL = 'own';
    const VIA_LUSIR = 'lu91';
    const VIA = [
        self::VIA_USER    => '用户上传',
        self::VIA_OFFICAL => '官方出品',
        self::VIA_LUSIR   => '91撸',
    ];

    protected $table = 'mv';

    protected $fillable = [
        'uid',
        'music_id',
        'coins',
        'title',
        'm3u8',
        'full_m3u8',
        'v_ext',
        'duration',
        'vip_coins',
        'gif_thumb',
        'gif_width',
        'gif_height',
        'cover_thumb',
        'thumb_width',
        'thumb_height',
        'directors',
        'actors',
        'category',
        'tags',
        'via',
        'onshelf_tm',
        'rating',
        'refresh_at',
        'is_free',
        'like',
        'is_recommend',
        'comment',
        'status',
        'thumb_start_time',
        'thumb_duration',
        'is_hide',
        'is_feature',
        'y_cover',
        'created_at',
        'is_top',
        'count_pay',
        'topic_id',
        'construct_id'
    ];

    protected $appends = [
       // 'y_cover_url',
        'tags_list',
        'cover_thumb_url',
        //'gif_thumb_url',
        'created_str',
        'is_like',
        'duration_str',
        'is_pay',
        'payed_total',
    ];
    public $timestamps = false;
    public function user()
    {
        return $this->hasOne(MemberModel::class, 'uid', 'uid');
    }

    public function user_topic(){
        return $this->hasOne(UserTopicModel::class , 'id' , 'topic_id');
    }

    public function getTagsListAttribute()
    {
        if (!isset($this->attributes['tags'])) {
            return [];
        }
        return array_map('trim', explode(',', $this->attributes['tags']));
    }

    public function getYCoverUrlAttribute()
    {
        return url_cover($this->attributes['y_cover'] ?? '');
    }

    public function getCoverThumbUrlAttribute()
    {
        return url_cover($this->attributes['cover_thumb'] ?? '');
    }

    public function getGifThumbUrlAttribute()
    {
        return url_cover($this->attributes['gif_thumb'] ?? '');
    }
    public function getPayedTotalAttribute()
    {
        return (int)$this->coins*$this->count_pay;
    }

    public function getCreatedStrAttribute()
    {
        $str = $this->attributes['created_at'] ?? 0;
        if(is_numeric($str))
        {
            return date('Y-m-d h:i:s',$str );
        }
        return $str;
    }

    public function getDurationStrAttribute()
    {
        return durationToString($this->attributes['duration'] ?? '');
    }

    public function getIsLikeAttribute()
    {
        if (empty($this->watchUser) || !isset($this->attributes['id'])) {
            return 0;
        }
        static $ids = null;
        if (null === $ids) {
            $ids = redis()->sMembers(\MemberModel::REDIS_USER_LIKING_LIST . $this->watchUser['uid']);
        }
        if (in_array($this->attributes['id'], $ids)) {
            return 1;
        }
        return 0;
    }

    public function getIsPayAttribute()
    {
        if (empty($this->watchUser) || !isset($this->attributes['id'])) {
            return 0;
        }
        if ($this->watchUser->getAttribute('uid') == $this->attributes['uid']) {
            return 1;
        }
        if (FreeMemberModel::isFreeMember($this->watchUser->uid)) {
            return 1;
        }
        static $ids = null;
        if (null === $ids) {
            $ids = MvPayModel::getVidArrByUser($this->watchUser['uid']);
        };
        if (in_array($this->attributes['id'], $ids)) {
            return 1;
        }
        return 0;
    }

    /**
     * @return Builder
     */
    public static function queryWithUser()
    {
        return self::queryBase()->with('user');
    }

    /**
     * @return Builder
     */
    public static function queryMv(){

        return self::queryWithUser()->where('topic_id','=',0);
    }

    public static function queryBase()
    {
        return self::where('status', '=', self::STAT_CALLBACK_DONE)
            ->where('is_hide', '=', self::IS_HIDE_NO);
    }

    public static function queryFee()
    {
        return self::queryWithUser()->where('coins', '>', 0);
    }

    public static function queryRecommend()
    {
        return self::queryWithUser()->where('is_recommend', MvModel::RECOMMEND_YES);
    }

    public static function queryFeeRecommend()
    {
        return self::queryRecommend()->where('coins', '>', 0);
    }


    public static function queryFeature()
    {
        return self::queryBase()->where('is_feature' , '=' , self::IS_FEATURE_YES);
    }

    public static function queryFeatureWithUser()
    {
        return self::queryFeature()
            ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,oauth_type,oauth_id');
    }


    public function getSourceUrl(): string
    {
        $m3u8 = $this->attributes['full_m3u8'] ?? null;
        if (empty($m3u8)) {
            $m3u8 = $this->attributes['m3u8'] ?? '';
        }
        return getPlayUrl($m3u8);
    }


    public static function virtualByForDelele()
    {
        $model = self::make();
        $model->title = "视频已被删除";
        $model->is_null = true;
        $model->uid = 0;
        $model->id = 0;
        $model->cover_thumb = '';
        return $model;
    }

    public function emitChange($release)
    {
        parent::emitChange($release);

        if ($release) {
            MemberModel::where('uid', $this->uid)->decrement('videos_count');
            $uidAry = UserLikeModel::where('mv_id', $this->id)->pluck('uid');
            MemberModel::whereIn('uid', $uidAry)->decrement('likes_count');
            UserLikeModel::where('mv_id', $this->id)->delete();
            return;
        }
    }

    /**
     * 砖石视频价格设置配置
     * @return array
     */
    static function getGoldMVPriceConf()
    {

        return [
            [
                'title' => '免费',
                'price' => 0,
                'key'   => 'price_free'
            ]
            ,
            [
                'title' => '10金币',
                'price' => 10,
                'key'   => 'price_ten'
            ]
            ,
            [
                'title' => '20金币',
                'price' => 20,
                'key'   => 'price_two'
            ]
            ,
            [
                'title' => '30金币',
                'price' => 30,
                'key'   => 'price_three'
            ]
            ,
            [
                'title' => '40金币',
                'price' => 40,
                'key'   => 'price_four'
            ]
            ,
            [
                'title' => '50金币',
                'price' => 50,
                'key'   => 'price_five'
            ]
        ];
    }


    public function coinsAfterDiscount(MemberModel $member)
    {
        //获取价格
        $total = abs(intval($this->coins));
        return $total;
        //计算折扣配置
        $discount = []; //$this->getDiscountConfig();
        //会员价格优先
        if ($this->vip_coins != -1 && $member->expired_at > TIMESTAMP) {
            $total = abs(intval($this->vip_coins));
        } elseif ($discount && is_array($discount)) {
            //获取用户买过多少次视频
            $buyCount = \MvPayModel::buyCount($member->uid);
            //直接使用下标获取配置的折折扣后的价格
            if (isset($discount[$buyCount])) {
                $total = abs(ceil($total * $discount[$buyCount]));
            }
        }
        return $total;//应付金币
    }

    /**
     * 检查用户有没有发布收费视频的权限
     * @param $uid
     * @return array
     */
    static function checkMemberToReleaseGoldMV($uid = 0)
    {
        $result = [
            'can_release'     => 1,
            'can_release_fee' => 1,
            'msg_tips'=>'上传更多精彩视频',
        ];
        return $result;

        $blackList = MvBackUserModel::getBackUserList();
        if ($uid && $blackList && in_array($uid, $blackList)) {
            $result = [
                'can_release'     => 0,
                'can_release_fee' => 0,
                'msg_tips'=>'上传黑名单用户',
            ];
            return $result;
        }

        if (!$uid) {
            $result = [
                'can_release'     => 0,
                'can_release_fee' => 0,
                'msg_tips'=>'上传黑名单用户',
            ];
            return $result;
        }
        if (false && MvUploadIpInfoModel::checkIPNum() > 10) {
            errLog("触发今日ip额度10上传限制~ uid:{$uid}");
            $result = [
                'can_release'     => 0,
                'can_release_fee' => 0,
                'msg_tips'        => '触发今日额度上传限制~',
            ];
            return $result;
        }
        $w = [];
        $toaday = strtotime(date('Y-m-d 00:00:00'));
        $w[] = ['uid', '=', $uid];
        $w[] = ['created_at', '>=', $toaday];
        $w[] = ['is_hide', '=', self::IS_HIDE_NO];
        $dataMv = MvModel::where($w)->select(['coins'])->get();

        $dataSubMit = MvSubmitModel::where(
            [
                ['uid', '=', $uid],
                ['created_at', '>=', $toaday],
            ]
        )->whereIn('status', [MvSubmitModel::STAT_UNREVIEWED, MvSubmitModel::STAT_CALLBACK_ING])
            ->select(['coins'])
            ->get();

        $data = collect($dataMv)->merge($dataSubMit);
        $hasCount = $data->count();
        errLog("count:{$hasCount}");
        if ($hasCount < 2) {//前2个视频强制免费,
            $result['can_release_fee'] = 0;//是否可以发布付费视频
            return $result;
        }
        /**1、上传数量限制：
         * · 制片人可上传10部/日
         * · 普通用户可上传3部/日
         * 超过数量提示*/
        /*$default = 10;
        if ($hasCount >= $default) {
            $result['can_release'] = 0;
            $result['msg_tips'] = '已达到当日上传数量10部/日上限，明日再来噢～';
            return $result;
        }*/

        if ($data) {
            $data = $data->toArray();
            $data = array_column($data, 'coins');
            $ct_fee = 0;//上传的付费视频
            $ct_free = 0;//上传的免费视频
            foreach ($data as $_isFee) {
                $_isFee > 0 ? $ct_fee++ : $ct_free++;
            }
            $hasCount = $hasCount + 1;//1 为虚拟数 当前待上传
            if (($ct_fee / $hasCount) <= 0.5) { //比例调成50
                $result['can_release_fee'] = 1;//是否可以发布付费视频
            } else {
                $result['can_release_fee'] = 0;//是否可以发布付费视频
            }
        }
        return $result;
    }

    public static function detail($id){
        $key = sprintf(self::REDIS_MV_DETAIL_KEY, $id);
        return cached($key)
            ->serializerPHP()
            ->expired(3600)
            ->setSaveEmpty(true)
            ->fetch(function () use ($id){
                return self::queryWithUser()
                    ->where('id',$id)
                    ->first();
            });
    }

    const SEE_LIST = 'see:list:%s:%s:%s:%s';
    const RECOMMEND_LIST = 'recommend:list:%s:%s:%s:%s';
    const NAV_TYPE = 1;
    const CONSTRUCT_TYPE = 2;

    protected static function listPlayingIdsNew($rk_id, $type, $offset, $limit)
    {
        $rankKey = sprintf(self::RK_SEE_CONSTRUCT, $rk_id);
        if ($type == self::NAV_TYPE){
            $rankKey = sprintf(self::RK_SEE_NAVIGATION, $rk_id);
        }
        $ids = redis()->zRevRangeByScore(
            $rankKey, '+inf', '-inf',
            [
                'withscores' => TRUE,
                'limit'      => [$offset, $limit]
            ]
        );
        return array_keys($ids);
    }

    //type 1 导航 2结构
    public static function listRecommend($rk_id, $type, $page, $limit)
    {
        $rankKey = sprintf(self::RECOMMEND_LIST, $rk_id, $type, $page, $limit);
        return cached($rankKey)
            ->fetchPhp(function () use ($rk_id, $type, $page, $limit){
                $offset = ($page - 1) * $limit;
                $ids = self::ListRecommendIds($rk_id, $type, $offset, $limit);
                $rs = self::queryBase()
                    ->whereIn('id', $ids)
                    ->get();
                return array_keep_idx($rs, $ids);
            }, 60);
    }

    public static function listFindMvs($type, $is_aw, $sort, $page, $limit){
        $cacheKey = sprintf(self::REDIS_FIND_LIST_MV_KEY, $type, $is_aw, $sort, $page, $limit);
        return cached($cacheKey)
            ->group(self::REDIS_FIND_LIST_MV_GP)
            ->chinese(self::REDIS_FIND_LIST_MV_CN)
            ->fetchPhp(function () use($type, $is_aw, $sort, $page, $limit){
                return self::queryBase()
                    ->when($type == ConstructModel::FIND_TYPE_COINS, function ($q){
                        $q->where('coins', '>', 0);
                    })
                    ->when($type == ConstructModel::FIND_TYPE_VIP, function ($q){
                        $q->where('coins', 0);
                    })
                    //热门
                    ->when($sort=="hot",function ($q){
                        $q->orderByDesc('like');
                    })
                    //最新
                    ->when($sort=="new",function ($q){
                        $q->orderByDesc('refresh_at');
                    })
                    ->orderByDesc('id')
                    ->forPage($page,$limit)
                    ->get();
            });
    }


    const RECOMMEND_WEEK_LIST = 'recommend:list:%s:%s:%s';
    const WEEK_VIEW_TYPE = 'view';
    const WEEK_LIKE_TYPE = 'like';
    protected static function ListRecommendIds($rk_id, $type, $offset, $limit)
    {
        $date = date('Ym');
        $rankKey = sprintf(self::RK_RECOMMEND_CONSTRUCT, $date, $rk_id);
        if ($type == self::NAV_TYPE){
            $rankKey = sprintf(self::RK_RECOMMEND_NAVIGATION, $date, $rk_id);
        }
        $ids = redis()->zRevRangeByScore(
            $rankKey, '+inf', '-inf',
            [
                'withscores' => TRUE,
                'limit'      => [$offset, $limit]
            ]
        );
        return array_keys($ids);
    }

    public static function listRecommendWeek($type, $page, $limit)
    {
        $rankKey = sprintf(self::RECOMMEND_WEEK_LIST, $type, $page, $limit);
        return cached($rankKey)
            ->fetchPhp(function () use ($type, $page, $limit){
                $offset = ($page - 1) * $limit;
                $ids = self::ListRecommendWeekIds($type, $offset, $limit);
                $rs = self::queryBase()
                    ->whereIn('id', $ids)
                    ->get();
                return array_keep_idx($rs, $ids);
            });
    }

    public static function randMvs(MemberModel $member, $c_key, $construct_arr, $page, $limit)
    {
        $randKey = 'rand:mv:list:v1:' . $member->aff . ':' . $c_key;
        $cacheKey = "list:nag:" . $c_key . '-p' . $page . '-a' . $member->aff;
        $setKey = 'rand:mv:set:keys' . $member->aff . ':' . $c_key;
        redis()->sAdd($setKey, $cacheKey);

        if (!redis()->exists($randKey)) {
            $ids = self::queryBase()
                ->selectRaw('id')
                ->whereIn('construct_id', $construct_arr)
                ->orderBy('like')
                ->limit(300)
                ->get()
                ->pluck('id')
                ->toArray();

            shuffle($ids);
            redis()->sAddArray($randKey, $ids);
            redis()->expire($randKey, 3600);
            $setKeys = redis()->sMembers($setKey);
            foreach ($setKeys as $k) {
                redis()->del($k);
            }
        }
        $ids = redis()->sMembers($randKey);
        $ids = collect($ids)->forPage($page, $limit);

        return cached($cacheKey)
            ->group('list_rand_mv')
            ->fetchPhp(function () use ($ids) {
                return MvModel::queryBase()
                    ->whereIn("id", $ids)
                    ->get();
            }, rand(1800, 3600));
    }

    //type 1 导航 2结构
    public static function listSeeNew($rk_id, $type, $page, $limit)
    {
        $rankKey = sprintf(self::SEE_LIST, $rk_id, $type, $page, $limit);
        return cached($rankKey)
            ->fetchPhp(function () use ($rk_id, $type, $page, $limit){
                $offset = ($page - 1) * $limit;
                $ids = self::listPlayingIdsNew($rk_id, $type, $offset, $limit);
                $rs = self::queryBase()
                    ->whereIn('id', $ids)
                    ->get();
                return array_keep_idx($rs, $ids);
            }, 60);
    }

    public static function randConstructMvs(MemberModel $member, $c_key, $construct_id, $page, $limit)
    {
        $is_aw = 0;
        $randKey = 'rand:mv:list:v1:' . $member->aff . ':' . $c_key . ":" . $is_aw;
        $cacheKey = "list:nag:" . $c_key . '-t' . $is_aw . '-p' . $page . '-a' . $member->aff;
        $setKey = 'rand:mv:set:keys' . $member->aff . ':' . $c_key. ':' . $is_aw;
        redis()->sAdd($setKey, $cacheKey);

        if (!redis()->exists($randKey)) {
            $ids = self::queryBase()
                ->selectRaw('id')
                ->where('construct_id', $construct_id)
                ->orderBy('like')
                ->limit(300)
                ->get()
                ->pluck('id')
                ->toArray();

            shuffle($ids);
            redis()->sAddArray($randKey, $ids);
            redis()->expire($randKey, 3600);
            $setKeys = redis()->sMembers($setKey);
            foreach ($setKeys as $k) {
                redis()->del($k);
            }
        }
        $ids = redis()->sMembers($randKey);
        $ids = collect($ids)->forPage($page, $limit);

        return cached($cacheKey)
            ->group('list_rand_mv')
            ->fetchPhp(function () use ($ids) {
                return MvModel::queryBase()
                    ->whereIn("id", $ids)
                    ->get();
            }, rand(1800, 3600));
    }

    public static function getMvDataByTags($c_key, $construct_arr, $sort, $page, $limit){
        $cacheKey = sprintf(self::REDIS_NAG_TAB_MV_KEY,$c_key,$sort,$page,$limit);
        return cached($cacheKey)
            ->group(self::REDIS_NAG_TAB_MV_GROUP)
            ->chinese(self::REDIS_NAG_TAB_MV_CN)
            ->fetchPhp(function () use($construct_arr, $sort, $page, $limit){
                return self::queryBase()
                    ->when(is_array($construct_arr),function ($q) use ($construct_arr){
                        $q->whereIn('construct_id', $construct_arr);
                    }, function ($q) use ($construct_arr){
                        if ($construct_arr){
                            return $q->where('construct_id', $construct_arr);
                        }
                        return $q;
                    })
                    //热门
                    ->when($sort=="hot",function ($q){
                        $q->orderByDesc('rating');
                    })
                    //大家都喜欢
                    ->when($sort=="like",function ($q){
                        $q->orderByDesc('like');
                    })
                    //最新
                    ->when($sort=="new",function ($q){
                        $q->orderByDesc('refresh_at');
                    })
                    //畅销
                    ->when($sort == "sale", function ($q) {
                        $q->where('coins', '>', 0)->orderByDesc('count_pay');
                    })
                    ->orderByDesc('id')
                    ->forPage($page,$limit)
                    ->get();
            });
    }

    public static function randTagMvs(MemberModel $member, $tag, $show_aw, $page, $limit)
    {
        $c_key = substr(md5($tag), 0, 8);
        $randKey = 'rand:mv:tag:list:v1:' . $member->aff . ':' . $c_key . ":" . $show_aw;
        $cacheKey = "list:nag:" . $c_key . '-t' . $show_aw . '-p' . $page . '-a' . $member->aff;
        $setKey = 'rand:mv:set:keys' . $member->aff . ':' . $c_key. ':' . $show_aw;
        redis()->sAdd($setKey, $cacheKey);

        if (!redis()->exists($randKey)) {
            $ids = self::queryBase()
                ->selectRaw('id')
                ->whereRaw("match(tags) against(? in boolean mode)", [$tag])
                ->orderBy('like')
                ->limit(300)
                ->get()
                ->pluck('id')
                ->toArray();

            shuffle($ids);
            redis()->sAddArray($randKey, $ids);
            redis()->expire($randKey, 3600);
            $setKeys = redis()->sMembers($setKey);
            foreach ($setKeys as $k) {
                redis()->del($k);
            }
        }
        $ids = redis()->sMembers($randKey);
        $ids = collect($ids)->forPage($page, $limit);

        return cached($cacheKey)
            ->group('list_rand_mv')
            ->fetchPhp(function () use ($ids) {
                return MvModel::queryBase()
                    ->whereIn("id", $ids)
                    ->get();
            }, rand(1800, 3600));
    }

    public static function randFindMvs(MemberModel $member, $type, $is_aw, $page, $limit)
    {
        $randKey = 'rand:mv:find:list:v1:' . $member->aff . ':' . $type . ":" . $is_aw;
        $cacheKey = "list:find:" . $type . '-t' . $is_aw . '-p' . $page . '-a' . $member->aff;
        $setKey = 'rand:find:mv:set:keys' . $member->aff . ':' . $type. ':' . $is_aw;
        redis()->sAdd($setKey, $cacheKey);

        if (!redis()->exists($randKey)) {
            $ids = self::queryBase()
                ->selectRaw('id')
                ->when($type == ConstructModel::FIND_TYPE_VIP, function ($q){
                    return $q->where("coins", 0);
                })
                ->when($type == ConstructModel::FIND_TYPE_COINS, function ($q){
                    return $q->where("coins", '>', 0);
                })
                ->orderBy('like')
                ->limit(300)
                ->get()
                ->pluck('id')
                ->toArray();

            shuffle($ids);
            redis()->sAddArray($randKey, $ids);
            redis()->expire($randKey, 3600);
            $setKeys = redis()->sMembers($setKey);
            foreach ($setKeys as $k) {
                redis()->del($k);
            }
        }
        $ids = redis()->sMembers($randKey);
        $ids = collect($ids)->forPage($page, $limit);

        return cached($cacheKey)
            ->group('list_rand_mv')
            ->fetchPhp(function () use ($ids) {
                return MvModel::queryBase()
                    ->whereIn("id", $ids)
                    ->get();
            }, rand(1800, 3600));
    }

    public static function tagMvList($tag, $sort, $show_aw, $page, $limit){
        $c_key = substr(md5($tag), 0, 8);
        $cacheKey = sprintf(self::CK_TAG_LIST_MV, $c_key, $sort, $show_aw, $page, $limit);
        return cached($cacheKey)
            ->group(self::GP_TAG_LIST_MV_GP)
            ->chinese(self::CN_TAG_LIST_MV_CN)
            ->fetchPhp(function () use ($tag, $sort, $show_aw, $page, $limit){
                return self::queryBase()
                    ->whereRaw("match(tags) against(? in boolean mode)", [$tag])
                    ->when($sort == 'new', function ($q){
                        return $q->orderByDesc('refresh_at');
                    })
                    ->when($sort == 'hot', function ($q){
                        return $q->orderByDesc('like');
                    })
                    ->orderByDesc('id')
                    ->forPage($page, $limit)
                    ->get();
            });
    }

    public static function getHomeMvDataByTag($construct_id, $sort, $page, $limit)
    {
        return self::queryBase()
            ->when($construct_id, function ($q) use ($construct_id) {
                $q->where('construct_id', $construct_id);
            })
            ->orderByDesc('is_recommend')
            ->when($sort == 'like', function ($q) {
                $q->orderByDesc('like');
            })
            ->when($sort == 'hot', function ($q) {
                $q->orderByDesc('rating');
            })
            ->when($sort == 'sale', function ($q) {
                $q->orderByDesc('count_pay');
            })
            ->when($sort == 'new', function ($q) {
                $q->orderByDesc('refresh_at');
            })
            ->orderByDesc('id')
            ->forPage($page, $limit)
            ->get();
    }

    public static function add2SeeRank($mv_id, $construct_id)
    {
        //发现-最多观看
        self::addWeekRank(self::WEEK_VIEW_TYPE, $mv_id);

        if (!$construct_id) {
            return;
        }
        //正在看
        $seeConstruct = sprintf(MvModel::RK_SEE_CONSTRUCT, $construct_id);
        redis()->zAdd($seeConstruct, time(), $mv_id);
        if (redis()->sCard($seeConstruct) > 1000){
            redis()->zRemRangeByRank($seeConstruct, 1000, -1);
        }
        //推荐
        $month = date('Ym');
        $recommendConstruct = sprintf(self::RK_RECOMMEND_CONSTRUCT, $month, $construct_id);
        if (!redis()->exists($recommendConstruct)) {
            redis()->zIncrBy($recommendConstruct, 1, $mv_id);
        }
        redis()->zIncrBy($recommendConstruct, 1, $mv_id);
        $construct = ConstructModel::findById($construct_id);
        if ($construct && $construct->nag_id){
            $nav = NavigationModel::findById($construct->nag_id);
            //下面是列表
            if ($nav->bot_style == NavigationModel::BOT_STYLE_TWO){
                //导航正在看
                $seeNavigation = sprintf(MvModel::RK_SEE_NAVIGATION, $construct->nag_id);
                redis()->zAdd($seeNavigation, time(), $mv_id);
                if (redis()->sCard($seeNavigation) > 1000){
                    redis()->zRemRangeByRank($seeNavigation, 1000, -1);
                }
                //导航推荐
                $recommendNavigation = sprintf(self::RK_RECOMMEND_NAVIGATION, $month, $construct->nag_id);
                if (!redis()->exists($recommendNavigation)) {
                    redis()->expire($recommendNavigation, 691200);
                }
                redis()->zIncrBy($recommendNavigation, 1, $mv_id);
            }
        }
    }

    public static function addWeekRank($type, $mv_id)
    {
        //发现-最多观看/最多点赞
        $week = date('YW');
        $viewKey = sprintf(MvModel::RK_RECOMMEND_SET, $type, $week);
        if (!redis()->exists($viewKey)) {
            redis()->expire($viewKey, 604900);
        }
        redis()->zIncrBy($viewKey, 1, $mv_id);
    }

    protected static function ListRecommendWeekIds($type, $offset, $limit)
    {
        $date = date('YW');
        $rankKey = sprintf(self::RK_RECOMMEND_SET, $type, $date);
        $ids = redis()->zRevRangeByScore(
            $rankKey, '+inf', '-inf',
            [
                'withscores' => TRUE,
                'limit'      => [$offset, $limit]
            ]
        );
        return array_keys($ids);
    }
}