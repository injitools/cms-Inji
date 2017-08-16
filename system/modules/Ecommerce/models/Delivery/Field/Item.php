<?php

/**
 * Item option item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery\Field;

class Item extends \Model {

    public static $objectName = 'Элемент коллекции поля доставки';
    public static $cols = [
        //Основные параметры
        'delivery_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        'value' => ['type' => 'text'],
        'data' => ['type' => 'textarea'],
        //Системные
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'value' => 'Значение',
        'data' => 'Дополнительные данные',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['value', 'data', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['delivery_field_id', 'value', 'data']
            ]
        ]
    ];

    public function name() {
        return $this->value;
    }

    public static function relations() {
        return [
            'field' => [
                'model' => 'Ecommerce\Delivery\Field',
                'col' => 'delivery_field_id'
            ]
        ];
    }

}
