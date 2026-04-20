<?php


namespace service;

use AdsModel;
use ConstructModel;
use Illuminate\Support\Collection;
use MemberHotRankModel;
use MemberModel;
use NagConfModel;
use NavigationModel;
use MvModel;

class TabNewService extends \AbstractBaseService
{

    public function getNagList(MemberModel $member){
        $data[] = [
            'current'   => false,
            'id'        => -1,
            'name'      => '关注',
            'type'      => NavigationModel::NAG_CAT_ATT,
            'mid_style' => 0,
            'bot_style' => 0,
            'api'       => '/api/mvnew/listOfFollow',
            'params'    => ['id' =>0],
        ];
        $nags = NavigationModel::getList(NavigationModel::NAG_TYPE_MW);
        /** @var NavigationModel $nag */
        foreach ($nags as $nag) {
            $_data = [];
            $_data['current'] = (bool)$nag->is_current;
            $_data['id'] = $nag->id;
            $_data['name'] = $nag->title;
            $_data['type'] = NavigationModel::NAG_CAT_COM;
            $_data['h5_url'] = '';
            if ($nag->is_h5){
                $_data['type'] = NavigationModel::NAG_CAT_H5;
                $_data['h5_url'] = str_replace("{token}", \LibCryptPwa::encrypt($member->uuid, ''), $nag->h5_url);
                $_data['h5_url'] =  html_entity_decode(replace_share($_data['h5_url']));
            }
            $_data['api'] = "/api/tabnew/list_construct";
            if ($nag->is_find == NavigationModel::FIND_YES){
                $_data['type'] = NavigationModel::NAG_CAT_FIND;
                $_data['api'] = '/api/tabnew/list_discovery';
            }
            $_data['params'] = ['nag_id' =>$nag->id];
            $_data['mid_style'] = $nag->mid_style;
            $_data['bot_style'] = $nag->bot_style;

            $data[] = $_data;
        }

        return $data;
    }

    /**
     * @throws \Exception
     */
    public function getMidStyle(NavigationModel $nag){
        $data = [
            'mid_style_recommend' => [],
            'mid_style_category' => [],
        ];
        switch ($nag->mid_style){
            case NavigationModel::MID_STYLE_NULL:
                break;
            case NavigationModel::MID_STYLE_RECOMMEND:
                $data['mid_style_recommend'] = $this->getMidStyleRecomend($nag);
                break;
            case NavigationModel::MID_STYLE_CATEGORY:
                $data['mid_style_category'] = $this->getMidStyleCategory($nag);
                break;
            default:
                throw new \Exception("类型错误");
        }

        return $data;
    }

    public function getMidStyleRecomend(NavigationModel $nag){
        $conf_list =  NagConfModel::getConf($nag->id,NavigationModel::MID_STYLE_RECOMMEND);
        $data = [];
        collect($conf_list)->each(function ($conf) use (&$data){
            $data[] = [
                'title' => $conf->title,
                'icon'  => $conf->icon,
                'api'   => $conf->api,
                'type'  => $conf->type,
            ];
        });
        return $data;
    }

    public function getMidStyleCategory(NavigationModel $nag){
        $nag_id = $nag->id;
        $conf_list =  NagConfModel::getConf($nag_id,NavigationModel::MID_STYLE_CATEGORY,1);
        $title = "更多";
        $limit = 12;
        $icon = '';
        if (collect($conf_list)->count() > 0){
            $conf = collect($conf_list)->shift();
            /** @var NagConfModel $conf */
            $limit = $conf->show_num;
            $title = $conf->title;
            $icon = $conf->icon;
        }
        $cat_list =  ConstructModel::getTabListByNag($nag_id, 1, $limit);
        $cat_list = collect($cat_list);
        $current_count = $cat_list->count();
        if ($current_count == $limit){
            $cat_count =  ConstructModel::getTabCountByNag($nag_id);
            if ($cat_count > $current_count){
                $cat_list->pop();
                $last = ConstructModel::make([
                    'title' => $title,
                    'bg_thumb' => $icon,
                    'icon' => $icon,
                    'work_num' => 0,
                    'favorites_num' => 0,
                ]);
                $last->id = -1;
                $cat_list->push($last);
            }
        }

        return $cat_list;
    }

    /**
     * @throws \Exception
     */
    public function getBotStyle(MemberModel $member, NavigationModel $nag, $sort, $page, $limit){
        $data = [
            'bot_style_one' => [],
            'bot_style_two' => []
        ];
        switch ($nag->bot_style){
            case NavigationModel::BOT_STYLE_NULL:
                break;
            case NavigationModel::BOT_STYLE_ONE:
                $data['bot_style_one'] = $this->getBotStyleOne($member, $nag, $page);
                break;
            case NavigationModel::BOT_STYLE_TWO:
                $data['bot_style_two'] = $this->getBotStyleTwo($member,$nag,$sort,$page,$limit);
                break;
            default:
                throw new \Exception("类型错误");
        }

        //统计点击数
        jobs([NavigationModel::class, 'incrByClickNum'],[$nag->id]);

        return $data;
    }

    /**
     * @desc 多个分类展开
     * @param $nag_id
     * @return array|Collection
     */
    public function getBotStyleOne(MemberModel $member, NavigationModel $nag, $page){
        $data = ConstructModel::getMvByTag($member, $nag, $page);
        if ($data) {
            return collect($data)->map(function ($item) {
                $item['list'] = (new \service\MvService())->v2format($item['list']);
                return $item;
            });
        }
        return [];
    }

