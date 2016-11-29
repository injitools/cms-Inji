<?php

/**
 * UserAdds info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\UserAdds;

class Field extends \Model {

    public static $objectName = 'Поле информации при заказе';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'userfield' => ['type' => 'text'],
        'required' => ['type' => 'bool'],
        'save' => ['type' => 'bool'],
        //Системные
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'name' => 'Название',
        'type' => 'Тип',
        'required' => 'Обязательно',
        'save' => 'Сохраняется'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'type', 'userfield', 'required', 'save'
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'type'],
                ['required', 'save'],
                [ 'userfield']
            ]
        ]
    ];

}
