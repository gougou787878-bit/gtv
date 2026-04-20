<?php
class PostController extends SiteController
{
    //小蓝数据同步
    public function syncPostAction(){
        if (!$this->getRequest()->isPost()) {
            exit('fail');
        }
        $post = $_POST;
        if ($post['pwd'] != md5('c1999b118f786d90' . $post['time'])){
            exit('签名错误');
        }

        trigger_log("sync_blue_data--\n" . print_r($post, true));
        //判读是否存在
        $data = PostModel::where('_id', $post['id'])->first();
        if ($data) {
            exit('帖子已经存在');
        }

        $_id = $post['id'];
        $medias = $post['medias'];
        unset($post['id']);
        unset($post['medias']);

        $items = [16071129,16239164,16221693,15993021,1,2,3,4];
        $aff = collect($items)->random();
        $data = PostModel::make();
        $data->_id = $_id;
        $data->aff = $aff;
        $data->content = $data['content'] ?? '';
        $data->is_deleted = $post['is_deleted'];
        $data->like_num = $post['like_num'];
        $data->comment_num = 0;
        $data->is_best = $post['is_best'];
        $data->photo_num = $post['photo_num'];
        $data->video_num = $post['video_num'];
        $data->is_finished = $post['is_finished'];
        $data->ipstr = $post['ipstr'];
        $data->cityname = $post['cityname'];
        $data->topic_id = $post['topic_id'];
        $data->view_num = $post['view_num'];
        $data->refresh_at = \Carbon\Carbon::now();
        $data->title = $post['title'];
        $data->price = $post['price'];
        $data->status = PostModel::STATUS_WAIT;
        $data->created_at = $post['created_at'];
        $data->updated_at = $post['updated_at'];
        $data->favorite_num = $post['favorite_num'];
        $data->type = $post['type'];
        $data->set_top = $post['set_top'];
        $data->save();

        foreach ($medias as $v) {
            $tmp = [
                'aff'          => 0,
                'cover'        => trim(parse_url($v['cover'], PHP_URL_PATH), '/'),
                'thumb_width'  => $v['thumb_width'],
                'thumb_height' => $v['thumb_height'],
                'duration'     => $v['duration'],
                'pid'          => $data->id,
                'media_url'    => parse_url($v['media_url'], PHP_URL_PATH),
                'relate_type'  => $v['relate_type'],
                'status'       => $v['status'],
                'type'         => $v['type'],
                'created_at'   => $v['created_at'],
                'updated_at'   => \Carbon\Carbon::now(),
            ];
            $isOk = PostMediaModel::create($tmp);
            test_assert($isOk, '保存图片资源异常');
        }

        exit('success');
    }
}