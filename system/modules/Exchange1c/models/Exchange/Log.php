<?php

/**
 * Exchange Log
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Exchange;

class Log extends \Model {

    public static $logging = false;
    public static $cols = [
        'type' => ['type' => 'text'],
        'info' => ['type' => 'text'],
        'query' => ['type' => 'text'],
        'status' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
        'date_end' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'type', 'info', 'query', 'status', 'date_create', 'date_end'
            ],
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['type', 'info', 'status'],
                ['query']
            ]
        ]
    ];

}