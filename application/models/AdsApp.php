<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class AdsAppModel
 *
 * @property int $id 
 * @property string $title 标题
 * @property string $short_name  英文替换标识
 * @property string $description 描述
 * @property string $img_url ico地址
 * @property string $link_url 跳转地址
 * @property int $status 0-禁用，1-启用
 * @property int $clicked 点击次数
 * @property int $sort 排序
 * @property int $platform
 * @property int $created_at 创建时间
 *
 * @author xiongba
 * @date 2020-10-21 12:30:57
 *
 * @mixin \Eloquent
 */
class AdsAppModel extends Model
{

    protected $table = "ads_app";

    protected $primaryKey = 'id';

    protected $fillable = ['title', 'description', 'img_url', 'link_url', 'status', 'clicked', 'sort', 'created_at','platform','short_name'];

    protected $guarded = 'id';

    public $timestamps = false;


    const PLAT_FORM_POLO = 0;
    const PLAT_FORM_AV = 1;
    const PLAT = [
        self::PLAT_FORM_POLO=>'菠萝',
        self::PLAT_FORM_AV=>'Game',
    ];
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;
    const STATUS = [
        self::STATUS_FAIL    => '禁用',
        self::STATUS_SUCCESS => '启用'
    ];

    const REDIS_ADS_KEY = 'adsapp';
    const REDIS_SHARE_KEY = 'shareapp';
    public static function clearRedisCache()
    {
         redis()->del(self::REDIS_ADS_KEY);
         redis()->del(self::REDIS_SHARE_KEY);
         redis()->del(self::REDIS_ADS_KEY.':'.self::PLAT_FORM_POLO);
         redis()->del(self::REDIS_ADS_KEY.':'.self::PLAT_FORM_AV);
         return true;
    }

    static function getDataList($platForm=0,$limit = 50)
    {
        $w['status']=self::STATUS_SUCCESS;
       // $w['platform']=$platForm;
        return self::where($w)->orderByDesc('sort')->orderByDesc('id')->limit($limit)->get();
    }

    static function incrDownLoadNumber($id, $number = 1)
    {
        return self::where('id', '=', $id)->increment('clicked', $number);
    }

    static function getShareData(){
        return cached(self::REDIS_SHARE_KEY)->expired(3600)->serializerJSON()->fetch(function (){
            try{
                $data = file_get_contents(SYSTEM_SHARE_LINK);
                return json_decode($data,true);
            }catch (Throwable $exception){

                errLog("公共配置分享获取超时".$exception->getMessage());
            }
            return [];
        });
    }

    static function convertURLHOST($short_name,$link_url){
        if(empty($short_name)){
            return $link_url;
        }
        $data = self::getShareData();

        if($data){
            $origin_host = parse_url($link_url,PHP_URL_HOST);
            $replace_host = isset($data[$short_name])?$data[$short_name]:'';
            if($replace_host){
                return str_ireplace($origin_host,$replace_host,$link_url);
            }
        }
        return $link_url;
    }


}
