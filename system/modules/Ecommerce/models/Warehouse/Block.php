<?php

/**
 * Warehouse block
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Warehouse;

class Block extends \Inji\Model {
    public static $logging = false;
    public static $cols = [
        //Основные параметры
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'item_offer_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'offer'],
        'count' => ['type' => 'text'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'cart' => [
                'model' => 'Inji\Ecommerce\Cart',
                'col' => 'cart_id'
            ],
            'offer' => [
                'model' => 'Inji\Ecommerce\Item\Offer',
                'col' => 'item_offer_id'
            ],
        ];
    }

    public static function indexes() {
        return [
            'ecommerce_warehousesBlockCart' => [
                'type' => 'INDEX',
                'cols' => [
                    'warehouse_block_cart_id'
                ]
            ],
            'ecommerce_warehousesBlockItem' => [
                'type' => 'INDEX',
                'cols' => [
                    'warehouse_block_item_offer_id'
                ]
            ],
        ];
    }

}
