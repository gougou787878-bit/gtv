<?php

use helper\QueryHelper;
use service\AdService;

class SystemController extends BaseController
{
    use \repositories\SystemRepository, \repositories\SmsRepository, \repositories\ExchangeCodeRepository;
    public function IndexAction()
    {
        $ads = AdService::getADsByPosition(AdsModel::POSITION_SCREEN);
        if ($ads) {
            $rand = array_rand($ads);
            $ads = $ads[$rand];
        }

        $data = [
            'enableRecharge' => true, // 是否开启充值
            'versions' => $this->getUpdate($_POST['version'], $_POST['oauth_type']),
            'screen' => $ads,
        ];

        $this->showJson($data);
    }

    /**
     * 失败域名反馈
     * @throws Exception
     */
    public function domainAction()
    {
        $domain = $this->post['domain'] ?? '';
        if ($domain == '') {
            throw new \Exception('参数错误', 422);
        }
        $domain = explode(',', $domain);
        foreach ($domain as $item) {
            AreaLogModel::create([
                'uuid' => $this->member['uuid'],
                'url' => $item,
                'ip' => USER_IP,
                'sick' => 0
            ]);
        }
        $this->showJson(['success' => true, 'msg' => '提交成功']);
    }

    /**
     * 短信国家码
     */
    public function countryAction()
    {
        $result = (new SMSCountryModel)->getList();
        $this->showJson($result);
    }

    public function exchangeAction()
    {
        $code = $this->post['code'] ?? '';
        $code = trim($code);
        if ($code == '') {
            throw new \Yaf\Exception('请输入正确的参数', 422);
        }

        $this->handleExchangeCode($code);


        return $this->showJson(['success' => true, 'msg' => '兑换成功']);
    }

    /**
     * 应用中心-点击
     * @return bool
     */
    public function appclickAction()
    {
        $id = $this->post['id'] ?? 0;

        if (!$id) {
            throw new \Yaf\Exception('请输入正确的参数', 422);
        }

        //远程广告
        if (version_compare($_POST['version'], AdsModel::ADS_VERSION, '>')) {
            jobs([AdsModel::class, 'reportRemote'], [$id, date('Y-m-d H:i:s')]);
        }else{
            jobs([AdsAppModel::class, 'incrDownLoadNumber'], [$id, 1]);
        }

        $this->showJson(['success' => true, 'msg' => '提交成功']);
    }

    /**
     * 应用中心
     * @return bool
     */
    public function appcenterAction()
    {
        $adsData = AdService::getADsByPosition(AdsModel::POSITION_APP_CENTER);
        $appData = [];
        $m = request()->getMember();
        if ($m->build_id && in_array($m->build_id, BLACK_CHANNEL)) {
        } else {
            $appData = AdService::getAdsAppList();
        }
        $return = [
            'banner' => $adsData,
            'apps'   => $appData,
        ];

        $this->showJson($return);
    }

    /**
     * 广告-点击统计
     * @return bool
     */
    public function adsclickAction()
    {
        $id = $this->post['id'] ?? 0;
        if (!$id) {
            throw new \Yaf\Exception('请输入正确的参数', 422);
        }

        //远程广告
        if (version_compare($_POST['version'], AdsModel::ADS_VERSION, '>')) {
            jobs([AdsModel::class, 'reportRemote'], [$id, date('Y-m-d H:i:s')]);
        }else{
            bg_run(function () use ($id){
                AdsModel::where('id',$id)->increment('click_number');
            });
        }

        $this->showJson(['success' => true, 'msg' => '提交成功']);
    }

    public function nav_confAction(){
        try {
            $sj = [
                'name'      => '色界',
                'type'      => 'sj',
                'list'      => [
                    [
                        'name' => '男色',
                        'type' => 'ns'
                    ],
                    [
                        'name' => '黄游',
                        'type' => 'hy'
                    ],
                ]
            ];

            $nm = [
                'name'      => '男漫',
                'type'      => 'nm',
                'list'      => [
                    [
                        'name' => '动漫',
                        'type' => 'dm'
                    ],
                    [
                        'name' => '漫画',
                        'type' => 'mh'
                    ],
                    [
                        'name' => '小说',
                        'type' => 'xs'
                    ],
                ]
            ];

            return $this->showJson(['sj_conf' => $sj, 'nm_conf' => $nm]);
        }catch (Throwable $exception){
            return $this->errorJson($exception->getMessage());
        }
    }


