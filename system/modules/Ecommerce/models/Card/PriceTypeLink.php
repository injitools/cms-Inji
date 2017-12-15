<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 15.12.2017
 * Time: 16:59
 */

namespace Ecommerce\Card;


class PriceTypeLink extends \Model {
    static $cols = [
        'card_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'card'],
        'item_offer_price_type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['card_id', 'item_offer_price_type_id']
        ]
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['card_id', 'item_offer_price_type_id']
            ]
        ]
    ];

    static function relations() {
        return [
            'card' => [
                'model' => 'Ecommerce\Card',
                'col' => 'card_id'
            ],
            'type' => [
                'model' => 'Ecommerce\Item\Offer\Price\Type',
                'col' => 'item_offer_price_type_id'
            ]
        ];
    }
}