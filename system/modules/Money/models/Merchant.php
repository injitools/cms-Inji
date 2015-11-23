<?php

/**
 * Merchant
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class Merchant extends \Model
{
    static $objectName = 'Система оплаты';
    static $cols = [
        'name' => ['type' => 'text'],
        'object_name' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
        'active' => ['type' => 'bool'],
        'refill' => ['type' => 'bool'],
        'pay' => ['type' => 'bool'],
        'config' => ['type' => 'dataManager', 'relation' => 'configs'],
        'currency' => ['type' => 'dataManager', 'relation' => 'currencies']
    ];
    static $labels = [
        'name' => 'Название',
        'image_file_id' => 'Иконка',
        'active' => 'Активировано',
        'refill' => 'Пополнение',
        'pay' => 'Оплата',
        'object_name' => 'Класс обработчика',
    ];
    static $dataManagers = [
        'manager' => [
            'name' => 'Системы оплаты',
            'cols' => [
                'name',
                'object_name',
                'active',
                'pay',
                'refill'
            ],
        ],
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['name', 'image_file_id'],
                ['object_name', 'active'],
                ['pay','refill'],
                ['config'],
                ['currency']
            ]
    ]];

    static function relations()
    {
        return [
            'configs' => [
                'type' => 'many',
                'model' => 'Money\Merchant\Config',
                'col' => 'merchant_id'
            ],
            'currencies' => [
                'type' => 'many',
                'model' => 'Money\Merchant\Currency',
                'col' => 'merchant_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ]
        ];
    }

    function allowCurrencies($pay)
    {
        $allowCurrencies = [];
        foreach ($this->currencies as $merchantCurrency) {
            if ($merchantCurrency->currency_id == $pay->currency_id) {
                $allowCurrencies[] = ['type' => 'primary', 'currency' => $merchantCurrency->currency];
            } else {
                $transfer = Currency\ExchangeRate::get([['currency_id', $merchantCurrency->currency_id], ['target_currency_id', $pay->currency_id]]);
                if ($transfer) {
                    $allowCurrencies[] = ['type' => 'transfer', 'currency' => $merchantCurrency->currency, 'transfer' => $transfer];
                }
            }
        }
        return $allowCurrencies;
    }

}