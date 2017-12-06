<?php
/**
 * User invite
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User;


class LoginHistory extends \Model {
    public static $logging = false;
    public static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'ip' => ['type' => 'text'],
        'success' => ['type' => 'bool'],
        'date_create' => ['type' => 'dateTime']
    ];

    public static function lastSuccessLogin($userId, $ip = null) {
        $where = [
            ['user_id', $userId],
            ['success', 1]
        ];
        if ($ip) {
            $where[] = ['ip', $ip];
        }
        $last = LoginHistory::getList(['where' => $where, 'order' => ['date_create', 'desc'], 'limit' => 1, 'key' => false]);
        if ($last) {
            return $last[0];
        }
        return false;
    }

    public static function relations() {
        return [
            'user' => [
                'col' => 'user_id',
                'model' => 'Users\User'
            ]
        ];
    }
}