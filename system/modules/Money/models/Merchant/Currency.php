<?php

/**
 * Merchant currency
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Merchant;

class Currency extends \Model {

    public static $objectName = 'Валюта системы оплаты';
    public static $cols = [
        'code' => ['type' => 'text'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'merchant_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'merchant'],
    ];
    public static $labels = [
        'currency_id' => 'Валюта',
        'merchant_id' => 'Система оплаты',
        'code' => 'Код',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Валюты системы оплаты',
            'cols' => [
                'currency_id',
                'code',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['currency_id', 'code'],
                ['merchant_id'],
            ]
    ]];

    public static function relations() {
        return [
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ],
            'merchant' => [
                'model' => 'Money\Merchant',
                'col' => 'merchant_id'
            ]
        ];
    }

}
