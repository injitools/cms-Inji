<?php

/**
 * Item option
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item\Offer;

class Option extends \Model {

    public static $objectName = 'Свойство предложения';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'filter_name' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
        'code' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'postfix' => ['type' => 'text'],
        'default_val' => ['type' => 'text'],
        'view' => ['type' => 'bool'],
        'searchable' => ['type' => 'bool'],
        //Системные
        'weight' => ['type' => 'number'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'advance' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'item' => ['type' => 'dataManager', 'relation' => 'items'],
    ];
    public static $labels = [
        'name' => 'Название',
        'filter_name' => 'Название в фильтре',
        'image_file_id' => 'Иконка',
        'code' => 'Код',
        'type' => 'Тип',
        'postfix' => 'Постфикс',
        'default_val' => 'Значение по умолчанию',
        'view' => 'Отображается',
        'searchable' => 'Используется при поиске',
        'weight' => 'Вес сортировки',
        'advance' => 'Дополнительные параметры',
        'user_id' => 'Создатель',
        'date_create' => 'Дата создания',
        'item' => 'Значения для списка'
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Свойства предложения',
            'cols' => [
                'name', 'code', 'type', 'item', 'view', 'searchable', 'user_id', 'date_create'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'filter_name'],
                ['code', 'type', 'image_file_id'],
                ['default_val', 'postfix'],
                ['view', 'searchable'],
                ['item']
            ]
        ]
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'items' => [
                'type' => 'many',
                'model' => 'Ecommerce\Item\Offer\Option\Item',
                'col' => 'item_offer_option_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
        ];
    }

    public function beforeSave() {
        if (!isset($this->id)) {
            $this->user_id = \Users\User::$cur->id;
        }
    }

}
