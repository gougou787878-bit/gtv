<?php


use Illuminate\Database\Eloquent\Model;
use tools\RedisService;

/**
 * class VersionModel
 *
 * @property int $id
 * @property string $version 版本号
 * @property int $platform 0 菠萝 1 av
 * @property string $type 型号
 * @property string $apk 下载连接
 * @property string $tips 更新说明
 * @property string $bundle_id ios企业安装包id
 * @property int $must 0 不强制更新 1强制
 * @property int $created_at 创建时间
 * @property string $via 渠道判断
 * @property int $status 1 启用  2 停用
 * @property string $message 系统维护公告
 * @property int $mstatus 系统公告状态 0 没有 1通知 2禁用
 * @property int $from_id 更新起点
 * @property int $to_id 更新终点
 * @property string $sha256 sha256
 *
 * @author xiongba
 * @date 2021-04-28 11:44:51
 *
 * @mixin \Eloquent
 */
class VersionModel extends Model
{

    protected $table = "version";

    protected $primaryKey = 'id';

    protected $fillable = [
        'version',
        'platform',
        'type',
        'apk',
        'tips',
        'bundle_id',
        'must',
        'created_at',
        'via',
        'status',
        'message',
        'mstatus',
        'from_id',
        'to_id',
        'sha256'
    ];

    const PLAT_FORM_POLO = 0;
    const PLAT_FORM_AV = 1;
    const PLAT = [
        self::PLAT_FORM_POLO=>'菠萝',
        self::PLAT_FORM_AV=>'AV',
    ];
    protected $guarded = 'id';

    public $timestamps = false;

    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;

    const CHAN_TF = 'testflight';// tf 包
    const CHAN_PG = 'normal';// 企業簽  包
    const CHAN_PWA = 'pwa';// 企業簽  包

    const STATUS = [
        self::STATUS_SUCCESS => '启用',
        self::STATUS_FAIL    => '停用',
    ];

    const MUST_UPDATE = 1;
    const MUST_UPDATE_NOT = 0;
    const MUST = [
        self::MUST_UPDATE_NOT => '软更',
        self::MUST_UPDATE     => '强更',
    ];

    const TYPE_ANDROID = 'android';
    const TYPE_IOS = 'ios';

    const TYPE = [
        self::TYPE_ANDROID => '安卓',
        self::TYPE_IOS     => '苹果'
    ];
    const REDIS_VERSION_KEY = [
        'ios'     => 'version:ios',
        'android' => 'version:android',
    ];

    public function getApkAttribute()
    {
        $val = $this->attributes['apk'];
        if (str_ends_with($val, '.apk')) {
            $val = parse_url($val, PHP_URL_PATH);
            $val = TB_APP_DOWN_URL . $val;
        }
        return $val;
    }


    /**
     * @param $type
     * @param int $status
     * @param string $channel
     * @param string $platForm
     * @return self
     */
    static function getleastVersion($type, $status = self::STATUS_SUCCESS, $channel = '',$platForm = 0)
    {
        $key = 'ver:' . $type;
        $where['type'] = $type;
        $where['status'] = $status;
        //$where['platform'] = $platForm;
        if ($channel) {
            $key .= $channel;
            $where['via'] = $channel;
        }
        $version = cached($key)
            ->serializerPHP()
            ->expired(3600)
            ->fetch(function () use ($where) {
                $d = self::query()
                    ->select(['id','version','type','apk','tips','must','via','sha256'])
                    ->where($where)
                    ->orderByDesc('id')
                    ->first();
                if (is_null($d) && isset($where['via'])) {
                    unset($where['via']);
                    $d = self::query()
                        ->select(['id','version','type','apk','tips','must','via','sha256'])
                        ->where($where)
                        ->orderByDesc('id')
                        ->first();
                }
                return is_null($d) ? null : $d->toArray();
        });
        if ($version && VersionModel::TYPE_IOS == $type) {
            $text = $version['apk'];
            $position = \itbdw\Ip\IpLocation::getLocation(USER_IP);
            $province = $position['province'] ?? '';
            if(empty($province)){
                $province = '日本';
            }
            $flag_via = '[=]';//后台分割标识
            if (strpos($text, "\n") !== false) {
                $ary = explode("\n", $text);
                foreach ($ary as $item) {
                    $item = trim($item);
                    if (empty($item)) {
                        continue;
                    }
                    if (strpos($item, $flag_via) === false) {
                        $version['apk'] = trim($item);
                        break;
                    }
                    list($areaStr, $url) = explode($flag_via, $item);
                    $area = explode(',', $areaStr);
                    foreach ($area as $v) {
                        $v = trim($v);
                        if(empty($v)){
                            continue;
                        }
                        if ($province && strpos($v, $province) !== false) {
                            $version['apk'] = trim($url);
                            break 2;
                        }
                    }
                }
            }
            if (stripos($version['apk'], '.plist') != false) {
                $version['apk'] = "itms-services://?action=download-manifest&url=" . $version['apk'];
            }
        }
        if (false !== strpos($version['apk'] ?? '','.apk')){
            $old_host = parse_url($version['apk'] , PHP_URL_HOST);
            $new_host = parse_url(TB_APP_DOWN_URL , PHP_URL_HOST);
            $version['apk'] = str_replace($old_host, $new_host , $version['apk']);
        }
        return $version;
    }

