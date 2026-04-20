<?php


use App\modules\Admin\service\AgentChartService;
use App\modules\Admin\service\ChartService;
use Carbon\Carbon;
use tools\RedisService;
use traits\OverloadActionTrait;
use Yaf\Controller_Abstract;

class HomestatController extends BackendBaseController
{
    use OverloadActionTrait;

    public $today_start;

    //面板控制缓存
    static $backendPannelRedisExperid = 360;
    //面板统计缓冲过期控制
    static $backendCountRedisExperid = 120;

    public function init()
    {
        parent::init();
        $this->today_start = strtotime(date('Y-m-d 00:00:00', TIMESTAMP));
    }

    public function indexAction(){
        $this->getView()->display('statics/indexnew.phtml');
    }
    /**
     * 面板统计展示(新)
     */
    protected function _getPanelTotal()
    {
        $data = [];
        //今日注册
        $data['reg_total'] = SysTotalModel::getValueBy('member:create');
        //今日Android注册
        $data['reg_and'] = SysTotalModel::getValueBy('member:create:and');
        //今日pwa注册
        $data['reg_pwa'] = $data['reg_total'] - $data['reg_and'];
        //渠道注册
        $data['channel_reg']  = SysTotalModel::getValueBy('member:create:invite');
        //今日活跃
        $data['active_total'] = SysTotalModel::getValueBy('member:active');
        //今日android活跃
        $data['active_and'] = SysTotalModel::getValueBy('member:active:and');
        //今日pwa活跃
        $data['active_pwa'] = SysTotalModel::getValueBy('member:active:pwa');
        $data['session'] = SysTotalModel::getValueBy('member:active') - SysTotalModel::getValueBy('member:create');

        //今日推广总额
        $data['today_sharechange'] = cached('now:order:tui')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return OrdersModel::where('status', 3)
                    ->where('updated_at', '>=', $this->today_start)
                    ->whereNotIn('build_id', ['', '0'])->sum('pay_amount') / 100;
        });
        //今日金币充值
        $data['pay_coin'] = cached('t:od:coins')
            ->expired(self::$backendPannelRedisExperid)
            ->fetch(function (){
                return  OrdersModel::where('status', 3)
                        ->where('order_type',OrdersModel::TYPE_GLOD)
                        ->where('updated_at', '>=',$this->today_start)
                        ->sum('pay_amount') / 100;
            });
        //今日VIP充值
        $data['pay_vip'] = cached('t:od:vip')
            ->expired(self::$backendPannelRedisExperid)
            ->fetch(function (){
                return  OrdersModel::where('status', 3)
                        ->where('order_type',OrdersModel::TYPE_VIP)
                        ->where('updated_at', '>=',$this->today_start)
                        ->sum('pay_amount') / 100;
            });
        $data['today_order_charge'] = $data['pay_vip']+ $data['pay_coin'];
        //总订单充值
        $all_order = cached('t:od:all:count')
            ->expired(self::$backendPannelRedisExperid)
            ->fetch(function (){
                return  OrdersModel::where('updated_at', '>=', $this->today_start)
                    ->count();
            });

        $payed_order = cached('payed_order')
            ->expired(self::$backendPannelRedisExperid)
            ->fetch(function (){
                return OrdersModel::query()
                    ->where('status', 3)
                    ->where('updated_at', '>=', $this->today_start)
                    ->count();
            });
        $pay_percent = $all_order ? $payed_order/$all_order : 0;
        $pay_percent = sprintf("%.02f",$pay_percent * 100);
        //新用户订单
        $data['newer_order'] = SysTotalModel::getValueBy('pay-account-new');

        //今日金币消耗
        $data['today_gold_pay'] = cached('now:consume:gold')->expired(3600)->fetch(function () {
            return UsersCoinrecordModel::where('type', 'expend')->where('addtime', '>=',
                $this->today_start)->sum('totalcoin');
        });

        //今日视频上传/通过
        $data['total_today_mv'] = cached('now:mv:up')->expired(3600)->fetch(function () {
            return MvSubmitModel::where('created_at', '>', $this->today_start)->count('id');
        });
        $data['total_today_mv_pass'] = cached('now:mv:pass')->expired(3600)->fetch(function () {
            return MvModel::where('status', '=', MvModel::STAT_CALLBACK_DONE)
                ->where('created_at', '>', $this->today_start)
                ->where('refresh_at', '>', $this->today_start)->count('id');
        });
        $data['total_today_mv'] += $data['total_today_mv_pass'];

        // 砖石视频
        $data['gold_mv_buy_num'] = cached('now:mvpay:num')->expired(3600)->fetch(function () {
            return MvPayModel::where('created_at', '>', date('Y-m-d 00:00:00', TIMESTAMP))->count('id');
        });
        $data['gold_mv_buy'] = cached('now:mvpay')->expired(3600)->fetch(function () {
            return MvPayModel::where('created_at', '>', date('Y-m-d 00:00:00', TIMESTAMP))->sum('coins');
        });

        //落地页总访问
        $data['welcome'] =  SysTotalModel::getValueBy('welcome');
        //渠道访问
        $data['channel_welcome'] =  SysTotalModel::getValueBy('channel:welcome');
        //Android下载量
        $data['down_and'] =  SysTotalModel::getValueBy('and:download');
        //pwa下载量
        $data['down_pwa'] =  SysTotalModel::getValueBy('pwa:download');
        //总下载量
        $data['down_total'] =  $data['down_and'] + $data['down_pwa'];
        $downRate = $data['welcome'] == 0 ? 0 : sprintf("%.02f",($data['down_total'] / $data['welcome']) * 100);

        $keep_1day = SysTotalModel::getValueBy('keep:1day');
        $keep_3day = SysTotalModel::getValueBy('keep:3day');
        $keep_7day = SysTotalModel::getValueBy('keep:7day');
        $ckeep_1day = SysTotalModel::getValueBy('ckeep:1day');
        $ckeep_3day = SysTotalModel::getValueBy('ckeep:3day');
        $ckeep_7day = SysTotalModel::getValueBy('ckeep:7day');

        //格式化数据
