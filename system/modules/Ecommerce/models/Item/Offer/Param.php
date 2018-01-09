<?php

/**
 * Item param
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Item\Offer;
/**
 * @property int $id
 * @property int $item_offer_option_id
 * @property int $item_offer_id
 * @property string $value
 * @property string $date_create
 *
 * @property-read \Ecommerce\Item\Offer\Option $option
 * @property-read \Ecommerce\Item\Offer\Option\Item $optionItem
 * @property-read \Ecommerce\Item\Offer $offer
 * @property-read \Files\File $file
 */
class Param extends \Inji\Model {

    public static $objectName = 'Параметр товара';
    public static $labels = [
        'item_offer_option_id' => 'Параметр предложения',
        'item_offer_id' => 'Предложение',
        'value' => 'Значение',
    ];
    public static $cols = [
        //Основные параметры
        'item_offer_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'offer'],
        'item_offer_option_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'option', 'onChange' => 'reloadForm'],
        'value' => ['type' => 'dynamicType', 'typeSource' => 'selfMethod', 'selfMethod' => 'realType'],
        //Системные
        'date_create' => ['type' => 'dateTime']
    ];

    public static function indexes() {
        return [
            'ecommerce_itemOfferOptionRelation' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_offer_param_item_offer_id',
                    'item_offer_param_item_offer_option_id'
                ]
            ],
            'ecommerce_paramItemOfferIndex' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_offer_param_item_offer_id',
                ]
            ],
            'ecommerce_paramOfferOptionIndex' => [
                'type' => 'INDEX',
                'cols' => [
                    'item_offer_param_item_offer_option_id'
                ]
            ],
        ];
    }

    public function realType() {
        if ($this->option && $this->option->type) {
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
            'name' => 'Параметры предложения',
            'cols' => [
                'item_offer_option_id',
                'item_offer_id',
                'value',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['item_offer_id', 'item_offer_option_id'],
                ['value']
            ]
        ]];

    /**
     * Return final param value
     *
     * @param string $default
     * @return string
     */
    public function valueText($default = '') {
        if ($this->option->type == 'select' && $this->optionItem) {
            return $this->optionItem->value;
        } elseif ($this->option->type !== 'select' && $this->value) {
            return $this->value;
        }
        return $default;
    }

    public static function relations() {
        return [
            'file' => [
                'model' => 'Inji\Files\File',
                'col' => 'value'
            ],
            'option' => [
                'model' => 'Inji\Ecommerce\Item\Offer\Option',
                'col' => 'item_offer_option_id'
            ],
            'offer' => [
                'model' => 'Inji\Ecommerce\Item\Offer',
                'col' => 'item_offer_id'
            ],
            'optionItem' => [
                'model' => 'Inji\Ecommerce\Item\Offer\Option\Item',
                'col' => 'value'
            ]
        ];
    }
}