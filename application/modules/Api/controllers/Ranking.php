<?php


use tools\RedisService;

class RankingController extends BaseController
{

    protected function getType(){
        $type = $this->post['type']??'';
        if(in_array($type,['day,month','week'])){
            $type = 'day';
        }
        return $type;
    }

    /**
     * 收益达人排行
     * @return bool
     */
    public function profitAction(){
        $data = UsersCoinrecordModel::getTopProfit(10,$this->getType());
        return $this->showJson($data);
    }

    /**
     * av 小电影 排行
     * @return bool
     */
    public function gvAction(){
        $data = RankModel::getHomeTop(RankModel::TYPE_MV,RankModel::FIELD_TYPE_PLAY,10,$this->getType());
        return $this->showJson($data);
    }

    /**
     * 剧集 剧情 连续剧 排行
     * @return bool
     */
    public function topicAction(){
        $data = RankModel::getHomeTop(RankModel::TYPE_TOPIC,RankModel::FIELD_TYPE_PLAY,10,$this->getType());
        return $this->showJson($data);
    }


}