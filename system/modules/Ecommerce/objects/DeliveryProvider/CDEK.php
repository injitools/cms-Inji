<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce\DeliveryProvider;

use Ecommerce\Delivery\Field\Item;
use Ecommerce\Delivery\Provider\ConfigItem;

class CDEK extends \Ecommerce\DeliveryProvider {
    static $name = 'СДЭК - курьерская служба';

    /**
     * @param \Ecommerce\Cart $cart
     * @return \Money\Sums
     */
    static function calcPrice($cart) {
        $calc = new \Ecommerce\Vendor\CalculatePriceDeliveryCdek();
        $fields = [];
        $cityId = 0;
        $senderCity = 44;
        foreach ($cart->delivery->fields as $field) {
            if ($field->code === 'city' && !empty($_POST['deliveryFields'][$field->id]) && is_string($_POST['deliveryFields'][$field->id])) {
                $item = Item::get([['id', $_POST['deliveryFields'][$field->id]], ['delivery_field_id', $field->id]]);
                if ($item) {
                    $cityId = json_decode($item->data, true)['ID'];
                }
            }
        }
        if ($cityId == 278) {
            $senderCity = 278;
            foreach ($cart->cartItems as $cartItem) {
                if ($cartItem->item->offers(['key' => false]) && $cartItem->item->offers(['key' => false])[0]->warehouses) {
                    $msocow = 0;
                    $kras = 0;
                    foreach ($cartItem->item->offers(['key' => false])[0]->warehouses as $warehouse) {
                        if ($warehouse->warehouse_id != 6) {
                            $kras += $warehouse->count;
                        } else {
                            $msocow += $warehouse->count;
                        }

                    }
                    if ($kras < $cartItem->count) {
                        $senderCity = 44;
                    }
                }
            }
        } else {
            $senderCity = 44;
        }
        if ($cityId) {
            $config = ConfigItem::getList(['where' => ['delivery_provider_id', $cart->delivery->delivery_provider_id], 'key' => 'name']);
            $calc->setAuth($config['authLogin']->value, $config['authPassword']->value);
            $calc->setDateExecute(date('Y-m-d H:i:s'));
            $calc->setSenderCityId($senderCity);
            //устанавливаем город-получатель
            $calc->setReceiverCityId($cityId);
            $calc->setTariffId('136');
            $calc->addGoodsItemBySize(3, 25, 25, 24);
            if ($calc->calculate()) {
                return new \Money\Sums([$cart->delivery->currency_id => $calc->getResult()['result']['price']]);
            } else {
                //var_dump($calc->getError());
            }

        }
        return new \Money\Sums([$cart->delivery->currency_id => 0]);
    }
}