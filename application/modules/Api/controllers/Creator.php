<?php

use helper\QueryHelper;
use service\CreatorService;
use service\ProxyService;
use service\UserVideoService;

class CreatorController extends BaseController
{

    /**
     * 创作者申请-验证配置接口
     */
    public function preCheckAction()
    {
        $member = request()->getMember();
        //公共配置
        $confData[] = [
            'title'       => '注册账号',
            'sub_title'   => '',
            'icon'        => url_ads('/new/ads/20210830/2021083011335929916.png'),
            'reached'     => $member->phone ? 1 : 0,
            'reached_str' => $member->phone ? '已达标' : '未达标',
        ];
        $confData[] = [
            'title'       => 'VIP等级月卡及以上',
            'sub_title'   => '',
            'reached'     => 0,
            'icon'        => url_ads('/new/ads/20210830/2021083011345779951.png'),
            'reached_str' => '未达标',
        ];
        $confData[] = [
            'title'       => '累计粉丝达到100',
            'sub_title'   => '',
            'reached'     => ($member->fans_count >= 100) ? 1 : 0,
            'icon'        => url_ads('/new/ads/20210830/2021083011345779951.png'),
            'reached_str' => ($member->fans_count >= 100) ? '已达标' : '未达标',
        ];
        //$confData[2]['reached'] = 1;
        //check vip
        if ($member->expired_at > TIMESTAMP && $member->vip_level >= MemberModel::VIP_LEVEL_MOON) {
            $confData[1]['reached'] = 1;
            $confData[1]['reached_str'] = '已达标';

        }

        $hasReached = $confData[0]['reached'] && $confData[1]['reached'] && $confData[2]['reached'];
        //$hasReached =1;
        $data = ['rules' => $confData, 'has_reached' => $hasReached ? 1 : 0];
        return $this->showJson($data);
    }


    /**
     * 创作者申请
     */
    public function applyAction()
    {
        $data = $this->post;
        $member = request()->getMember();
        $phone = $member->phone;
        $tag = $data['tag'] ?? '';
        $type = $data['type'] ?? \MemberMakerModel::TYPE_PERSONAL;
        $contact = $data['contact'] ?? '';
        $description = $data['description'] ?? '';
        if (empty($phone)) {
            return $this->errorJson('请绑定手机');
        }
        //初步筛选
        if ($member->expired_at < TIMESTAMP || !$member->vip_level) {
            return $this->errorJson('充值会员用户才能提交创作者申请哟~');
        }
        $has = \MemberMakerModel::where(['uuid' => $member['uuid']])->first();
        if ($has) {
            if (\MemberMakerModel::CREATOR_STAT_BAN == $has->status) {
                $data['msg'] = '您的创作者身份已经禁用';
            } elseif (\MemberMakerModel::CREATOR_STAT_YES == $has->status) {
                $data['msg'] = '您已经是创作者，不需要重复申请';
            }
        }

        /** @var MemberMakerModel $maker */
        $maker = CreatorService::applyCreator($member, $tag, $description, $type, $contact);
        $data = [];
        $data['status'] = $maker->status;
        $data['msg'] = '申请成功，已进入审核队列，请稍后查看';
        if (\MemberMakerModel::CREATOR_STAT_BAN == $maker->status) {
            $data['msg'] = '您的创作者身份已经禁用';
        } elseif (\MemberMakerModel::CREATOR_STAT_YES == $maker->status) {
            $data['msg'] = '您已经是创作者，不需要重复申请';
        } elseif (\MemberMakerModel::CREATOR_STAT_NO == $maker->status) {
            $data['msg'] = "您的创作者申请不符合要求，拒绝原因：{$maker->refuse_reason}";
        }
        MessageModel::createSystemMessage($member->uuid , MessageModel::SYSTEM_MSG_TPL_CREATOR_CHECKING , ['name'=>$member->nickname]);
        return $this->showJson($data);

    }

    /**
     *制片人信息
     */
    public function infoAction()
    {
        $member = request()->getMember();
        if (!$member->auth_status) {
            return $this->errorJson('你还不是制片人~');
        }
        //print_r($member->toArray());die;
        $data = [];
        $rate_rule = MemberMakerModel::getMakerRule();
        $data['uid'] = $member->uid;
        $data['avatar_url'] = $member->avatar_url;
        $data['nickname'] = $member->nickname;
        $data['auth_level'] = 0;
        $data['auth_status'] = 0;
        $data['auth_rate'] = '25%';
        $data['month_row'] = null;
        $data['vip_level_text'] = MemberModel::USER_VIP_TYPE[$member->vip_level]??"非VIP";
        if ($member->auth_status) {
            $data['auth_status'] = 1;
            /** @var MemberMakerModel $makerInfo */
            $makerInfo = MemberMakerModel::getMakeInfo($member->uuid);
            $data['auth_level'] = (int)$makerInfo->level_num;
            $data['auth_rate'] = sprintf("%d%%", $makerInfo->pay_rate * 100);
            $data['month_row'] = CreatorService::getMonthReportRow($member);
        }
        $data['rate_rule'] = $rate_rule;
        $data['description'] = "1. 达成等级要求后，自动升向对应等级；
2. VIP等级超过创作者等级要求的，也视为达成要求；
3. 遵守平台上传规范，GTVApp主推精选长视频或剧集视频要求；
4. 系统实时按月统计收益和推广相关指标作为参考；
5. 精选视频尽量不带水印或特殊处理，有条件的剪辑高光预览视频,系统会推荐增加流量曝光加持；
6. 创作者等级信息每日更新,用心制作好视频,享受平台高流量保障和更高视频收益永久分成。";

        return $this->showJson($data);

    }

    /**
     * 用户每月绩效报表记录日志
     */
    public function monthlyReportAction()
    {
        $data = CreatorService::geMonthlyReport(request()->getMember());
        $data = $data ? $data : null;
        return $this->showJson(['list' => $data]);
    }


}
