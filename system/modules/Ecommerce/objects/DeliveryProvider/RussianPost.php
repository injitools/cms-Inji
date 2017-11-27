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
            'weight' => '1000',
            'date' => date('Ymd'),
            //'sumoc' => $cart->itemsSum()->sums[0],
            'from' => $senderCity,
            'to' => $city,
            'delivery' => 1,
        ];
        $result = \Cache::get('russianPostCalc', $data, function ($data) {
            return file_get_contents('http://tariff.russianpost.ru/tariff/v1/calculate?json&' . http_build_query($data));
        }, 4 * 60 * 60);
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
        if (!empty($result['from']) && !empty($result['to'])) {
            $url = "http://postcalc.ru/web.php?Extended=1&Output=json&From={$result['from']}&Weight=3000&Valuation=0&Step=0&Date=22.11.2017&IBase=p&ProcessingFee=0&PackingFee=0&Round=0.01&VAT=1&To={$result['to']}";
            $result = \Cache::get('russianPostTimeCalc', $url, function ($data) {
                return file_get_contents($data);
            }, 4 * 60 * 60);
            $result = json_decode($result, true);
            if (!empty($result['ПростаяБандероль']['СрокДоставки'])) {
                return ['min' => $result['ПростаяБандероль']['СрокДоставки']];
            }


        }
        return ['min' => 0, 'max' => 0];
    }

    static function availablePayTypeGroups($cart) {
        return ['online'];
    }
}