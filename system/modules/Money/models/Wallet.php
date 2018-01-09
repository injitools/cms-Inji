<?php

/**
 * Wallet
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class Wallet extends \Inji\Model {

    public static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'historyMgr' => ['type' => 'dataManager', 'relation' => 'history'],
        'amount' => ['type' => 'decimal'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'user_id' => 'Пользователь',
        'currency_id' => 'Валюта',
        'amount' => 'Сумма',
        'historyMgr' => 'История',
    ];

    public static function relations() {
        return [
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ],
            'history' => [
                'type' => 'many',
                'model' => 'Money\Wallet\History',
                'col' => 'wallet_id',

            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
        ];
    }

    public static $dataManagers = [
        'manager' => [
            'cols' => ['user:id', 'user_id', 'currency_id', 'amount', 'historyMgr'],
            'sortable' => ['user:id', 'user_id', 'currency_id', 'amount'],
            'filters' => ['user:info:first_name', 'user:info:last_name', 'currency_id'],
            'actions' => [
                'Delete' => false,
                'diff' => [
                    'name' => 'Списать/пополнить',
                    'className' => 'Money\WalletDiff',
                    'aditionalInfo' => [
                        'amount' => [
                            'type' => 'text',
                            'label' => 'Сумма(отрицательна для списания)',
                        ],
                        'comment' => [
                            'type' => 'text',
                            'label' => 'Комментарий',
                        ],
                    ]
                ],],
        ]
    ];

    public function beforeSave() {
        if ($this->pk()) {
            $cur = Wallet::get($this->pk());
            if ($cur->amount != $this->amount) {
                $history = new Wallet\History();
                $history->wallet_id = $this->pk();
                $history->old = $cur->amount;
                $history->new = $this->amount;
                $history->amount = $this->amount - $cur->amount;
                $history->save();
            }
        }
    }

    public function diff($amount, $comment = '') {
        $amount = (float)$amount;
        $query = \App::$cur->db->newQuery();
        $string = 'UPDATE ' . \App::$cur->db->table_prefix . $this->table() . ' SET `' . $this->colPrefix() . 'amount`=`' . $this->colPrefix() . 'amount`+' . $amount . ' where `' . $this->index() . '` = ' . $this->id;
        $query->query($string);
        $history = new Wallet\History();
        $history->wallet_id = $this->pk();
        $history->old = $this->amount;
        $history->new = $this->amount + $amount;
        $history->amount = $amount;
        $history->comment = $comment;
        $history->save();
    }

    public function name() {
        return $this->currency->name();
    }

    public function showAmount() {
        switch ($this->currency->round_type) {
            case 'floor':
                $dif = (float)('1' . str_repeat('0', $this->currency->round_precision));
                return floor($this->amount * $dif) / $dif;
            default:
                return $this->amount;
        }
    }

    public function beforeDelete() {
        if ($this->id) {
            Wallet\History::deleteList(['wallet_id', $this->id]);
            Wallet\Block::deleteList(['wallet_id', $this->id]);
        }
    }

}
