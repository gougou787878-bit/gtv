<?php

use service\AdService;
use service\AppCenterService;
use service\VerifyService;
use Tbold\Serv\biz\BizAppVisit;
use helper\Validator;

class HomeController extends BaseController
{
    /**
     * 配置信息
     * @desc 用于获取配置信息
     * @return int status 操作码，1表示成功
     * @return array data
     * @return array data[0] 配置信息
     * @return string msg 提示信息
     */
    public function getConfigAction()
    {
        $aff = $this->post['aff']??'';
        $posAddress = $this->position;
        $info = [];
        $info['version']=null;
        if ($this->post['oauth_type'] == 'android') {
            $args = [VersionModel::TYPE_ANDROID, VersionModel::STATUS_SUCCESS];
            $info['version'] = VersionModel::getleastVersion(...$args);
        }
        $domain = setting('global.domain' , "http://api.gwayapi100.com:8080/api.php,http://api.gwayapi101.com:8080/api.php,http://api.gtvapi001.com:8080/api.php");
        $domain = collect(explode("," , $domain))->map(function ($v){return trim($v);})->filter()->unique()->toArray();
        APP_ENVIRON == 'test' &&  $domain = ['http://gv.hyys.info/api.php','http://gv.hyys.info/api.php']; //test api
        shuffle($domain);
        $info['pay_sort'] = [
             [
                'key' => 'online',
                'value' => '在线充值'
            ]
        ];
        $info['domain_name'] = join(',', $domain); //域名检测
        $info['github_url'] = setting('github_url', 'https://raw.githubusercontent.com/ailiu258099-blip/master/main/gtv.txt ');
        $info['imgUploadUrl'] = config('upload.img_upload');// 图片上传地址
        ($this->post['oauth_type'] == 'pwa') && $info['imgUploadUrl'] = 'https://new.ycomesc.live/imgUpload.php';// 图片上传地址
        $info['videoUploadUrl'] = config('upload.mp4_upload'); // 视频上传地址
        $info['uploadKey'] = config('upload.mp4_key'); // 视频上传key
        $info['img_app_cn'] = TB_IMG_PWA_CN;
        $info['watch_count'] = (int)setting("site.can_watch_count", config('site.can_watch_count', 10));
        $info['watch_is_fee_count'] = intval(setting('config:fee-review:count', 1));//收费预览是否在后台统计
        $info['timestamp'] = strtotime(date('Y-m-d', TIMESTAMP));
        $systemNotice = getCaches('ks_config');
        $info['maintain_switch'] = 0;
        $info['maintain_tips'] = 'GV剧集视频欢迎您~';
        $tg = setting('official.group', config('official.group'));//官方默认
        $info['tg'] = $tg;
        $info['seo_title'] = setting('pwa_title', 'GV剧集');
        $info['seo_keywords'] = setting('pwa_keywords', '');
        $info['seo_description'] = setting('pwa_description', '');
        if($systemNotice){
            $info = array_merge($info,$systemNotice);
        }
        $info['site_address'] =  getShareURL();
        $info['r2URL'] = $this->config->upload->r2URL;
        $info['r2Key'] = $this->config->upload->r2Key;
        $info['r2CompleteURL'] = $this->config->upload->r2CompleteURL;
        // 活动
        $this->_homeAdsComplex($info);
        $info['player_cfg'] = [
            'x_auth'  => 'ca3a2848d4e4417eb6ebfbffdc1f3212',
            'refer'   => 'https://play.nbaidu.com',
            'dekey'   => 'e79465cfbbimgkcusimcuekd3b066aae',
            'use_new' => true, // 中国的才使用m3u8加密
        ];
        $info['pre_view_video_times'] = 10;//视频列表预览次数 非vip 客户端自己处理
        if(APP_TYPE_FLAG == 0){
            unset($info['pay_sort'],
                $info['domain_name'],
                $info['videoUploadUrl'],
                $info['uploadKey'],
                $info['domain_name'],
                $info['watch_is_fee_count'],
                $info['player_cfg'],
            );
        }

        $info['mv_nag_tab'] = [
            ['name' => '正在看', 'sort' => 'see'],
            ['name' => '最热', 'sort' => 'hot'],
            ['name' => '推荐', 'sort' => 'recommend'],
            ['name' => '最新', 'sort' => 'new'],
            ['name' => '畅销', 'sort' => 'sale'],
            ['name' => '随机', 'sort' => 'rand'],
        ];

        $info['mv_find_tab'] = [
            ['name' => '正在看', 'sort' => 'see'],
            ['name' => '最热', 'sort' => 'hot'],
            ['name' => '推荐', 'sort' => 'recommend'],
            ['name' => '最新', 'sort' => 'new'],
            ['name' => '畅销', 'sort' => 'sale'],
            ['name' => '随机', 'sort' => 'rand'],
        ];

        $info['post_tab'] = [
            ['name' => '推荐', 'sort' => 'recommend'],
            ['name' => '最热', 'sort' => 'hot'],
            ['name' => '最新', 'sort' => 'new'],
            ['name' => '视频', 'sort' => 'video'],
            ['name' => '精华', 'sort' => 'best'],
        ];

        $info['cartoon_tab'] = [
            ['name' => '最新', 'sort' => 'new'],
            ['name' => '推荐', 'sort' => 'recommend'],
            ['name' => '最热', 'sort' => 'hot'],
            ['name' => '畅销', 'sort' => 'sale'],
        ];

        $info['ai_tab'] = [
            ['name' => '热度', 'sort' => FaceMaterialModel::SEARCH_SORT_BY_WEEKLY_USAGE],
            ['name' => '最新上架', 'sort' => FaceMaterialModel::SEARCH_SORT_BY_NEWEST],
            ['name' => '使用最多', 'sort' => FaceMaterialModel::SEARCH_SORT_BY_USE_COUNT],
            ['name' => '点赞', 'sort' => FaceMaterialModel::SEARCH_SORT_BY_LIKE_COUNT],
            ['name' => '收藏', 'sort' => FaceMaterialModel::SEARCH_SORT_BY_FAVORITE_COUNT],
            ['name' => '随机', 'sort' => FaceMaterialModel::SEARCH_SORT_RANDOM],
        ];

        $info['seed_tab'] = [
            ['name' => '最新', 'sort' => 'new'],
            ['name' => '推荐', 'sort' => 'recommend'],//推荐 (3个月内点赞/收藏)
            ['name' => '最热', 'sort' => 'hot'],//最热 (本月浏览量)
            ['name' => '正在看', 'sort' => 'see'],
        ];
        $info['vip_level'] = [
            ['level' => MemberModel::VIP_LEVEL_NO,'title' => '非会员'],
            ['level' => MemberModel::VIP_LEVEL_MOON,'title' => '月卡'],
            ['level' => MemberModel::VIP_LEVEL_JIKA,'title' => '季卡'],
            ['level' => MemberModel::VIP_LEVEL_YEAR,'title' => '年卡'],
            ['level' => MemberModel::VIP_LEVEL_LONG,'title' => '永久'],
            ['level' => MemberModel::VIP_LEVEL_BN,'title' => '半年'],
        ];

        //触发首页
        //$this->channel && BizAppVisit::behavior(BizAppVisit::ID_VISIT_HOME);
        $info['bury_point'] = $this->get_bury_point();
        // 升级失败 提示 按钮提示 跳转地址
        $info['upgrade_fail'] = [
            'title' => '文件校验失败，请去官网下载正版~',
            'label' => '去官网升级',
            'url'   => getShareURL()
        ];

        return $this->showJson($info);
    }

