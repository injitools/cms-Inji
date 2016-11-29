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

class Invite extends \Model {

    public static $objectName = "Код пришлашения";
    public static $labels = [
        'code' => 'Код',
        'type' => 'Тип',
        'user_id' => 'Пользователь',
        'limit' => 'Лимит приглашений',
        'count' => 'Использовано',
        'date_create' => 'Дата',
    ];
    public static $cols = [
        'code' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'limit' => ['type' => 'number'],
        'count' => ['type' => 'number'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'code',
                'type',
                'user_id',
                'limit',
                'count',
                'date_create'
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['code', 'type',],
                ['user_id'],
                ['limit', 'count'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
        ];
    }

    public function name() {
        return $this->code;
    }

}
