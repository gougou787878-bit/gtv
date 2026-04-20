<?php

namespace service;

use LibUpload;
use Throwable;
use MemberFaceModel;
use tools\HttpCurl;
use MemberStripModel;

class AiSdkService
{

    const STRIP_LOG_FILE = '/storage/logs/strip.log';
    const IMAGE_FACE_URI = '/api/public/generate/face-swap';
    const VIDEO_FACE_LOG_FILE = '/storage/logs/video_face.log';
    const IMAGE_FACE_BACK_URI = '/index.php?m=ai&a=on_image_face';
    const IMAGE_FACE_TASK_URI = '/api/public/task/list';
    const IMAGE_FACE_LOG_FILE = '/storage/logs/img_face.log';

    const STRIP_NEW_API = '/api/public/generate/undress/images/male';
    const STRIP_NEW_BACK_URI = '/index.php?m=ai&a=on_strip';

    public static function js($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
    public static function wr_strip_log($tip, $data)
    {
        wf($tip, $data, false, self::STRIP_LOG_FILE);
    }

    public static function wr_video_face_log($tip, $data)
    {
        wf($tip, $data, false, self::VIDEO_FACE_LOG_FILE);
    }

    public static function wr_image_face_log($tip, $data)
    {
        wf($tip, $data, false, self::IMAGE_FACE_LOG_FILE);
    }

    /**
     * @throws \Exception
     */
    public static function upload_img($fr, $type = 1)
    {
        self::wr_image_face_log('开始处理图片', $fr);
        $image = file_get_contents($fr);
        //list($image, $content_type, $code, $err) = self::fetch_image($fr);
        //self::wr_image_face_log('info:', [$image, $content_type, $code, $err]);
        test_assert($image, '请求远程异常' . $fr);
        $md5 = substr(md5($fr), 0, 16);
        $to = APP_PATH . '/storage/data/images/' . $md5 . '_to';
        $dirname = dirname($to);
        if (!is_dir($dirname) || !file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }
        self::wr_image_face_log('写入路径:', $to);
        $rs = file_put_contents($to, $image);
        self::wr_image_face_log('写入文件结果:', $rs);
        test_assert($rs, '无法写入文件:' . $to);

        $flag = false;
        for ($i = 1; $i <= 3; $i++) {
            $return = LibUpload::upload2Remote(uniqid(), $to, 'upload');
            self::wr_image_face_log('上传返回', $return);
            if ($return && $return['code'] == 1) {
                $flag = true;
                break;
            }
        }
        test_assert($flag, '上传图片异常');
        unlink($to);
        self::wr_image_face_log('处理完成', $return['msg']);
        return $return['msg'];
    }

    // ======================图片换脸===========================
    public static function image_face_api($id, $fr, $fr2)
    {
        self::wr_image_face_log('ID', $id);
        self::wr_image_face_log('背景', $fr);
        self::wr_image_face_log('图片', $fr2);
        $bid = strtoupper(sprintf('%s_%s_%s', SYSTEM_ID, 'image_face', $id));
        $header = [
            'apikey:' . config('ai_image_face.key'),
            'Content-Type:application/x-www-form-urlencoded'
        ];
        $data = [
            'source_path' => $fr2,
            'target_path' => $fr,
            'bid'         => $bid,
            'fee'         => 10,
            'title'       => '',
            'notify_url'  => "https://app.gtav.info" . self::IMAGE_FACE_BACK_URI,
            'app_id'      => SYSTEM_ID
        ];
        self::wr_image_face_log('调用参数', $data);
        $url = config('ai_image_face.url') . self::IMAGE_FACE_URI;
        self::wr_image_face_log('请求地址', $url);
        $http = new HttpCurl();
        $rs = $http->post($url, $data, $header, true, 60);
        self::wr_image_face_log('返回数据', $rs);
        test_assert($rs, '调用远程出现异常-001');
        $rs = json_decode($rs, true);
        test_assert($rs, '调用远程出现异常-002');
        test_assert(!isset($rs['request_id']), '调用远程出现异常-003');
        self::wr_image_face_log('任务ID', $rs['task_id']);
        return $rs['task_id'];
    }

    public static function start_image_change_face_task()
    {
        try {
            MemberFaceModel::where('status', MemberFaceModel::STATUS_WAIT)
                ->get()
                ->map(function (MemberFaceModel $item) {
                    $target_path = TB_IMG_ADM_US . '/' . ltrim(parse_url($item->ground, PHP_URL_PATH), '/');
                    $source_path = TB_IMG_ADM_US . '/' . ltrim(parse_url($item->thumb, PHP_URL_PATH), '/');
                    $item->task_id = self::image_face_api($item->id, $target_path, $source_path);
                    $item->status = MemberFaceModel::STATUS_DOING;
                    $isOk = $item->save();
                    test_assert($isOk, '系统异常');
                });
        } catch (Throwable $e) {
            self::wr_image_face_log('出现异常', $e->getMessage());
        }
        sleep(5);
    }

    /**
     * @throws \Exception
     */
    public static function on_image_face($data)
    {
        /**
         * @var $model MemberFaceModel
         */
        $model = MemberFaceModel::where('task_id', $data['task_id'])
            ->where('status', MemberFaceModel::STATUS_DOING)
            ->first();
        test_assert($model, '记录不存在');

        if ($data['status'] != 2) {
            $model->status = MemberFaceModel::STATUS_FAIL;
            $model->reason = $data['error'] ?? '';
            $is_ok = $model->save();
            test_assert($is_ok, '维护数据出现异常');
            return;
        }
        if (!count($data['out_data']) || !$data['out_data'][0]) {
            $model->status = MemberFaceModel::STATUS_FAIL;
            $model->reason = $data['error'] ?? '';
            $is_ok = $model->save();
            test_assert($is_ok, '维护数据出现异常');
            return;
        }

        // 开始上传到远程
        $dir = APP_PATH . '/storage/data/images';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $new_url = $data['out_data'][0];
        $file = $dir . '/' . md5($new_url) . '.png';
        $txt = file_get_contents($new_url);
        test_assert($txt, '请求异常: ' . $new_url);
        $rs = file_put_contents($file, $txt);
        test_assert($rs, '写入异常: ' . $file);
        $ret = self::upload_image($file);
        file_exists($file) && unlink($file);

        $model->status = MemberFaceModel::STATUS_SUCCESS;
        $model->face_thumb = $ret['uri'];
        $model->face_thumb_w = $ret['w'];
        $model->face_thumb_h = $ret['h'];
        $is_ok = $model->save();
        test_assert($is_ok, '维护数据出现异常');
    }

    public static function upload_image($file)
    {
        try {
            $return = LibUpload::upload2Remote(uniqid(), $file, 'upload');
            wf('返回数据', $return);
            test_assert($return['code'] == 1, '上传图片到远程异常');
            $url = TB_IMG_ADM_US . $return['msg'];
            list($w, $h) = getimagesize($url);
            return ['uri' => $return['msg'], 'w' => $w, 'h' => $h];
        } catch (Throwable $e) {
            wf('上传远程出现异常', $e->getMessage());
            return ['msg' => $e->getMessage()];
        }
    }

    /*****************************************AI脱衣*********************************************/

    public static function start_task_strip(){
        $http = new HttpCurl();
        MemberStripModel::where('status', MemberStripModel::STATUS_WAIT)
            ->chunkById(100, function ($items) use ($http) {
                collect($items)->map(function (MemberStripModel $item) use ($http) {
                    try {
                        $header = [
                            'apikey:' . config('ai_strip.key'),
                            'Content-Type:application/x-www-form-urlencoded'
                        ];

                        $thumb = TB_IMG_ADM_US . '/' . ltrim(parse_url($item->thumb, PHP_URL_PATH), '/');
                        $bid = strtoupper(sprintf('%s_%s_%s', SYSTEM_ID, 'strip', $item->id));
                        $data = [
                            'source_path'     => $thumb,
                            'bid'             => $bid,
                            'fee'             => 10,
                            'notify_url'      => "https://app.gtav.info" . self::STRIP_NEW_BACK_URI,
                            'app_id'          => SYSTEM_ID
                        ];

                        self::wr_strip_log('调用参数', $data);
                        $url = config('ai_strip.url') . self::STRIP_NEW_API;
                        $rs = $http->post($url, $data, $header, true, 60);
                        self::wr_strip_log('返回数据', $rs);
                        test_assert($rs, '调用远程出现异常-001');
                        $rs = json_decode($rs, true);
                        test_assert($rs, '调用远程出现异常-002');
                        test_assert(!isset($rs['request_id']), '调用远程出现异常-003');
                        $item->task_id = $rs['task_id'];
                        $item->status = MemberStripModel::STATUS_DOING;
                        $is_ok = $item->save();
                        test_assert($is_ok, '出现异常');
                        self::wr_strip_log('任务ID', $rs['task_id']);
                    } catch (Throwable $e) {
                        wf('出现异常了', $e->getMessage(), false, self::STRIP_LOG_FILE);
                        $item->reason = $e->getMessage();
                        $is_ok = $item->save();
                        test_assert($is_ok, '出现异常');
                    }
                });
            });
        sleep(5);
    }

    /**
     * @throws \Exception
     */
    public static function on_strip($data){
        /**
         * @var $model MemberStripModel
         */
        $model = MemberStripModel::where('task_id', $data['task_id'])
            ->where('status', MemberStripModel::STATUS_DOING)
            ->first();
        test_assert($model, '记录不存在');

        if ($data['status'] != 2) {
            $model->status = MemberStripModel::STATUS_FAIL;
            $model->reason = $data['error'] ?? '';
            $is_ok = $model->save();
            test_assert($is_ok, '维护数据出现异常');
            return;
        }

        if (!count($data['out_data']) || !$data['out_data'][0]) {
            $model->status = MemberStripModel::STATUS_FAIL;
            $model->reason = $data['error'] ?? '';
            $is_ok = $model->save();
            test_assert($is_ok, '维护数据出现异常');
            return;
        }

        // 开始上传到远程
        $dir = APP_PATH . '/storage/data/images';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $new_url = $data['out_data'][0];
        // 上传远程图片
        test_assert($new_url, '回调成功,图片地址异常');
        list($w, $h) = getimagesize($new_url);
        $url = self::upload_img($new_url);
        $model->status = MemberStripModel::STATUS_SUCCESS;
        $model->strip_thumb = $url;
        $model->strip_thumb_w = $w;
        $model->strip_thumb_h = $h;
        $isOk = $model->save();
        test_assert($isOk, '维护数据出现异常');
    }
}