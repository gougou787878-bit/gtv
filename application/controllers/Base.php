<?php

use Tbold\Serv\biz\BizAppVisit;
use tools\IpLocation;

/**
 * Class BaseController
 */
class BaseController extends \Yaf\Controller_Abstract
{
    /** @var MemberModel */
    public $member; // 用户信息
    public $config; // 配置信息
    public $post;

    // 分页参数
    public $page;
    public $limit;
    public $offset;
    public $toPage = false;
    public $position; // 位置信息
    public $channel='';
    public $isChanLiveRequest = false;//是否直播接入
    protected $last_ix;

    public function init()
    {
        register('controller',$this);
        defined('TABLE_PREFIX') or define('TABLE_PREFIX', 'ks_');
        $this->post = &$_POST;
        $this->config = \Yaf\Registry::get('config');
        //$this->verifyVersion();
        //白名单放行
        if (in_array(USER_IP, getBanIpList()) || redis()->sIsMember('ban:ip:list', USER_IP)) {
            header("Status: 503 Service Unavailable");
            exit();
        }
        // 获取用户信息
        $this->member = LibMember::getInstance()->FetchMember();
        if (!$this->member && $this->member['uid'] < 1) {
            header("Status: 503 Service Unavailable");
            exit();
        }

        $this->channel = $this->member['build_id'] ?? '';

        // 分页参数
        $this->initPageConfig();

        // 位置信息
        $this->initPosition();
        defined('USER_COUNTRY') or define('USER_COUNTRY', ($this->position['country'] ?? '中国') == '中国' ? 'CN' : 'US');
        //永久拉黑处理
        if ($this->member['role_id'] == 4) {
            exit('当前服务维护中...');
        }
        //用户行为
        if($this->channel){
            /** @var MemberModel $member */
            $member = request()->getMember();
            $biz = BizAppVisit::make([]);
            $biz->setUuid($member->uuid);
            $biz->setAgentChannel($this->channel);
            $biz->setUid($member->uid);
            $biz->setCreatedAt(TIMESTAMP);
        }
    }

    /**
     * 验证版本
     * @author xiongba
     */
    public function verifyVersion(){
        $firstVersion = setting('first:version' , '0');
        $lastVersion = setting('last:version' , '99999');
        $currentVersion = $_POST['version'] ?? false;
        if (empty($currentVersion) || !\helper\OperateHelper::inVer($currentVersion , $firstVersion , $lastVersion)){
            header("Status: 503 Service Unavailable");
            exit();
        }
    }


    protected function initPageConfig(){
        $this->limit = $_POST['limit'] ?? 24;
        $this->page = $_POST['page'] ?? 1;
        if ($this->page <= 0) {
            $this->page = 1;
        }
        $this->offset = ($this->page - 1) * $this->limit;
        if (isset($_POST['limit']) && isset($_POST['limit'])) {
            $this->toPage = true;
        }
        $this->last_ix = $_POST['last_ix'] ?? -1;
    }

    /**
     * 初始化用户位置信息
     */
    protected function initPosition()
    {
        $position = cached(IP_LOCATION_KEY)
            ->serializerJSON()
            ->fetch(function () {
                //$position = \itbdw\Ip\IpLocation::getLocation(USER_IP);
                $position = IpLocation::getLocation(USER_IP);
                if (empty($position)) {
                    $position = [];
                }
                return $position;
            });

        if (!isset($position['country'])) {
            $position['country'] = '中国';
        }
        if (!isset($position['city'])) {
            $position['city'] = '火星';
        }
        if (!isset($position['province'])) {
            $position['province'] = '火星';
        }
        $this->position = $position;
    }



    protected function errLog($msg, $type = 3)
    {
        errLog($msg, $type);
    }

    public function errorJson($msg, $status = 0, $data = null)
    {
        return $this->showJson($data, $status, $msg);
    }

    /**
     * @param $msg
     * @param array $extra
     * @return bool|mixed
     */
    public function successMsg($msg, array $extra = [])
    {
        return $this->showJson(array_merge(['success' => true, 'msg' => $msg], $extra));
    }

