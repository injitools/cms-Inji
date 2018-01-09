<?php

/**
 * Link between delivery and link
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Delivery;

class CityLink extends \Inji\Model {

    public static $labels = [
        'delivery_id' => 'Тип доставки',
        'city_id' => 'Город',
        'date_create' => 'Дата создания'
    ];
    public static $cols = [
        'delivery_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'delivery'],
        'city_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'city'],
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Города доставки',
            'cols' => ['city_id', 'date_create'],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['delivery_id', 'city_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'city' => [
                'model' => 'Inji\Geography\City',
                'col' => 'city_id'
            ],
            'delivery' => [
                'model' => 'Inji\Ecommerce\Delivery',
                'col' => 'delivery_id'
            ],
        ];
    }

}
