<?php


use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 *小兰  制片人 中心
 *
 * class MemberMakerModel
 *
 * @property int $id
 * @property string $uuid
 * @property int $level_num 创作者等级
 * @property string $phone 手机
 * @property int $status 0 缺省 1待审核  2 未通过  3. 禁用 4 正常
 * @property string $refuse_reason 拒绝理由
 * @property string $created_at
 * @property string $nickname 用户昵称，和用户表保持一致
 * @property string $contact 扩展联系方式
 * @property int $total_coins 累计汤币
 * @property string $pay_rate 提现结算比例
 * @property int $topic_count 合集数量
 *
 * @mixin \Eloquent
 */
class MemberMakerModel extends Model
{

    protected $table = "member_maker";

    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'level_num',
        'phone',
        'status',
        'refuse_reason',
        'created_at',
        'nickname',
        'contact',
        'total_coins',
        'pay_rate',
        'topic_count'
    ];

    protected $guarded = 'id';

    public $timestamps = false;


    const CREATOR_STAT_DF = 0;
    const CREATOR_STAT_ING = 1;
    const CREATOR_STAT_NO = 2;
    const CREATOR_STAT_BAN = 3;
    const CREATOR_STAT_YES = 4;
    const CREATOR_STATUS_TEXT = [
        self::CREATOR_STAT_DF  => '缺省',
        self::CREATOR_STAT_ING => '审核中',
        self::CREATOR_STAT_NO  => '未通过',
        self::CREATOR_STAT_BAN => '禁用',
        self::CREATOR_STAT_YES => '正常',
    ];
    //1 个人类型 2 团队类型 机构
    const TYPE_PERSONAL = 1;
    const TYPE_TEAM = 2;
    const TYPE = [
        self::TYPE_PERSONAL => '个人',
        self::TYPE_TEAM     => '团队',
    ];

    public function member()
    {
        return $this->hasOne(MemberModel::class, 'uuid', 'uuid');
    }

    /**
     * @param $uuid
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    static function getMakeRowInfo($uuid)
    {
        return self::where(['uuid' => $uuid])->first();
    }

    /**
     * @param $uuid
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    static function getMakeInfo($uuid)
    {
        return self::where(['uuid' => $uuid, 'status' => self::CREATOR_STAT_YES])->first();
    }

    static function getMakerRate($uuid)
    {

        /** @var MemberMakerModel $row */
        $row = self::getMakeInfo($uuid);
        if (is_null($row)) {
            return UserWithdrawModel::USER_WITHDRAW_MONEY_RATE_MV;
        }
        return $row->pay_rate;
    }

    static function getMakerLevel($uuid)
    {

        /** @var MemberMakerModel $row */
        $row = self::getMakeInfo($uuid);
        if (is_null($row)) {
            return 0;
        }
        return $row->level_num;
    }

    /**
     * 每日计划任务控制台使用
     * @param $level
     * @return mixed
     */
    static function getRateInfoByLevel($level)
    {
        $data = collect(self::getMakerRule())->keyBy('level')->toArray();
        return isset($data[$level]) ? $data[$level] : $data[0];
    }

    static function getMakerRule()
    {
        return [
            [
                'level'      => 1,
                'name'       => 'LV1',
                'vip'        => '月度',
                'vip_level'  => MemberModel::VIP_LEVEL_MOON,
                'tui_total'  => 0,
                'mv_coins'   => 0,
                'mv_number'  => 0,
                'rate'       => '25%',
                'rate_value' => '0.25',
            ],
            [
                'level'      => 2,
                'name'       => 'LV2',
                'vip'        => '月度',
                'vip_level'  => MemberModel::VIP_LEVEL_MOON,
                'tui_total'  => 0,
                'mv_coins'   => 5000,
                'mv_number'  => 20,
                'rate'       => '30%',
                'rate_value' => '0.30',
            ],
            [
                'level'      => 3,
                'name'       => 'LV3',
                'vip'        => '季卡',
                'vip_level'  => MemberModel::VIP_LEVEL_JIKA,
                'tui_total'  => 0,
                'mv_coins'   => 10000,
                'mv_number'  => 30,
                'rate'       => '32%',
                'rate_value' => '0.32',
            ],
            [
                'level'      => 4,
                'name'       => 'LV4',
                'vip'        => '季卡',
                'vip_level'  => MemberModel::VIP_LEVEL_JIKA,
                'tui_total'  => 1000,
                'mv_coins'   => 22000,
                'mv_number'  => 40,
                'rate'       => '34%',
                'rate_value' => '0.34',
            ],
            [
                'level'      => 5,
                'name'       => 'LV5',
                'vip'        => '年卡',
                'vip_level'  => MemberModel::VIP_LEVEL_YEAR,
                'tui_total'  => 2000,
                'mv_coins'   => 45000,
                'mv_number'  => 50,
                'rate'       => '36%',
                'rate_value' => '0.36',
            ],
            [
                'level'      => 6,
                'name'       => 'LV6',
                'vip'        => '年卡',
                'vip_level'  => MemberModel::VIP_LEVEL_YEAR,
                'tui_total'  => 5000,
                'mv_coins'   => 90000,
                'mv_number'  => 80,
                'rate'       => '38%',
                'rate_value' => '0.38',
            ],
            [
                'level'      => 7,
                'name'       => 'LV7',
                'vip'        => '永久',
                'vip_level'  => MemberModel::VIP_LEVEL_LONG,
                'tui_total'  => 10000,
                'mv_coins'   => 180000,
                'mv_number'  => 100,
                'rate'       => '40%',
                'rate_value' => '0.40',
            ],
            [
                'level'      => 8,
                'name'       => 'LV8',
                'vip'        => '永久',
                'vip_level'  => MemberModel::VIP_LEVEL_LONG,
                'tui_total'  => 32000,
                'mv_coins'   => 360000,
                'mv_number'  => 120,
                'rate'       => '44%',
                'rate_value' => '0.44',
            ],
            [
                'level'      => 9,
                'name'       => 'LV9',
                'vip'        => '永久',
                'vip_level'  => MemberModel::VIP_LEVEL_LONG,
                'tui_total'  => 64000,
                'mv_coins'   => 720000,
                'mv_number'  => 150,
                'rate'       => '48%',
                'rate_value' => '0.48',
            ],
        ];
    }
}
