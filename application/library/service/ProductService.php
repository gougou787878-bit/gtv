<?php
/**
 * 产品service层
 *
 * PHP version 7.1.0
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 *
 * @file ProductService.php
 * @author xiongba
 * @version 1.0
 * @package service
 */

namespace service;

use tools\RedisService;

/**
 * 产品service层
 * Class ProductService
 * @package service
 * @author xiongba
 * @date 2020-03-12 15:29:01
 */
class ProductService
{


    /**
     * 获取金币列表
     * @param \MemberModel $member
     * @param $type
     * @return array
     * @author xiongba
     * @date 2020-03-12 15:30:01
     */
    public function diamond(\MemberModel $member, $type)
    {
        return cached(\ProductModel::MONEY_PRODUCT_LIST)
            ->suffix("_{$type}")
            ->expired(86400)
            ->serializerJSON()
            ->compress([config('img.img_ads_url')])
            ->fetch(function () use ($type) {
                $data = \ProductModel::getByType($type);
                $result = [];
                /** @var \ProductModel $item */
                foreach ($data as $item) {
                    $result[] = $item->toApiArray();
                }
                return array_group($result, 'pt');
            });
    }



    /**
     * 获取指定类型的产品列表
     * @param $type
     * @return array
     * @author xiongba
     */
    public function getProductListType($type,\MemberModel $member)
    {
        $redisKey = \ProductModel::MONEY_PRODUCT_LIST . "_v2_{$type}";
        $list = cached($redisKey)
            ->expired(86400)
            ->serializerJSON()
            ->fetch(function () use ($type, $redisKey) {
                $result = \ProductModel::where('status', 1)
                    ->where('type', $type)
                    ->when(\ProductModel::TYPE_VIP == $type, function ($query) {
                        return $query->with([
                            'map' => function ($query) {
                                return $query->where(['status' => \ProductRightMapModel::STATUS_YES])
                                    ->with('right')
                                    ->orderByDesc('sort');//权益排序
                            }
                        ]);
                    })
                    ->orderBy('sort_order', 'asc')
                    ->get();
                $list = [];
                /** @var \ProductModel $row */
                foreach ($result as $row) {
                    //产品权益 权益 设配器
                    $map = function () use ($row) {
                        if (empty($row->map)) {
                            return [];
                        }
                        return collect($row->map)->map(function (\ProductRightMapModel $_mp) {
                            if ($_right = $_mp->right) {
                                return [
                                    'id'       => $_right->id,
                                    'name'     => $_right->name,
                                    'sub_name' => $_right->sub_name,
                                    'desc'     => $_right->desc,
                                    'img_url'  => $_right->img_url,
                                ];
                            }
                        })->filter()->values()->toArray();
                    };

                    $value = [
                        'id'          => $row->id,
                        'pname'       => $row->pname,
                        'pt'          => $row->pay_type,
                        'img'         => url_ads($row->img),
                        'op'          => (int)$row->price / 100,// 原价
                        'p'           => (int)$row->promo_price / 100, // 现价
                        'coins'       => (int)$row->coins,// 多少金币
                        'free_coins'  => (int)$row->free_coins,//赠送金币
                        'description' => $row->description,
                        'valid_date'  => sprintf('有效期:%d天', $row->valid_date),
                        'vip_level'   => 0  , //紧急修复  $row->vip_level,
                        'pw'          => $row->getPayWay(),
                        'pw_new'      => $row->getPayWayNew(),
                        'right'       => $map(),//权益适配器
                    ];
                    if ($row->pay_type == 'online') {
                        $list['online'][] = $value;
                    } elseif ($row->pay_type == 'agent') {
                        $list['agent'][] = $value;
                    }
                }
                \CacheKeysModel::createOrEdit($redisKey, '产品列表');
                return $list;
            });

        return $this->filterProduct($list , $member);

    }


    public function filterProduct($list, \MemberModel $member)
    {
        foreach (['online', 'agent'] as $key) {
            if (!isset($list[$key])) {
                continue;
            }
            $listGoods = $list[$key];
            $regdate = $member->getOriginal('regdate');
            $special_id = explode(',', setting('new_user_pid', ''));
            $newList = [];
            $yesterday_time = time() - 86400;
            foreach ($listGoods as $_goods) {
                if (in_array($_goods['id'], $special_id)) {
                    if ($regdate < $yesterday_time) {
                        continue;
                    }
                }
                $newList[] = $_goods;
            }
            $list[$key] = $newList;
        }

        return $list;
    }

}