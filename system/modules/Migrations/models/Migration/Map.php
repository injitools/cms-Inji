<?php

/**
 * Migration Map
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Migration;

class Map extends \Inji\Model {

    public static $objectName = 'Карта миграции данных';
    public static $labels = [
        'name' => 'Название',
        'alias' => 'Алиас',
        'migration_id' => 'Миграция данных',
        'date_create' => 'Дата создания'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'migration_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'migration'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Карты миграции данных',
            'cols' => ['name', 'alias', 'migration_id', 'date_create'],
            'actions' => [
                'mapEdit' => [
                    'className' => 'Href',
                    'href' => '/admin/migrations/map',
                    'text' => '<i class = "glyphicon glyphicon-cog"></i>'
                ],
                'Edit', 'Delete'
            ],
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'alias'],
                ['migration_id']
            ]
        ]
    ];

    public static function relations() {
        return [
            'migration' => [
                'model' => 'Migrations\Migration',
                'col' => 'migration_id'
            ],
            'paths' => [
                'type' => 'many',
                'model' => 'Migrations\Migration\Map\Path',
                'col' => 'migration_map_id'
            ]
        ];
    }

}
