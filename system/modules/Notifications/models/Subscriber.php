<?php

/**
 * Subscriber
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications;

class Subscriber extends \Model {

    public static $logging = false;
    public static $cols = [
        'key' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'subscribes' => [
                'type' => 'many',
                'mode' => 'Notifications\Subscribe',
                'col' => 'subscriber_id'
            ]
        ];
    }
}