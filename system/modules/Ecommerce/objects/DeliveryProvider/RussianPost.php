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
            $fieldInfo = Field::get('deliveryfield_city', 'code');
            $field = \Ecommerce\Delivery\Field::get('city', 'code');
            if (isset($cart->infos[$fieldInfo->id])) {
                $item = \Ecommerce\Delivery\Field\Item::get([['id', $cart->infos[$fieldInfo->id]->value], ['delivery_field_id', $field->id]]);
                if ($item) {
                    $data = json_decode($item->data, true);
                    if (!empty($data['PostCodeList'])) {
                        $city = explode(',', $data['PostCodeList'])[0];
                    }

                }
            }
            if (!$city) {
                return [];
            }
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
        });
        return json_decode($result, true);
    }

    static function calcPrice($cart) {
        $result = static::request($cart);
        if (empty($result['paynds'])) {
            return new \Money\Sums([$cart->delivery->currency_id => 0]);
        }
        $sum = $result['paynds'];
        return new \Money\Sums([$cart->delivery->currency_id => round($sum / 100 * 1.1, 2)]);
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