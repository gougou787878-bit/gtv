<?php
namespace App\console;

use service\AiSdkService;

class AiFaceImgConsole extends AbstractConsole
{


    public $name = 'ai-face-img';

    public $description = 'AI图片换脸';

    public function process($argc, $argv) {
        AiSdkService::start_image_change_face_task();
       echo "\r\n end ############ \r\n";
    }





}