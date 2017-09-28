<?php

/**
 * Exchange
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c;

class Exchange extends \Model {

    public static $labels = [
        'type' => 'Тип',
        'session' => 'Сессия',
        'path' => 'Директория',
        'log' => 'События',
        'file' => 'Файлы',
        'date_create' => 'Дата начала',
        'date_end' => 'Дата окончания',
    ];
    public static $cols = [
        'type' => ['type' => 'text'],
        'session' => ['type' => 'text'],
        'path' => ['type' => 'text'],
        'cleared' => ['type' => 'number'],
        'log' => ['type' => 'dataManager', 'relation' => 'logs'],
        'file' => ['type' => 'dataManager', 'relation' => 'files'],
        'date_create' => ['type' => 'dateTime'],
        'date_end' => ['type' => 'dateTime', 'null' => true],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'История обмена',
            'cols' => ['type', 'session', 'path', 'log', 'file', 'date_create'],
            'sortable' => ['type', 'session', 'path', 'log', 'file', 'date_create'],
            'filters' => ['type', 'session', 'path', 'log', 'file', 'date_create'],
            'actions' => [
                'reEx' => [
                    'className' => 'Href',
                    'href' => '/admin/exchange1c/reExchange',
                    'text' => '<i class = "glyphicon glyphicon-refresh"></i>'
                ],
                'Edit', 'Delete'
            ],
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['type', 'session'],
                ['path']
            ]
        ]
    ];

    public static function relations() {
        return [
            'logs' => [
                'type' => 'many',
                'model' => 'Exchange1c\Exchange\Log',
                'col' => 'exchange_id'
            ],
            'files' => [
                'type' => 'many',
                'model' => 'Exchange1c\Exchange\File',
                'col' => 'exchange_id'
            ]
        ];
    }

}
