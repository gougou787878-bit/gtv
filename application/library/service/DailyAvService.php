<?php
/**
 *
 * @date 2020/2/27
 * @author
 * @todo 每日推荐AV 蜜桃业务逻辑控制
 *
 */

namespace service;

use helper\QueryHelper;

/**
 * Class DailyAvService
 * @package service
 */
class DailyAvService
{
    const D_MV_KEY = 'daily:av:';

    /**
     * @param $date
     * @param $member
     * @param $limit
     * @return \AvDailyModel
     */
    static function getDailyVideoInfoByDate($date,$member,$limit = 12)
    {
        $data = cached(self::D_MV_KEY . $date)
            ->serializerJSON()
            ->clearCached()
            ->expired(3600)
            ->fetch(function () use ($date) {
                $data = \AvDailyModel::queryBase()->where('day', $date)->first();
                if (is_null($data)) {
                    return null;
                }
                return $data->toArray();
            });
        if (!$data) {
            return [];
        }
        return [$data,AvService::getVideos($data['avids'],$member,$limit)];
    }


    /**
     * 清除列表缓存
     * @param string $date
     * @return int
     */
    static function clearMvList($date)
    {
        redis()->del(self::D_MV_KEY . $date);
        return true;
    }


}