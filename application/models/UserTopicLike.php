<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class UserTopicLikeModel
 *
 * @property int $id
 * @property int $uid 用户uid
 * @property int $topic_id 剧集id
 * @property int $topic_uid 剧集作者的id
 * @property int $topic_del 剧集是否删除
 * @property int $created_at 创建时间
 *
 * @property UserTopicModel $topic
 *
 * @author xiongba
 * @date 2021-08-19 20:00:45
 *
 * @mixin \Eloquent
 */
class UserTopicLikeModel extends Model
{

    protected $table = "user_topic_like";

    protected $primaryKey = 'id';

    protected $fillable = ['uid', 'topic_id', 'topic_uid', 'topic_del', 'created_at'];

    protected $guarded = 'id';

    public $timestamps = false;

    public static function createBy(MemberModel $member, UserTopicModel $topic)
    {
        return self::create([
            'uid'        => $member->uid,
            'topic_id'   => $topic->id,
            'topic_uid'  => $topic->uid,
            'topic_del'  => 0,
            'created_at' => time()
        ]);

    }

    public function topic()
    {
        return $this->hasOne(UserTopicModel::class, 'id', 'topic_id');
    }


}
