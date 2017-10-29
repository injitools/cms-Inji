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


class RussianPost extends \Ecommerce\DeliveryProvider {
    static $name = 'PickPoint - курьерская служба';

    /**
     * @param \Ecommerce\Cart $cart
     * @return \Money\Sums
     *
     */
    static function calcPrice($cart) {

        $city = '';
        foreach ($cart->delivery->fields as $field) {
            if ($field->code === 'index') {
                if (!empty($_POST['deliveryFields'][$field->id]) && is_string($_POST['deliveryFields'][$field->id])) {
                    $city = $_POST['deliveryFields'][$field->id];
                } elseif (isset($cart->deliveryInfos[$field->id])) {
                    $city = $cart->deliveryInfos[$field->id]->value;
                }
            }
        }
        if (!$city) {
            return new \Money\Sums([$cart->delivery->currency_id => 0]);
        }
        $senderCity = '101000';

        $url = 'http://tariff.russianpost.ru/tariff/v1/calculate?json&';
        $data = [
            'object' => 4030,
            'weight' => '1',
            'date' => date('Ymd'),
            'sumoc' => $cart->itemsSum()->sums[0],
            'from' => $senderCity,
            'to' => $city,
            'closed' => 1,
            'service' => 2,
            'isavia' => 0
        ];
        $result = json_decode(file_get_contents($url . http_build_query($data)), true);
        // var_dump($result);
        $sum = !empty($result['tariff'][0]['ground']['valnds']) ? $result['tariff'][0]['ground']['valnds'] : (!empty($result['tariff'][0]['avia']['valnds']) ? $result['tariff'][0]['avia']['valnds'] : 0);
        return new \Money\Sums([$cart->delivery->currency_id => $sum / 100 * 1.1]);
    }
}