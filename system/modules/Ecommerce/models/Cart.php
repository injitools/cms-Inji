<?php

/**
 * Cart
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;

class Cart extends \Model {

    public static $objectName = 'Корзины';

    public static function indexes() {
        return [
            'ecommerce_cartStatusBlock' => [
                'type' => 'INDEX',
                'cols' => [
                    'cart_cart_status_id',
                    'cart_warehouse_block'
                ]
            ],
            'ecommerce_cartStats' => [
                'type' => 'INDEX',
                'cols' => [
                    'cart_cart_status_id',
                ]
            ],
            'ecommerce_cartBlock' => [
                'type' => 'INDEX',
                'cols' => [
                    'cart_warehouse_block'
                ]
            ],
        ];
    }

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'cartItems' => [
                'type' => 'many',
                'model' => 'Ecommerce\Cart\Item',
                'col' => 'cart_id',
            ],
            'events' => [
                'type' => 'many',
                'model' => 'Ecommerce\Cart\Event',
                'col' => 'cart_id',
            ],
            'status' => [
                'model' => 'Ecommerce\Cart\Status',
                'col' => 'cart_status_id'
            ],
            'delivery' => [
                'model' => 'Ecommerce\Delivery',
                'col' => 'delivery_id'
            ],
            'payType' => [
                'model' => 'Ecommerce\PayType',
                'col' => 'paytype_id'
            ],
            'infos' => [
                'type' => 'many',
                'model' => 'Ecommerce\Cart\Info',
                'col' => 'cart_id'
            ],
            'deliveryInfos' => [
                'type' => 'many',
                'model' => 'Ecommerce\Cart\DeliveryInfo',
                'col' => 'cart_id'
            ],
            'extras' => [
                'type' => 'many',
                'model' => 'Ecommerce\Cart\Extra',
                'col' => 'cart_id'
            ],
            'card' => [
                'model' => 'Ecommerce\Card\Item',
                'col' => 'card_item_id'
            ],
            'pays' => [
                'type' => 'many',
                'model' => 'Money\Pay',
                'col' => 'data'
            ],
            'discounts' => [
                'type' => 'relModel',
                'relModel' => 'Ecommerce\Cart\Discount',
                'model' => 'Ecommerce\Discount',
            ]
        ];
    }

    public function beforeDelete() {
        foreach ($this->cartItems as $cartItem) {
            $cartItem->delete();
        }
        foreach ($this->infos as $info) {
            $info->delete();
        }
        foreach ($this->extras as $extra) {
            $extra->delete();
        }
        foreach ($this->events as $event) {
            $event->delete();
        }
    }

    public static $labels = [
        'user_id' => 'Пользователь',
        'cart_status_id' => 'Статус',
        'delivery_id' => 'Доставка',
        'comment' => 'Комментарий',
        'bonus_used' => 'Выгодные рубли',
        'complete_data' => 'Время заказа',
        'info' => 'Информация',
        'items' => 'Товары',
        'paytype_id' => 'Способ оплаты',
        'payed' => 'Оплачен',
        'exported' => 'Выгружено',
        'warehouse_block' => 'Блокировка товаров',
        'extra' => 'Доп.',
        'card_item_id' => 'Дисконтная карта',
        'info' => 'Информация',
        'contacts' => 'Информация',
        'pay' => 'Счета',
        'sums' => 'Суммы',
        'deliveryInfo' => 'Для доставки',
        'discount' => 'Скидки',
    ];
    public static $cols = [
        //Основные параметры
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'cart_status_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'status'],
        'delivery_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'delivery'],
        'paytype_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'payType'],
        'card_item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'card'],
        'warehouse_block' => ['type' => 'bool'],
        'payed' => ['type' => 'bool'],
        'comment' => ['type' => 'textarea'],
        //Системные
        'exported' => ['type' => 'bool'],
        'complete_data' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
        'date_status' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
        'date_last_activ' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
        'date_create' => ['type' => 'dateTime'],
        //Виджеты
        'sums' => [
            'type' => 'void',
            'view' => [
                'type' => 'widget',
                'widget' => 'Ecommerce\adminSums',
            ],
        ],
        'contacts' => [
            'type' => 'void',
            'view' => [
                'type' => 'widget',
                'widget' => 'Ecommerce\admin/contacts',
            ],
        ],
        //Менеджеры
        'extra' => ['type' => 'dataManager', 'relation' => 'extras'],
        'pay' => ['type' => 'dataManager', 'relation' => 'pays'],
        'items' => ['type' => 'dataManager', 'relation' => 'cartItems'],
        'info' => ['type' => 'dataManager', 'relation' => 'infos'],
        'deliveryInfo' => ['type' => 'dataManager', 'relation' => 'deliveryInfos'],
        'discount' => ['type' => 'dataManager', 'relation' => 'discounts'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'contacts',
                'items',
                'extra',
                'discount',
                'sums',
                'cart_status_id',
                'delivery_id',
                'deliveryInfo',
                'payed',
                'pay',
                'complete_data',
            ],
            'sortable' => [
                'cart_status_id',
                'delivery_id',
                'payed',
                'complete_data',
            ],
            'filters' => [
                'cart_status_id',
                'delivery_id',
                'payed',
                'complete_data',
            ],
            'preSort' => [
                'complete_data' => 'desc'
            ],
            'actions' => [
                'Ecommerce\CloseCartBtn', 'Open', 'Edit', 'Delete'
            ]
        ]
    ];

    public static function itemName($item) {
        return $item->pk() . '. ' . $item->name();
    }

    public static $forms = [
        'manager' => [
            'inputs' => [
                'userSearch' => [
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'user',
                    'label' => 'Покупатель',
                    'cols' => [
                        'info:first_name',
                        'info:last_name',
                        'info:middle_name',
                        'mail'
                    ],
                    'col' => 'user_id',
                    'required' => true,
                    'showCol' => [
                        'type' => 'staticMethod',
                        'class' => 'Ecommerce\Cart',
                        'method' => 'itemName',
                    ],
                ],
                'cardSearch' => [
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'card',
                    'label' => 'Дисконтная карта',
                    'cols' => [
                        'code',
                        'user:info:first_name',
                        'user:info:last_name',
                        'user:info:middle_name',
                        'user:mail'
                    ],
                    'col' => 'card_item_id',
                ],
            ],
            'map' => [
                ['userSearch', 'cart_status_id'],
                ['paytype_id', 'delivery_id'],
                ['cardSearch', 'comment'],
                ['warehouse_block', 'complete_data'],
                ['payed'],
                ['items'],
                ['extra'],
                ['pay'],
                ['info'],
                ['deliveryInfo']
            ]
        ],
    ];

    public function checkStage() {
        $sum = $this->itemsSum();
        $stages = Cart\Stage::getList(['order' => ['sum', 'asc']]);
        $groups = [];
        foreach ($stages as $stage) {
            if ($sum->greater(new \Money\Sums([$stage->currency_id => $stage->sum])) || $sum->equal(new \Money\Sums([$stage->currency_id => $stage->sum]))) {
                $groups[$stage->group] = $stage;
            }
        }
        $discounts = Cart\Discount::getList(['where' => ['cart_id', $this->id]]);
        foreach ($discounts as $discount) {
            if (!isset($groups[$discount->group]) && $discount->auto) {
                $discount->delete();
            }
            if (isset($groups[$discount->group]) && $groups[$discount->group]->type == 'discount') {
                $discount->discount_id = $groups[$discount->group]->value;
                $discount->save();
                unset($groups[$discount->group]);
            }
        }
        foreach ($groups as $group) {
            if ($group && $group->type == 'discount') {
                $rel = $this->addRelation('discounts', $group->value);
                $rel->auto = true;
                $rel->group = 'discount';
                $rel->save();
            }
        }
    }

    public function needDelivery() {
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->item->type && $cartItem->item->type->delivery) {
                return true;
            }
        }
        return false;
    }

    public function deliverySum() {
        $sum = new \Money\Sums([]);
        if ($this->delivery && $this->needDelivery()) {
            $sums = $this->itemsSum();
            $deliveryPrice = new \Money\Sums([$this->delivery->currency_id => $this->delivery->max_cart_price]);
            if ($this->delivery->max_cart_price && $sums->greater($deliveryPrice) || $sums->equal($deliveryPrice)) {
                $sum->sums = [$this->delivery->currency_id => 0];
            } else if ($this->delivery->prices) {
                foreach ($this->delivery->prices(['order' => ['cart_price', 'asc']]) as $delPrice) {
                    $deliveryPrice = new \Money\Sums([$delPrice->currency_id => $delPrice->cart_price]);
                    if ($sums->greater($deliveryPrice) || $sums->equal($deliveryPrice)) {
                        $sum->sums = [$delPrice->currency_id => $delPrice->price];
                    }
                }
                if (!$sum->sums) {
                    $sum->sums = [$this->delivery->currency_id => $this->delivery->price];
                }
            } else {
                $sum->sums = [$this->delivery->currency_id => $this->delivery->price];
            }
        }
        return $sum;
    }

    public function hasDiscount() {
        return (bool) $this->card || $this->discounts;
    }

    public function discountSum() {
        $sums = [];
        foreach ($this->cartItems as $cartItem) {
            $sums[$cartItem->price->currency_id] = isset($sums[$cartItem->price->currency_id]) ? $sums[$cartItem->price->currency_id] + $cartItem->discount() * $cartItem->count : $cartItem->discount() * $cartItem->count;
        }
        return new \Money\Sums($sums);
    }

    public function finalSum() {
        $sums = $this->itemsSum();
        $sums = $sums->minus($this->discountSum());
        $sums = $sums->plus($this->deliverySum());
        return $sums;
    }

    public function itemsSum() {
        $cart = Cart::get($this->id);
        $sums = [];
        foreach ($cart->cartItems as $cartItem) {
            if (!$cartItem->price) {
                continue;
            }
            $sums[$cartItem->price->currency_id] = isset($sums[$cartItem->price->currency_id]) ? $sums[$cartItem->price->currency_id] + $cartItem->price->price * $cartItem->count : $cartItem->price->price * $cartItem->count;
        }
        return new \Money\Sums($sums);
    }

    public function addItem($offer_price_id, $count = 1, $final_price = 0) {
        $price = Item\Offer\Price::get((int) $offer_price_id);

        if (!$price) {
            return false;
        }

        if ($count <= 0) {
            $count = 1;
        }

        $cartItem = new Cart\Item();
        $cartItem->cart_id = $this->id;
        $cartItem->item_id = $price->offer->item->id;
        $cartItem->count = $count;
        $cartItem->item_offer_price_id = $price->id;
        $cartItem->final_price = $final_price ? $final_price : $price->price;
        $cartItem->save();
        return true;
    }

    public function calc($save = true) {
        if ($save) {
            $this->save();
        }
    }

}
