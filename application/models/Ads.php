<?php


use Illuminate\Database\Eloquent\Model;
use tools\RedisService;

/**
 * class AdsModel
 *
 * @property int $id
 * @property string $title 广告标题
 * @property string $channel 渠道广告
 * @property string $mv_m3u8 视频m3u8
 * @property string $description 广告词
 * @property string $img_url 图片地址
 * @property int $img_type 1:上传的图片;2:网络图片
 * @property string $url 广告跳转地址/QQ号/微信号
 * @property int $position 位置:配置查看adscontroller
 * @property string $ios_url ios下载地址
 * @property string $android_url andeoid下载地址
 * @property int $type 广告类型 1：下载链接 2：跳转qq 3:跳转微信
 * @property int $platform
 * @property int $value 目标ID,分类id|系列id|标签id|视频id
 * @property int $status 0-禁用，1-启用
 * @property int $created_at 创建时间
 * @property int $click_number
 * @property int $show_user 0全部1 48小时前248小时后
 * @property string $expired_date 广告过期时间  eg：2022-10-10 00:00:00
 *
 * @author xiongba
 * @date 2021-04-23 18:10:12
 *
 * @mixin \Eloquent
 */
class AdsModel extends EloquentModel
{

    protected $table = "ads";

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'channel',
        'mv_m3u8',
        'description',
        'img_url',
        'img_type',
        'url',
        'position',
        'ios_url',
        'android_url',
        'type',
        'platform',
        'value',
        'status',
        'created_at',
        'show_user',
        'click_number',
        'expired_date'
    ];

    protected $guarded = 'id';

    public $timestamps = false;


    const PLAT_FORM_POLO = 0;
    const PLAT_FORM_AV = 1;
    const PLAT = [
        self::PLAT_FORM_POLO => '菠萝',
        self::PLAT_FORM_AV   => 'AV',
    ];

    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;
    const STATUS = [
        self::STATUS_FAIL    => '禁用',
        self::STATUS_SUCCESS => '启用',
    ];
    const REDIS_ADS_KEY = 'ads:pos_';
    const POSITION_SCREEN = 1; // 启动页广告
    const POSITION_LIST = 2; // 视频列表
    const POSITION_RECOMMEND = 3; // 剧集首页
    const POSITION_DIAMOND_PLAZA = 4; //金币商城
    const POSITION_MEMBER_RECHARGE = 5; //会员充值
    //const POSITION_DIAMOND_VIDEO_PLAZA = 6; //金币视频广场
    const POSITION_ACTIVE_POP = 7; //活动弹窗
    const POSITION_SEARCH_INDEX = 9; //搜索主页
    const POSITION_INDEX_HOME_UP = 10; //产品列表
    const POSITION_INDEX_HOME = 11; // 视频主页
    const POSITION_APP_CENTER = 12; // 应用中心
    //const POSITION_JINGXUAN = 13; // 精选视频广告
    const POSITION_TAB_FEATURE = 14; // tab 精选列表
    const POSITION_SITE_TOP = 15; // 进站必涮
    const POSITION_WEEK = 16; // 每周精选
    const POSITION_GAME = 17; // 游戏
    const POSITION_PLAY = 18; // 播放页小
    //const POSITION_BANNER = 301; // banner广告
    //const POSITION_PLAY = 201; // 直播播放
    const POS_FIND_BANNER = 107; // 求片广告
    const POSITION_AV_FEATURE_BANNER = 202; // av 推荐广告
    const POSITION_AV_FEATURE_SUB = 203; // av 推荐页子广告
    const POSITION_AV_CAT_SUB = 204; // av 分类导航页子广告
    const POSITION_AV_PLAY = 205; // av 播放页广告 小
    const POSITION_AV_VIP = 206; // av vip专区
    const POSITION_AV_VIP_SUB = 207; // av vip专区
    const POSITION_MV_LIST_MIX = 300;//视频混合列表
    const POSITION_LIVE_BANNER = 301;//直播banner
    const POSITION_LIVE_DETAIL = 302;//直播详情
    const POS_COMMUNITY_BANNER = 303; // 社区广告
    const POSITION_COMMUNITY_DETAIL = 304;//帖子详情
    const POSITION_MANHUA = 305; // 漫画banner
    const POSITION_PIC = 306; // 图集广告
    const POSITION_STORY = 307; // 小说广告
    const POSITION_PORN_GAME_BANNER = 308;//黄游banner
    const POSITION_PORN_GAME_DETAIL = 309;//黄游详情
    const POSITION_CARTOON_BANNER = 310;//动漫banner
    const POSITION_CARTOON_DETAIL = 311;//动漫详情
    const POSITION_FLOATING_ADS = 312;//悬浮广告
    const POSITION_SEED_LIST = 313;//种子banner
    const POSITION_SEED_DETAIL = 314;//种子详情
    const POSITION_AI_HL_BANNER = 171;//AI换脸banner

    const POSITION = [ // 广告位置
        self::POSITION_SCREEN          => '启动页广告',
        self::POSITION_INDEX_HOME_UP   => '视频主页(上)',
        self::POSITION_INDEX_HOME      => '视频主页(中)',
        self::POSITION_LIST            => '分类视频列表',
        self::POSITION_RECOMMEND       => '剧集广告',
        //self::POSITION_PLAY                => '直播播放',
        self::POSITION_DIAMOND_PLAZA   => '金币商城',
        self::POSITION_MEMBER_RECHARGE => '会员充值',
        //self::POSITION_DIAMOND_VIDEO_PLAZA => '金币视频广场',
        //self::POSITION_PRODUCT_LIST        => '产品列表',

        self::POSITION_APP_CENTER   => '应用中心',
        //self::POSITION_JINGXUAN            => '精选视频广告',
        //self::POSITION_TAB_FEATURE         => 'tab列表',
        //self::POSITION_SITE_TOP            => '进站必涮',
        //self::POSITION_WEEK                => '每周精选',
        self::POSITION_SEARCH_INDEX => '搜索主页',
        self::POSITION_ACTIVE_POP   => '活动弹窗',
        //self::POSITION_GLOABLE_FULI        => '会员福利',
        //self::POSITION_GAME        => '游戏页(旧)',
        self::POSITION_PLAY         => '播放页小',
        self::POSITION_AI_HL_BANNER         => 'AI换脸banner',
        //av 相关
        /*self::POSITION_AV_FEATURE_BANNER   => 'AV-推荐',
        self::POSITION_AV_FEATURE_SUB      => 'AV-推荐小',
        self::POSITION_AV_CAT_SUB          => 'AV-分类导航小',
        self::POSITION_AV_PLAY             => 'AV-播放页',
        self::POSITION_AV_VIP              => 'AV-VIP专区',
        self::POSITION_AV_VIP_SUB          => 'AV-VIP小',*/
    ];


    // 广告类型
    const ADS_TYPE = [
        0 => '默认处理',
        1 => '外部跳转连接',
        2 => '内部跳转标签',
        3 => '内部跳转连接',
        4 => '内部跳转视频详情',
        // 5 => '直接安装App',
        6 => '跳转到VIP',
        7 => '跳转金币商城',
        8 => '跳转到游戏',
    ];

    protected $appends = [
        'img_url_full',
        'is_expired'
    ];

    /**
     * 替换图片地址
     * @param $value
     * @return string
     */
    public function getImgUrlFullAttribute()
    {
        return $this->img_url ? url_ads($this->img_url) : '';
    }

    public static function clearRedisCache($position, $channel = '')
    {
        $key = self::REDIS_ADS_KEY . $position . $channel;
        return redis()->del($key);
    }
    /**
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        $expired_date = $this->getAttribute('expired_date');
        if ($expired_date) {
            return strtotime($expired_date) > time() ? false : true;
        }
        return false;
    }

    // 广告位置
    const POSITION_REMOTE = [
        self::POSITION_SCREEN               =>  '1',//'启屏页',
        self::POSITION_ACTIVE_POP           =>  '2',//'活动弹窗',
        self::POSITION_TAB_FEATURE          =>  '3',//视频banner,
        self::POSITION_RECOMMEND            =>  '4',//剧集banner,
        self::POSITION_INDEX_HOME           =>  '5',//视频主页(中)',
        self::POSITION_APP_CENTER           =>  '6',//'应用中心',
        self::POSITION_SEARCH_INDEX         =>  '7',//'搜索主页',
        self::POSITION_PLAY                 =>  '8',//'视频详情',
        self::POSITION_MV_LIST_MIX          =>  '9',//视频列表混合
        self::POSITION_LIVE_BANNER          =>  '10',//'直播banner',
        self::POSITION_LIVE_DETAIL          =>  '11',//'直播详情',
        self::POS_COMMUNITY_BANNER          =>  '12',//'社区banner广告',
        self::POSITION_COMMUNITY_DETAIL     =>  '13',//帖子详情
        self::POSITION_MANHUA               =>  '14',//'漫画banner',
        self::POSITION_PIC                  =>  '15',//'图集banner',
        self::POSITION_STORY                =>  '16',//小说广告,
        self::POSITION_PORN_GAME_BANNER     =>  '17',//'黄游banner',
        self::POSITION_PORN_GAME_DETAIL     =>  '18',//'黄游详情',
        self::POSITION_CARTOON_BANNER       =>  '19',//'动漫banner',
        self::POSITION_CARTOON_DETAIL       =>  '20',//'动漫详情',
        self::POSITION_FLOATING_ADS         =>  '21',//悬浮广告
        self::POSITION_SEED_LIST            =>  '22',//种子banner
        self::POSITION_SEED_DETAIL          =>  '23',//种子详情
        self::POSITION_AI_HL_BANNER         =>  '24',//'AI-banner',
    ];

    const SIZE_TIPS = [
        self::POSITION_SCREEN            => '750X1334',
        self::POSITION_ACTIVE_POP        => '610X680',
        self::POSITION_TAB_FEATURE       => '100X100',
        self::POSITION_RECOMMEND         => '100X100',
        self::POSITION_INDEX_HOME        => '100X100',
        self::POSITION_APP_CENTER        => '340X192',
        self::POSITION_SEARCH_INDEX      => '100X100',
        self::POSITION_PLAY              => '100X100',
        self::POSITION_MV_LIST_MIX       => '700X200',
        self::POSITION_LIVE_BANNER       => '100X100',
        self::POSITION_LIVE_DETAIL       => '100X100',
        self::POS_COMMUNITY_BANNER       => '100X100',
        self::POSITION_COMMUNITY_DETAIL  => '100X100',
        self::POSITION_MANHUA            => '100X100',
        self::POSITION_PIC               => '100X100',
        self::POSITION_STORY             => '100X100',
        self::POSITION_PORN_GAME_BANNER  => '100X100',
        self::POSITION_PORN_GAME_DETAIL  => '100X100',
        self::POSITION_CARTOON_BANNER    => '100X100',
        self::POSITION_CARTOON_DETAIL    => '100X100',
        self::POSITION_FLOATING_ADS      => '100X100',
        self::POSITION_SEED_LIST         => '100X100',
        self::POSITION_SEED_DETAIL       => '100X100',
    ];


    // 广告类型 前面远程 => 原系统
    const ADS_TYPE_REMOTE = [
        0 => '0',//默认处理
        1 => '1',//外部跳转连接
        2 => '2',//内部跳转标签
        3 => '3',//内部跳转连接
        4 => '4',//内部跳转视频详情
        // 5 => '直接安装App',
        6 => '6',//跳转到VIP
        7 => '7',//跳转金币商城
    ];

    const ADS_APP_REPORT_KEY = 'ads:remote:report';
    const TYPE_COMMON = 0;
    const TYPE_APP_CENTER = 1;
    const TYPE_APP_NOTICE = 2;

    const NT_APP_IN = 1;
    const NT_APP_OUT = 0;

    const CK_ADS_REMOTE_LIST = 'ck:ads:remote:list:%d';
    const GP_ADS_REMOTE_LIST = 'gp:ads:remote:list';
    const CN_ADS_REMOTE_LIST = '远程广告列表';

    const ADS_VERSION = '3.0.0';

    public static function getRemoteAdsList($type = self::TYPE_COMMON){
        return cached(sprintf(self::CK_ADS_REMOTE_LIST, $type))
            ->group(self::GP_ADS_REMOTE_LIST)
            ->chinese(self::CN_ADS_REMOTE_LIST)
            ->fetchJson(function () use ($type){
                $http = new \tools\HttpCurl();
                $params = [
                    'hash' => config('ads.key'),
                    'type' => $type,
                ];
                $data = $http->get(config('ads.app.list.url'), $params);
                $data = json_decode($data, true);
                if (!$data['status'] == 1){
                    return [];
                }
                $list = [];
                if ($type == self::TYPE_COMMON){
                    if ($data['data']){
                        $list = array_reduce($data['data'], function($result, $item) {
                            $result[$item['position_val']][] = $item;
                            return $result;
                        }, []);
                    }
                }else{
                    $list = $data['data'];
                }

                return $list;
            }, 300);
    }

    public static function getPositionByRemote($position){
        static $list = null;
        if ($list === null){
            $list = self::getRemoteAdsList();
        }
        $key = self::POSITION_REMOTE[$position];
        if (!array_key_exists($key, $list)){
            return [];
        }
        $data = $list[$key];
        if (!$data){
            return [];
        }
        array_multisort(array_column($data, 'sort'), SORT_DESC, $data);
        return collect($data)->map(function ($item){
            $img = parse_url($item['image'], PHP_URL_PATH);
            $is_expired = false;
//            if ($item['end_at']) {
//                $is_expired = strtotime($item['end_at']) > time() ? false : true;
//            }
            $m3u8 = '';
            if ($item['m3u8']){
                //$m3u8 = getPlayUrl(parse_url($item['m3u8'], PHP_URL_PATH), false);
            }
            return [
                'id'            => $item['id'],
                'title'         => $item['title'],
                'img_url'       => $img,
                'url'           => replace_share($item['address']),
                'type'          => intval(self::ADS_TYPE_REMOTE[$item['type_val']] ?? 0),
                'value'         => 0,
                'expired_date'  => $item['end_at'],
                'mv_m3u8'       => $m3u8,
                'img_url_full'  => url_cover($img),
                'is_expired'    => $is_expired,
                'advertise_code' => $item['_id'],
                'advertise_location_code' => $item['position_val'] ?: '-1_null',
                'ad_type'       => $item['ad_type'] ?: '',
                'ad_slot_name'  => $item['position_name'] ?: '',
            ];
        })->filter()->values();
    }

    public static function getAppByRemote(){
        $list = self::getRemoteAdsList(self::TYPE_APP_CENTER);
        array_multisort(array_column($list, 'sort'), SORT_DESC, $list);
        return collect($list)->map(function ($item){
            $img = parse_url($item['image'], PHP_URL_PATH);
            return [
                'id'          => $item['id'],
                'title'       => $item['title'],
                'short_name'  => '',
                'description' => $item['desc'],
                'img_url_2'   => $img ?: '',
                'img_url'     => $img ? url_cover($img) : '',
                'link_url'    => replace_share($item['address']),
                'clicked'     => rand(100000, 1000000),
                'created_at'  => date('Y/m/d'),
                'advertise_code' => $item['_id'] ?? '',
                'advertise_location_code' => $item['position_val'] ?: '-1_null',
                'ad_type'       => $item['ad_type'] ?: '',
                'ad_slot_name'  => $item['position_name'] ?: '应用中心',
            ];
        })->filter()->values();
    }

    public static function getNoticeAppByRemote(){
        $list = self::getRemoteAdsList(self::TYPE_APP_NOTICE);
        array_multisort(array_column($list, 'sort'), SORT_DESC, $list);
        return collect($list)->map(function ($item){
            $img = parse_url($item['image'], PHP_URL_PATH);
            return [
                'id'          => $item['id'],
                'title'       => $item['title'],
                'short_name'  => '',
                'description' => $item['desc'],
                'img_url'     => $img ? url_cover($img) : '',
                'link_url'    => $item['address'],
                'clicked'     => 0,
                'created_at'  => date('Y/m/d'),
                'app_type'    => $item['app_type'],
                'advertise_code' => $item['_id'],
                'advertise_location_code' => $item['position_val'] ?: '-1_null',
                'ad_type'       => $item['ad_type'] ?: '',
                'ad_slot_name'  => $item['position_name'] ?: '弹窗APP',
            ];
        })->filter()->values();
    }

    public static function reportRemote($id, $time){
        //写入队列
        redis()->lPush(self::ADS_APP_REPORT_KEY, json_encode(['id' => $id, 'time' => $time]));
        if (redis()->lLen(self::ADS_APP_REPORT_KEY) >= 100){
            $data = [];
            $list = [];
            // 循环弹出前 100 条数据
            for ($i = 0; $i < 100; $i++) {
                $value = redis()->rPop(self::ADS_APP_REPORT_KEY);
                if ($value === false) {
                    break; // 队列为空时停止
                }
                $list[] = json_decode($value, true);
                $data[] = $value;
            }
            if (empty($list)){
                echo "空的",PHP_EOL;
                return;
            }
            $http = new \tools\HttpCurl();
            $params = ['hash' => config('ads.key'), 'list' => $list];
            $header = ['Content-Type:application/json'];
            //error_log(var_export($params, true). PHP_EOL, 3, APP_PATH . '/storage/logs/report.log');
            $result = $http->post(config('ads.app.report.url'), json_encode($params), $header);
            $result = json_decode($result, true);
            if ($result['status'] != 1){
                collect($data)->map(function ($item){
                    redis()->lPush(self::ADS_APP_REPORT_KEY, $item);
                });
            }
        }
    }

    //循环广告
    public static function formatMixAds($list, $page){
        $count = count($list);
        if ($count == 0){
            return [];
        }
        $result = [];
        $pageSize = 2;
        $start = ($pageSize * ($page - 1)) % $count;
        // 取出 $pageSize 个广告（循环）
        for ($i = 0; $i < $pageSize; $i++) {
            $index = ($start + $i) % $count;
            $result[] = $list[$index];
        }
        return $result;
    }
}
