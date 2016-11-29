<?php

/**
 * Migration
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations;

class Migration extends \Model {

    public static $objectName = 'Миграция данных';
    public static $labels = [
        'name' => 'Название',
        'alias' => 'Алиас',
        'secret' => 'Секрет',
        'maps' => 'Карты данных'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'secret' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'maps' => ['type' => 'dataManager', 'relation' => 'maps']
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'maps', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'alias'],
                ['secret'],
                ['maps']
            ]
        ]
    ];

    public static function relations() {
        return [
            'maps' => [
                'type' => 'many',
                'model' => 'Migrations\Migration\Map',
                'col' => 'migration_id'
            ],
            'objects' => [
                'type' => 'many',
                'model' => 'Migrations\Migration\Object',
                'col' => 'migration_id'
            ]
        ];
    }

}
