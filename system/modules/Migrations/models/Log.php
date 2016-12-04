<?php

/**
 * Log
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations;

class Log extends \Model {

    public static $logging = false;
    public static $objectName = 'История миграции';
    public static $labels = [
        'result' => 'Результат',
        'source' => 'Источник',
        'event' => 'События',
        'date_create' => 'Дата начала'
    ];
    public static $cols = [
        'migration_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'migration'],
        'migration_map_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'map'],
        'result' => ['type' => 'text'],
        'source' => ['type' => 'text'],
        'date_end' => ['type' => 'dateTime'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'event' => ['type' => 'dataManager', 'relation' => 'events']
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['source', 'result', 'event', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['source'],
                ['result'],
                ['event'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'events' => [
                'type' => 'many',
                'model' => 'Migrations\Log\Event',
                'col' => 'log_id'
            ],
            'migration' => [
                'col' => 'migration_id',
                'model' => 'Migrations\Migration'
            ],
            'map' => [
                'col' => 'migration_map_id',
                'model' => 'Migrations\Migration\Map'
            ]
        ];
    }
}