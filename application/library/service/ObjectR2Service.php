<?php
/**
 * 对象存储 初始化请求
 */

namespace service;

use Factory\Log\Log;
use Illuminate\Database\Eloquent\Model;
use tools\HttpCurl;

class ObjectR2Service
{

    /**
     * 获取新上传R2-token 桶信息相关 按自然天过期
     *
     * @return bool|mixed|string|null
     */

    static function r2UploadInfo()
    {
        $data = self::_getR2UploadInfo();
        if ($data['code'] == 200) {
            return $data['data'];
        }
        errLog("_getR2UploadInfoError:" . var_export($data, true));
        return [];
    }

    private static function _getR2UploadInfo()
    {
        $signKey = config('r2.key');
        $now = time();
        $data['sign'] = md5("{$now}{$signKey}");
        $data['timestamp'] = $now;
        $string = str_replace('amp;', '', http_build_query($data));
        $result = null;
        try {
            $result = (new HttpCurl())->get(config('r2.old_url') . '?' . $string);
            $result = json_decode($result, true);
        } catch (\Throwable $e) {
            errLog('_getR2UploadInfo:异常:' . $e->getMessage());
        }
        return $result;

    }
}