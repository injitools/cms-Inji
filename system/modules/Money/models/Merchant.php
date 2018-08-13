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
/**
 * @property string $name
 * @property string $object_name
 * @property int $image_file_id
 * @property int $preview_image_file_id
 * @property boolean $active
 * @property boolean $deposit
 * @property boolean $withdraw
 * @property boolean $pay
 * @property string $date_create
 *
 * @property \Money\Merchant\Currency[] $currencies
 */
class Merchant extends \Model {

    public static $objectName = 'Система оплаты';
    public static $cols = [
        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        'object_name' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
        'preview_image_file_id' => ['type' => 'image'],
        'active' => ['type' => 'bool'],
        'deposit' => ['type' => 'bool'],
        'withdraw' => ['type' => 'bool'],
        'pay' => ['type' => 'bool'],
        'config' => ['type' => 'dataManager', 'relation' => 'configs'],
        'currency' => ['type' => 'dataManager', 'relation' => 'currencies'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'name' => 'Название',
        'image_file_id' => 'Иконка',
        'preview_image_file_id' => 'Превью экрана оплаты',
        'active' => 'Активировано',
        'deposit' => 'Пополнение',
        'withdraw' => 'Вывод',
        'pay' => 'Оплата',
        'object_name' => 'Класс обработчика',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Системы оплаты',
            'cols' => [
                'name',
                'code',
                'object_name',
                'active',
                'pay',
                'deposit',
                'withdraw'
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'code'],
                ['object_name', 'active'],
                ['pay', 'withdraw', 'deposit'],
                ['image_file_id', 'preview_image_file_id'],
                ['config'],
                ['currency']
            ]
        ]];

    public static function relations() {
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
            ],
            'previewImage' => [
                'model' => 'Files\File',
                'col' => 'preview_image_file_id'
            ]
        ];
    }

    public function allowCurrencies($pay) {
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
