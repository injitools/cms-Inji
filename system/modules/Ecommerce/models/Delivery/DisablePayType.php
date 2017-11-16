<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 13.11.2017
 * Time: 14:11
 */

namespace Ecommerce\Delivery;


class DisablePayType extends \Model {
    static $cols = [
        'delivery_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'delivery'],
        'paytype_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'payType'],
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['paytype_id']
        ]
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['paytype_id']
            ]
        ]
    ];

    static function relations() {
        return [
            'delivery' => [
                'col' => 'delivery_id',
                'model' => 'Ecommerce\Delivery',
            ],
            'payType' => [
                'col' => 'paytype_id',
                'model' => 'Ecommerce\PayType',
            ]
        ];
    }
}