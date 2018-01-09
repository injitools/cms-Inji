<?php

/**
 * Delivery user info
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Delivery;

class Value extends \Inji\Model {
    public static $logging = false;
    public static $cols = [
        //Основные параметры
        'delivery_save_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'save'],
        'delivery_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        'value' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['cart_id', 'delivery_field_id', 'value']
            ]
        ]
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'cart_id',
                'delivery_field_id',
                'value'
            ]
        ]
    ];

    public static function relations() {
        return [
            'field' => [
                'model' => 'Inji\Ecommerce\Delivery\Field',
                'col' => 'delivery_field_id'
            ],
            'save' => [
                'model' => 'Inji\Ecommerce\Delivery\Save',
                'col' => 'cart_id'
            ],
        ];
    }

}
