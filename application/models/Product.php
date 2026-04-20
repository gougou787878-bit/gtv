<?php


use Illuminate\Database\Eloquent\Model;
use tools\RedisService;

/**
 * class ProductModel
 *
 * @property int $id
 * @property int $type 1:vip2钻石
 * @property string $pname 产品名称
 * @property string $img 图片
 * @property int $price 价格:单位分
 * @property int $promo_price 推广价格:单位分
 * @property int $valid_date VIP多少天
 * @property int $coins 多少钻石
 * @property int $free_day
 * @property int $free_coins 赠送多少钻石
 * @property string $pay_type 支付方式online线上支付agent代理支付
 * @property int $status 产品状态 0:未上架 1:上架 2:下架
 * @property int $sort_order 排序
 * @property int $payway_wechat 微信支付1支持0不支持
 * @property int $payway_bank 银联支付1支持0不支持
 * @property int $payway_alipay 支付宝支付1支持0不支持
 * @property int $payway_visa 01
 * @property int $payway_ecny 01
 * @property int $payway_huabei 0支持1不支持
 * @property int $payway_agent 0支持1不支持
 * @property string $description 产品描述
 * @property string $url 跳转地址
 * @property int $updated_at
 * @property int $created_at
 * @property int $vip_level vip等级 0普通 1 月卡 2季卡 3 年卡
 * @property int $platform 0 polo 1 av
 * @property int $ticket
 * @property int $free_ai_num
 * @property int $download_num
 *
 * @author xiongba
 * @date 2021-04-24 11:32:02
 *
 * @mixin \Eloquent
 */
class ProductModel extends Model
{

