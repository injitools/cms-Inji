<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce;


class DeliveryProvider {
    static $name = 'Unnamed';

    static function calcPrice($cart) {
        return new \Money\Sums([]);
    }

    static function deliveryTime($cart) {
        return [
            'min' => 0,
            'max' => 0
        ];
    }

    static function config() {
        $provider = \Ecommerce\Delivery\Provider::get((new \ReflectionClass(get_called_class()))->getShortName(), 'object');
        $config = [];
        foreach ($provider->configs as $item) {
            $config[$item->name] = $item->value;
        }
        return $config;
    }

    static function availablePayTypeGroups($cart) {
        return ['*'];
    }

    /**
     * @param \Ecommerce\Cart $cart
     * @return bool|\Ecommerce\Delivery\Field\Item
     */
    static function getCity($cart) {
        $fieldInfo = \Ecommerce\UserAdds\Field::get('deliveryfield_city', 'code');
        $field = \Ecommerce\Delivery\Field::get('city', 'code');
        $cityItem = null;
        if (isset($cart->infos[$fieldInfo->id])) {
            $itemId = $cart->infos[$fieldInfo->id]->value;
        }
        if (isset($cart->deliveryInfos[$field->id])) {
            $itemId = $cart->deliveryInfos[$field->id]->value;
        }
        if (!empty($itemId)) {
            return \Ecommerce\Delivery\Field\Item::get([['id', $itemId], ['delivery_field_id', $field->id]]);
        }
        return false;
    }
}