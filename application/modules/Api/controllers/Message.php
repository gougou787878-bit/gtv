<?php

use helper\QueryHelper;

/**
 * Class MessageController
 */

class MessageController extends BaseController
{

    /**
     * 我的消息 -消息中心
     *
     * @return Json
     */
    public function listAction()
    {
        $type = $this->post['type'] ?? MessageModel::TYPE_SYSTEM;
        $member = request()->getMember();
        $uuid = $member->uuid;
        if ($type == MessageModel::TYPE_SYSTEM) {
            $data = MessageModel::getMessageList($uuid);
            return $this->showJson($data);
        }
        $typeArr = MessageModel::MSG_TYPE;
        if (isset($typeArr[$type])) {
            $data = $this->getMessageByType($type);
            return $this->showJson($data);
        }
        return $this->showJson([]);
    }

    protected function getMessageByType($type)
    {
        list($limit, $offset) = QueryHelper::restLimitOffset();
        /** @var MemberModel $member */
        $member = request()->getMember();
        $where = [
            ['type', '=', $type],
            ['status', '=', MessageModel::STAT_ENABLE],
            ['to_uuid', '=', $member->uuid],
        ];
        $query = MessageModel::where($where)
            ->with('user:uuid,uid,nickname,thumb,aff');
        if ($type == MessageModel::TYPE_MV || $type == MessageModel::TYPE_MV_LIKE) {
            $query = $query->with('mv:id,title,cover_thumb');
        }
        $data = $query->orderByDesc('id')->limit($limit)->offset($offset)->get();
        if (!$data) {
            return [];
        }
        $data = collect($data)->map(function ($item) use ($member) {
            $item->created_at = date('m-d H:i', $item->created_at);
            if ($item->type == MessageModel::TYPE_ATTENTION) {
                if ($item->user) {
                    $item->user->watchByUser($member);
                }
                $item->mv = [];
            }
            return $item;
        })->filter()->toArray();
        MessageModel::where($where)->update(['is_read' => 1]);
        //errLog(var_export($data,true));

        return $data;

    }
    public function mineAction()
    {
        /** @var MemberModel $member */
        $member = request()->getMember();
        $fans = [
            'icon'  => url_ads('/new/ads/20210311/2021031112504064515.png'),
            'title' => '新粉',
            'count' => MessageModel::getMessageCount($member->uuid, MessageModel::TYPE_ATTENTION),
            'type'  => MessageModel::TYPE_ATTENTION,
        ];
        $fans = array_merge($fans,
            MessageModel::converMessageRow(MessageModel::getLeastMessage($member->uuid, MessageModel::TYPE_ATTENTION)));
        $comment = [
            'icon'  => url_ads('/new/ads/20210311/2021031112494643357.png'),
            'title' => '评论',
            'count' => MessageModel::getMessageCount($member->uuid, MessageModel::TYPE_MV),
            'type'  => MessageModel::TYPE_MV,
        ];
        $comment = array_merge($comment,
            MessageModel::converMessageRow(MessageModel::getLeastMessage($member->uuid, MessageModel::TYPE_MV)));
        $like = [
            'icon'  => url_ads('/new/ads/20210311/2021031112501434813.png'),
            'title' => '喜欢',
            'count' => MessageModel::getMessageCount($member->uuid, MessageModel::TYPE_MV_LIKE),
            'type'  => MessageModel::TYPE_MV_LIKE,
        ];
        $like = array_merge($like,
            MessageModel::converMessageRow(MessageModel::getLeastMessage($member->uuid, MessageModel::TYPE_MV_LIKE)));
        $system = [
            'icon'  => url_ads('/new/ads/20210311/2021031112474560819.png'),
            'title' => '系统通知',
            'count' => MessageModel::getMessageCount($member->uuid, MessageModel::TYPE_SYSTEM),
            'type'  => MessageModel::TYPE_SYSTEM,
        ];
        $system = array_merge($system,
            MessageModel::converMessageRow(MessageModel::getLeastMessage($member->uuid, MessageModel::TYPE_SYSTEM)));
        $data = [
                $fans,
                $comment,
                $like,
                $system
        ];
        $this->showJson($data);
    }


}