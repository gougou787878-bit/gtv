<?php


/**
 * class UserLikesModel
 *
 * @property int $id
 * @property int $mv_id
 * @property string $updated_at
 * @property string $created_at
 * @property int $uid
 *
 * @author xiongba
 * @date 2020-02-28 15:24:48
 *
 * @mixin \Eloquent
 */
class UserLikeModel extends EloquentModel
{
    protected $table = 'user_likes';

    protected $fillable = [
        'mv_id', 'uid'
    ];

    public $timestamps = true;

    public function videos()
    {
        return $this->hasOne(MvModel::class, 'id', 'mv_id')
            ->with('user:uid,nickname,thumb,vip_level,sexType,expired_at,uuid,aff');
    }
}