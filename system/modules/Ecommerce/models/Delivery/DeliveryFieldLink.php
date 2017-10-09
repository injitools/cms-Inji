<?php

/**
 * Link between delivery and link
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery;

class DeliveryFieldLink extends \Model {

    public static $labels = [
        'delivery_id' => 'Тип доставки',
        'delivery_field_id' => 'Поле доставки',
        'date_create' => 'Дата создания'
    ];
    public static $cols = [
        'delivery_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'delivery'],
        'delivery_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Поля для доставки',
            'cols' => ['delivery_field_id', 'date_create'],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['delivery_id', 'delivery_field_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'field' => [
                'model' => 'Ecommerce\Delivery\Field',
                'col' => 'delivery_field_id'
            ],
            'delivery' => [
                'model' => 'Ecommerce\Delivery',
                'col' => 'delivery_id'
            ],
        ];
    }

}
