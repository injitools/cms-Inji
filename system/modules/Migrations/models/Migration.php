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
/**
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $secret
 * @property string $date_create
 * @property \Migrations\Migration\Map[] $maps
 * @property \Migrations\Migration\ObjectItem[] $objects
 *
 * @method  \Migrations\Migration\Map[] maps($params)
 * @method \Migrations\Migration\ObjectItem[] objects($params)
 */
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
                'model' => 'Migrations\Migration\ObjectItem',
                'col' => 'migration_id'
            ]
        ];
    }

}