    /**
     * @desc 所有分类一起查
     * @throws \Exception
     */
    public function getBotStyleTwo(MemberModel $member, NavigationModel $nag, $sort, $page, $limit)
    {
        $nag_id = $nag->id;
        //获取结构
        $construct_arr = ConstructModel::getConstructIdsByNag($nag->id);
        if (!$construct_arr){
            throw new \Exception("数据正在配置中");
        }
        $c_key = sprintf("nag:%d",$nag_id);
        if ($sort == "rand"){
            $data = MvModel::randMvs($member, $c_key, $construct_arr, $page, $limit);
        }elseif ($sort == "see"){
            $data = MvModel::listSeeNew($nag_id, MvModel::NAV_TYPE, $page, $limit);
        }elseif ($sort == "recommend"){
            $data = MvModel::listRecommend($nag_id, MvModel::NAV_TYPE, $page, $limit);
        }else{
            $data = MvModel::getMvDataByTags($c_key, $construct_arr, $sort, $page, $limit);
        }
        if ($data) {
            return (new \service\MvService())->v2format($data);
        }
        return [];
    }

    /**
     * @desc 结构视频列表
     */
    public function getMvDataByCat(MemberModel $member, ConstructModel $construct, $sort, $page, $limit){
        $construct_id = $construct->id;
        $is_com = 0;
        switch ($construct->type) {
            case ConstructModel::TYPE_LIKE:
                $sort = 'like';
                break;
            case ConstructModel::TYPE_NEW:
                $sort = 'new';
                break;
            case ConstructModel::TYPE_HOT:
                $sort = 'hot';
                break;
            case ConstructModel::TYPE_SALE:
                $sort = 'sale';
                break;
            default:
                $is_com = 1;
                break;
        }
        $c_key = sprintf("cat:%d",$construct_id);
        if ($is_com == 0){
            $construct_id = 0;
        }

        if ($sort == "rand"){
            $data = MvModel::randConstructMvs($member, $c_key, $construct_id, $page, $limit);
        }elseif ($sort == "see"){
            $data = MvModel::listSeeNew($construct_id, MvModel::CONSTRUCT_TYPE, $page, $limit);
        }elseif ($sort == "recommend"){
            $data = MvModel::listRecommend($construct_id, MvModel::CONSTRUCT_TYPE, $page, $limit);
        }else{
            $data = MvModel::getMvDataByTags($c_key, $construct_id, $sort, $page, $limit);
        }
        if ($data) {
            return (new \service\MvService())->v2format($data);
        }
        return [];
    }

    public function tab_list(MemberModel $member, $nag_id, $page, $limit){
        $tab_list =  ConstructModel::getTabListByNag($nag_id, $page, $limit);
        if (collect($tab_list)->count() > 0){
            return $this->formatTab($member,$tab_list);
        }
        return [];
    }

    public function formatTab(MemberModel $member, $tab_list){
        return collect($tab_list)->map(function (ConstructModel $tab) use ($member){
            $tab->watchByUser($member);
            return $tab;
        });
    }

    /**
     * @desc 发现视频列表
     */
    public function listFind(MemberModel $member, $type, $sort, $page, $limit){
        $data = [];
        $is_aw = 0;
        switch ($type) {
            case ConstructModel::FIND_TYPE_VIP:
            case ConstructModel::FIND_TYPE_COINS:
                if ($sort == 'rand'){
                    $data = MvModel::randFindMvs($member, $type, $is_aw, $page, $limit);
                }else{
                    $data = MvModel::listFindMvs($type, $is_aw, $sort, $page, $limit);
                }
                break;
            case ConstructModel::FIND_TYPE_LIKE:
                $data = MvModel::listRecommendWeek(MvModel::WEEK_LIKE_TYPE, $page, $limit);
                break;
            case ConstructModel::FIND_TYPE_VIEW:
                $data = MvModel::listRecommendWeek(MvModel::WEEK_VIEW_TYPE, $page, $limit);
                break;
            default:
                test_assert(false, '类型错误');
                break;
        }

        if ($data) {
            return (new \service\MvService())->v2format($data);
        }
        return [];
    }

    public function hotRank(MemberModel $member, $page){
        $rank = [];
        if ($page == 1){
            //排行榜单
            $rank = cached("hot_rank_index")
                ->fetchPhp(function () {
                    $service = new TopCreatorService();
                    $uidAry = $service->getUp('moon', 10);
                    $user = MemberModel::whereIn('uid', $uidAry)->get(['uid', 'nickname', 'thumb']);
                    $user = array_sort_by_idx($user->toArray() , $uidAry , 'uid');
                    return [
                        'name' => '创作达人',
                        'type' => 'up',
                        'icon' => url_live('/new/xiao/20201014/2020101418031579387.png'),
                        'item' => $user
                    ];
                });
        }

        $list = MemberHotRankModel::getList($page);
        $new = [];
        $mvService = new MvService();
        foreach ($list as $value){
            $value->member->watchByUser($member);
            $tmp = $value->member;
            $tmp->mv_list = $mvService->v2format($value->mv_list, false);
            $new[] = $tmp;
        }

        return [
            'rank' => $rank,
            'list' => $new
        ];
    }

    public function listMvByTag(MemberModel $member, $tag, $sort, $page, $limit)
    {
        $show_aw = 'all';
        if ($sort == 'rand') {
            $data = MvModel::randTagMvs($member, $tag, $show_aw, $page, $limit);
        } else {
            $data = MvModel::tagMvList($tag, $sort, $show_aw, $page, $limit);
        }
        return (new MvService())->v2format($data);
    }

}