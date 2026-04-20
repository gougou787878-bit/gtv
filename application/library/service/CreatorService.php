<?php
/**
 *
 * @date 2020/2/27
 * @author
 * @copyright kuaishou by KS
 * @todo 传作者合集视频管理服务
 *
 */

namespace service;


/**
 * Class CreatorService
 * @package service
 */
class CreatorService
{
    /**
     * 申请成为创作者
     * @param $member
     * @param $tag
     * @param $description
     * @param string $type
     * @param string $contact
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function applyCreator($member, $tag, $description, $type = '1', $contact = '')
    {

        $applyData = [];
        $applyData['created_at'] = date('Y-m-d H:i:s');
        //$applyData['update_at'] = date('Y-m-d H:i:s');
        $applyData['uuid'] = $member->uuid;
        $applyData['level_num'] = 1;//default;
        $applyData['phone'] = $member->phone;
        $applyData['nickname'] = $member->nickname;
        //$applyData['type'] = $type;
        $applyData['status'] = \MemberMakerModel::CREATOR_STAT_ING;
        //$applyData['creator_tag'] = strip_tags($tag);
        //$applyData['creator_desc'] = strip_tags($description);
        $applyData['contact'] = $contact ? strip_tags($contact) : $member->phone;
        $creatorObj = \MemberMakerModel::updateOrCreate(['uuid' => $member->uuid], $applyData);
        return $creatorObj;
    }

    /**
     * 用户每月绩效报表记录日志
     * @param $member
     * @param int $limit 100 默认最新50条
     * @return array
     */
    public static function geMonthlyReport($member, $limit = 50)
    {
        return \MemberMakerStatModel::where('uuid', $member->uuid)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                if (is_null($item)) {
                    return null;
                }
                return [
                    'id'        => $item->id,
                    'month'     => sprintf("%s", substr($item->date_month, -2)),
                    'tui_total' => (string)$item->tui_total,
                    'mv_coins'  => (string)$item->mv_coins,
                    'mv_number' => (string)$item->mv_number,
                ];

            })->filter()->toArray();
    }
    static function getMonthReportRow($member){
        $item =  \MemberMakerStatModel::where('uuid', $member->uuid)
            ->orderByDesc('id')->first();
        if(is_null($item)){
            return null;
        }
        return [
            'id'        => $item->id,
            'month'     => sprintf("%s", substr($item->date_month, -2)),
            'tui_total' => (string)$item->tui_total,
            'mv_coins'  => (string)$item->mv_coins,
            'mv_number' => (string)$item->mv_number,
        ];
    }


}