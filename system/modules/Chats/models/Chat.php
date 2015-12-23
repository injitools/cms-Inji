<?php

/**
 * Chat
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Chats;

class Chat extends \Model
{
    static $cols = [
        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
    ];

    static function relations()
    {
        return [
            'events' => [
                'type' => 'many',
                'model' => 'Chats\Chat\Event',
                'col' => 'chat_id'
            ],
            'messages' => [
                'type' => 'many',
                'model' => 'Chats\Chat\Message',
                'col' => 'chat_id'
            ],
            'members' => [
                'type' => 'many',
                'model' => 'Chats\Chat\Member',
                'col' => 'chat_id'
            ],
        ];
    }

}