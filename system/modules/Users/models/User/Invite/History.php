<?php

/**
 * User invite history
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User\Invite;

class History extends \Model {

    public static $objectName = "История кода пришлашения";
    public static $labels = [
        'user_invite_id' => 'Приглашение',
        'type' => 'Тип',
        'user_id' => 'Пользователь',
        'date_create' => 'Дата',
    ];
    public static $cols = [
        'type' => ['type' => 'text'],
        'user_invite_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'invite'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'user_invite_id',
                'type',
                'user_id',
                'date_create',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['user_invite_id', 'type'],
                ['user_id', 'date_create'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'invite' => [
                'model' => 'Users\User\Invite',
                'col' => 'user_invite_id'
            ],
        ];
    }

    public function name() {
        return $this->code;
    }

}
