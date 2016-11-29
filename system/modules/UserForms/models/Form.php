<?php

/**
 * Form
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace UserForms;

class Form extends \Model {

    public static $objectName = 'Форма обращения с сайта';
    public static $labels = [
        'name' => 'Название',
        'description' => 'Описание',
        'user_id' => 'Пользователь',
        'inputs' => 'Поля формы',
        'date_create' => 'Дата'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'description' => ['type' => 'html'],
        'user_id' => [ 'type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'inputs' => [ 'type' => 'dataManager', 'relation' => 'inputs'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'inputs',
                'user_id',
                'date_create',
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Форма приема обращений с сайта',
            'map' => [
                ['name'],
                ['description'],
                ['inputs'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => '\Users\User',
                'col' => 'user_id'
            ],
            'inputs' => [
                'type' => 'many',
                'model' => '\UserForms\Input',
                'col' => 'form_id',
            ],
        ];
    }

}
