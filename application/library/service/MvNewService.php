<?php


namespace service;


use DB;
use helper\QueryHelper;
use MvModel;
use repositories\MvRepository;
use repositories\UsersRepository;
use UserAttentionModel;
use const Yaf\ENVIRON;

class MvNewService extends \AbstractBaseService
{

    public function formatItem($datum, $watchByMember = null)
    {
        if(is_null($datum) || empty($datum)){
            return [];
        }

        /** @var MvModel $datum */
        $datum->addHidden([
            'v_ext',
            'cover_thumb',
            'm3u8',
            'full_m3u8',
             'is_hide',
            'y_cover',
            'tags',
            'actors',
            'category',
            'via',
            'onshelf_tm',
            'music_id',
            'thumb_width',
            'thumb_height',
            'gif_thumb',
            'gif_width',
            'gif_height',
            'directors',
            'thumb_start_time',
            'thumb_duration',
            'thumb_start_time',
        ]);
        if ($watchByMember !== false) {
            $datum->watchByUser($watchByMember);
            if ($datum->user) {
                $datum->user->addHidden([
                    'phone',
                    'birthday',
                    'thumb',
                    'app_version',
                    'regip',
                    'lastvisit',
                    'lastip',
                    'username',
                    'password',
                    'oauth_id',
                    'build_id',
                    'oauth_type',
                    'uuid',
                    'gender',
                    'regdate',
                    'invited_by',
                    'invited_num',
                    'login_count',
                    'chat_uid',
                    'live_supper',
                    'is_live_super',
                    'role_id',
                    'role_type',
                    'new_topic_reply',
                    'validate',
                    'share',
                    'score_total',
                    'is_reg',
                    'live_count',
                    'videos_count',
                    'fabulous_count',
                    'consumption',
                    'votes',
                    'votes_total',
                    'user_activation_key',
                    'level_anchor',
                    'tui_coins',
                    'total_tui_coins',
                    'score',
                    'coins',
                    'fans_count',
                    'is_recommend',
                    'followed_count',
                    'likes_count',
                    'sexType',
                    'level',
                    'exp',
                    'coins_total',
                   // 'person_signnatrue',
                ]);
                $datum->user->watchByUser($watchByMember);
            } else {
                $datum->user = \MemberModel::virtualByForDelele();
            }
        }
        $datum->is_free = $datum->coins <= 0 ? 1 : 0;
        $attributes = $datum->getAttributes();
        $m3u8 = $attributes['m3u8'] ?? null;
        if (empty($m3u8)) {
            $m3u8 = $attributes['full_m3u8'] ?? '';
        }
        $datum->play_url = getPlayUrl($m3u8, true);
        $datum->hasLongVideo = $datum->duration > 30;
        if(IS_FAKE_CLIENT){
            $datum->play_url = getPlayUrl(ILLEGAL_ORG_VIDEO);
        }

        return $datum;
    }

    public function v2format($items, $watchByMember = null)
    {
        if (empty($items)) {
            return [];
        }
        $lists = [];
        foreach ($items as $datum) {
            $lists[] = $this->formatItem($datum, $watchByMember);
        }

        return $lists;
    }


    /**
     * @param \MemberModel $member
     * @return array
     */
    public function hottestList(\MemberModel $member): array
    {
        //晚上11点到凌晨1点高峰期走固定列表,90%的概率
        if (in_array((int)date('H'),[22,23,0,1]) && rand(1,10) != 10){
            $this->getHotListBusy($member);
        }
        $uid = $member->uid;
        $perfect = 'hottest';
        $videoIds = redis()->sMembers($perfect);
        $ttl = redis()->ttl($perfect.'_ttl');

        if (empty($videoIds) || $ttl<60) {
            $videoIds = MvModel::queryBase()
                ->where('topic_id',0)
                ->where('like','>',199)
                ->pluck('id')->toArray();
            redis()->sAddArray($perfect, $videoIds);
            redis()->setex($perfect.'_ttl',900,1);
        }
        $history = new VisitHistoryService($uid);
        $historyIds = $history->getAll() ?? [];
        $list = collect($videoIds)->diff($historyIds);
        $limit = 20;
        if ($list->count() < $limit) {
            return [];
        }
        $list = $list->shuffle()->slice(0, $limit);
        $items = \MvModel::queryMv()
            ->whereIn('id', $list)
            ->orderByDesc('like')
            ->get();
        return $this->v2format($items, $member);
    }

