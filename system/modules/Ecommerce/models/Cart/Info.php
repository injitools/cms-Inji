<?php

/**
 * Cart info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Cart;

class Info extends \Inji\Model {

    public static $objectName = 'Информация';
    public static $labels = [
        'name' => 'Название',
        'value' => 'Значение',
        'useradds_field_id' => 'Поле',
        'cart_id' => 'Корзина'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'value' => ['type' => 'dynamicType', 'typeSource' => 'selfMethod', 'selfMethod' => 'realType'],
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'useradds_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'value',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'value'],
                ['useradds_field_id', 'cart_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'cart' => [
                'model' => 'Inji\Ecommerce\Cart',
                'col' => 'cart_id'
            ],
            'field' => [
                'model' => 'Inji\Ecommerce\UserAdds\Field',
                'col' => 'useradds_field_id'
            ]
        ];
    }

    public function realType() {
        if ($this->field && $this->field->type) {
            $type = $this->field->type;

            if ($type == 'select') {
                return [
                    'type' => 'select',
                    'source' => 'relation',
                    'relation' => 'field:fieldItems',
                ];
            } elseif ($type === 'autocomplete') {
                return [
                    'type' => 'autocomplete',
                    'options' => json_decode($this->field->options, true)
                ];
            }
            return $type;
        }
        return 'text';
    }
}
