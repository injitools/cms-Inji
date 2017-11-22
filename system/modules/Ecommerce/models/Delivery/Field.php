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
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $code
 * @property string $options
 * @property string $userfield
 * @property string $placeholder
 * @property bool $required
 * @property bool $save
 * @property number $weight
 * @property number $help_text
 * @property string $date_create
 *
 * @property-read \Ecommerce\Delivery\Field\Item[] $fieldItems
 * @property-read \Ecommerce\Delivery\DeliveryFieldLink[] $fieldRel
 *
 * @method \Ecommerce\Delivery\Field\Item[] fieldItems($options)
 * @method \Ecommerce\Delivery\DeliveryFieldLink[] fieldRel($options)
 */
class Field extends \Model {

    public static $objectName = 'Поле доставки';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'select', 'source' => 'array', 'empty' => false, 'sourceArray' => [
            'text' => 'Текстовое поле',
            'textarea' => 'Многострочный текст',
            'select' => 'Выпадающий список',
        ]],
        'code' => ['type' => 'text'],
        'help_text' => ['type' => 'text'],
        'options' => ['type' => 'textarea'],
        'userfield' => ['type' => 'text'],
        'placeholder' => ['type' => 'text'],
        'required' => ['type' => 'bool'],
        'save' => ['type' => 'bool'],
        'fieldItem' => ['type' => 'dataManager', 'relation' => 'fieldItems'],
        'weight' => ['type' => 'number'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'name' => 'Название',
        'type' => 'Тип',
        'options' => 'Опции поля',
        'userfield' => 'Связь с данными пользователя',
        'required' => 'Обязательно',
        'save' => 'Сохраняется',
        'fieldItem' => 'Значения для списка',
        'date_create' => 'Дата создания',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'code', 'type', 'userfield', 'required', 'fieldItem', 'save'
            ],
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'type'],
                ['code', 'required', 'save'],
                ['userfield'],
                ['options']
            ]
        ],
        'public' => [
            'map' => []
        ]
    ];

    public static function relations() {
        return [
            'fieldItems' => [
                'model' => 'Ecommerce\Delivery\Field\Item',
                'col' => 'delivery_field_id',
                'type' => 'many'
            ],
            'fieldRel' => [
                'model' => 'Ecommerce\Delivery\DeliveryFieldLink',
                'col' => 'delivery_field_id',
            ],
        ];
    }

}
