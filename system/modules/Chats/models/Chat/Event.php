<?php

/**
 * Event
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Chats\Chat;

class Event extends \Model {

    public static $cols = [
        'type' => ['type' => 'text'],
        'chat_message_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'message'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'chat_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'chat'],
        'data' => ['type' => 'textarea']
    ];

    public static function relations() {
        return[
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'message' => [
                'model' => 'Chats\Chat\Message',
                'col' => 'chat_message_id'
            ],
            'chat' => [
                'model' => 'Chats\Chat',
                'col' => 'chat_id'
            ]
        ];
    }

}
