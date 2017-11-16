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
use Ecommerce\UserAdds\Field;

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
        $tariff = 136;
        $fieldInfo = Field::get('deliveryfield_city', 'code');
        $field = \Ecommerce\Delivery\Field::get('city', 'code');
        if (isset($cart->infos[$fieldInfo->id])) {
            $item = Item::get([['id', $cart->infos[$fieldInfo->id]->value], ['delivery_field_id', $field->id]]);
            if ($item) {
                $cityId = json_decode($item->data, true)['ID'];
            }
        }
        foreach ($cart->delivery->fields as $field) {
            if ($field->code === 'cdektype' && !empty($_POST['deliveryFields'][$field->id]) && is_numeric($_POST['deliveryFields'][$field->id])) {
                $item = Item::get([['id', $_POST['deliveryFields'][$field->id]], ['delivery_field_id', $field->id]]);
                if ($item) {
                    $tariff = $item->data;
                }
            }
        }
        if ($cityId) {
            $config = ConfigItem::getList(['where' => ['delivery_provider_id', $cart->delivery->delivery_provider_id], 'key' => 'name']);
            $calc->setAuth($config['authLogin']->value, $config['authPassword']->value);
            $calc->setDateExecute(date('Y-m-d H:i:s'));
            $calc->setSenderCityId($senderCity);
            //устанавливаем город-получатель
            $calc->setReceiverCityId($cityId);
            $calc->setTariffId($tariff);
            $calc->addGoodsItemBySize(3, 25, 25, 24);
            if ($calc->calculate()) {
                return new \Money\Sums([$cart->delivery->currency_id => $calc->getResult()['result']['price']* 1.1]);
            } else {
                //var_dump($tariff,$calc->getError());
            }

        }
        return new \Money\Sums([$cart->delivery->currency_id => 0]);
    }
}