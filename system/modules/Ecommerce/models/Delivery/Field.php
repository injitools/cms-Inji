<?php

/**
 * Delivery field
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery;

class Field extends \Model {

    public static $objectName = 'Поле доставки';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'userfield' => ['type' => 'text'],
        'required' => ['type' => 'bool'],
        'save' => ['type' => 'bool'],
        'fieldItem' => ['type' => 'dataManager', 'relation' => 'fieldItems'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'name' => 'Название',
        'type' => 'Тип',
        'userfield' => 'Связь с данными пользователя',
        'required' => 'Обязательно',
        'save' => 'Сохраняется',
        'fieldItem' => 'Значения для списка',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'type', 'userfield', 'required', 'fieldItem', 'save'
            ],
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

    public static function relations() {
        return [
            'fieldItems' => [
                'model' => 'Ecommerce\Delivery\Field\Item',
                'col' => 'delivery_field_id',
                'type' => 'many'
            ],
        ];
    }

}
