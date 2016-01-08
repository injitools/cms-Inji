<?php

/**
 * Session
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users;

class Session extends \Model
{
    static $cols = [
        'hash' => ['type' => 'text'],
        'ip' => ['type' => 'text'],
        'agent' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'realtion', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime']
    ];

    static function relations()
    {
        return [
            'user' => [
                'model' => '\Users\User',
                'col' => 'user_id'
            ]
        ];
    }

}
