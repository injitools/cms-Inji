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

class CDEK extends \Ecommerce\DeliveryProvider {
    static $name = 'СДЭК - курьерская служба';

    /**
     * @param \Ecommerce\Cart $cart
     */
    static function getTariff($cart) {
        $tariff = 138;
        $field = \Ecommerce\Delivery\Field::get('cdektype', 'code');
        if (isset($cart->deliveryInfos[$field->id])) {
            $item = \Ecommerce\Delivery\Field\Item::get([['id', $cart->deliveryInfos[$field->id]->value], ['delivery_field_id', $field->id]]);
            if ($item) {
                $tariff = $item->data;
            }
        }
        return $tariff;
    }

    static function request($cart) {
        $cityId = 0;
        $senderCity = 44;
        $cityItem = static::getCity($cart);
        if ($cityItem) {
            $cityId = json_decode($cityItem->data, true)['ID'];
        }
        if (!$cityId) {
            return false;
        }
        $tariff = static::getTariff($cart);
        $config = self::config();
        return \Cache::get('pickPointCalc', [
            'login' => $config['authLogin'],
            'pass' => $config['authPassword'],
            'senderCity' => $senderCity,
            'cityId' => $cityId,
            'tariff' => $tariff,
        ], function ($data) {
            $calc = new \Ecommerce\Vendor\CalculatePriceDeliveryCdek();
            $calc->setAuth($data['login'], $data['pass']);
            $calc->setDateExecute(date('Y-m-d H:i:s'));
            $calc->setSenderCityId($data['senderCity']);
            //устанавливаем город-получатель
            $calc->setReceiverCityId($data['cityId']);
            $calc->setTariffId($data['tariff']);
            $calc->addGoodsItemBySize(3, 25, 25, 24);
            if ($calc->calculate()) {
                return $calc->getResult();
            } else {
                //var_dump($tariff,$calc->getError());
                return false;
            }
        }, 4 * 60 * 60);


    }

    static function calcPrice($cart) {
        $result = self::request($cart);
        $curId = $cart->delivery ? $cart->delivery->currency_id : 0;
        if (!empty($result['result']['price'])) {
            return new \Money\Sums([$curId => round($result['result']['price'] * 1.1, 2)]);
        }
        return new \Money\Sums([$curId => 0]);
    }

    static function deliveryTime($cart) {
        $result = self::request($cart);
        if (isset($result['result']['deliveryPeriodMin'])) {
            return [
                'min' => $result['result']['deliveryPeriodMin'],
                'max' => $result['result']['deliveryPeriodMax'],
            ];
        }
        return [
            'min' => 0,
            'max' => 0,
        ];
    }

    static function availablePayTypeGroups($cart) {
        $tariff = static::getTariff($cart);
        if ($tariff == 139) {
            return ['online', 'courier'];
        }
        return ['online','inPickPoint'];
    }
}