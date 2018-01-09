<?php

/**
 * Item option relation
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Item\Option;

class Relation extends \Inji\Model {

    public static $objectName = 'Связь каталога и опций';
    public static $labels = [
        'category_id' => 'Категория',
        'item_option_id' => 'Свойство',
        'date_create' => 'Свойство',
    ];
    public static $cols = [
        //Основные параметры
        'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
        'item_option_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'option'],
        //Системные
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['category_id', 'item_option_id']
            ]
        ]
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'item_option_id', 'date_create'
            ]
        ]
    ];

    public static function relations() {
        return [
            'category' => [
                'model' => 'Inji\Ecommerce\Category',
                'col' => 'category_id'
            ],
            'option' => [
                'model' => 'Inji\Ecommerce\Item\Option',
                'col' => 'item_option_id'
            ],
        ];
    }

}