    protected $table = "product";

    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'pname',
        'img',
        'price',
        'promo_price',
        'valid_date',
        'coins',
        'free_day',
        'free_coins',
        'pay_type',
        'status',
        'sort_order',
        'payway_wechat',
        'payway_bank',
        'payway_alipay',
        'payway_visa',
        'payway_huabei',
        'payway_agent',
        'payway_ecny',
        'description',
        'url',
        'updated_at',
        'created_at',
        'vip_level',
        'platform',
        'ticket',
        'free_ai_num',
        'free_aimagic_num',
        'free_strip_num',
        'download_num',
    ];

    protected $guarded = 'id';

    public $timestamps = false;
    const PAY_TYPE_ONLINE = 'online';
    const PAY_TYPE_AGENT = 'agent';
    const PAY_TYPE = [
        self::PAY_TYPE_ONLINE => '在线支付',
        self::PAY_TYPE_AGENT  => '代理支付',
    ];

    const MONEY_PRODUCT_LIST = 'money_products_lists';
    const COINS_PRODUCT_LIST = 'coins_products_lists';
    const PLAT_FORM_POLO = 0;
    const PLAT_FORM_AV = 1;
    const PLAT = [
        self::PLAT_FORM_POLO => '菠萝',
        self::PLAT_FORM_AV   => 'AV',
    ];
    const STAT_OFF = 0;
    const STAT_ON = 1;
    const STAT = [
        self::STAT_OFF => '失效',
        self::STAT_ON  => '启用',
    ];

    const TYPE_VIP = 1, TYPE_DIAMOND = 2, TYPE_GAME = 3;
    const TYPE = [
        self::TYPE_VIP     => '会员',
        self::TYPE_DIAMOND => '金币',
        self::TYPE_GAME    => '游戏',
    ];

    //vip等级 0普通 1 月卡 2季卡 3 年卡
    const VIP_LEVEL_DONT = 0, VIP_LEVEL_MOON = 1, VIP_LEVEL_SEASON = 2, VIP_LEVEL_YEAR = 3;
    const VIP_LEVEL = [
        self::VIP_LEVEL_DONT   => '普通',
        self::VIP_LEVEL_MOON   => '月卡',
        self::VIP_LEVEL_SEASON => '季卡',
        self::VIP_LEVEL_YEAR   => '年卡',
    ];

    const PAY_WAY_ICON = [
        'payway_wechat' => '/upload/ads/20231113/2023111322212890286.png',
        'payway_bank'   => '/upload/ads/20231113/2023111322211069167.png',
        'payway_alipay' => '/upload/ads/20231113/2023111322194996934.png',
        'payway_visa'   => '/upload/ads/20231113/2023111322260442421.png',
        'payway_ecny'   => '/upload/ads/20231113/2023111322241723124.png',
        'payway_huabei' => '/upload/ads/20231113/2023111323020191054.png',
        'payway_agent'  => '/upload/ads/20231113/2023111322055640993.png'
    ];

    public function map()
    {
        return $this->hasMany(ProductRightMapModel::class, 'product_id', 'id');
    }

    static function clearRedisCache($type)
    {
        $redisKey = self::MONEY_PRODUCT_LIST . "_{$type}";
        redis()->del($redisKey);
        $key = self::MONEY_PRODUCT_LIST . "_v2_{$type}";
        redis()->del($key);
    }


    /**
     * @param int $type 要获取的类型
     * @return ProductModel[]|\Illuminate\Support\Collection
     * @author xiongba
     * @date 2020-03-12 15:50:25
     */
    public static function getByType($type)
    {
        $where = [
            'status' => 1,
            'type'   => $type,
        ];
        return \ProductModel::where($where)->orderBy('sort_order', 'asc')->get();
    }


    public function getPayWay()
    {
        $payWay = [];
        if ($this->payway_alipay) {
            $payWay[] = 'pa';
        }
        if ($this->payway_bank) {
            $payWay[] = 'pb';
        }
        if ($this->payway_visa) {
            $payWay[] = 'pv';
        }
        if ($this->payway_huabei) {
            $payWay[] = 'ph';
        }
        if ($this->payway_wechat) {
            $payWay[] = 'pw';
        }
        if ($this->payway_agent) {
            $payWay[] = 'pg';
        }
        if ($this->payway_ecny) {
            $payWay[] = 'ps';
        }
        return $payWay;
    }

    /**
     * @return array
     */
    public function getPayWayNew()
    {
        $payWay = [];
        if ($this->payway_alipay) {
            $payWay[] = [
                'type' => 'pa',
                'name' => '支付宝',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_alipay']),
            ];
        }
        if ($this->payway_bank) {
            $payWay[] = [
                'type' => 'pb',
                'name' => '银联',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_bank']),
            ];
        }
        if ($this->payway_visa) {
            $payWay[] = [
                'type' => 'pv',
                'name' => 'visa',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_visa']),
            ];
        }
        if ($this->payway_huabei) {
            $payWay[] = [
                'type' => 'ps',
                'name' => '花呗',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_huabei']),
            ];
        }
        if ($this->payway_wechat) {
            $payWay[] = [
                'type' => 'pw',
                'name' => '微信',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_wechat']),
            ];
        }
        if ($this->payway_agent) {
            $payWay[] = [
                'type' => 'pg',
                'name' => '人工充值',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_agent']),
            ];
        }
        if ($this->payway_ecny) {
            $payWay[] = [
                'type' => 'ps',
                'name' => '数字人名币',
                'icon' => url_cover(self::PAY_WAY_ICON['payway_ecny']),
            ];
        }
        return $payWay;
    }

    public function toApiArray()
    {
        return [
            'img'         => url_ads($this->img),
            'op'          => (int)$this->price / 100,
            'p'           => (int)$this->promo_price / 100,
            'coins'       => (int)$this->coins,
            'free_coins'  => (int)$this->free_coins,
            'id'          => (int)$this->id,
            'pname'       => $this->pname,
            'pt'          => $this->pay_type,
            'description' => $this->description ?? '',
            'pw'          => $this->getPayWay()
        ];
    }

    /**
     * @return array
     */
    public static function getAdminVIPDataList()
    {
        return self::where(['type' => self::TYPE_VIP])
            ->get(['id', 'pname'])
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->id . '|' . $item->pname];
            })->toArray();
    }

    //后台使用
    public function getMapToString(){
        if($this->type == self::TYPE_VIP){
            if($mapData = $this->load('map')->map){
                $data = collect($mapData)->sortBy('id')->map(function ($item){
                    if($right =  $item->load('right')->right){
                        return "$right->id | $right->name | $right->desc";
                    }
                    return null;
                })->filter()->values()->toArray();
                return implode('<br/>',$data);
            }
        }
        return '';
    }
}
