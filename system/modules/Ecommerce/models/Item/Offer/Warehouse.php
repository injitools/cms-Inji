<?php

/**
 * Item offer warehouse
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item\Offer;

class Warehouse extends \Model {

    public static $objectName = 'Товар на складе';
    public static $labels = [
        'count' => 'Количество',
        'warehouse_id' => 'Склад',
        'item_offer_id' => 'Торговое предложение',
    ];
    public static $cols = [
        //Основные параметры
        'warehouse_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'warehouse'],
        'item_offer_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'offer'],
        'count' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Складской учет',
            'cols' => [
                'warehouse_id',
                'count',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['item_offer_id', 'warehouse_id'],
                ['count'],
            ]
    ]];

    public static function indexes() {
        return [
            'ecommerce_warehousePriceIndex' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_offer_warehouse_item_offer_id'
                ]
            ]
        ];
    }

    public static function relations() {
        return [
            'warehouse' => [
                'model' => 'Ecommerce\Warehouse',
                'col' => 'warehouse_id'
            ],
            'offer' => [
                'model' => 'Ecommerce\Item\Offer',
                'col' => 'item_offer_id'
            ],
        ];
    }

}
