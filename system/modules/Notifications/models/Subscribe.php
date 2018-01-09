<?php

/**
 * Subscribe
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications;

class Subscribe extends \Inji\Model {

    public static $cols = [
        'chanel_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'chanel'],
        'subscriber_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'subscriber'],
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'chanel' => [
                'model' => 'Notifications\Chanel',
                'col' => 'chanel_id'
            ],
            'subscriber' => [
                'model' => 'Notifications\Subscriber',
                'col' => 'subscriber_id'
            ]
        ];
    }
}