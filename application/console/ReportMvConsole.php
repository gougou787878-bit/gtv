<?php
namespace App\console;

use AdsModel;
use MvModel;

class ReportMvConsole extends AbstractConsole
{


    public $name = 'report-mv';

    public $description = '上报视频到资源中心';

    public function process($argc, $argv) {
        set_time_limit(0);
        echo "start 上报广告点击\r\n";
        MvModel::query()
            ->where('created_at', '>', strtotime('-1 day'))
            ->chunkById(500,function (\Illuminate\Support\Collection $items){
                collect($items)->each(function (MvModel $mv) {
                    $url = 'https://videosapi.91mv.app/v1/videos/postInfo';
                    $data = [
                        'app_name' => SYSTEM_ID,
                        'type' => 3,
                        'cate' => 0,
                        'mod' => 1,
                        '_id' => $mv->music_id,
                        'title' => $mv->title,
                        'm3u8' => parse_url($mv->m3u8, PHP_URL_PATH),
                        'cover_thumb' => parse_url($mv->cover_thumb, PHP_URL_PATH),
                        'directors' => $mv->directors,
                        'actors' => $mv->actors,
                        'tags' => $mv->tags,
                        'release_at' => date('Y-m-d H:i:s', $mv->onshelf_tm),
                        'duration' => $mv->duration,
                        'is_free' => $mv->is_free
                    ];
                    $http = new \tools\HttpCurl();
                    $res = $http->post($url, $data);
                    trigger_log($res);
                });
            });
       echo "\r\n end ############ \r\n";
    }





}