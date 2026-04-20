<?php

use service\TabService;

/**
 * Class TabController
 * @author xiongba
 * @date 2020-10-31 15:46:57
 */
class TabController extends BaseController
{

    /**
     * tab 栏目标签
     */
    public function indexAction()
    {
        $data = [

            [
                'current' => false,
                'id'      => -1,
                'name'    => '关注',
                'type'    => 'follow',
                'api'     => 'api/mvnew/listOfFollow',
                'params'  => ['tabId'=>0],
            ],
            [
                'current' => true,
                'id'      => -1,
                'name'    => '推荐',
                'type'    => 'home',
                'api'     => 'api/mvnew/home',
                'params'  => ['tabId'=>0],
            ],
            [
                'current' => false,
                'id'      => -1,
                'name'    => '最热',
                'type'    => 'find',
                'api'     => 'api/mvnew/listOfHottest',
                'params'  => ['tabId'=>0],
            ],
            [
                'current' => false,
                'id'      => -1,
                'name'    => '最新',
                'type'    => 'latest',
                'api'     => 'api/mvnew/listOfLatest',
                'params'  => ['tabId'=>0],
            ],

           /* [
                'current' => false,
                'id'      => -1,
                'name'    => '热卖',
                'type'    => 'hotBuy',
                'api'     => 'api/mvnew/hotBuy',
                'params'  => ['tabId'=>0],
            ],*/

        ];

        $tabs = TabService::getTabList();
        /** @var TabModel[] $tabs */
        $rs = $data;
        $_data = [];
        foreach ($tabs as $tab) {
            $_data['current'] = false;
            $_data['id'] = $tab['tab_id'];
            $_data['name'] = $tab['tab_name'];
            $_data['type'] = 'tab';
            $_data['api'] = 'api/mvnew/listOfTab';
            $_data['params'] = ['tabId' => $tab['tab_id']];
            $rs[] = $_data;
        }
        $this->showJson($rs);
    }

    /**
     * @return bool|void
     */
    public function categoryAction()
    {
        $type = $this->post['type'] ?? '';
        if ($type == 'cat') {
            $data = TabService::getCateList();
        } elseif ($type == 'tab') {
            $data = TabService::getTabList();
        } elseif ($type == 'search') {
            $data = TabService::getSearchList();
        } else {
            $data = TabService::getUploadTabList();
        }
        return $this->showJson($data);
    }



}