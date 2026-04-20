<?php


namespace App\console;


use App\library\service\LiveService;

class LiveCoverConsole extends AbstractConsole
{

    public $name = 'live-cover';

    public $description = '直播封面更新;eg:php yaf live-over';

    public function process($argc, $argv)
    {
        $start = date("Y-m-d H:i:s");
        echo "#################  start [ {$start} ]##############".PHP_EOL;

        LiveService::live_cover();

        $end = date("Y-m-d H:i:s");
        echo "##################  over [ {$end} ]##############".PHP_EOL;
    }

}