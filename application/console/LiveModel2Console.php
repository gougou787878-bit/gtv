<?php


namespace App\console;


use App\library\service\LiveService;
use LiveModel;

class LiveModel2Console extends AbstractConsole
{

    public $name = 'live-model2';

    public $description = '直播模块更新;eg:php yaf live-model';

    public function process($argc, $argv)
    {
        $start = date("Y-m-d H:i:s");
        echo "#################  start [ {$start} ]##############".PHP_EOL;

        LiveModel::where('sort', '!=', 0)->update(['sort' => 0]);
        foreach (LiveService::CATEGORIES as $v) {
            list($tag, $sort) = $v;
            LiveService::list_models2($tag, $sort);
        }
        sleep(60);

        $end = date("Y-m-d H:i:s");
        echo "##################  over [ {$end} ]##############".PHP_EOL;
    }

}