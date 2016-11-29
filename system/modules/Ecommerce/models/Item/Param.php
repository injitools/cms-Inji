<?php

/**
 * Item param
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item;

class Param extends \Model {

    public static $objectName = 'Параметр товара';
    public static $labels = [
        'item_option_id' => 'Параметр',
        'item_id' => 'Товар',
        'value' => 'Значение',
    ];
    public static $cols = [
        //Основные параметры
        'item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'item'],
        'item_option_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'option', 'onChange' => 'reloadForm'],
        'value' => ['type' => 'dynamicType', 'typeSource' => 'selfMethod', 'selfMethod' => 'realType'],
        //Системные
        'date_create' => ['type' => 'dateTime']
    ];

    public static function indexes() {
        return [
            'ecommerce_itemOptionRelation' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_param_item_id',
                    'item_param_item_option_id'
                ]
            ],
            'ecommerce_paramItemIndex' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_param_item_id',
                ]
            ],
            'ecommerce_paramOptionIndex' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_param_item_option_id'
                ]
            ],
        ];
    }

    public function realType() {
        if ($this->option) {
            $type = $this->option->type;

            if ($type == 'select') {
                return [
                    'type' => 'select',
                    'source' => 'relation',
                    'relation' => 'option:items',
                ];
            }
            return $type;
        }
        return 'text';
    }

    public static $dataManagers = [

        'manager' => [
            'name' => 'Параметры товара',
            'cols' => [
                'item_option_id',
                'item_id',
                'value',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['item_id', 'item_option_id'],
                ['value']
            ]
    ]];

    function name() {
        return $this->value;
    }

    public function value($default = '') {
        if ($this->option->type != 'select') {
            return $this->value;
        } elseif ($this->optionItem) {
            return $this->optionItem->value;
        }
        return $default;
    }

    public static function relations() {
        return [
            'file' => [
                'model' => 'Files\File',
                'col' => 'value'
            ],
            'option' => [
                'model' => 'Ecommerce\Item\Option',
                'col' => 'item_option_id'
            ],
            'item' => [
                'model' => 'Ecommerce\Item',
                'col' => 'item_id'
            ],
            'optionItem' => [
                'model' => 'Ecommerce\Item\Option\Item',
                'col' => 'value'
            ]
        ];
    }

}
