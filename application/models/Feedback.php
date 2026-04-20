<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class FeedbackModel
 *
 * @property int $id 
 * @property int $uid 用户ID
 * @property string $title 标题
 * @property string $version 系统版本号
 * @property string $model 设备
 * @property string $content 内容
 * @property int $addtime 提交时间
 * @property int $status 状态
 * @property int $platform
 * @property int $uptime 更新时间
 * @property string $thumb 图片
 *
 * @property MemberModel|null $withMember
 * @property FeedbackReplyModel|null $withReply
 *
 * @author xiongba
 * @date 2020-03-30 17:40:52
 *
 * @mixin \Eloquent
 */
class FeedbackModel extends Model
{

    protected $table = "feedback";

    protected $primaryKey = 'id';

    protected $fillable = ['uid', 'title', 'version', 'model', 'content', 'addtime', 'status', 'uptime', 'thumb','platform'];

    protected $guarded = 'id';

    public $timestamps = false;
    const STATUS_ING = 0;
    const STATUS_DONE = 1;
    const STATUS = [
        self::STATUS_ING  => '待处理',
        self::STATUS_DONE => '已处理',
    ];


    public function withReply()
    {
        return $this->hasOne(FeedbackReplyModel::class,'fid','id');
    }

    public function withMember()
    {
        return $this->hasOne(MemberModel::class, 'uid','uid');
    }

    public static function recoverVip($uid){
        /** @var MemberSnapshotModel $snapShot */
        $snapShot = MemberSnapshotModel::where('uid', $uid)->where('created_at','>','2024-04-25')->first();
        //不存在或者已经恢复 不处理。
        if (is_null($snapShot) || $snapShot->status) {
            return '';
        }

        $data = @json_decode($snapShot->data, true);
        if ($data && is_array($data)) {
            $user = $data['user'];
            $free_member = $data['free_member'];
            $member = MemberModel::find($snapShot->uid);
            if ($member) {
                if ($user['vip_level'] > $member->vip_level){
                    //恢复用户
                    $member->expired_at = max($user['expired_at'],$member->expired_at);
                    $member->vip_level = $user['vip_level'];
                    $member->save();
                    //恢复通卡设置
                    if ($free_member){
                        /** @var FreeMemberModel $freemember */
                        $freemember = FreeMemberModel::where('uid',$snapShot->uid)->first();
                        if ($freemember){
                            $freemember->expired_at = max($freemember->expired_at,$free_member['expired_at']);
                            $freemember->save();
                        }else{
                            if ($free_member['expired_at'] > TIMESTAMP){
                                FreeMemberModel::create([
                                    'uid' => $uid,
                                    'created_at' => time(),
                                    'expired_at' => $free_member['expired_at']
                                ]);
                            }
                        }
                    }
                    $snapShot->update(['status' => 1]);
                }
                MemberModel::clearFor($member->getAttributes());
            }
        }
    }
}
