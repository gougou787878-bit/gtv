<?php


namespace repositories;


use tools\RedisService;

trait CommentsRepository
{
    /**
     * 发布评论
     * @param string $mvID
     * @param string $cID
     * @param string $comment
     * @return bool
     */
    public function handleCreateComment(string $mvID, string $cID, string $comment)
    {

        $maybesample = \AdsampleModel::checkTextSimilar($comment);
        $status = $maybesample ? 0 : 1;

        $data = [
            'mv_id'    => $mvID,
            'c_id'     => $cID,
            'uid'      => $this->member['uid'],
            'comment'  => $comment,
            'ipstr'    => USER_IP,
            'cityname' => $this->position['country'] == '中国' ? ($this->position['city'] ?? $this->position['province'])
                : '火星',
            'status'   => $status
        ];

        \CommentModel::create($data);
        if ($cID == 0) {
            $mv = \MvModel::where('id','=',$mvID)->first();
            if ($mv) {
                $_mvUser = $mv->user;
                \MvModel::where('id', $mvID)->increment('comment');
                \MessageModel::createMessage($this->member['uuid'], $_mvUser->uuid, "[{$this->member['nickname']}]评论了您~", $comment, $mvID,
                    \MessageModel::TYPE_MV);
            }
        }
        RedisService::redis()->zIncrBy(\CommentModel::REDIS_COMMENT_TODAY_COUNT, 1, date('Ymd'));
        RedisService::redis()->del(\CommentModel::REDIS_COMMENT_LIST . $mvID . '_1');
        return true;
    }

    /**
     * 评论列表
     * @param string $id
     * @return array
     */
    public function getCommentList(string $id)
    {
        $items = RedisService::get(\CommentModel::REDIS_COMMENT_LIST . $id . '_' . $this->page);
        if (!$items) {
            $comments = \CommentModel::query()
                ->where('mv_id', $id)
                ->where('status', \CommentModel::STATUS_SUCCESS)
                ->where('c_id', 0)
                ->orderBy('created_at', 'desc')
                ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,auth_status')
                ->with('child')
                ->offset($this->offset)->limit($this->limit)->get();
            $items = $comments->toArray();
            RedisService::set(\CommentModel::REDIS_COMMENT_LIST . $id . '_' . $this->page, $items, 600);
        }

        $items = $this->fetchComment($items);

        return $items;
    }

    /**
     * 评论点赞
     * @param $id
     * @return bool
     */
    public function handleCreateCommentLiking($id)
    {
        $has = RedisService::sIsMember(\CommentModel::REDIS_COMMENT_LIKED . $this->member['uuid'], $id);
        if ($has) {
            RedisService::redis()->sRem(\CommentModel::REDIS_COMMENT_LIKED . $this->member['uuid'], $id);
            \CommentModel::query()->where('id', $id)->decrement('like_num');
        } else {
            RedisService::redis()->sAdd(\CommentModel::REDIS_COMMENT_LIKED . $this->member['uuid'], $id);
            \CommentModel::query()->where('id', $id)->increment('like_num');
        }
        return true;
    }

    /**
     * 格式化评论输出
     * @param $comments
     * @param bool $isChild
     * @return array
     */
    public function fetchComment($comments, $isChild = false)
    {
        $data = [];
        $commentLikeList = RedisService::redis()->sMembers(\CommentModel::REDIS_COMMENT_LIKED . $this->member['uuid']);
        foreach ($comments as $key => $comment) {
            if (!empty($comment['user']['thumb'])) {
                $comment['user']['thumb'] = $this->fetchUserThumb($comment['user']['thumb']);
            }
            $result = [
                'id'           => $comment['id'],
                'mvID'         => $comment['mv_id'],
                'cID'          => $comment['c_id'],
                'comment'      => $comment['comment'],
                'likes'        => $comment['like_num'],
                'hasLike'      => in_array($comment['id'], $commentLikeList),
                'createdAt'    => $comment['created_at'],
                'createdAtStr' => (isset($comment['cityname']) ? $comment['cityname'] : '') . '·' . $this->formatTimestamp($comment['created_at']),
                'user'         => [
                    //'isVV'      => $comment['user']['expired_at'] > TIMESTAMP,
                    'auth_status' => $comment['user']['auth_status'],
                    'is_vip'      => $comment['user']['expired_at'] > TIMESTAMP,
                    'vip_level'   => $comment['user']['vip_level'] ?? 0,
                    'uuid'        => $comment['user']['uuid'],
                    'uid'         => $comment['user']['uid'],
                    'sexType'     => $comment['user']['sexType'],
                    'thumb'       => $comment['user']['thumb'],
                    'nickname'    => $comment['user']['nickname'],
                ],
            ];
            if (!$isChild) {
                $result['child'] = $this->fetchComment($comment['child'], true);
            }
            $data[] = $result;
        }
        return $data;
    }
}