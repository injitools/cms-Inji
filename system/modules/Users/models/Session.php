<?php

/**
 * Session
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Users;

class Session extends \Inji\Model {

    public static $logging = false;
    public static $cols = [
        'hash' => ['type' => 'text'],
        'ip' => ['type' => 'text'],
        'agent' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'realtion', 'relation' => 'user'],
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Inji\Users\User',
                'col' => 'user_id'
            ]
        ];
    }
}