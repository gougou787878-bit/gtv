<?php


namespace service;

class TabService extends \AbstractBaseService
{

    const TAB_LIST_KEY = 'tab:list';
    const TAB_CAT_KEY = 'tab:cat';
    const TAB_SEARCH_KEY = 'tab:sear';
    const TAB_UPLOAD_KEY = 'tab:up';


    static function getCateList(){
        return self::getTabDataByType(self::TAB_CAT_KEY);

    }
    static function getSearchList(){
        return self::getTabDataByType(self::TAB_SEARCH_KEY);

    }
    static function getTabList(){
        return self::getTabDataByType(self::TAB_LIST_KEY);

    }
    static function getUploadTabList(){
        return self::getTabDataByType(self::TAB_UPLOAD_KEY);
    }

    protected static function getTabDataByType($type)
    {
        return cached($type)->serializerJSON()
            ->usingFuck()
            ->expired(4000)
            ->fetch(function () use ($type) {
                \CacheKeysModel::createOrEdit($type, 'tab列表');
                if($type == self::TAB_LIST_KEY){
                    $query = \TabModel::queryTab();
                }elseif($type == self::TAB_CAT_KEY){
                    $query = \TabModel::queryCategory();
                }elseif($type == self::TAB_SEARCH_KEY){
                    $query = \TabModel::querySearch();
                }elseif($type == self::TAB_UPLOAD_KEY){
                    $query = \TabModel::queryUp();
                }
                return $query->orderByDesc('sort_num')
                    ->get(['tab_id', 'tab_name', 'tags_str'])
                    ->toArray();
            });
    }

    static function clearTabList()
    {
        redis()->del(self::TAB_LIST_KEY);
        redis()->del(self::TAB_CAT_KEY);
        redis()->del(self::TAB_SEARCH_KEY);
        redis()->del(self::TAB_UPLOAD_KEY);
    }


}