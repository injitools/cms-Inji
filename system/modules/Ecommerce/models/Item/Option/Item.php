<?php

/**
 * Item option item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item\Option;

class Item extends \Model {

    public static $objectName = 'Элемент коллекции опции';
    public static $cols = [
        //Основные параметры
        'item_option_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'option'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'value' => ['type' => 'text'],
        //Системные
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'value' => 'Значение'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['value', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['item_option_id', 'value']
            ]
        ]
    ];

    public function name() {
        return $this->value;
    }

    public static function relations() {
        return [
            'option' => [
                'model' => 'Ecommerce\Item\Option',
                'col' => 'item_option_id'
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ]
        ];
    }

}
