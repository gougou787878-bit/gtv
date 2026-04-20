<?php
/**
 *
 * @date 2020/2/27
 * @author
 * @copyright kuaishou by KS
 * @todo 广告相关业务处理
 *
 */

namespace service;

use AdsModel;

/**
 * Class AdService
 * @package service
 */
class AdService
{
    const AD_EXPIRED = 3500;

    /**
     * 根据广告位置获取广告列表 渠道
     *
     * @param string $position
     * @param string $channel
     * @param string $isLive
     * @param string $platForm
     * @return array
     */
    static function getADsByPosition($position = '1', $channel = '',$isLive = false,$platForm=0)
    {
        $channel = '';
        //远程广告
        if (version_compare($_POST['version'], AdsModel::ADS_VERSION, '>')) {
            $adData = AdsModel::getPositionByRemote($position);
        }else{
            $adData = self::_getADsByPositionNormal($position,$platForm);//default ad
        }

        if ($adData) {
            $adData = collect($adData)->map(function ($_itemAd){
                // 1 => '外部跳转连接', 如果外部广告过期就不显示了
                if ($_itemAd['type'] == 1 && $_itemAd['is_expired']) {
                    return null;
                }
                $_itemAd['img_url'] = $_itemAd['img_url_full'];
                unset($_itemAd['img_url_full']);
                return $_itemAd;
            })->filter()->values()->toArray();
           // shuffle($adData);
        }
        return $adData;
    }

    protected static function _getADsByPositionNormal($position,$platForm=0)
    {
        $redisKey = \AdsModel::REDIS_ADS_KEY . $position;
        return self::getDBADS($redisKey, $position);
    }

    protected static function getDBADS($key, $position, $where = [],$platForm=0)
    {
        $where[] = ['status', '=', \AdsModel::STATUS_SUCCESS];
        $where[] = ['position', '=', $position];
        $data = cached($key)->expired(self::AD_EXPIRED)->serializerJSON()->fetch(function () use ($key, $where) {
            $data = \AdsModel::query()
                //->select(['id', 'title','description', 'mv_m3u8','img_url', 'url', 'type', 'ios_url', 'android_url', 'value'])
                ->select(['id', 'title','description', 'mv_m3u8','img_url', 'url', 'type','expired_date'])
                ->where($where)
                ->orderByDesc('show_user')
                ->orderByDesc('id')
                ->get();
            \CacheKeysModel::createOrEdit($key, '广告列表');
            return is_null($data) ? [] : $data->toArray();
        });
        return $data;
    }


    /**
     * 应用中心 应用列表获取
     * @param int $platForm
     * @return mixed
     */
    static function getAdsAppList($platForm=0)
    {
        //远程广告
        if (version_compare($_POST['version'], AdsModel::ADS_VERSION, '>')) {
            return AdsModel::getAppByRemote();
        }
        
        $data = cached(\AdsAppModel::REDIS_ADS_KEY)->serializerJSON()->expired(86000)->fetch(function (
        )use($platForm) {

            /*$response = file_get_contents(APP_ADS_APP, false, stream_context_create([
                'http' => [
                    'method'  => "POST",
                    'timeout' => 15
                ]
            ]));
            return json_decode($response,true);*/

            $rs = \AdsAppModel::getDataList($platForm);
            $return = [];
            if (!is_null($rs)) {
                foreach ($rs as $item) {
                    //$replace_url = \AdsAppModel::convertURLHOST($item->short_name,$item->link_url);
                    $replace_url = $item->link_url;
                    if ($item->short_name){
                        if (str_contains($item->short_name,"https://")){
                            $replace_url = replace_share($item->short_name);
                        }else{
                            $replace_url = "https://".replace_share($item->short_name);
                        }
                    }
                    $return[] = [
                        'id'          => $item->id,
                        'title'       => $item->title,
                        'description' => $item->description,
                        'img_url'     => $item->img_url ? url_ads($item->img_url) : '',
                        'link_url'    => $replace_url,
                        'clicked'     => $item->clicked,
                        'created_at'  => date('Y/m/d', $item->created_at),
                    ];
                }
            }
            return $return;
        });
        return $data;
    }

    /**
     * 应用中心 应用列表获取
     */
    public static function getNoticeAppList(\MemberModel $member)
    {
        $rs_data = AdsModel::getNoticeAppByRemote();

        $channel_username = '';
        //渠道用户
        if (!empty($member->build_id) && $member->build_id != 'GW'){
            $channel_username = \MemberModel::info($member->invited_by);
        }
        $list = [];
        if (!is_null($rs_data)) {
            foreach ($rs_data as $item) {
                if ($channel_username && $item['app_type'] == AdsModel::NT_APP_IN){
                    $item['link_url'] = $item['link_url'] . '?channel_code=' . $channel_username;
                }
                $item['link_url'] = replace_share($item['link_url']);
                unset($item['app_type']);
                $list[] = $item;
            }
        }

        return $list;
    }

}