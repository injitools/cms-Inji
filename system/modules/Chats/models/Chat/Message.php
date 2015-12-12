<?php

/**
 * Message
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Chats\Chat;

class Message extends \Model
{
    static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'text' => ['type' => 'textarea'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'chat_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'chat'],
    ];

    static function relations()
    {
        return[
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'parent' => [
                'model' => 'Chats\Chat\Message',
                'col' => 'parent_id'
            ],
            'chat' => [
                'model' => 'Chats\Chat',
                'col' => 'chat_id'
            ]
        ];
    }

}
