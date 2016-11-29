<?php

/**
 * Migration object param
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Migration\Object;

class Param extends \Model {

    public static $objectName = 'Параметр объекта миграции';
    public static $labels = [
        'code' => 'Код',
        'type' => 'Тип',
        'object_id' => 'Миграция данных'
    ];
    public static $cols = [
        'code' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'value' => ['type' => 'text'],
        'options' => ['type' => 'textarea'],
        'object_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'object'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Параметры объекта миграции',
            'cols' => ['code', 'type', 'object_id']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['code', 'type', 'object_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'object' => [
                'model' => 'Migrations\Migration\Object',
                'col' => 'object_id'
            ],
            'parent' => [
                'model' => 'Migrations\Migration\Object\Param',
                'col' => 'parent_id'
            ],
            'values' => [
                'type' => 'many',
                'model' => 'Migrations\Migration\Object\Param\Value',
                'col' => 'param_id'
            ],
            'childs' => [
                'type' => 'many',
                'model' => 'Migrations\Migration\Object\Param',
                'col' => 'parent_id'
            ]
        ];
    }

}
