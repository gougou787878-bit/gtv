<?php


namespace App\console;


use DB;

class FixLikeConsole extends AbstractConsole
{

    public $name = 'update-likes-for-mv-del';

    public $description = '修复用户点赞视频之后，视频被删除，用户的点赞对不上的bug';


    public function process($argc, $argv)
    {
        DB::enableQueryLog();
        $f = \UserTopicModel::where('id', 196)
            ->update([
                'mv_id_str' => \DB::raw("IF(`video_count`=0,4333,CONCAT_WS(',',`mv_id_str`,4333))"),
                'video_count'=>\DB::raw("`video_count`+1")
            ]);
        print_r(DB::getQueryLog());
        var_dump($f);
    }


}