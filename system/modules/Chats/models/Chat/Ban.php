<?php

/**
 * Ban
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Chats\Chat;

class Ban extends \Inji\Model {

    public static $cols = [
        'chat_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'chat'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'chat_message_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'message'],
        'comment' => ['type' => 'textarea']
    ];

    public static function relations() {
        return[
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'chat' => [
                'model' => 'Chats\Chat',
                'col' => 'chat_id'
            ],
            'message' => [
                'model' => 'Chats\Chat\Message',
                'col' => 'chat_message_id'
            ]
        ];
    }

}
