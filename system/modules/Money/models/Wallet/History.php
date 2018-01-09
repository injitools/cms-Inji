<?php

/**
 * Wallet history
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Wallet;

class History extends \Inji\Model {

    public static $objectName = 'История кошелька';
    public static $cols = [
        'wallet_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'wallet'],
        'new' => ['type' => 'decimal'],
        'old' => ['type' => 'decimal'],
        'amount' => ['type' => 'decimal'],
        'comment' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'wallet:user' => 'Пользователь',
        'old' => 'До',
        'new' => 'После',
        'amount' => 'Сумма',
        'comment' => 'Комментарий',
        'date_create' => 'Дата'
    ];

    public static function relations() {
        return [
            'wallet' => [
                'model' => 'Money\Wallet',
                'col' => 'wallet_id'
            ]
        ];
    }

    public static $dataManagers = [
        'manager' => [
            'name' => 'История кошельков',
            'cols' => [
                'wallet:user_id',
                'wallet:currency_id',
                'old',
                'new',
                'amount',
                'comment',
                'date_create'
            ],
            'sortable' => [
                'old',
                'new',
                'amount',
                'date_create',
            ],
            'filters' => [
                'wallet:user:mail',
                'wallet:user:info:first_name',
                'wallet:user:info:last_name',
                'wallet:currency_id',
                'old',
                'new',
                'amount',
                'comment',
                'date_create'
            ],
            'preSort' => [
                'date_create' => 'desc'
            ],
            'actions' => []
        ]
    ];

}