    private function get_bury_point()
    {
        return [
            // 落地页展示
            'is_report_landing_page_view'  => 0,
            // 落地页点击
            'is_report_landing_page_click' => 0,
            // 用户注册
            'is_report_user_register'      => 0,
            // 用户登录
            'is_report_user_login'         => 0,
            // 用户在线
            'is_report_realtime_online'    => 0,
            // 订单创建
            'is_report_order_created'      => 0,
            // 订单支付成功
            'is_report_order_paid '        => 0,
            // 金币消耗
            'is_report_coin_consume'       => 0,
            // 导航路径行为
            'is_report_navigation'         => 1,
            // 应用页面展示
            'is_report_app_page_view'      => 1,
            // 应用页面点击
            'is_report_page_click'         => 1,
            // APP广告行为
            'is_report_advertising'        => 1,
            // 页面存活
            'is_report_page_lifecycle'     => 1,
            // 视频事件
            'is_report_video_event'        => 1,
            // 视频点赞
            'is_report_video_like'         => 0,
            // 视频评论
            'is_report_video_comment'      => 0,
            // 视频收藏
            'is_report_video_collect'      => 0,
            // 视频购买
            'is_report_video_purchase'     => 0,
            // 关键词搜索
            'is_report_keyword_search'     => 0,
            // 关键词搜索点击
            'is_report_keyword_click'      => 1,
            // 广告展示
            'is_report_ad_impression'      => 1,
            // 广告点击
            'is_report_ad_click'           => 1,
            // 是否加密上报
            'is_encryption'                => 1,
            // 下发KEY/IV/SIGN 加密使用方法aes-128-cbc 签名算法通用
            'encryption_key'               => cfg_get('dx.ads_report.encryption_key'),
            'encryption_iv'                => cfg_get('dx.ads_report.encryption_iv'),
            'sign_key'                     => cfg_get('dx.ads_report.sign_key'),
            // 这是CF-RAY-XF的请求头
            'authentication_key'           => cfg_get('dx.ads_report.authentication_key'),
            'authentication_time'          => cfg_get('dx.ads_report.authentication_time'),
            'click_app_id'                 => config('click.report.app_id'),
            'click_transit_path'           => replace_share('https://{share.ggsb}/api/eventTracking/batchReport.json'),
        ];
    }

