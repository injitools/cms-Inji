<?php

/**
 * Cart Event
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;

class Event extends \Model {

    public static $cols = [
        //Основные параметры
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'cart_event_type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
        'info' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function indexes() {
        return [
            'ecommerce_cartEventCart' => [
                'type' => 'INDEX',
                'cols' => [
                    'cart_event_cart_id'
                ]
            ],
            'ecommerce_cartEventDate' => [
                'type' => 'INDEX',
                'cols' => [
                    'cart_event_date_create'
                ]
            ],
        ];
    }

    public static function relations() {
        return [
            'type' => [
                'model' => 'Ecommerce\Cart\Event\Type',
                'col' => 'cart_event_type_id',
            ],
            'cart' => [
                'model' => 'Ecommerce\Cart',
                'col' => 'cart_id',
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id',
            ],
        ];
    }

    public function afterSave() {
        $this->cart->date_last_activ = $this->date_create;
        $this->cart->save();
    }

}
