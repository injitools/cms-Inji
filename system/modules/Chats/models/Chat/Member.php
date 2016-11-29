<?php

/**
 * Member
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Chats\Chat;

class Member extends \Model {

    public static $cols = [
        'chat_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'chat'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'chat_member_status_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'status'],
        'date_last_active' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return[
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'status' => [
                'model' => 'Chats\Chat\Member\Status',
                'col' => 'chat_member_status_id'
            ],
            'chat' => [
                'model' => 'Chats\Chat',
                'col' => 'chat_id'
            ]
        ];
    }

}