    /**
     *  后台版本管理 缓存清除
     * @param $type
     * @param string $channel
     */
    static function clearVersionCache($type, $channel = '')
    {
        $key = 'ver:' . $type;
        if($type == 'ios'){
            $key_chan = 'ver:' . $type . 'normal';
            redis()->del($key_chan);
            $key_chan = 'ver:' . $type . 'testflight';
            redis()->del($key_chan);
            $key_chan = 'ver:' . $type . 'pwa';
            redis()->del($key_chan);
        }
        redis()->del($key);
        cached('')->clearGroup('version');
    }

    public static function get_main_android_least_version_v2()
    {
        return cached('version:android:v3')
            ->group('version')
            ->chinese('版本管理')
            ->fetchPhp(function () {
                $where = [
                    ['via', '=', ""],
                    ['type', '=', VersionModel::TYPE_ANDROID],
                    ['status', '=', VersionModel::STATUS_SUCCESS],
                ];
                return VersionModel::query()->where($where)->orderByDesc('id')->first();
            }, 86400);
    }

    /**
     *  版本获取
     * @param $type
     * @param int $status
     * @param string $channel 渠道| 默认空
     * @return mixed
     */
    public static function getLeastVersionNew($type, $status = self::STATUS_SUCCESS, $channel = '')
    {
        return cached('version:' . $type . '-' . $channel)
            ->group('version')
            ->chinese('版本管理')
            ->fetchPhp(function () use ($type, $status, $channel) {
                $where = [
                    ['type', '=', $type],
                    ['status', '=', $status],
                    ['via', '=', $channel],
                ];
                return self::query()->where($where)->orderByDesc('id')->first();
            }, 86400);
    }

    public static function get_android_version($code)
    {
        $channel_android = self::getLeastVersionNew(VersionModel::TYPE_ANDROID, VersionModel::STATUS_SUCCESS, $code);
        if ($code && $channel_android && $channel_android->via == $code) {
            // 渠道包 直接下载渠道包地址
            $is_download = 1;
            $version_and = $channel_android->apk;
            $special_and = $channel_android->apk;
            return [$is_download, $version_and, $special_and];
        }

        // 主包
        $main_android = self::get_main_android_least_version_v2();

        // 只有主包 则显示主包地址与主包相对地址
        $is_download = 0;
        $version_and = $main_android ? $main_android->apk : "";
        $main_url = $main_android ? $main_android->apk : "";
        $special_and = parse_url($main_url, PHP_URL_PATH);
        return [$is_download, $version_and, $special_and];
    }

    public static function defend_apk($apk, $is_update = 1)
    {
        try {
            $filename = ltrim(parse_url($apk, PHP_URL_PATH), '/');
            $dirname = '/home/yaf-gtv/www/public/apk';
            $file_path = $dirname . '/' . $filename;
            $tmp_path = $dirname . '/' . $filename . '_bk';
            wf("获取信息", [$dirname, $file_path], false, '/storage/logs/apk.log');
            if (file_exists($file_path) && $is_update == 0){
                wf("跳过存在", $file_path, false, '/storage/logs/apk.log');
                return;
            }
            $dirname = dirname($file_path);
            if (!file_exists($dirname)) {
                wf("创建目录", $dirname, false, '/storage/logs/apk.log');
                $rs = mkdir($dirname, 0777, true);
                test_assert($rs, '无法创建目录:' . $dirname);
            }
            wf("获取文件", $apk, false, '/storage/logs/apk.log');
            download_apk($apk, $tmp_path, $file_path);
            wf('下载成功', [$tmp_path, $file_path], false, '/storage/logs/apk.log');

            $cmd = sprintf('chown www:www -R %s', $dirname);
            wf('给予权限', $cmd, false, '/storage/logs/apk.log');
            exec($cmd, $log, $status);
            test_assert(!$status, '给予权限异常');

//            $txt = file_get_contents($apk);
//            test_assert($txt, '无法获取文件:' . $apk);
//            wf("写入文件", $file_path, false, '/storage/logs/apk.log');
//            $rs = file_put_contents($file_path, $txt);
//            test_assert($rs, '无法写入文件:' . $file_path);
            //清除缓存
            self::clearVersionCache(self::TYPE_ANDROID);
        } catch (Throwable $e) {
            wf("出现异常", $e->getMessage(), false, '/storage/logs/apk.log');
        }
    }

    public static function report_apk($address)
    {
        try {
            $address = parse_url($address , PHP_URL_PATH);
            $data = [
                'app_id'        => config('click.report.app_id'),
                'share_name'    => '{share.gtv}',
                'app_url'       => replace_share('https://{share.gtv}'),
                'app_apk'       => TB_APP_DOWN_URL . $address,
            ];
            $http = new \tools\HttpCurl();
            $rs = $http->post(config('channel.report.apk'), $data);
            wf('上报apk链接结果:', $rs, false, '/storage/logs/apk.log');
        } catch (Throwable $e) {
            wf("出现异常", $e->getMessage(), false, '/storage/logs/apk.log');
        }
    }
}
