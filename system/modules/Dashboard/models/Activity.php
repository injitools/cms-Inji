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

namespace Inji\Dashboard;

/**
 * Description of Activity
 *
 * @author benzu
 */
class Activity extends \Inji\Model {

    public static $logging = false;
    public static $cols = [
        'type' => ['type' => 'select', 'source' => 'array',
            'sourceArray' => [
                'changes' => 'Изменение',
                'new' => 'Создание',
                'delete' => 'Удаление'
            ]
        ],
        'item_id' => ['type' => 'number',
            'view' => [
                'type' => 'moduleMethod',
                'module' => 'Dashboard',
                'method' => 'itemHref'
            ]
        ],
        'module' => ['type' => 'text',
            'view' => [
                'type' => 'moduleMethod',
                'module' => 'Dashboard',
                'method' => 'moduleHref'
            ]
        ],
        'model' => ['type' => 'text',
            'view' => [
                'type' => 'moduleMethod',
                'module' => 'Dashboard',
                'method' => 'modelHref'
            ]
        ],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'changes_text' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
        'change' => ['type' => 'dataManager', 'relation' => 'changes']
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['type', 'module', 'model', 'item_id', 'user_id', 'changes_text', 'change', 'date_create'],
            'sortable' => ['type', 'item_id', 'module', 'model', 'user_id', 'date_create'],
            'preSort' => ['date_create' => 'desc'],
            'actions' => ['Edit' => ['access' => ['groups' => [0]]], 'Delete' => ['access' => ['groups' => [0]]]],
            'filters' => ['type', 'module', 'model', 'item_id', 'changes_text', 'change', 'date_create']
        ]
    ];
    public static $labels = [
        'type' => 'Тип события',
        'item_id' => 'Объект',
        'module' => 'Модуль',
        'model' => 'Сущность',
        'user_id' => 'Пользователь',
        'changes_text' => 'Сводка изменений',
        'change' => 'Изменения',
        'date_create' => 'Дата события'
    ];

    public static function relations() {
        return [
            'user' => [
                'col' => 'user_id',
                'model' => 'Inji\Users\User'
            ],
            'changes' => [
                'type' => 'many',
                'col' => 'activity_id',
                'model' => 'Inji\Dashboard\Activity\Change'
            ]
        ];
    }

}
