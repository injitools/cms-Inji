<?php

/**
 * Cart active discount
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;

class Discount extends \Model {

    public static $objectName = 'Скидка корзины';
    public static $cols = [
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'discount_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'discount'],
        'auto' => ['type' => 'bool'],
        'group' => ['type' => 'text'],
    ];
    public static $labels = [
        'cart_id' => 'Корзина',
        'discount_id' => 'Скидки',
        'auto' => 'Добавлена автоматически',
        'group' => 'Группа',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['discount_id', 'auto', 'group']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['cart_id', 'discount_id'],
                ['auto', 'group']
            ]
        ]
    ];

    public static function relations() {
        return [
            'cart' => [
                'model' => 'Ecommerce\Cart',
                'col' => 'cart_id'
            ],
            'discount' => [
                'model' => 'Ecommerce\Discount',
                'col' => 'discount_id'
            ]
        ];
    }

}
