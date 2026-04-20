<?php


namespace App\console;


use App\console\Queue\QueueOption;

class ResetRedisConsole extends AbstractConsole
{

    public $name = 'reset';

    public $description = '导入redis';


    public function process($argc, $argv)
    {
        $cachedTagKeys = [];
        $feeIds = [];
        $VideoIds = [];
        $redis = redis();
        $all = \MvModel::where('status', \MvModel::STAT_CALLBACK_DONE)
            ->get()
            ->map(function (\MvModel $item) use (&$VideoIds, &$cachedTagKeys, &$feeIds) {
                if ($item->coins > 0 && $item->is_recommend) {
                    $feeIds[] = $item->id;
                }
                $VideoIds[] = $item->id;
                return $item;
            });

        foreach ($cachedTagKeys as $key => $_) {
            $redis->del($key);
        }
        //$redis->del(\MvModel::REDIS_MV_LIST);
        $redis->del(\MvModel::RECOMMEND_FEE_KEY);
       /* collect(array_chunk($VideoIds , 50))->map(function ($ids) use($redis){
            $redis->sAddArray(\MvModel::REDIS_MV_LIST, $ids);
        });*/
        collect(array_chunk($VideoIds , 50))->map(function ($ids) use($redis){
            $redis->sAddArray(\MvModel::RECOMMEND_FEE_KEY, $ids);
        });

    }


}