<?php

/**
 * Event
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Events;

class Event extends \Model {

    public static $objectName = 'Событие системы';
    public static $labels = [
        'name' => 'Название',
        'event' => 'Событие'
    ];
    public static $storage = ['type' => 'moduleConfig'];
    public static $cols = [
        'name' => ['type' => 'text'],
        'event' => ['type' => 'text'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'event'
            ],
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'event'],
            ]
        ]
    ];

    public static function index() {
        return 'id';
    }

}
