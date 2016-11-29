<?php

/**
 * Recive
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace UserForms;

class Recive extends \Model {

    public static $objectName = 'Полученная форма';
    public static $labels = [
        'form_title' => 'Название',
        'form_user_id' => 'Пользователь',
        'inputs' => 'Поля формы',
        'date_create' => 'Дата',
        'form_id' => 'Форма',
        'data' => 'Данные',
    ];
    public static $cols = [
        'form_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'form'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'data' => [
            'type' => 'json',
            'view' => [
                'type' => 'moduleMethod',
                'module' => 'UserForms',
                'method' => 'formData'
            ],
        ],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Полученные формы',
            'cols' => [
                'form_id',
                'data',
                'date_create',
            ],
            'sortable' => [
                'form_id',
                'data',
                'date_create',
            ],
            'preSort' => [
                'date_create' => 'desc'
            ]
        ]
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => '\Users\User',
                'col' => 'user_id'
            ],
            'form' => [
                'model' => '\UserForms\Form',
                'col' => 'form_id'
            ],
        ];
    }

}