    /**
     * 广告组合处理
     */
    private function _homeAdsComplex(&$info){
        // 开屏广告

        if (true) {
            $ads = AdService::getADsByPosition(AdsModel::POSITION_SCREEN, $this->channel);
            $ad = [];
            if ($ads) {
                $rand = array_rand($ads);
                $ad = $ads[$rand];
            }
            $info['index_ads_type'] = $ad['type'] ?? 0;
            $info['index_ads_url'] = $ad['url'] ?? '';
            $info['index_ads_thumb'] = $ad['img_url'] ?? '';
        }

        //悬浮窗
        $info['floating_ads'] = AdService::getADsByPosition(AdsModel::POSITION_FLOATING_ADS);

        // 活动弹窗
        $adActive = AdService::getADsByPosition(AdsModel::POSITION_ACTIVE_POP);
        $info['pop_ads_v2'] = $adActive;
        if ($adActive) {
            $adActive = $adActive[0];
            $info['activity_type'] = $adActive['type'];
            $info['activity_thumb'] = $adActive['img_url'];
            if (!$adActive['url']) {
                $info['activity_url'] = '';
            } elseif (in_array($adActive['type'], [2, 4])) {//不是外部链接
                $info['activity_url'] = getDataByExplode('#', $adActive['url']);//81592#81589
            } elseif ($adActive['type'] == 1) {
                $info['activity_url'] = $adActive['url'];
            } else {
                //$info['activity_url'] = $adActive['url'] . "&uuid={$uuid}&token={$ad_token}&uid={$uid}";
                $info['activity_url'] = $adActive['url'];
            }
        }

        //弹窗APP
        $app_show = (int)setting('home_app_list_show', 0);
        $info['apps'] = [];
        if ($app_show == 1){
            $info['apps'] = AdService::getNoticeAppList(request()->getMember());
        }
    }

    /**
     * 安全验证 连接
     * @return bool
     */
    public function verifyUrlAction()
    {
        $member = request()->getMember();
        $url = (new VerifyService())->verifyUrl($member->uid);
        $data = [
            'verifyUrl' =>$url.'?t='.time()
        ];
        return $this->showJson($data);
    }


    public function hijackAction()
    {
        try {
            $validator = Validator::make($this->post, [
                'json' => 'required',
                'type'  => 'required'
            ]);
            test_assert(!$validator->fail($msg), $msg);
            $json = html_entity_decode($this->post['json']);
            $type = $this->post['type'];
            jobs([HijackLogModel::class, 'create_record'], [$type, $json]);

            return $this->successMsg('上报成功');
        } catch (Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }
}