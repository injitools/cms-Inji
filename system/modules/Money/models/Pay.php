<?php

/**
 * Pay
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class Pay extends \Model {

    public static function relations() {
        return [
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ],
            'status' => [
                'model' => 'Money\Pay\Status',
                'col' => 'pay_status_id'
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ]
        ];
    }

    public static $cols = [
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'pay_status_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'status'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'sum' => ['type' => 'decimal'],
        'type' => ['type' => 'text'],
        'callback_module' => ['type' => 'text'],
        'callback_method' => ['type' => 'text'],
        'description' => ['type' => 'textarea'],
        'data' => ['type' => 'text'],
        'date_recive' => ['type' => 'dateTime'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'currency_id' => 'Валюта',
        'pay_status_id' => 'Статус',
        'sum' => 'Сумма',
        'user_id' => 'Пользователь',
        'type' => 'Тип',
        'description' => 'Описание',
        'callback_module' => 'Модуль обработчика',
        'callback_method' => 'Метод обработчика',
        'data' => 'Данные обработчика',
        'date_recive' => 'Дата получения',
        'date_create' => 'Дата создания'
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Счета оплаты',
            'cols' => [
                'sum',
                'currency_id',
                'pay_status_id',
                'user_id',
                'date_recive',
                'date_create'
            ],
            'sortable' => [
                'sum',
                'currency_id',
                'pay_status_id',
                'date_recive',
                'date_create'
            ],
            'preSort' => [
                'date_create' => 'desc'
            ],
            'actions' => [
                'Money\ClosePayBtn', 'Edit', 'Delete'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Счет оплаты',
            'map' => [
                ['sum', 'currency_id'],
                ['user_id', 'date_recive'],
                ['pay_status_id', 'type'],
                ['description'],
                ['callback_module', 'callback_method', 'data']
            ]
        ]
    ];

}
