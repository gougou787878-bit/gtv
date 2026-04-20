<?php

namespace service;

use AiNavModel;
use CURLFile;
use DB;
use Carbon\Carbon;
use helper\QueryHelper;
use FaceCateModel;
use FaceMaterialModel;
use MemberAiFreeModel;
use MemberFaceModel;
use MemberModel;
use FaceMaterialCommentModel;
use FaceMaterialUserLikeModel;
use MemberStripFreeModel;
use MemberStripModel;

class AiService
{

    public function ai_nav(){
        return AiNavModel::list();
    }

    /*****************************************AI脱衣*********************************************/
    public function getStripPreData(MemberModel $member){

        $free_num = MemberStripFreeModel::getValueByType($member->aff);
        return [
            'max_size'       => '2M',
            'free_num'       => (string)$free_num ?? '0',
            'coins'          => $member->coins,
            'times'          => '60',
            'ai_ty_coins'    => setting('ai_ty_coins', 9),
            'ai_ty_tips'     => setting('ai_ty_tips', ''),
            'exp_before_img' => url_cover('/upload_01/ads/20260120/2026012012584120655.png'),
            'exp_after_img'  => url_cover('/upload_01/ads/20260120/2026012012584968044.png'),
        ];
    }

    //检查图片格式
    protected function check_type($file)
    {
        $url = TB_IMG_ADM_US . $file;
        $image = file_get_contents($url);
        test_assert($image, '请求远程异常:' . $url);
        $md5 = substr(md5($url), 0, 16);
        $from = APP_PATH . '/storage/data/images/' . $md5 . '_fr';
        $dirname = dirname($from);
        if (!is_dir($dirname) || !file_exists($dirname)) {
            mkdir($dirname, 0755, true);
        }
        $rs = file_put_contents($from, $image);
        test_assert($rs, '无法写入文件:' . $from);
        $cover = new CURLFile(realpath($from), mime_content_type($from));
        test_assert($cover, '仅支持JPEG|JPG|PNG|GIF|BMP|WEBP|AVIF图片格式,其他格式请自行转码');
        //删除图片
        unlink($from);
        if (!in_array($cover->mime,  ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/avif'])){
            test_assert(false, '仅支持JPEG|JPG|PNG|GIF|BMP|WEBP|AVIF图片格式,其他格式请自行转码');
        }
    }

    public function strip(MemberModel $member, $thumb, $thumb_w, $thumb_h)
    {
        //检查格式
        $this->check_type($thumb);
        transaction(function () use ($member, $thumb, $thumb_w, $thumb_h) {
            $free_ai_num = MemberStripFreeModel::getValueByType($member->aff);
            if ($free_ai_num > 0){
                $isOk = MemberStripFreeModel::decrValueByType($member->aff);
                test_assert($isOk, '免费次数扣除失败');
                $need_coins = 0;
                $pay_type = MemberStripModel::TYPE_TIME;
            }else{
                $pay_type = MemberStripModel::TYPE_COINS;
                $need_coins = setting('ai_ty_coins',9);

                if ($member->coins < $need_coins) {
                    throw new \Exception('余额不足，不能进行支付');
                }

                $isOk = MemberModel::where('aff', $member->aff)
                    ->where('coins', '>=', $need_coins)
                    ->update([
                        'coins'       => DB::raw("coins-{$need_coins}"),
                        'consumption' => DB::raw("consumption+{$need_coins}")
                    ]);
                if (empty($isOk)) {
                    throw new \Exception('扣款失败,请确认您的金币是否足够', 1008);
                }
                //记录日志
                $tips = "[AI脱衣扣除]#金币： $need_coins";
                $rs3 = \UsersCoinrecordModel::createForExpend("aiTy", $member->uid, 0,
                    $need_coins,
                    0,
                    0,
                    0,
                    0,
                    null,
                    $tips);
                if (empty($rs3)) {
                    throw new \Exception('记录日志错误', 1008);
                }
                //清除缓存
                MemberModel::clearFor($member);

                //金币消耗上报
                (new EventTrackerService(
                    $member->oauth_type,
                    $member->invited_by,
                    $member->uid,
                    $member->oauth_id,
                    $_POST['device_brand'] ?? '',
                    $_POST['device_model'] ?? ''
                ))->addTask([
                    'event'                 => EventTrackerService::EVENT_COIN_CONSUME,
                    'product_id'            => '0',
                    'product_name'          => 'AI脱衣',
                    'coin_consume_amount'   => (int)$need_coins,
                    'coin_balance_before'   => (int)($member->coins),
                    'coin_balance_after'    => (int)($member->coins - $need_coins),
                    'consume_reason_key'    => 'ai_strip',
                    'consume_reason_name'   => 'AI脱衣',
                    'order_id'              => (string)$rs3->id,
                    'create_time'           => to_timestamp($rs3->addtime),
                ]);
            }
            $rs = MemberStripModel::create_record($member->aff, $thumb, $thumb_w, $thumb_h, $pay_type, $need_coins);
            test_assert($rs, '系统异常，请稍后再试');
        });
    }

    public function list_my_strip(MemberModel $member, $status, $page, $limit)
    {
        return MemberStripModel::list_my_strip($member->aff, $status, $page, $limit);
    }

    public function list_face_nav()
    {
        $list = FaceCateModel::list_cate();
        $first = [
            'id' => 0,
            'name' => '全部素材'
        ];
        return collect($list)->prepend($first);
    }

    public function list_face_material(MemberModel $member, $id, $page, $limit)
    {
        //FaceMaterialModel::setWatchUser($member);
        if ($id){
            $cat = FaceCateModel::find($id);
            test_assert($cat, '分类不存在');
        }
        return FaceMaterialModel::list_material($id, $page, $limit);
    }

    public function getFacePreData(MemberModel $member, $id){
        $detail = null;
        if ($id > 0){
            $detail = FaceMaterialModel::get_detail($id);
            test_assert($detail, '素材不存在');
        }
        $free_num = MemberAiFreeModel::getValueByType($member->aff);
        return [
            'max_size'       => '2M',
            'free_num'       => $free_num,
            'coins'          => $member->coins,
            'ai_ht_coins'    => setting('ai_ht_coins', 9),
            'ai_ht_tips'     => setting('ai_ht_tips', ''),
            'exp_correct_img'=> url_cover('/upload_01/ads/20250212/2025021218285664497.png'),
            'exp_error1_img' => url_cover('/upload_01/ads/20250212/2025021218291187299.png'),
            'exp_error2_img' => url_cover('/upload_01/ads/20250212/2025021218292398581.png'),
            'detail'         => $detail,
        ];
    }

    public function change_face(MemberModel $member, $material_id, $thumb, $thumb_w, $thumb_h)
    {
        /** @var FaceMaterialModel $material */
        $material = FaceMaterialModel::get_detail($material_id);
        test_assert($material, '素材已被删除');
        transaction(function () use ($material, $member, $material_id, $thumb, $thumb_w, $thumb_h) {
            $free_ai_num = MemberAiFreeModel::getValueByType($member->aff);
            if ($free_ai_num > 0){
                $isOk = MemberAiFreeModel::decrValueByType($member->aff);
                test_assert($isOk, '免费次数扣除失败');
                $need_coins = 0;
                $pay_type = MemberFaceModel::TYPE_TIME;
            }else{
                $pay_type = MemberFaceModel::TYPE_COINS;
                $need_coins = $material->coins;
                if (!$need_coins){
                    //金币数未配置 走默认的
                    $need_coins = setting('ai_ht_coins',10);
                }
                if ($member->coins < $need_coins) {
                    throw new \Exception('余额不足，不能进行支付');
                }
                $itOk = MemberModel::where([
                    ['uid', '=', $member->uid],
                    ['coins', '>=', $need_coins],
                ])->update([
                    'coins'       => DB::raw("coins-{$need_coins}"),
                    'consumption' => DB::raw("consumption+{$need_coins}")
                ]);
                if (empty($itOk)) {
                    throw new \Exception('扣款失败,请确认您的金币是否足够', 1008);
                }

                $tips = "[AI换脸扣除]#金币： $need_coins";
                $rs3 = \UsersCoinrecordModel::createForExpend('aiHl', $member->uid, 0,
                    $need_coins,
                    $material_id,
                    0,
                    0,
                    0,
                    null,
                    $tips);
                if (empty($rs3)) {
                    throw new \Exception('记录日志错误', 1008);
                }
                //清除缓存
                MemberModel::clearFor($member);

                //金币消耗上报
                (new EventTrackerService(
                    $member->oauth_type,
                    $member->invited_by,
                    $member->uid,
                    $member->oauth_id,
                    $_POST['device_brand'] ?? '',
                    $_POST['device_model'] ?? ''
                ))->addTask([
                    'event'                 => EventTrackerService::EVENT_COIN_CONSUME,
                    'product_id'            => (string)$material->id,
                    'product_name'          => $material->title,
                    'coin_consume_amount'   => (int)$material->coins,
                    'coin_balance_before'   => (int)($member->coins),
                    'coin_balance_after'    => (int)($member->coins - $need_coins),
                    'consume_reason_key'    => 'ai_image_face',
                    'consume_reason_name'   => 'AI图片换脸',
                    'order_id'              => (string)$rs3->id,
                    'create_time'           => to_timestamp($rs3->addtime),
                ]);
            }
            $rs = MemberFaceModel::create_record($member->aff, $material_id, $pay_type, $need_coins, $material->thumb, $material->thumb_w, $material->thumb_h, $thumb, $thumb_w, $thumb_h);
            test_assert($rs, '系统异常，请稍后再试');

            // 使用次数维护
            $isOk = $material->increment('use_ct');
            test_assert($isOk, '系统异常,请稍后重试');
        });
    }

    public function customize_face(MemberModel $member, $ground, $ground_w, $ground_h, $thumb, $thumb_w, $thumb_h)
    {
        transaction(function () use ($member, $ground, $ground_w, $ground_h, $thumb, $thumb_w, $thumb_h) {
                $need_coins = setting('ai_ht_coins',10);

                if ($member->coins < $need_coins) {
                    throw new \Exception('余额不足', 1008);
                }

                $itOk = MemberModel::where([
                    ['uid', '=', $member->uid],
                    ['coins', '>=', $need_coins],
                ])->update([
                    'coins'       => DB::raw("coins-{$need_coins}"),
                    'consumption' => DB::raw("consumption+{$need_coins}")
                ]);
                if (empty($itOk)) {
                    throw new \Exception('扣款失败,请确认您的金币是否足够', 1008);
                }

                $tips = "[AI换脸扣除]#金币： $need_coins";
                $rs3 = \UsersCoinrecordModel::createForExpend('aiHl', $member->uid, 0,
                    $need_coins,
                    0,
                    0,
                    0,
                    0,
                    null,
                    $tips);
                if (empty($rs3)) {
                    throw new \Exception('记录日志错误', 1008);
                }
                $rs = MemberFaceModel::create_customize_record($member->aff, MemberFaceModel::TYPE_COINS, $need_coins, $ground, $ground_w, $ground_h, $thumb, $thumb_w, $thumb_h);
                test_assert($rs, '系统异常，请稍后再试');
                MemberModel::clearFor($member);

            //金币消耗上报
            (new EventTrackerService(
                $member->oauth_type,
                $member->invited_by,
                $member->uid,
                $member->oauth_id,
                $_POST['device_brand'] ?? '',
                $_POST['device_model'] ?? ''
            ))->addTask([
                'event'                 => EventTrackerService::EVENT_COIN_CONSUME,
                'product_id'            => '0',
                'product_name'          => '',
                'coin_consume_amount'   => (int)$need_coins,
                'coin_balance_before'   => (int)($member->coins),
                'coin_balance_after'    => (int)($member->coins - $need_coins),
                'consume_reason_key'    => 'ai_image_face',
                'consume_reason_name'   => 'AI图片换脸',
                'order_id'              => (string)$rs3->id,
                'create_time'           => to_timestamp($rs3->addtime),
            ]);

        });
    }

    public function list_my_face(MemberModel $member, $status, $page, $limit)
    {
        return MemberFaceModel::list_my_face($member->aff, $status, $page, $limit);
    }

    public function createComComment(MemberModel $member, $commentId, $content, $cityname)
    {
        $aff = $member->aff;
    
        $parentComment = FaceMaterialCommentModel::getCommentByIds($member, $commentId);
        test_assert($parentComment,'此评论不存在');

        $data = [
            'material_id'       => $parentComment->material_id,
            'pid'           => $parentComment->id,
            'aff'           => $aff,
            'comment'       => $content,
            'status'        => FaceMaterialCommentModel::STATUS_WAIT,
            'refuse_reason' => '',
            'ipstr'         => USER_IP,
            'is_top'        => FaceMaterialCommentModel::TOP_NO,
            'is_finished'   => FaceMaterialCommentModel::FINISH_OK,
            'cityname'      => $cityname,
            'created_at'    => \Carbon\Carbon::now(),
        ];
        $comment = FaceMaterialCommentModel::create($data);
        test_assert($comment,'系统异常,异常码:1001');

        return true;
    }

      /**
     * @throws \Exception
     */
    public function createPostCommentNew(MemberModel $member, $id, $content, $cityname)
    {
        $aff = $member->aff;
       
        $post = FaceMaterialModel::find($id);

        test_assert($post,'此素材不存在');
        $status = FaceMaterialCommentModel::STATUS_WAIT;
        //年卡及以上会员直接通过
//        if (in_array($member->vip_level,[\MemberModel::VIP_LEVEL_YEAR,\MemberModel::VIP_LEVEL_LONG]) && $member->is_vip){
//            $status = \FaceMaterialCommentModel::STATUS_PASS;
//        }
        $data = [
            'material_id'       => $post->id,
            'pid'           => 0,
            'aff'           => $aff,
            'comment'       => $content,
            'status'        => $status,
            'refuse_reason' => '',
            'ipstr'         => USER_IP,
            'is_top'        => FaceMaterialCommentModel::TOP_NO,
            'is_finished'   => FaceMaterialCommentModel::FINISH_OK,
            'cityname'      => $cityname,
            'created_at'    => \Carbon\Carbon::now()
        ];
        $comment = FaceMaterialCommentModel::create($data);
        test_assert($comment,'系统异常,异常码:1001');

        bg_run(function () use ($member, $content, $comment){
            //检查评论
            FilterService::checkPostComment($member, $content, $comment);
        });

        return true;
    }


    /**
     * 评论点赞/取消点赞
     * @throws \Exception
     */
     function likeComment(MemberModel $member,$post_id, $type,$action_type)
    {
        $aff = $member->aff;
        if($type == FaceMaterialUserLikeModel::TYPE_COMMENT){
            $comment = FaceMaterialCommentModel::find($post_id);
            test_assert($comment,'此评论不存在');
        }
        if($type == FaceMaterialUserLikeModel::TYPE_MATERIAL){
            $post = FaceMaterialModel::find($post_id);
            test_assert($post,'此素材不存在');
        }
        $record = FaceMaterialUserLikeModel::getIdsById($aff,$post_id,$type,$action_type);
        if (!$record) {
            $data = [
                'aff'        => $aff,
                'related_id' => $post_id,
                'type' => $type,
                'action_type' => $action_type,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            FaceMaterialUserLikeModel::create($data);
            $res_fields = 'is_like';
            if($action_type == FaceMaterialUserLikeModel::ACTION_LIKE){
                if($type == FaceMaterialUserLikeModel::TYPE_COMMENT){
                    FaceMaterialCommentModel::where('id', $post_id)->increment('like_num');
                }
                if($type == FaceMaterialUserLikeModel::TYPE_MATERIAL){
                    FaceMaterialModel::where('id', $post_id)->increment('like_count');
                }
            }

            if($action_type == FaceMaterialUserLikeModel::ACTION_COLLECT){
               FaceMaterialModel::where('id', $post_id)->increment('favorite_count');
                $res_fields = 'is_favorite';
            }
            return [
                'message' => FaceMaterialUserLikeModel::TYPE_TIPS[$type].FaceMaterialUserLikeModel::ACTION_TIPS[$action_type].'成功',
                $res_fields => 1,
            ];
        } else {
            $record->delete();

            $res_fields = 'is_like';
            if($action_type == FaceMaterialUserLikeModel::ACTION_LIKE){
                if($type == FaceMaterialUserLikeModel::TYPE_COMMENT){
                    FaceMaterialCommentModel::where('id', $post_id)->where('like_num','>',0)->decrement('like_num');
                }
                if($type == FaceMaterialUserLikeModel::TYPE_MATERIAL){
                    FaceMaterialModel::where('id', $post_id)->where('like_count','>',0)->decrement('like_count');
                }
            }

            if($action_type == FaceMaterialUserLikeModel::ACTION_COLLECT){
                $res_fields = 'is_favorite';
               FaceMaterialModel::where('id', $post_id)->where('favorite_count','>',0)->decrement('favorite_count');
            }

            return [
                'message' => '已取消'.FaceMaterialUserLikeModel::TYPE_TIPS[$type].FaceMaterialUserLikeModel::ACTION_TIPS[$action_type],
                $res_fields => 0,
            ];
        }
    }


    /**
     * @throws \RedisException
     * @throws \Exception
     */
    public function listCommentsByPostIdNew(MemberModel $member, $postId)
    {
        FaceMaterialCommentModel::setWatchUserStatic($member);
        list($page,$limit) = QueryHelper::pageLimit();
        $post = FaceMaterialModel::find($postId);
        test_assert($post,"此素材不存在");
        return FaceMaterialCommentModel::getCommentById($postId, $page, $limit);
    }


    public function listCommentsByCommentId(MemberModel $member, $commentId, $page, $limit)
    {
        FaceMaterialCommentModel::setWatchUserStatic($member);
        $comment = FaceMaterialCommentModel::find($commentId);
        test_assert($comment,'此评论不存在');
        $post = FaceMaterialModel::find($comment->material_id);
        test_assert($post,'此素材不存在');
        return FaceMaterialCommentModel::listCommentsByCommentId($comment->id, $comment->material_id, $page, $limit);
    }

}