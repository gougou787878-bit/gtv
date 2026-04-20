<?php

use service\AppCenterService;
use service\EventTrackerService;
use Tbold\Serv\biz\BizDown;
use const http\Client\Curl\VERSIONS;


/**
 * H5页面父类
 * Class IndexController
 */
class IndexController extends \Yaf\Controller_Abstract
{
    protected $view;
    public $config;

    /**
     * 初始化数据
     */
    public function init()
    {
        $this->config = \Yaf\Registry::get('config');
        $this->view = $this->getView();

        if (ini_get('yaf.environ') == 'product' && $_SERVER['HTTP_HOST']) {
            if (stripos($_SERVER['HTTP_HOST'], 'staff') !== false) {
                header("HTTP/1.1 301 Moved Permanently");
                $url = getShareURL();
                header('Location: ' . $url);
            }
        }
        //header('Location: https://www.bluemv.net/');
    }

    /**
     * 纯json发送
     * @param $data
     * @return mixed
     */
    public function ej($data)
    {
        @header('Content-Type: application/json');
        $returnData = $data;
        return $this->getResponse()->setBody(json_encode($returnData, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 官网首页
     * http://{host}/index.php/chan/k1000/DGFH
     * http://{host}/index.php/af/DGFH
     * http://{host}/index.php/?code=DGFH
     */
    public function indexAction()
    {

        //DailyStatModel::addStat();
        \SysTotalModel::incrBy('welcome');
        $parmas = $this->getRequest()->getParams();
        $tg = setting('official.group', config('official.group'));//官方默认
        $code = $parmas['code'] ?? '';//推广码

        $channel = $parmas['chan'] ?? '';//推广渠道
        if (!$code && isset($_GET['code'])) {
            $code = trim($_GET['code']);
        }
        $isOpenWebApp = 0;
        //渠道
        if ($channel) {
            $channelUserData = AgentsUserModel::verifyChan($channel);
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if($channelUserData){
                $biz = \Tbold\Serv\biz\BizWebVisit::make([]);
                $biz->setAgentChannel($channel);
                $biz->setAgentId($channelUserData['root_id']);
                $biz->setAgentName($channelUserData['username']);
                $biz->setCreatedAt(TIMESTAMP);
                $biz->setUrl($referer);
                //$biz->push();
                setcookie('cc_info',
                    json_encode(['referer' => $referer, 'channel' => $channel]),
                    [
                        'expires'  => time() + 31536000,
                        'path'     => '/',
                        'domain'   => parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST),
                        'secure'   => false,
                        'httponly' => false,
                    ]
                );
                $isOpenWebApp = $channelUserData['web_stat'];
                SysTotalModel::incrBy('channel:welcome');
            }else{
                $channel = '';
            }
        }

        $day = (int)date('H') % 5 + 1;
        $site = sprintf("https://w%d.%s",$day,web_site('gtv'));
        $web_aff_code = $isOpenWebApp ? $code : '';
        $webAppUrl = "{$site}/?aff_code={$web_aff_code}";
        $buffer['web_app_url'] = $webAppUrl;

        $aff = '';
        if ($code) {
            $aff = get_num($code);
            $this->writeOpenLog($aff, $channel);
        }
        $site = getShareURL();
        $buffer['share'] = $aff ? "gtv_aff:" . $code : 'gtv_aff:';
        $buffer['m_url'] = $code ? $site . "/af/" . $code : $site;
        $buffer['group'] = $tg;
        $buffer['channel'] = $channel;
        if (false  && $this->isIOSDevice()) {
            $this->view->assign(['aff_code'=>$code]);
            return $this->display('dmd');
            //header('location: /index.php?a=pwa&' . http_build_query(['aff_code' => $code] , '' , '&'));
            //return;
        }
        $android = VersionModel::getleastVersion(VersionModel::TYPE_ANDROID, VersionModel::STATUS_SUCCESS, '',
            VersionModel::PLAT_FORM_POLO);
        $buffer['version_and'] = '';
        $android && $buffer['version_and'] = $android['apk'];
        $buffer['version_ios_pwa'] = "/index.php?m=index&a=pwa&aff_code={$code}";
        $buffer['shangwu'] = [
            //['name'=>'商务TGC','link'=>'https://t.me/moyi6'],
            ['name'=>'商务外事','link'=>' https://t.me/xiaolanshangwu'],
            ['name'=>'产品外事','link'=>'https://t.me/guangxi1112']
        ];

        $product_id = AppCenterService::PRODUCT_ID;
        $buffer['daohang91'] = "https://site.91url.info";
        if($product_id)
        {
            $buffer['daohang91'] = "https://site.91url.info/?product_id={$product_id}&channel={$channel}";
        }
        $buffer['show_daohang'] = in_array($channel, BLACK_CHANNEL) ? 0 : 1;//特殊渠道不显示导航
        $buffer['show_daohang'] = 0;
        $buffer['is_ios'] = $this->isIOSDevice();
        $this->view->assign($buffer);
        return $this->display('index0410');
        //return $this->display('index');
        //$content = $this->render('index');
        $content = $this->render('index0410');
        $base64 = base64_encode($content);
        echo <<<HTML
<script>Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",decode:function(input){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=this._keyStr.indexOf(input.charAt(i++));enc2=this._keyStr.indexOf(input.charAt(i++));enc3=this._keyStr.indexOf(input.charAt(i++));enc4=this._keyStr.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!=64){output=output+String.fromCharCode(chr2)}if(enc4!=64){output=output+String.fromCharCode(chr3)}}output=Base64._utf8_decode(output);return output},_utf8_decode:function(utftext){var string="";var i=0;var c=c1=c2=0;while(i<utftext.length){c=utftext.charCodeAt(i);if(c<128){string+=String.fromCharCode(c);i++}else if((c>191)&&(c<224)){c2=utftext.charCodeAt(i+1);string+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2}else{c2=utftext.charCodeAt(i+1);c3=utftext.charCodeAt(i+2);string+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3}}return string}};
    document.write(Base64.decode("$base64"));</script>
<noscript>error ..</noscript>
HTML;
    }

    function isIOSDevice(){
        $agent = $_SERVER['HTTP_USER_AGENT']??'';
        if(stripos($agent,'iphone')!==false || stripos($agent,'ipad')!==false){
            return true;
        }
        return false;
    }
    function isSafariDevice(){
        return true;
        if($this->isIOSDevice()){
            $agent = $_SERVER['HTTP_USER_AGENT']??'';
            if(stripos($agent,'safari')!==false && stripos($agent,'chrome')==false){
                return true;
            }
        }
        return false;
    }

    protected function writeOpenLog($aff, $channel)
    {
        //return ;//AffOpenLogModel  无用
        if (USER_IP == 'unknown') {
            return;
        }
        $query = cached('aff:log:' . $aff)
            ->setSaveEmpty(true)
            ->expired(600)
            ->fetch(function () use ($aff) {
            return MemberModel::query()->where('aff', $aff)
                ->first(['uid']);
        });
        if (!empty($query)) {
            $uniqueStr = $aff . USER_IP . $channel . ($_SERVER['HTTP_USER_AGENT'] ?? '');
            $uniqueKey = sprintf("aff:log:unique:%s", md5($uniqueStr));
            if (!redis()->exists($uniqueKey)) {
                AffOpenLogModel::create([
                    'aff'        => $aff,
                    'ip'         => USER_IP,
                    'ua'         => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'channel'    => $channel,
                    'created_at' => TIMESTAMP
                ]);
                redis()->set($uniqueKey, 1, 600);

                //官网推广
                \SysTotalModel::incrBy('now:aff:open');
                //官网IP来源
                $userIp = USER_IP;
                $date = date('Y-m-d');
                $keyNorepeatIp = "{$date}-ip-norepeat";
                if (redis()->sAdd($keyNorepeatIp, $userIp)) {
                    \SysTotalModel::incrBy('now:aff:open:ip:norepeat');
                    if (redis()->ttl($keyNorepeatIp) == -1){
                        redis()->expireAt($keyNorepeatIp,strtotime('+1 day'));
                    }
                }
            }
        }
    }

    public function pwaAction(){
        $aff_code = $_GET['aff_code']??'';
        $this->view->assign(['aff_code'=>$aff_code]);
        $this->display('dmd');
        //$this->display('pwa');
    }
    public function mobileconfigAction(){
        if(!$this->isSafariDevice()){
            echo "请使用ios设备自带safari浏览器打开~";exit();
        }
        $aff_code = $_GET['aff_code']??'';
        if($aff_code){
            //pwa下载统计
            $this->reportData( BizDown::TYPE_IOS);
        }
        SysTotalModel::incrBy('pwa:download');
        //pwa解析ip： 103.3.63.118
        $main_domain = 'gtv005.co';//主域名
        $main_domain = 'gtv009.fun';//主域名
        $main_domain = 'gtv010.fun';//主域名
        $main_domain = 'gtv010.biz';//主域名
        $day = (int)date('H') % 5 + 1;
//        if ($day == 4){
//            $day_arr = [1, 2, 3, 5];
//            $day = $day_arr[array_rand($day_arr)];
//        }
        $site = sprintf("https://p%d.%s",$day,pwa_site('gtv'));
        $pwa_url = "{$site}/?aff_code={$aff_code}";
        
        //$pwa_url = "http://install.gtav.info/?aff_code={$aff_code}";
        $mobileconfig_file = APP_PATH.'/script/itms-services-gtv.mobileconfig';
        $string = file_get_contents($mobileconfig_file);
        $string = str_replace("{{PWA}}",$pwa_url,$string);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=itms-services-blue.mobileconfig'); //文件名
        header("Content-Type: text/xml");
        ob_clean();
        flush();
        echo $string;
        exit();
    }

    protected function reportData($type)
    {
        return;
        if (!isset($_COOKIE['cc_info']) || !$_COOKIE['cc_info']) {
            return;
        }
        $info = @json_decode($_COOKIE['cc_info']);
        if (!$info || !is_object($info)) {
            return;
        }
        BizDown::make([
            'url'           => $info->referer,
            'type'          => $type,
            'ip'            => USER_IP,
            'agent_channel' => $info->channel,
            'created_at'    => TIMESTAMP
        ])->push();

    }
    public function statAction()
    {
        SysTotalModel::incrBy('and:download');
        //安卓下载统计
        $this->reportData(BizDown::TYPE_ANDROID);
    }

    public function ztAction(){
        $token = $_GET['token'];
        test_assert($token, '请在APP内打开页面');
        $this->view->assign('token', $token);
        $this->display('zt');
    }

    public function uu_msgAction(){
        $data = [
            'has_auth'  => 0,
            'tips'      => setting('kd_vip_tips', ''),
            'tg'        => '',
            'pt'        => '',
        ];
        $token = $_GET['token'] ?? '';
        if (empty($token)){
            return $this->showJson($data);
        }

        $uuid = LibCryptPwa::decrypt($token, '');
        if (!$uuid){
            return $this->showJson($data);
        }

        $redisKey = 'user:' . $uuid;
        $attributes = cached($redisKey)->fetchJson(function () use ($uuid) {
            return MemberModel::findByUuid($uuid)->toArray();
        });
        $member = MemberModel::makeOnce($attributes);
        $allow_vip = [
            MemberModel::VIP_LEVEL_LONG,
        ];
        //$kd_active_time = setting('kd_active_time', '2025-06-06 11:00:00');
        $kd_active_vip_level = explode(',', setting('kd_active_vip_level'));
        $kd_active_vip_level = array_filter($kd_active_vip_level);
        $kd_active_vip_level = $kd_active_vip_level ?: $allow_vip;

        $auth = in_array($member->vip_level, $kd_active_vip_level) && $member->expired_at > TIMESTAMP;
        if ($auth){
            $data['has_auth'] = 1;
            $data['tips'] = '';
            $data['tg'] = setting('kd_tg_group', '');
            $data['pt'] = setting('kd_pt_group', '');
            return $this->showJson($data);
        }
        return $this->showJson($data);
    }

    public function showJson($data, $status = 200, $msg = null)
    {
        $data = [
            'data'   => $data,
            'msg'    => $msg,
            'status' => $status,
        ];
        $response = $this->getResponse();
        $response->setBody(json_encode($data));
        $response->setHeader('content-Type', 'application/json', true);
        return $response;
    }

    // 不操作任何数据 只做查询
    public function api_indexAction()
    {
        SysTotalModel::incrBy('welcome');//总访问
        $url = $_GET['url'] ?? '';
        //匹配链接
        //$url = "https://aa8eb.rmmwkyxip.com/chan/xb1792/aE9WG";
        //$url = "https://aa8eb.rmmwkyxip.com/af/bERr";
        //$url = "https://aa8eb.rmmwkyxip.com?channel_code=ug-unadsnew";
        //$url = "https://aa8eb.rmmwkyxip.com";
        // 正则表达式模式，用于匹配 URL 中的两个部分
        $p1 = '/\/chan\/([A-Za-z0-9]+)\/([A-Za-z0-9]+)/';
        $p2 = '/\/af\/([A-Za-z0-9]+)/';
        // 使用 preg_match 执行匹配
        $chan = '';
        $code = '';
        if (preg_match($p1, $url, $matches)) {
            $chan = $matches[1];
            $code = $matches[2];
        } else {
            $parsedUrl = parse_url($url);
            // 从查询字符串中提取参数
            parse_str($parsedUrl['query'] ?? '', $queryParams);
            // 输出 channel_code 和它的值
            if (isset($queryParams['channel_code'])) {
                $channel_code = $queryParams['channel_code'];
                $arr = AgentsUserModel::getChannelByUsername($channel_code);
                if (!empty($arr)){
                    $chan = $arr['chan'];
                    $code = $arr['invite_code'];
                }
            } else {
                if (preg_match($p2, $url, $matches)) {
                    $code = $matches[1];
                }
            }
        }
        //渠道
        list($is_download, $version_and, $special_and) = VersionModel::get_android_version($code);
        if ($chan && $code) {
            $exist = AgentsUserModel::verifyChanAff($code);
            if($exist){
                SysTotalModel::incrBy('channel:welcome');
                SysTotalModel::incrBy("channel:visit:{$chan}");
                $aff = (int)get_num($code);
                SysTotalModel::incrBy("channel:visit:code:{$aff}");

                $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
                if ($is_download == 1 && str_contains($ua, 'android')) {
                    SysTotalModel::incrBy('and:download');
                }
            }
        }
        if ($code) {
            //ip邀请
            $aff = get_num($code);
            $this->writeOpenLog($aff, $chan);
        }
        $rs['is_download'] = $is_download;
        $rs['version_and'] = $version_and;
        $rs['special_and'] = $special_and;
        //$rs['share'] = $code ? "gtv_aff:" . $code : 'gtv_aff:';
        $rs['share'] = "gtv_aff=" . $code;
        $rs['aff_code'] = $code;
        $rs['group'] = setting('official.group', 'https://t.me/gtvlife');//TG用户交流群
        $rs['shangwu'] = setting('sw_tg', 'https://t.me/xiaolanshangwu');
        $rs['store_url'] = '';
        header('Content-type: application/json');
        exit(json_encode($rs));
    }

    public function ios_indexAction(){
        if(!$this->isSafariDevice()){
            echo "请使用ios设备自带safari浏览器打开~";exit();
        }
        $aff_code = $_GET['aff_code']??'';
        $trace_id = trim($_GET['trace_id'] ?? '');
        SysTotalModel::incrBy('pwa:download');
        $day = (int)date('H') % 5 + 1;
        $site = sprintf("https://p%d.%s",$day,pwa_site('gtv'));
        //$pwa_url = "{$site}/?aff_code={$aff_code}";
        $pwa_url = sprintf("$site/?aff_code=%s&amp;trace_id=%s", $aff_code, $trace_id);

        $mobileconfig_file = APP_PATH.'/script/itms-services-gtv.mobileconfig';
        $string = file_get_contents($mobileconfig_file);
        $string = str_replace("{{PWA}}",$pwa_url,$string);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=itms-services-blue.mobileconfig'); //文件名
        header("Content-Type: text/xml");
        ob_clean();
        flush();
        echo $string;
        exit();
    }

    /**
     * 公司点击上报
     */
    public function reportAction(){
        try {
            $report = $_POST['report_json'];
            test_assert($report, '数据为空');
            $device = $this->getClientType();
            $service = new EventTrackerService($device, '','','','','',$_SERVER['HTTP_USER_AGENT'] ?? '');
            $report = htmlspecialchars_decode($report);
            $service->addTask(json_decode($report, true));
            return $this->showJson(['msg' => '上报成功']);
        } catch (Throwable $e) {
            return $this->showJson(['msg' => '数据异常']);
        }
    }

    public function getClientType() {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (strpos($ua, 'android') !== false) {
            return MemberModel::TYPE_ANDROID;
        }

        // iOS 设备包括 iPhone / iPad / iPod
        if (strpos($ua, 'iphone') !== false || strpos($ua, 'ipad') !== false || strpos($ua, 'ipod') !== false) {
            return MemberModel::TYPE_PWA;
        }

        return 'PC';
    }

    public function report_eventAction()
    {
        try {
            $json = file_get_contents('php://input');
            $params = json_decode($json, true);
            test_assert($params, '数据为空');
            $key = EventTrackerService::EVENT_TRACKING_REPORT_KEY;
            test_assert(is_array($params), 'json异常');
            foreach ($params as &$task) {
                if (!isset($task['ip']) || !$task['ip']) {
                    $ip = USER_IP;
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
                        $ip = '';
                    }
                    $task['ip'] = $ip;
                }
                redis()->rPush($key, json_encode($task));
            }
            $data = [
                'data'   => '',
                'msg'    => '获取成功',
                'status' => 1
            ];
            exit(json_encode($data));
        } catch (Throwable $e) {
            $data = [
                'data'   => '',
                'msg'    => '数据异常',
                'status' => 0
            ];
            exit(json_encode($data));
        }
    }

    public function report_encAction()
    {
        $url = trim($_GET['url'] ?? '');
        list($code, $chan, $username) = $this->parser_url($url);
        // report_enc
        $is_enc = 1;
        $url = $is_enc ? replace_share('https://{share.ggsb}/api/eventTracking/batchReport.json') : '/index.php?m=index&a=report_event';
        $data = [
            'is_encryption'       => $is_enc,
            'app_name'            => 'GTV',
            // 下发KEY/IV/SIGN 加密使用方法aes-128-cbc 签名算法通用
            'encryption_key'      => cfg_get('dx.ads_report.encryption_key'),
            'encryption_iv'       => cfg_get('dx.ads_report.encryption_iv'),
            'sign_key'            => cfg_get('dx.ads_report.sign_key'),
            'channel'             => $username,
            // 这是CF-RAY-XF的请求头
            'authentication_key'  => cfg_get('dx.ads_report.authentication_key'),
            'authentication_time' => cfg_get('dx.ads_report.authentication_time'),
            'click_app_id'        => config('click.report.app_id'),
            'click_transit_path'  => $url,
        ];
        $raw = openssl_encrypt(json_encode($data), 'aes-128-cbc', 'ad9972b0430a186e', 0, 'f18ae198ecd2efa0');
        exit($raw);
    }

    private function parser_url($url){
        $p1 = '/\/chan\/([A-Za-z0-9]+)\/([A-Za-z0-9]+)/';
        $p2 = '/\/af\/([A-Za-z0-9]+)/';
        // 使用 preg_match 执行匹配
        $chan = '';
        $code = '';
        $username = '';
        if (preg_match($p1, $url, $matches)) {
            $chan = $matches[1];
            $code = $matches[2];
        } else {
            $parsedUrl = parse_url($url);
            // 从查询字符串中提取参数
            parse_str($parsedUrl['query'] ?? '', $queryParams);
            // 输出 channel_code 和它的值
            if (isset($queryParams['channel_code'])) {
                $channel_code = $queryParams['channel_code'];
                $arr = AgentsUserModel::getChannelByUsernameYac($channel_code);
                if (!empty($arr)){
                    $chan = $arr['chan'];
                    $code = $arr['invite_code'];
                    $username = $arr['username'];
                }
            } elseif (isset($queryParams['aff_code'])){
                $aff_code = $queryParams['aff_code'];
                $aff = get_num($aff_code);
                $arr = AgentsUserModel::getChannelByAffYac($aff);
                if (!empty($arr)){
                    $chan = $arr['chan'];
                    $code = $arr['invite_code'];
                    $username = $arr['username'];
                }
            }else {
                if (preg_match($p2, $url, $matches)) {
                    $code = $matches[1];
                }
            }
        }

        if ($code && !$username){
            $aff = (int)get_num($code);
            $username = AgentsUserModel::getUsernameByAff($aff);
        }

        return [$code, $chan, $username];
    }
}