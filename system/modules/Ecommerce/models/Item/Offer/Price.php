<?php

/**
 * Item offer price
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item\Offer;
/**
 * Class Price
 *
 * @property int $id
 * @property int $item_offer_id
 * @property int $item_offer_price_type_id
 * @property string $name
 * @property number $price
 * @property int $currency_id
 * @property int $weight
 * @property string $date_create
 *
 * @property-read \Ecommerce\Item\Offer $offer
 * @method \Ecommerce\Item\Offer offer($options)
 * @property-read \Ecommerce\Item\Offer\Price\Type $type
 * @method \Ecommerce\Item\Offer\Price\Type type($options)
 * @property-read \Money\Currency $currency
 * @method \Money\Currency currency($options)
 */
class Price extends \Model {

    public static $objectName = 'Цена';
    public static $cols = [
        //Основные параметры
        'item_offer_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'offer'],
        'item_offer_price_type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
        'name' => ['type' => 'text'],
        'price' => ['type' => 'decimal'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        //Системные
        'weight' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'price' => 'Цена',
        'item_offer_price_type_id' => 'Тип цены',
        'item_offer_id' => 'Товар',
        'currency_id' => 'Валюта',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Цены',
            'cols' => [
                'item_offer_price_type_id',
                'price',
                'currency_id',
            ]
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['price', 'currency_id'],
                ['item_offer_price_type_id', 'item_offer_id']
            ]
        ]];

    public static function relations() {
        return [
            'offer' => [
                'model' => 'Ecommerce\Item\Offer',
                'col' => 'item_offer_id'
            ],
            'type' => [
                'model' => 'Ecommerce\Item\Offer\Price\Type',
                'col' => 'item_offer_price_type_id'
            ],
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ],
        ];
    }

}
