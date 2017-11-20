<?php

/**
 * Delivery user info
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;
/**
 * Class DeliveryInfo
 * @property int $id
 * @property string $name
 * @property int $cart_id
 * @property int $delivery_field_id
 * @property string $value
 *
 * @property \Ecommerce\Delivery\Field $field
 * @property \Ecommerce\Cart $cart
 *
 */
class DeliveryInfo extends \Model {

    public static $objectName = 'Информация о доставке';
    public static $labels = [
        'name' => 'Название',
        'cart_id' => 'Корзина',
        'delivery_field_id' => 'Поле',
        'value' => 'Значение',
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'cart_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cart'],
        'delivery_field_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'field'],
        'value' => ['type' => 'dynamicType', 'typeSource' => 'selfMethod', 'selfMethod' => 'realType'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'value'],
                ['delivery_field_id', 'cart_id'],
            ]
        ]
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'value'
            ]
        ]
    ];

    public static function relations() {
        return [
            'field' => [
                'model' => 'Ecommerce\Delivery\Field',
                'col' => 'delivery_field_id'
            ],
            'cart' => [
                'model' => 'Ecommerce\Cart',
                'col' => 'cart_id'
            ],
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
