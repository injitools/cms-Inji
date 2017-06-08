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

/**
 *
 * @property string $name
 * @property string $description
 * @property int $user_id
 * @property string $submit
 * @property string $date_create
 * @property-read \Users\User $user
 * @property-read \UserForms\Input[] $inputs
 * @method inputs (Array $options)
 */
class Form extends \Model {

    public static $objectName = 'Форма обращения с сайта';
    public static $labels = [
        'name' => 'Название',
        'description' => 'Описание',
        'user_id' => 'Пользователь',
        'inputs' => 'Поля формы',
        'submit' => 'Текст кнопки отправки',
        'date_create' => 'Дата'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'submit' => ['type' => 'text'],
        'description' => ['type' => 'html'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'inputsMgr' => ['type' => 'dataManager', 'relation' => 'inputs'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'inputs',
                'submit',
                'user_id',
                'date_create',
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Форма приема обращений с сайта',
            'map' => [
                ['name', 'submit'],
                ['description'],
                ['inputsMgr'],
            ]
        ]
    ];

    public function beforeSave() {
        if (!$this->id) {
            $this->user_id = \Users\User::$cur->id;
        }
    }

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
