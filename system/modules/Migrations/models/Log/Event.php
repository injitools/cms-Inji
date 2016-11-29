<?php

/**
 * Log event
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Log;

class Event extends \Model {

    public static $objectName = 'Событие истории миграции';
    public static $labels = [
        'type' => 'Тип',
        'info' => 'Информация',
        'date_create' => 'Дата события'
    ];
    public static $cols = [
        'type' => ['type' => 'text'],
        'info' => ['type' => 'textarea'],
        'log_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'log'],
        'map_path_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'path'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['type', 'info', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['type'],
                ['info'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'log' => [
                'col' => 'log_id',
                'model' => 'Migrations\Log'
            ],
            'path' => [
                'col' => 'map_path_id',
                'model' => 'Migrations\Migrations\Map\Path'
            ]
        ];
    }

}
