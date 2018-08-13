<?php

/**
 * Currency
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;
/**
 * @property string $name
 * @property string $code
 * @property bool $wallet
 * @property bool $deposit
 * @property bool $withdraw
 * @property string $round_type
 * @property string $round_precision
 */
class Currency extends \Model {

    public static $objectName = 'Валюта';
    public static $labels = [
        'name' => 'Название',
        'code' => 'Обозначение',
        'wallet' => 'Кошелек на сайте',
        'deposit' => 'Пополнение',
        'withdraw' => 'Вывод',
        'transfer' => 'Переводы',
        'round_type' => 'Округдение при выводе',
        'round_precision' => 'Количество занков после запятов при округдлении',
        'date_create' => 'Дата создания',
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        'deposit' => ['type' => 'bool'],
        'wallet' => ['type' => 'bool'],
        'withdraw' => ['type' => 'bool'],
        'transfer' => ['type' => 'bool'],
        'round_type' => ['type' => 'text'],
        'round_precision' => ['type' => 'number'],
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Валюты',
            'sortMode'=>true,
            'cols' => ['name', 'code', 'wallet', 'deposit','withdraw', 'transfer', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'code'],
                ['round_type', 'round_precision'],
                ['deposit', 'wallet', 'transfer','withdraw'],
            ]
        ]
    ];

    public function acronym() {
        return "<acronym title='{$this->name()}'>{$this->code}</acronym>";
    }

    public function beforeDelete() {
        if ($this->id) {
            $wallets = Wallet::getList(['where' => ['currency_id', $this->id]]);
            foreach ($wallets as $wallet) {
                $wallet->delete();
            }
        }
    }

}
