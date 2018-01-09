<?php

/**
 * User social
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User;

class Social extends \Inji\Model {

    public static $objectName = "Связь с соц.сетью";
    public static $cols = [
        'uid' => ['type' => 'text'],
        'social_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'social'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'code' => 'Код соц сети',
        'uid' => 'id в соц сети',
        'user_id' => 'Пользователь',
        'date_create' => 'Дата создания'
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Inji\Users\User',
                'col' => 'user_id'
            ],
            'social' => [
                'model' => 'Inji\Users\Social',
                'col' => 'social_id'
            ],
        ];
    }

}
