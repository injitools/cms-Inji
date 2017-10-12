<?php

/**
 * Cart item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;
/**
 * Class Cart
 *
 * @property int $cart_id
 * @property number $count
 * @property int $item_offer_price_id
 * @property int $item_id
 * @property number $final_price
 * @property number $discount
 * @property string $date_create
 *
 * @property \Ecommerce\Item $item
 * @property \Ecommerce\Item\Offer\Price $price
 * @property \Ecommerce\Cart $cart
 */
class Item extends \Model {

    public function beforeSave() {
        if (!$this->id) {
            $event = new Event(['cart_id' => $this->cart_id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 1, 'info' => $this->item_offer_price_id]);
            $event->save();
        } else {
            $cur = Item::get($this->id);
            if ($cur->item_id != $this->item_id) {
                $event = new Event(['cart_id' => $this->cart->cart_id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 2, 'info' => $cur->item_offer_price_id]);
                $event->save();
                $event = new Event(['cart_id' => $this->cart->cart_id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 1, 'info' => $this->item_offer_price_id]);
                $event->save();
            } else {
                if ($cur->item_offer_price_id != $this->item_offer_price_id) {
                    $event = new Event(['cart_id' => $this->cart->cart_id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 3, 'info' => $this->item_offer_price_id]);
                    $event->save();
                }
                if ($cur->count != $this->count) {
                    $event = new Event(['cart_id' => $this->cart->cart_id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 4, 'info' => $this->item_offer_price_id . "|" . ($this->count - $cur->count)]);
                    $event->save();
                }
            }
        }
    }

    public function afterSave() {
        $block = \Ecommerce\Warehouse\Block::get([['cart_id', $this->cart->id], ['item_offer_id', $this->price->item_offer_id]]);
        if (in_array($this->cart_status_id, [0, 1, 2, 3, 6])) {
            if (in_array($this->cart_status_id, [0, 1])) {
                $cur = new \DateTime();
                $lastActive = new \DateTime($this->date_last_activ);
                $interval = $cur->diff($lastActive);
                if ($interval->days || $interval->h || $interval->i >= 30) {
                    if ($block) {
                        $block->delete();
                    }
                    $this->cart->save();
                    return;
                }
            }
            $block = \Ecommerce\Warehouse\Block::get([['cart_id', $this->cart_id], ['item_offer_id', $this->price->item_offer_id]]);
            if ($block) {
                $block->count = $this->count;
                $block->save();
            } else {
                $block = new \Ecommerce\Warehouse\Block();
                $block->item_offer_id = $this->price->item_offer_id;
                $block->cart_id = $this->cart_id;
                $block->count = $this->count;
                $block->save();
            }
        } elseif ($block) {
            $block->delete();
        }
        $this->cart->checkStage();
    }

    public function afterDelete() {
        $event = new Event(['cart_id' => $this->cart_id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 2, 'info' => $this->item_offer_price_id]);
        $event->save();
        \Ecommerce\Warehouse\Block::deleteList([['cart_id', $this->cart->id], ['item_offer_id', $this->price->item_offer_id]]);
        $this->cart->checkStage();
    }

    public function discount() {
        $discountSum = 0;
        if ($this->item->type && $this->item->type->discount) {
            foreach ($this->cart->discounts as $discount) {
                switch ($discount->type) {
                    case 'procent':
                        $discountSum += round($this->price->price * ($discount->amount / 100), 2);
                        break;
                    case 'amount':
                        $discountSum += round($discount->amount, 2);
                        break;
                }
            }
        }
        return $discountSum;
    }

    public static $labels = [
        'item_id' => 'Товар',
        'item_offer_price_id' => 'Цена в каталоге',
        'count' => 'Количество',
        'cart_id' => 'Корзина',
        'final_price' => 'Итоговая цена за единицу',
    ];
    public static $cols = [
        //Основные параметры
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'count' => ['type' => 'decimal'],
        'item_offer_price_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'price', 'showCol' => 'price'],
        'item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'item'],
        'final_price' => ['type' => 'decimal'],
        'discount' => ['type' => 'decimal'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function indexes() {
        return [
            'ecommerce_cartItemCart' => [
                'type' => 'INDEX',
                'cols' => [
                    'cart_item_cart_id'
                ]
            ]
        ];
    }

    public static $dataManagers = [
        'manager' => [
            'name' => 'Покупки',
            'cols' => [
                'item_id',
                'price:offer:article',
                'item_offer_price_id',
                'price:currency' => ['label' => 'Валюта'],
                'count',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'relations' => [
                'item_id' => [
                    'col' => 'item_offer_price_id',
                    'model' => 'Item',
                    'relation' => 'prices'
                ]
            ],
            'map' => [
                ['cart_id', 'item_id', 'item_offer_price_id'],
                ['final_price', 'count'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'item' => [
                'model' => 'Ecommerce\Item',
                'col' => 'item_id'
            ],
            'price' => [
                'model' => 'Ecommerce\Item\Offer\Price',
                'col' => 'item_offer_price_id'
            ],
            'cart' => [
                'model' => 'Ecommerce\Cart',
                'col' => 'cart_id'
            ]
        ];
    }

}