    public function downloadAction(){
        try {
            $validator = \helper\Validator::make($this->post, [
                'mv_id' => 'required|numeric|min:1',//视频ID
            ]);
            if ($validator->fail($msg)) {
                return $this->errorJson($msg);
            }
            $mvId = (int)($this->post['mv_id']);
            $type = $this->post['type']??'mv';
            $member = request()->getMember();
            if($member->isBan()){
                throw new Exception('你已被禁言，请联系管理员');
            }

            if (!frequencyLimit(5, 1, $member)) {
                throw new Exception('短时间内下载太頻繁了,稍后再试试');
            }

            if (!$member->is_vip){
                throw new Exception('下载权限不足');
            }

            /** @var UserDownloadModel $download_info */
            $download_info = UserDownloadModel::findByAff($member->aff);
            if (empty($download_info) || $download_info->val <= 0){
                throw new Exception('下载次数已用完');
            }

            if($type == 'cartoon'){
                $cartoon_chapter = CartoonChaptersModel::find($mvId);
                test_assert($cartoon_chapter, '视频不存在');
                $cartoon_chapter->watchByUser($member);
                if ($cartoon_chapter->coins) {
                    if (empty($cartoon_chapter->is_pay)) {
                        throw new Exception('下载权限不足1');
                    }
                }
                //扣减下载次数
                $download_info->val -= 1;
                $is_ok = $download_info->save();
                test_assert($is_ok, '下载地址获取失败，请重试');

                $hls = $cartoon_chapter->source;
                $title = $cartoon_chapter->cartoon->title;
            }else{
                $mv_info = MvModel::find($mvId);
                test_assert($mv_info, '视频不存在');
                $mv_info->watchByUser($member);
                if ($mv_info->coins) {
                    if (empty($mv_info->is_pay)) {
                        throw new Exception('下载权限不足');
                    }
                }
                //扣减下载次数
                $download_info->val -= 1;
                $is_ok = $download_info->save();
                test_assert($is_ok, '下载地址获取失败，请重试');

                $hls = $mv_info->full_m3u8 ?: $mv_info->m3u8;
                $title = $mv_info->title;
            }


            $return = [
                'is_permit'         => 1,
                'message'           => "您正在下载【{$title}】",
                'resource_download' => getPlayUrlPwa($hls),
            ];

            return $this->showJson($return);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage());
        }
    }


    public function feedback_reward_typesAction(){
        try {
            $list = FeedbackRewardModel::typeList();
            return $this->listJson($list);
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage(), $e->getCode());
        }
    }


    public function feedback_rewardAction()
    {
        try {
            $validator = \helper\Validator::make($this->post, [
                'type'      => 'required',
                'content'   => 'required',
                'images'    => 'required',
            ]);
            $rs = $validator->fail($msg);
            test_assert(!$rs, $msg);
            $type = $this->post['type'];
            $content = $this->post['content'];
            $images = $this->post['images'];
            $images = htmlspecialchars_decode($images);
            $images = json_decode($images, true);
            $images = is_array($images) ? $images : [];
            $member = request()->getMember();
            if (!frequencyLimit(30, 2, $member)) {
                return $this->errorJson('发送太频换,稍后再次重试反饋');
            }
            if (!in_array($type, array_keys(FeedbackRewardModel::TYPE_TIPS))){
                return $this->errorJson('反馈类型错误');
            }

            if ((mb_strlen($content) < 5)) {
                return $this->errorJson('详细描述下问题，方便快速解决哦');
            }

            $model = FeedbackRewardModel::make();
            $model->aff = $member->aff;
            $model->content = $content;
            $model->type = $type;
            $model->status = FeedbackRewardModel::STATUS_NO;
            $model->created_at = \Carbon\Carbon::now();
            $model->updated_at = \Carbon\Carbon::now();
            if ($images){
                $arr = [];
                foreach ($images as $v){
                    if ($v['media_url']){
                        $arr[] = $v['media_url'];
                    }
                }
                $model->images = json_encode($arr);
            }
            $isOk = $model->save();
            test_assert($isOk, '反馈失败请重试');
            return $this->successMsg('您的反馈已提交');
        } catch (\Throwable $e) {
            return $this->errorJson($e->getMessage(), $e->getCode());
        }
    }
}
