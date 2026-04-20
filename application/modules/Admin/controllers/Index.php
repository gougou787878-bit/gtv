<?php

use service\AgentChartService;
use service\ChartService;
use tools\RedisService;


/**
 * 主页,常用操作
 * Class IndexController
 */
class IndexController extends AdminController
{

    public $today_start;
    //曲线统计缓冲过期控制
    static $backendChartRedisExperid = 900;
    //面板控制缓存
    static $backendPannelRedisExperid = 360;

    public function init()
    {
        parent::init();
        $this->today_start = strtotime(date('Y-m-d 00:00:00', TIMESTAMP));
    }

    public function homeAction()
    {
        if($this->getUser()->role_id == 1){
            //return $this->getView()->display('statics/index.phtml');
            return $this->getView()->display('statics/indexnew.phtml');
        }
        echo $this->getUser()->username.",登陆".ADMIN_TITLE."后台,".date("Y-m-d H:i");
    }

    private function _getPanelData()
    {
        // 今日注册
        $todayReg = cached('now:reg')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MemberModel::where('regdate', '>=', $this->today_start)->count();
        });

        $todayRegNoInvite = cached('now:reg:invite')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MemberModel::where('regdate', '>=', $this->today_start)
                ->where('invited_by', '=', 0)->count();
        });
        $todayAffOpen = cached('now:aff:open')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return AffOpenLogModel::query()->where('created_at', '>=', $this->today_start)->count('id');
        });

        //今日活跃
        $todayActive = cached('now:user:active')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MemberLogModel::where('lastactivity', '>=', $this->today_start)->count('id');
        });

        //今日金币消耗
        $todayGoldPay = cached('now:consume:gold')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return UsersCoinrecordModel::where('type', 'expend')->where('addtime', '>=',
                $this->today_start)->sum('totalcoin');
        });
        $todaySharechange = cached('now:order:tui')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return OrdersModel::where('status', 3)
                    ->where('updated_at', '>=', $this->today_start)
                    ->whereNotIn('build_id', ['', '0'])->sum('pay_amount') / 100;
        });
        $todayOrderCharge = cached('now:order:charge')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return OrdersModel::where('status', 3)->where('updated_at', '>=',
                    $this->today_start)->sum('pay_amount') / 100;
        });


        $totalTodayMv = cached('now:mv:up')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MvSubmitModel::where('created_at', '>', $this->today_start)->count('id');
        });
        $totalTodayMvPass = cached('now:mv:pass')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MvModel::where('status', '=', MvModel::STAT_CALLBACK_DONE)
                ->where('created_at', '>', $this->today_start)
                ->where('refresh_at', '>', $this->today_start)->count('id');
        });
        $totalTodayMv += $totalTodayMvPass;


        // 砖石视频
        $goldMvBuyNum = cached('now:mvpay:num')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MvPayModel::where('created_at', '>', date('Y-m-d 00:00:00', TIMESTAMP))->count('id');
        });
        $goldMvBuy = cached('now:mvpay')->expired(self::$backendPannelRedisExperid)->fetch(function () {
            return MvPayModel::where('created_at', '>', date('Y-m-d 00:00:00', TIMESTAMP))->sum('coins');
        });
        //推广独立ip 来源
        $tuiIp = cached('t:ip')->expired(self::$backendPannelRedisExperid)->fetch(function (){
            $d = AffOpenLogModel::selectRaw('DISTINCT ip')->where('created_at', '>=', $this->today_start)->get();
            return  collect($d)->count();
        });
        $totalData = [
            ['name' => '今日新增', 'number' => $todayReg ],
            ['name' => '今日活跃', 'number' => $todayActive],
            ['name'   => '（官方|渠道）来源', 'number' => $todayRegNoInvite . ' | ' . ($todayReg - $todayRegNoInvite)],
            ['name' => '官网推广', 'number' => $todayAffOpen ],
            ['name' => '官网推广IP', 'number' => $tuiIp],
            ['name' => '今日推广总额', 'number' => $todaySharechange],
            ['name' => '今日交易额', 'number' => $todayOrderCharge],
            ['name' => '今日金币消耗', 'number' => $todayGoldPay],
            ['name' => '今日视频 | 通过', 'number' => $totalTodayMv . ' | ' . $totalTodayMvPass],
            ['name' => '视频购买 | 视频金币', 'number' => $goldMvBuyNum . ' | ' . $goldMvBuy],

        ];
        return $totalData;
    }

    /**
     *面板数据ajax
     */
    public function panelAjaxAction()
    {
        set_time_limit(300);
        $result = $this->_getPanelData();
        $str = '';
        if ($result) {
            foreach ($result as $item) {
                $number = $item['number'];
                $str .= <<<DIV
<div class="layui-col-xs2 layadmin-backlog-body">
        <h3>{$item['name']}</h3>
        <p><cite>{$item['number']}</cite></p>
</div>
DIV;
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
    /*public function chartAjaxAction()
    {
        set_time_limit(300);
        $staticDataMethodList = [
                [ChartService::class, 'chartNewUser'], //日新增
                [ChartService::class, 'chartInviteNewUser'], //日邀请
                [ChartService::class, 'chartActiveUser'], //日活跃
                [ChartService::class, 'chartOrderCharge'],//日充值
                [ChartService::class, 'chartOrderChargeNum'],//日订单（笔数）
                [ChartService::class, 'chartUserCoinCharge'],//金币交易额
                //[ChartService::class, 'chartUserCoinChargeIncome'],//冲钻交易
                //新增曲线在这写 喋喋哒哒
            ];
        $dataResultList = collect($staticDataMethodList)->map(function ($item) {
            return cached("chart:tj:{$item[1]}")->expired(self::$backendChartRedisExperid)->usingFuck()
                ->serializerJSON()->fetch(function () use ($item) {
                return call_user_func($item);
            });
        })->values()->toArray();
        $category = ChartService::getDateList();
        $legendData = array_column($dataResultList, 'name');
        $seriesData = array_map(function ($item) {
            return [
                'data'   => array_map(function ($v) {
                    return $v / HT_JE_BEI;
                }, array_values($item['data'])),
                'type'   => 'line',
                'smooth' => true,
                'name'   => $item['name'],
            ];
        }, $dataResultList);

        return $this->showJson(['legendData' => $legendData, 'category' => $category, 'seriesData' => $seriesData]);
    }*/

    public function chartAjaxAction()
    {
        set_time_limit(300);
        $dataResultList = cached(__FUNCTION__)
            ->serializerJSON()
            ->expired(7200)
            ->fetch(function () {
                return ChartService::getDailyData();
            });
        //print_r($dataResultList);die;
        $category = array_keys($dataResultList['user']['data']);
        $legendData = array_column($dataResultList, 'name');
        $seriesData = array_map(function ($item) {
            return [
                'data'   => array_map(function ($v) {
                    return $v ;
                    return $v / HT_JE_BEI;
                }, array_values($item['data'])),
                'type'   => 'line',
                'smooth' => true,
                'name'   => $item['name'],
            ];
        }, $dataResultList);
        return $this->showJson([
            'legendData' => $legendData,
            'category'   => $category,
            'seriesData' => collect($seriesData)->values()->toArray()
        ]);
    }

    public function indexAction()
    {
        $user = $this->getUser();
        try {
            $ruleId = $this->getRule($user->uid);
        } catch (\exception\ErrorPageException $e) {
            $ruleId = [];
        }
        if (empty($ruleId)) {
            $menu = [];
        } else {
            $menu = AdminMenuModel::getTreeAll(AdminMenuModel::STATUS_YES, $ruleId);
        }
        $this->getView()
            ->display('layout/index.phtml', [
                'menu' => $menu
            ]);
    }


    public function showAffAction()
    {//推广码转aff
        $str = $this->get['num'] ?? '';
        if ($str) {
            $aff = generate_code(trim($str));
            $url = getShareURL() . '/af/' . $aff;
            return $this->showJson(['aff' => $aff, 'url' => $url]);
        }
    }

    public function showNumAction()
    {//aff转推广码
        $str = $this->get['aff'] ?? '';
        if ($str) {
            return $this->showJson(get_num(trim($str)));
        }
    }

    public function affAction()
    {
        $this->getView()
            ->display('aff/page.phtml');
    }


    public function configAction()
    {
        $this->getView()
            ->assign('config', getCaches('ks_config'))
            ->display('aff/config.phtml');
    }

    /**
     * 保存更新系统公告通知
     * @return bool|void
     */
    public function doEditConfigAction()
    {
        $post = [
            'maintain_switch'   => $this->post['maintain_switch'],
            'maintain_tips'     => $this->post['maintain_tips'],
            'maintain_tips_ios' => $this->post['maintain_tips_ios'],
        ];
        setCaches('ks_config', $post);
        if ($this->post['is_sync']) {
            MessageModel::createMessage(
                MessageModel::SYSTEM_MSG_UUID,
                '系统公告',
                $post['maintain_tips'],
                MessageModel::TYPE_BULLETION,
                $post['maintain_switch']
            );
        }
        return $this->showJson('修改成功', 1);
    }

    public function previewAction()
    {
        $this->getView()
            ->display('statics/play.phtml');
    }

    /**
     * 代理曲线统计绘制表数据
     * $legendData; 表头过滤title
     * $category;轴坐标点
     * $seriesData;
     * @return bool
     */
    public function agentChartAjaxAction()
    {
        $channel = $_GET['c'] ?? '';
        $key = __FUNCTION__ . $channel;
        set_time_limit(300);
        $dataResultList = RedisService::get($key);
        if (!$dataResultList) {
            $dataResultList[] = AgentChartService::chartNewUser($channel);
            $dataResultList[] = AgentChartService::chartOrderVIPCharge($channel);
            $dataResultList[] = AgentChartService::chartOrderCharge($channel);
            $dataResultList && RedisService::set($key, $dataResultList, self::$backendChartRedisExperid);
        }
        /*echo "<pre>";
        print_r($dataResultList);*/
        $category = AgentChartService::getDateList();
        $legendData = array_column($dataResultList, 'name');
        $seriesData = array_map(function ($item) {
            return [
                'data'   => array_map(function ($v) {
                    return $v;
                    return $v / HT_JE_BEI;
                }, array_values($item['data'])),
                'type'   => 'line',
                'smooth' => true,
                'name'   => $item['name'],
            ];
        }, $dataResultList);
        return $this->showJson(['legendData' => $legendData, 'category' => $category, 'seriesData' => $seriesData]);
    }
}
