<?php

/**
 * Delivery user info
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;

class DeliveryInfo extends \Model {

    public static $objectName = 'Информация о доставке';
    public static $labels = [
        'name' => 'Название',
        'cart_id' => 'Корзина',
        'delivery_field_id' => 'Поле',
        'value' => 'Значение',
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'delivery_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        'value' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'value'],
                ['delivery_field_id', 'cart_id'],
            ]
        ]
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'value'
            ]
        ]
    ];

    public static function relations() {
        return [
            'field' => [
                'model' => 'Ecommerce\Delivery\Field',
                'col' => 'delivery_field_id'
            ],
            'cart' => [
                'model' => 'Ecommerce\Cart',
                'col' => 'cart_id'
            ],
        ];
    }

}