    public function getHotListBusy(\MemberModel $member){
        list($page,$limit) = QueryHelper::pageLimit();
        $key = sprintf('home:hot:mv:list:%d:%d',$page,$limit);
        $items = cached($key)
            ->serializerPHP()
            ->expired(3600)
            ->fetch(function () use ($page,$limit){
                return  MvModel::queryMv()
                    ->where('like', '>', 199)
                    ->orderByDesc('like')
                    ->orderByDesc('id')
                    ->forPage($page,$limit)
                    ->get();
            });

        return $this->v2format($items, $member);
    }

    /**
     * tab av
     * @param int $tab_id
     * @param \MemberModel $member
     * @return array
     */
    public function getTabList($tab_id,\MemberModel $member)
    {
        //晚上11点到凌晨1点高峰期走固定列表,90%的概率
        if (in_array((int)date('H'),[22,23,0,1]) && rand(1,10) != 10){
            $this->getTabListBusy($tab_id,$member);
        }

        $uid = $member->uid;
        $perfect = 'tab:data:'.$tab_id;
        $videoIds = redis()->sMembers($perfect);
        $ttl = redis()->ttl($perfect.'_ttl');
        if (empty($videoIds) || $ttl<60) {
            $matchStr = \TabModel::getMatchString($tab_id);
            $videoIds =  \MvModel::queryBase()
                ->where('topic_id',0)
                ->whereRaw("match(tags) against(? IN BOOLEAN MODE)", [$matchStr])
                ->pluck('id')->toArray();
            redis()->sAddArray($perfect, $videoIds);
            redis()->setex($perfect.'_ttl',1000,1);
        }
        $history = new VisitHistoryService($uid);
        $historyIds = $history->getAll() ?? [];
        $list = collect($videoIds)->diff($historyIds);
        $limit = 20;
        if ($list->count() < $limit) {
            return [];
        }
        $list = $list->shuffle()->slice(0, $limit);
        $history->addVisit($list);
        $items = \MvModel::queryMv()
            ->whereIn('id', $list)
            ->orderByDesc('refresh_at')
            ->get();
        $items = $this->v2format($items, $member);
        return $items;
    }

    /**
     * tab av
     * @param int $tab_id
     * @param \MemberModel $member
     * @return array
     */
    public function getTabListBusy($tab_id,\MemberModel $member,$sort='new')
    {
        list($page,$limit) = QueryHelper::pageLimit();
        $key = sprintf('home:tab:mv:list:%d:%d:%d:%s',$tab_id,$page,$limit,$sort);
        $items = cached($key)
            ->serializerPHP()
            ->expired(3600)
            ->fetch(function () use ($tab_id,$sort,$page,$limit){
                $matchStr = \TabModel::getMatchString($tab_id);
                return  MvModel::queryMv()
                    ->whereRaw("match(tags) against(? in boolean mode)", [$matchStr])
                    ->when($sort,function ($query)use($sort){
                        if($sort == 'pay'){
                            $query->orderByDesc('count_pay');
                        }elseif ($sort == 'view'){
                            $query->orderByDesc('rating');
                        }else{
                            $query->orderByDesc('refresh_at');
                        }
                    })
                    ->orderByDesc('id')
                    ->forPage($page,$limit)
                    ->get();
            });

        return $this->v2format($items, $member);
    }

}