//        $data = array_map(function ($v){
//            return $this->htdiv($v);
//        },$data);

        return  [
            ['name' => '总留存(1天/3天/7天)', 'number' => sprintf("%d/%d/%d",$keep_1day,$keep_3day,$keep_7day)],
            ['name' => '渠道留存(1天/3天/7天)', 'number' => sprintf("%d/%d/%d",$ckeep_1day,$ckeep_3day,$ckeep_7day)],
            ['name' => '今日留存', 'number' => $data['session']],
            ['name' => '今日注册', 'number' => $data['reg_total']],
            ['name' => '今日注册(安卓/PWA)', 'number' => sprintf("%d/%d",$data['reg_and'],$data['reg_pwa'])],
            ['name' => '官方注册/渠道注册', 'number' => $data['reg_total'] - $data['channel_reg']. '/' . $data['channel_reg']],
            ['name' => '今日活跃', 'number' => $data['active_total']],
            ['name' => '今日活跃(安卓/PWA)', 'number' => sprintf("%d/%d",$data['active_and'],$data['active_pwa'])],
            ['name' => '今日VIP充值', 'number' => $data['pay_vip']],
            ['name' => '今日金币充值', 'number' => $data['pay_coin']],
            ['name' => '今日总充值', 'number' => $data['today_order_charge']],
            ['name' => '今日充值成功率', 'number' => $pay_percent . '%'],
            ['name' => '今日推广充值', 'number' => $data['today_sharechange']],
            ['name' => '新用户订单', 'number' => $data['newer_order'] ],
            ['name' => '落地页访问(总数/渠道)', 'number' => sprintf("%d/%d",$data['welcome'],$data['channel_welcome'])],
            ['name' => '总下载量', 'number' => $data['down_total'] ],
            ['name' => '下载量(安卓/PWA/IOS)', 'number' => sprintf("%d/%d/%d",$data['down_and'],$data['down_pwa'], 0)],
            ['name' => '点击率', 'number' => $downRate.'%'],
            ['name' => '今日金币消耗', 'number' => $data['today_gold_pay']],
            ['name' => '今日视频 | 通过', 'number' => $data['total_today_mv']  . ' | ' . $data['total_today_mv_pass'] ],
            ['name' => '视频购买 | 视频金币', 'number' => $data['gold_mv_buy_num']  . ' | ' . $data['gold_mv_buy'] ],
        ];
    }

    public function htdiv($dividend,$divisor = HT_JE_BEI)
    {
        if ($dividend == 0) {
            return 0;
        }

        return $dividend / $divisor;
    }

    public function ajaxConsoleTotalAction()
    {
        $total = redis()->getWithSerialize('backendCount');
        if (!$total) {
            $total = $this->_getPanelTotal();
            redis()->setWithSerialize('backendCount', $total, self::$backendCountRedisExperid);
        }
        $str = '';
        if ($total) {
            foreach ($total as $k => $v) {
                $str .= <<<LI
<li class="layui-col-xs2">
    <a  class="layadmin-backlog-body">
        <h3>{$v['name']}</h3>
        <p><cite>{$v['number']}</cite></p>
    </a>
</li>
LI;
            }
        }
        echo $str;
    }

    /**
     * 曲线统计绘制表数据
     * $legendData; 表头过滤title
     * $category;轴坐标点
     * $seriesData;
     * @return bool
     */
    public function chartAjaxAction()
    {
        $legendChart = new \tools\LegendChart("最近15天数据");
        $category = [];
        for ($i = 15; $i > 0; $i--) {
            $category[] = date('Ymd', strtotime("-$i days"));
        }
        $data = cached('blued/admin/char-ajax-'.date('d'))
            ->expired(3600)
            ->serializerJSON()
            ->fetch(function () use ($category) {
                return \DailyStatModel::whereIn('day', $category)
                    ->orderBy('day')
                    ->get()
                    ->map(function (\DailyStatModel $daily) {
                        return $daily->getAttributes();
                    });
            });
        $all = \DailyStatModel::makeCollect($data);
        /** @var DailyStatModel $item */
        foreach ($all as $item){
            $legendChart->addLine('日新增',$item->day,$item->user);
            $legendChart->addLine('日邀请',$item->day,$item->tui_user);
            $legendChart->addLine('日活跃',$item->day,$item->activity);
            $legendChart->addLine('日充值',$item->day,sprintf("%.2f",$this->htdiv($item->charge)));
            $legendChart->addLine('VIP充值',$item->day,sprintf("%.2f",$this->htdiv($item->vip_charge)));
        }

        return $this->ajaxSuccess($legendChart);
    }

    /**
     * 获取对应的model名称
     * @return string
     * @author xiongba
     * @date 2019-11-04 17:20:15
     */
    protected function getModelClass(): string
    {
        return '';
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2019-11-04 17:19:41
     */
    protected function getPkName(): string
    {
        return '';
    }

    /**
     * 定义数据操作的表主键名称
     * @return string
     * @author xiongba
     * @date 2019-11-04 17:19:41
     */
    protected function getLogDesc(): string
    {
        return '';
    }

}