<?php

/**
 * Input
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace UserForms;

class Input extends \Model {

    public static $objectName = 'Поле формы';
    public static $labels = [
        'label' => 'Название',
        'type' => 'Тип',
        'required' => 'Обязательное',
        'params' => 'Параметры'
    ];
    public static $cols = [
        'label' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'required' => ['type' => 'bool'],
        'form_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'form'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'input_params' => ['type' => 'textarea'],
        'weight' => ['type' => 'number']
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'label',
                'type',
                'required',
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['label', 'form_id'],
                ['type', 'required'],
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
                'col' => 'form_id',
            ],
        ];
    }

}
