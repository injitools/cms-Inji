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
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $code
 * @property string $options
 * @property string $userfield
 * @property string $placeholder
 * @property string $help_text
 * @property bool $required
 * @property bool $save
 * @property number $weight
 * @property string $date_create
 *
 * @property-read \Ecommerce\UserAdds\Field\Item[] $fieldItems
 *
 * @method \Ecommerce\UserAdds\Field\Item[] fieldItems($options)
 */
class Field extends \Model {

    public static $objectName = 'Поле информации при заказе';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'select', 'source' => 'array', 'empty' => false, 'sourceArray' => [
            'text' => 'Текстовое поле',
            'textarea' => 'Многострочный текст',
            'select' => 'Выпадающий список',
        ]],
        'code' => ['type' => 'text'],
        'options' => ['type' => 'textarea'],
        'userfield' => ['type' => 'text'],
        'placeholder' => ['type' => 'text'],
        'help_text' => ['type' => 'text'],
        'required' => ['type' => 'bool'],
        'save' => ['type' => 'bool'],
        //Системные
        'fieldItem' => ['type' => 'dataManager', 'relation' => 'fieldItems'],
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'name' => 'Название',
        'type' => 'Тип',
        'required' => 'Обязательно',
        'placeholder' => 'Плейсхолдер',
        'userfield' => 'Связь с полем профиля',
        'help_text' => 'Подсказка для заполнения',
        'save' => 'Сохраняется',
        'options' => 'Системные настройки поля',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'type', 'userfield', 'placeholder', 'required', 'save'
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'type'],
                ['required', 'save'],
                ['userfield', 'placeholder'],
                ['help_text'],
                ['options']
            ]
        ]
    ];

    public static function relations() {
        return [
            'fieldItems' => [
                'model' => 'Ecommerce\UserAdds\Field\Item',
                'col' => 'useradds_field_id',
                'type' => 'many'
            ],
        ];
    }
}
