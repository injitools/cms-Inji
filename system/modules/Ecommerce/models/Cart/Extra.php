<?php

/**
 * Cart Extra
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Cart;

class Extra extends \Inji\Model {

    public static $labels = [
        'name' => 'Название',
        'price' => 'Цена',
        'count' => 'Количество',
        'cart_id' => 'Корзина',
        'currency_id' => 'Валюта',
    ];
    public static $cols = [
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'name' => ['type' => 'text'],
        'info' => ['type' => 'textarea'],
        'count' => ['type' => 'decimal'],
        'price' => ['type' => 'decimal'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Дополнительно',
            'cols' => [
                'name',
                'price',
                'currency_id',
                'count',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name'],
                ['price', 'currency_id'],
                ['count', 'cart_id'],
            ]
        ]
    ];

    public function afterSave() {
        $this->cart->calc();
    }

    public static function relations() {
        return [
            'cart' => [
                'model' => 'Inji\Ecommerce\Cart',
                'col' => 'cart_id'
            ],
            'currency' => [
                'model' => 'Inji\Money\Currency',
                'col' => 'currency_id'
            ]
        ];
    }

}
