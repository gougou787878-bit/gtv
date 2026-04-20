<?php


namespace App\console;


use App\console\Queue\QueueOption;

class FixMvFeatureConsole extends AbstractConsole
{

    public $name = 'fix-mv-feature';

    public $description = '精选推荐视频修复、发现页视频修复';


    public function process($argc, $argv)
    {
        $mvData = \MvModel::queryFeature()->select(['id', 'coins'])->get();
        if (is_null($mvData)) {
            echo "\r\n no queryFeature data \r\n";
            return;
        }
        $keyfind = 'mv_list';
        $keyFee = 'mv:feature:fee';
        $keyFree = 'mv:feature:free';
        //清除
        redis()->del($keyFee);
        redis()->del($keyFree);
        redis()->del($keyfind);

        //构造
        foreach ($mvData as $_mvItem) {
            if (!is_null($_mvItem)) {
                if ($_mvItem->coins > 0) {
                    redis()->sAdd($keyFee, $_mvItem->id);
                } else {
                    redis()->sAdd($keyFree, $_mvItem->id);
                }
            }
        }
        $videoIds = \MvModel::queryBase()->pluck('id')->toArray();
        if ($videoIds) {
            redis()->sAddArray($keyfind, $videoIds);
        }

        //统计
        $countFee = redis()->sCard($keyFee);
        $countFree = redis()->sCard($keyFree);
        $countFind = redis()->sCard($keyfind);
        echo "\r\n #######total  fee: {$countFee}  free:{$countFree}   find:{$countFind}######### \r\n";
        echo "\r\n ############fixfeature and fix-find Over ############## \r\n";

    }


}