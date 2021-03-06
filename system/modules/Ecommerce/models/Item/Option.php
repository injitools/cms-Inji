<?php

/**
 * Item option
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item;
/**
 * @property int $id
 * @property string $name
 * @property string $filter_name
 * @property int $image_file_id
 * @property string $code
 * @property string $type
 * @property string $postfix
 * @property string $default_val
 * @property bool $view
 * @property bool $searchable
 * @property int $item_option_group_id
 * @property int $weight
 * @property int $user_id
 * @property string $advance
 * @property string $date_create
 *
 * @property-read \Users\User $user
 * @property-read \Ecommerce\Item\Option\Group $group
 * @property-read \Ecommerce\Item\Option\Item[] $items
 * @property-read \Files\File $image
 */
class Option extends \Model {

    public static $objectName = 'Свойство';
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
        'item_option_group_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'group'],
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
        'item_option_group_id' => 'Группа опций',
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
            'name' => 'Свойства товаров',
            'cols' => [
                'name', 'item_option_group_id', 'code', 'type', 'item', 'view', 'searchable', 'user_id', 'date_create'
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'filter_name'],
                ['code', 'type', 'image_file_id'],
                ['default_val', 'postfix'],
                ['view', 'searchable', 'item_option_group_id'],
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
            'group' => [
                'model' => 'Ecommerce\Item\Option\Group',
                'col' => 'item_option_group_id'
            ],
            'items' => [
                'type' => 'many',
                'model' => 'Ecommerce\Item\Option\Item',
                'col' => 'item_option_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
        ];
    }

    public function beforeSave() {
        if (!isset($this->id) && class_exists('Users\User')) {
            $this->user_id = \Users\User::$cur->id;
        }
        if ($this->advance && is_array($this->advance)) {
            $this->advance = json_encode($this->advance);
        }
    }
}