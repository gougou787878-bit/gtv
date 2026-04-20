<?php
/**
 *验证码相关g
 *
 */

namespace service;

use tools\CurlService;

/**
 * Class VerifyService
 * @package service
 */
class VerifyService
{
    const VERIFY_CODE_URL = 'https://img.sslchat2me.com/captcha/%s/%s';
    const VERIFY_CHECK_URL = 'https://pay.hyys.info/captcha/check';
    const VERIFY_CODE = 666;
    const VERIFY_CODE_TEXT = '图形验证码失败';
    public function verifyUrl($aff): string
    {
        return sprintf(self::VERIFY_CODE_URL, SYSTEM_ID, (string)$aff);
    }

    public function verifyCheck($aff, $verifyCode): bool
    {
        if (empty($verifyCode)) {
            return false;
        }
        $data = [
            'app_name' => SYSTEM_ID,
            'aff'      => $aff,
            'code'     => $verifyCode,
        ];
        try {
            $result = (new CurlService())->curlPost(self::VERIFY_CHECK_URL, $data, 5);
            //errLog("smsverfiy".var_export($result,1));
            return strcasecmp($result, 'SUCCESS') === 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}