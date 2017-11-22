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


use Ecommerce\UserAdds\Field;

class RussianPost extends \Ecommerce\DeliveryProvider {
    static $name = 'PickPoint - курьерская служба';

    static function request($cart) {
        $city = '';
        foreach ($cart->deliveryInfos as $deliveryInfo) {
            if ($deliveryInfo->field->code == 'index') {
                $city = $deliveryInfo->value;
            }
        }
        if (!$city) {
            $cityItem = static::getCity($cart);
            if ($cityItem) {
                $data = json_decode($cityItem->data, true);
                if (!empty($data['PostCodeList'])) {
                    $city = explode(',', $data['PostCodeList'])[0];
                }
            }
        }
        if (!$city) {
            return [];
        }
        $senderCity = '101000';

        $data = [
            'object' => 4030,
            'weight' => '3000',
            'date' => date('Ymd'),
            //'sumoc' => $cart->itemsSum()->sums[0],
            'from' => $senderCity,
            'to' => $city,
            'delivery' => 1,
        ];
        $result = \Cache::get('russianPostCalc', $data, function ($data) {
            return file_get_contents('http://tariff.russianpost.ru/tariff/v1/calculate?json&' . http_build_query($data));
        });
        return json_decode($result, true);
    }

    static function calcPrice($cart) {
        $result = static::request($cart);
        $curId = $cart->delivery ? $cart->delivery->currency_id : 0;
        if (empty($result['paynds'])) {
            return new \Money\Sums([$curId => 0]);
        }
        $sum = $result['paynds'];
        return new \Money\Sums([$curId => round($sum / 100 * 1.1, 2)]);
    }

    static function deliveryTime($cart) {
        $result = static::request($cart);
        if (!empty($result['delivery'])) {
            return $result['delivery'];
        }
        return ['min' => 0, 'max' => 0];
    }

    static function availablePayTypeGroups($cart) {
        return ['online'];
    }
}