    /**
     * 返回数据
     * @param $data
     * @param int $status
     * @param string $msg
     * @return bool
     */
    public function showJson($data, $status = 1, $msg = '')
    {
        @header('Content-Type: application/json');
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $url_replace = [];
        if (defined('APP_TYPE_FLAG') && APP_TYPE_FLAG == 1) {
            //区分 国内 国外 加速
            //将国外源换成国内源 避免后台有漏网之鱼，将后台源也换成国内源
            $cnBase = TB_IMG_PWA_CN;
            $cnBase = parse_url($cnBase, PHP_URL_HOST);
            $url_replace['images.91tv.tv'] = $cnBase;
            $url_replace['imgpublic.ycomesc.com'] = $cnBase;
            $url_replace['imgpublic.ycomesc.live'] = $cnBase;
        } else {
            //pwa端
            $cnBase = parse_url(TB_IMG_PWA_CN , PHP_URL_HOST);
//            if (version_compare($_POST['version'] ?? '1.0.0', '3.0.0', '>=')) {
//                $cnBase = parse_url('https://newh5.niqcaok.cn', PHP_URL_HOST);
//            }
            $url_replace['images.91tv.tv'] = $cnBase;
            $url_replace['imgpublic.ycomesc.com'] = $cnBase;
            $url_replace['imgpublic.ycomesc.live'] = $cnBase;
        }
        $data = str_ireplace(array_keys($url_replace), array_values($url_replace), $data);
        //$data = str_replace($this->config->img->us_base_url, $this->config->img->cn_base_url, $data);
        $data = json_decode($data, true);
        $returnData = [
            'data'      => $data,
            'status'    => $status,
            'msg'       => $msg,
            'crypt'     => true,
            'isVV'      => $this->member['expired_at'] > TIMESTAMP,
            'needLogin' => $this->needLogin(),
            'isLogin'   => ($this->member['username'] ?? '') != '',
            'req_time'  => TIMESTAMP
        ];
        if (MODULE_NAME_TEST and !isset($_POST['crypt'])) {
            $crypt = APP_TYPE_FLAG ? (new LibCrypt()) : (new LibCryptPwa());
            $returnData = $crypt->replyData($returnData);
            return $this->getResponse()->setBody($returnData);

        } else {
            return $this->getResponse()->setBody(json_encode($returnData, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 发放登录token
     * @param string $uuid
     * @return string
     */
    public function token($uuid = ''): string
    {
        $signKey = $this->config->token->login ?? '';
        $uuid = $uuid == '' ? $this->member['uuid'] : $uuid;
        return request()->getMember()->token();
    }

    /**
     * H5 token 验证器
     * @param $uuid
     * @param $token
     * @return bool
     */
    public function verifyToken($uuid, $token)
    {
        $signKey = $this->config->token->login ?? '';
        if ($token != md5($signKey . $uuid . $signKey)) {
            return false;
        }
        return true;
    }


    /**
     * 是否需要登陆
     * @return bool
     * @author xiongba
     */
    public function needLogin(): bool
    {
        if (empty($this->member['is_reg'])) {
            return false;
        }
        return !$this->hasLogin();
    }

    /**
     * 是否登录
     * @return bool
     */
    public function hasLogin(): bool
    {
        $userToken = $this->post['token'] ?? '';
        if (empty($userToken)) {
            return false;
        }
        return ($userToken == $this->token($this->member['uuid']));
    }

    /**
     * 保存缓存key
     * @param $key
     * @param $value
     * @param $memo
     * @param bool $timestamp
     */
    public function setCacheWithSql($key, $value, $memo, $timestamp = false)
    {
        $data = [
            'name' => $memo,
            'key' => $key,
        ];
        CacheKeysModel::updateOrCreate($data);
        \tools\RedisService::set($key, $value, $timestamp);
    }

    /**
     * 格式化时间戳
     * @param string $timestamps
     * @return false|string
     */
    public function formatTimestamp(string $timestamps = '')
    {
        return  formatTimestamp($timestamps);
    }



    /**
     * @throws Exception
     */
    protected function verifyMemberSayRole()
    {
        $member = request()->getMember();
        if ($member->isBan()) {
            throw new \Exception('您已被禁言');
        }
    }

    /**
     * @param int $ttl
     * @param string $prefix
     * @throws Exception
     */
    protected function verifyFrequency(int $ttl = 1, $prefix = '')
    {
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS);
        if (!isset($debug[1])) {
            return;
        }
        $hash = md5($debug[0]['file'] . $debug[0]['line']);
        if (!redis()->setnxttl($prefix . 'fr:' . $this->member['aff'] . ':' . $hash, 1, $ttl)) {
            throw new \Exception('您操作太快了，休息一下再来');
        }
    }

    /**
     * @throws Exception
     */
    protected function verifyAuth()
    {
        $member = request()->getMember();
        if (false && $member->isAuthStatus()) {
            throw new \Exception('只有认证用户可以操作');
        }
    }

    /**
     * @throws Exception
     */
    protected function verifyFeeVip()
    {
        $member = request()->getMember();
        if ($member->isFeeVip()) {
            throw new \Exception('只有收费会员才能操作');
        }
    }

    /**
     * 返回含有last_idx的列表
     *
     * 参数传递 两种方式传递参数等效
     *  $this->listJson($list , string $column ,array $extra)
     *  $this->listJson($list , array $extra ,string $column)
     *
     * 返回事例
     * ```php
     * merge( [
     *     'list' : $list,
     *     'last_idx' : last($list)[id],
     * ] , $extra )
     * ```
     *
     *
     * @param $list
     * @param string|array $column
     * @param array|string $extra
     * @return array|bool|mixed
     */
    public function listJson($list, $column = 'id', $extra = [])
    {
        if (is_array($column)) {
            // 当column参数是数组时候，交换column和extra的值，
            if (is_string($extra)) {
                list($extra, $column) = [$column, $extra];
            } else {
                list($extra, $column) = [$column, 'id'];
            }
        }
        if ($list instanceof \Illuminate\Support\Collection) {
            $list = $list->toArray();
        }

        $last_end = collect($list)->last();
        if (is_array($last_end) || $last_end instanceof ArrayAccess) {
            $last_idx = $last_end[$column] ?? '0';
        } else {
            $last_idx = $last_end;
        }
        if (empty($last_idx)) {
            $last_idx = (string)$last_idx;
        }
        $ret = array_merge([
            'list'    => $list,
            'last_ix' => (string)$last_idx
        ], $extra);

        return $this->showJson($ret);
    }

}
