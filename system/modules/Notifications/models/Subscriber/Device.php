<?php

/**
 * Subscriber Device
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications\Subscriber;

class Device extends \Inji\Model {

    public static $logging = false;
    public static $cols = [
        'key' => ['type' => 'text'],
        'subscriber_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'subscriber'],
        'date_last_check' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function indexes() {
        return [
            'notifications_subscriber_device_subscriber_device_key' => [
                'type' => 'UNIQUE INDEX',
                'cols' => [
                    'subscriber_device_key'
                ]
            ],
        ];
    }

    public static function relations() {
        return [
            'subscriber' => [
                'model' => 'Notifications\Subscriber',
                'col' => 'subscriber_id'
            ]
        ];
    }
}