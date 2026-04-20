<?php


use Illuminate\Database\Eloquent\Model;

/**
 * class SexTalkModel
 *
 * @property int $id 
 * @property string $tips 描述
 * @property int $status 状态  1 启用
 * @property string $created_at 
 *
 * @author xiongba
 * @date 2021-01-19 10:32:05
 *
 * @mixin \Eloquent
 */
class SexTalkModel extends Model
{

    protected $table = "sex_talk";

    protected $primaryKey = 'id';

    protected $fillable = ['tips', 'status', 'created_at'];

    protected $guarded = 'id';

    public $timestamps = false;


    const STAT_ENABLE = 1;
    const STAT_DISABLE = 0;

    const STAT = [
        self::STAT_ENABLE  => '启用',
        self::STAT_DISABLE => '禁用',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function queryBase()
    {
        return self::where('status', '=', self::STAT_ENABLE);
    }

    const SEX_TALK = 'sex:talk';

    /**
     * 热点配置全数据
     *
     * @return mixed
     */
    public static function getSexData()
    {
        $data = cached(self::SEX_TALK)->expired(3600)->serializerPHP()->fetch(function () {
            $data = self::queryBase()->select(['id', 'tips'])->get();
            if (!$data) {
                return [];
            }
            return $data->toArray();
        });
        return $data;
    }
    static function getSaohuaTips($sid)
    {
        $data = self::getSexData();
        $data = array_column($data, 'tips', 'id');
        return isset($data[$sid]) ? $data[$sid] : '';
    }

    /**
     *  清除缓存
     * @return int
     */
    public static function clearSexTalkCache()
    {
        return redis()->del(self::SEX_TALK);
    }

    /**
     * 获取骚花配置 随机块结构
     * @return array|mixed|null
     */
    public static function getHotMVSliceData()
    {
        static $data = null;
        if ($data === null) {
            $data = self::getSexData();
        }
        if (!$data) {
            return [];
        }
        if (count($data) < 2) {
            return $data;
        }
        shuffle($data);
        $sand = rand(3, 8);
        $chunkData = array_chunk($data,$sand);
        return $chunkData[0];
    }



}
