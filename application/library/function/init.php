<?php
defined('APP_TYPE_FLAG') or define('APP_TYPE_FLAG',1);//app入口
define('TIMESTAMP', time());  // 全局时间戳
define('USER_IP', client_ip()); // 用户IP
define('IP_LOCATION_KEY', 'ip:pos:' . substr(md5(client_ip()), 0, 16)); // 用户区域redis key
define('LAY_UI_STATIC', '/static/backend/');
define('HT_JE_BEI' , 100);
define('CHAT_SALT','ad!@#$)2722');
defined('APP_ENVIRON') or define('APP_ENVIRON', ini_get("yaf.environ"));
defined('ADMIN_TITLE') or define('ADMIN_TITLE', 'GTV');
defined('SHARE_TMP') or define('SHARE_TMP', 'GTV(GTVapp)专注于提供国内、日韩、欧美最新最全GV资源，原创、影视、经典同性等各种视频剧集尽在GTV！%s（因包含成人内容被微信、QQ屏蔽，请复制链接在浏览器中打开）');
defined('SYSTEM_ID') or define('SYSTEM_ID', 'gv');//gv
defined('SYNC_SMT_AV_URL') or define('SYNC_SMT_AV_URL', 'https://app.gtav.info/index.php?&m=mv&a=sync');//同步水蜜桃请求地址
defined('SYSTEM_NOTIFY_SLICE_URL_PRE') or define('SYSTEM_NOTIFY_SLICE_URL_PRE', 'https://app.gtav.info/index.php?&m=mv&a=prem3u8');
defined('SYSTEM_NOTIFY_SLICE_URL') or define('SYSTEM_NOTIFY_SLICE_URL', 'https://app.gtav.info/index.php?&m=mv&a=index');
defined('SYSTEM_NOTIFY_WITHDRAW_URL') or define('SYSTEM_NOTIFY_WITHDRAW_URL', 'https://app.gtav.info/index.php?&m=pay&a=notifywithraw');
define('ILLEGAL_ORG_VIDEO', '/watch8/ac7d812d297815adacf003b855d9ad36/ac7d812d297815adacf003b855d9ad36.m3u8');
define('ILLEGAL_ORG_IMG', '/new/xiao/20220517/2022051711574228306.png');
defined('SYSTEM_SHARE_LINK') or define('SYSTEM_SHARE_LINK', 'https://config.microservices.vip/2020090623125271421-share.json');

// 入口加解密
if(!APP_TYPE_FLAG){
    //errLog("initPre:".var_export($_POST,1));
}

if (!defined('API_CRYPT_KEY')){
    $_k1 = 'ljhlksslgkjfhlksuo8472rju6p2od03';
    $_s1 = 'kihfks3kjdhfksjh3kdjfs745dkslfh4';
    $keyData = [
        'v0' => [
            'key'  => $_k1,
            'sign' => $_s1,
        ],
        'v1' => [
            //'key'  => 'b3ecbc15b14caed41ec7d1659c91fba4',
            'key'  => '80f37c39878ad8e305d9a6bd7dca402c',
            //'sign' => 'b0b6786a26f0d4407b92b6fbdc353980',
            'sign' => '58e3e4610103ea92881d86a1dcf4e9e7',
        ],
        'v2' => [
            'key'  => 'hydo32pkdgbpq9kr92u6ur1ldxdtwanv',
            'sign' => 'ivkteikwaobfvbwe1r0vbqdppzqetu9k',
        ],
    ];
    $_ver = $_POST['_ver'] ?? 'v0';
    define('API_CRYPT_KEY', $keyData[$_ver]['key'] ?? $_k1) ;
    define('API_CRYPT_SIGN', $keyData[$_ver]['sign'] ?? $_s1) ;
    define('API_VER', $_ver) ;
}

$request = new Yaf\Request\Simple();
if (MODULE_NAME_TEST && !isset($_POST['crypt'])) {
    if (APP_TYPE_FLAG == 1) {
        if (false && API_VER != 'v1') {
            header("Status: 503 Service Unavailable");
            exit();
        }
        $crypt = new LibCrypt();
    } else {
        $crypt = new LibCryptPwa();
    }
    $_POST = $crypt->checkInputData($_POST);
}
if(APP_TYPE_FLAG){
    //errLog("init:".var_export([$_POST,$_SERVER],1));
}

if (isset($_POST['oauth_new_id']) && $_POST['oauth_new_id'] && strpos($_POST['oauth_new_id'], '00000000') === false) {
    //00000000-0000-0000-0000-000000000000  不含有的就替换
    $_POST['oauth_id'] = $_POST['oauth_new_id'];
}
$_POST = JAddSlashes($_POST);
$_GET = JAddSlashes($_GET);
$_COOKIE = JAddSlashes($_COOKIE);
$_REQUEST = JAddSlashes($_REQUEST);
$_f = false;
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if ($ua && stripos($ua, 'Dart') !== false) {
    $_f = true;
}
define("IS_FAKE_CLIENT",$_f);
defined('IS_NEW_VER') or define('IS_NEW_VER',version_compare($_POST['version']??'2.0.0', '1.2.0', '>='));//最新ver 2 密钥
//加密控制的域名
defined('NEW_PLAY_REPLACE_HOST_L') or define('NEW_PLAY_REPLACE_HOST_L', 'm3u8.aidouyin.me');
defined('NEW_PLAY_REPLACE_HOST') or define('NEW_PLAY_REPLACE_HOST', 'play.aidouyin.me');
//傻逼渠道 不要福利导航 不要应用中心
defined('BLACK_CHANNEL') or define('BLACK_CHANNEL', [
]);
defined('APP_ADS_APP') or define('APP_ADS_APP', 'https://xlan.bluemv.net/index.php?&m=activity&a=ads_app');//小蓝应用中心同步


//if (($_POST['oauth_type']??'') == 'android' && version_compare($_POST['version'], '1.2.0', '>=')) {
//    $__package_name__ = $_POST['__package_name__'] ?? '';
//    $__package_hash__ = $_POST['__package_hash__'] ?? '';
//    /*$real_hash = VersionModel::checkBound($__package_name__);
//    if ($real_hash && $real_hash == $__package_hash__) {
//        return true;
//    }*/
//    if(!$__package_name__ || !$__package_hash__){
//        header("Status: 503 Service Unavailable");
//        exit();
//    }
//}