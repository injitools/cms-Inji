<?php

/**
 * Cart info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;

class Info extends \Model {

    public static $objectName = 'Информация';
    public static $labels = [
        'name' => 'Название',
        'value' => 'Значение',
        'useradds_field_id' => 'Поле',
        'cart_id' => 'Корзина'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'value' => ['type' => 'text'],
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'useradds_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'value',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'value'],
                ['useradds_field_id', 'cart_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'cart' => [
                'model' => 'Ecommerce\Cart',
                'col' => 'cart_id'
            ],
            'field' => [
                'model' => 'Ecommerce\UserAdds\Field',
                'col' => 'useradds_field_id'
            ]
        ];
    }

}
