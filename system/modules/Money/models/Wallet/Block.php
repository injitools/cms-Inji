<?php

/**
 * Wallet block
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Wallet;

class Block extends \Inji\Model {

    public static $objectName = 'Блокировка кошелька';
    public static $cols = [
        'wallet_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'wallet'],
        'amount' => ['type' => 'decimal'],
        'data' => ['type' => 'text'],
        'comment' => ['type' => 'text'],
        'expired_type' => ['type' => 'text'],
        'date_expired' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'wallet:user' => 'Пользователь',
        'amount' => 'Сумма',
        'comment' => 'Комментарий',
        'date_create' => 'Дата',
        'date_expired' => 'Истекает'
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Блокировки кошельков',
            'cols' => [
                'wallet:user_id',
                'wallet:currency_id',
                'amount',
                'comment',
                'date_create',
                'date_expired'
            ],
            'sortable' => [
                'amount',
                'comment',
                'date_create',
                'date_expired'
            ],
            'filters' => [
                'wallet:user:mail',
                'wallet:user:info:first_name',
                'wallet:user:info:last_name',
                'wallet:currency_id',
                'amount',
                'comment',
                'date_create',
                'date_expired'
            ],
            'preSort' => [
                'date_create' => 'desc'
            ],
            'actions' => []
        ]
    ];

    public static function relations() {
        return [
            'wallet' => [
                'model' => 'Money\Wallet',
                'col' => 'wallet_id'
            ]
        ];
    }

}
