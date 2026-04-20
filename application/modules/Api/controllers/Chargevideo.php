<?php


use service\ProductService;
use service\UserService;

/**
 * Class ChargeVideoController
 * @author xiongba
 * @date 2020-11-02 15:37:12
 */
class ChargevideoController extends BaseController
{



    /**
     * 购买过的视频
     * @author xiongba
     * @date 2020-03-18 11:38:13
     */
    public function maiguoAction()
    {
        $lastIndex = intval($this->post['lastIndex'] ?? 0);
        $service = new \service\MvService();
        $data = $service->getBought(request()->getMember(), $lastIndex);
        return $this->showJson($data);
    }

    /**
     * 购买视频。自动从余额中扣除用户的金币
     * @return bool
     * @author xiongba
     */
    public function buyAction()
    {
        return $this->forward('Api', 'Userbuy', 'video');
    }

}