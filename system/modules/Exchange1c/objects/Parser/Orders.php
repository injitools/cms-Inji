<?php

/**
 * Parser Orders
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Parser;

class Orders {

    public $xml = null;

    public function __construct($xml) {
        $this->xml = $xml;
    }

    public function process() {
        if ($this->xml->Документ) {
            $this->parseOrders($this->xml->Документ);
        }
    }

    public function parseOrders($orders) {
        foreach ($orders as $order) {

            $cart = \Ecommerce\Cart::get((string) $order->Номер);

            if (!$cart) {
                continue;
            }

            $reqs = [];

            foreach ($order->ЗначенияРеквизитов->ЗначениеРеквизита as $req) {
                $reqs[(string) $req->Наименование] = (string) $req->Значение;
            }

            $payed = false;
            $cancel = false;

            if (!empty($reqs['Дата оплаты по 1С']) && $reqs['Дата оплаты по 1С'] != 'T') {
                $payed = true;
                $date = new \DateTime((string) $reqs['Дата оплаты по 1С']);
                $cart->payed_date = $date->format('Y-m-d H:i:s');
            } elseif (
                    (!empty($reqs['Отменен']) && $reqs['Отменен'] == 'true') ||
                    (!empty($reqs['Дата оплаты по 1С']) && $reqs['Дата оплаты по 1С'] == 'T')) {
                $cancel = true;
            }

            $this->updateCartItems($cart, $order->Товары->Товар);
            $cart->payed = $payed;
            if ($payed && $cart->cart_status_id == 3) {
                $cart->cart_status_id = 5;
                $cart->save();
            } elseif ($cancel && $cart->cart_status_id == 3) {
                $cart->cart_status_id = 4;
            }
            if ($cart->warehouse_block && !$payed && !$cancel && !empty($reqs['Проведен']) && $reqs['Проведен'] == 'true') {
                $cart->warehouse_block = 0;
                foreach ($cart->cartItems as $cci) {
                    if ($cci->price && $cci->price->offer) {
                        $cci->price->offer->changeWarehouse('-' . (float) $cci->count);
                    }
                }
            }
            $cart->save();
        }
    }

    public function updateCartItems($cart, $items) {
        $itemIds = [];
        $cItems = [];
        foreach ($items as $item) {
            $cItem = [];
            $id = \Migrations\Id::get([['parse_id', $item->Ид], ['type', 'Ecommerce\Item']]);
            if (!$id) {
                continue;
            }
            $itemIds[] = $id->object_id;
            $siteItem = \Ecommerce\Item::get($id->object_id);

            if (!$siteItem) {
                $cItem['item_id'] = 0;
                $cItem['item_offer_price_id'] = 0;
                $cItem['count'] = (string) $item->Количество;
                $cItem['name'] = (string) $item->Наименование;

                $cItems[] = $cItem;
                continue;
            }

            $pricesByPrice = $siteItem->prices(['key' => 'price']);
            $prices = $siteItem->prices;
            $default = key($prices);
            $itemPrice = number_format((string) $item->ЦенаЗаЕдиницу, 2, '.', '');
            if (!empty($pricesByPrice[$itemPrice])) {
                $price = $pricesByPrice[$itemPrice];
            } else {
                $rolePrice = 0;
                foreach ($siteItem->prices as $priceId => $itemPrice) {
                    if (!$itemPrice->type->cipt_roles) {
                        $default = $priceId;
                        continue;
                    }
                    if ($itemPrice->type->cipt_roles && $cart->user->user_role_id && false !== strpos($itemPrice->type->cipt_roles, "|{$cart->user->user_role_id}|")) {
                        $rolePrice = $priceId;
                    }
                }
                $price = $siteItem->prices[($rolePrice) ? $rolePrice : $default];
            }

            $cItem['item_id'] = $id->object_id;
            $cItem['item_offer_price_id'] = $price->id;
            $cItem['count'] = (string) $item->Количество;
            $cItem['final_price'] = (string) $item->ЦенаЗаЕдиницу;
            $cItem['name'] = (string) $item->Наименование;

            $cItems[] = $cItem;
        }
        foreach ($cart->cartItems as $cartItem) {
            $isset = false;
            foreach ($cItems as $key => $cItem) {
                if (!($cItem['item_id'] == $cartItem->item_id)) {
                    continue;
                }
                $isset = true;
                if ($cItem['final_price'] != $cartItem->final_price || $cItem['item_offer_price_id'] != $cartItem->item_offer_price_id || $cItem['count'] != $cartItem->count) {
                    $cartItem->item_offer_price_id = $cItem['item_offer_price_id'];
                    $cartItem->count = $cItem['count'];
                    $cartItem->final_price = $cItem['final_price'];
                    $cartItem->save();
                }
                unset($cItems[$key]);
            }
            if (!$isset && !empty($cItem['name']) && !in_array($cItem['name'], ['Доставка', 'Клубная карта', 'Пакет майка'])) {
                $cartItem->delete();
            }
        }
        if ($cItems) {
            foreach ($cItems as $cItem) {
                $cart->addItem($cItem['item_offer_price_id'], $cItem['count'], $cItem['final_price']);
            }
        }
        $cart->calc();
    }

}
