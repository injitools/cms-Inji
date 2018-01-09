<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Dashboard\Activity;

class Change extends \Inji\Model {

    public static $logging = false;
    public static $cols = [
        'activity_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'activity'],
        'col' => ['type' => 'text'],
        'old' => ['type' => 'textarea'],
        'new' => ['type' => 'textarea'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['col', 'old', 'new'],
            'actions' => ['Edit' => ['access' => ['groups' => [0]]], 'Delete' => ['access' => ['groups' => [0]]]]
        ]
    ];
    public static $labels = [
        'col' => 'Поле',
        'old' => 'Старое значение',
        'new' => 'Новое значение'
    ];

    public static function relations() {
        return [
            'activity' => [
                'col' => 'activity_id',
                'model' => 'Inji\Dashboard\Activity'
            ]
        ];
    }

}
