<?php

/**
 * Transfer
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class Transfer extends \Inji\Model {

    public static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'to_user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'toUser'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'amount' => ['type' => 'decimal'],
        'code' => ['type' => 'text'],
        'comment' => ['type' => 'textarea', 'validator' => 'commentClean'],
        'complete' => ['type' => 'bool'],
        'canceled' => ['type' => 'bool'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'user_id' => 'От кого',
        'to_user_id' => 'Кому',
        'currency_id' => 'Валюта',
        'amount' => 'Сумма',
        'code' => 'Код подтверждения',
        'comment' => 'Комментарий',
        'complete' => 'Завершен',
        'canceled' => 'Отменен',
        'date_create' => 'Дата создания',
    ];

    public static function itemName($item) {
        return $item->pk() . '. ' . $item->name();
    }

    public static $dataManagers = [
        'manager' => [
            'preSort' => [
                'date_create' => 'desc'
            ],
            'name' => 'Переводы',
            'cols' => [
                'user_id', 'to_user_id', 'currency_id', 'amount', 'comment', 'complete', 'canceled', 'date_create'
            ],
            'actions' => [
                'Money\CancelTransfer', 'Money\CompleteTransfer'
            ],
            'sortable' => [
                'user_id', 'to_user_id', 'currency_id', 'amount', 'comment', 'complete', 'canceled', 'date_create'
            ],
            'filters' => [
                'user_id', 'to_user_id', 'currency_id', 'amount', 'comment', 'complete', 'canceled', 'date_create'
            ]
        ]
    ];
    public static $forms = [
        'transfer' => [
            'name' => 'Перевод средств',
            'successText' => 'Операция перевода средств была успешно начата',
            'inputs' => [
                'userSearch' => [
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'toUser',
                    'showCol' => [
                        'type' => 'staticMethod',
                        'class' => 'Money\Transfer',
                        'method' => 'itemName',
                    ],
                    'label' => 'Получатель',
                    'cols' => [
                        'info:first_name',
                        'info:last_name',
                        'info:middle_name',
                        'mail'
                    ],
                    'col' => 'to_user_id',
                    'required' => true,
                    'validator' => 'userSearch'
                ],
                'wallets' => [
                    'type' => 'select',
                    'source' => 'method',
                    'module' => 'Money',
                    'method' => 'getUserWallets',
                    'params' => [null, false, true, true],
                    'label' => 'Кошелек',
                    'col' => 'currency_id',
                    'required' => true
                ],
                'amount' => [
                    'type' => 'number',
                    'validator' => 'amount',
                    'required' => true
                ],
            ],
            'map' => [
                ['userSearch'],
                ['wallets', 'amount'],
                ['comment']
            ]
        ]
    ];

    public static function validators() {
        return [
            'userSearch' => function($activeForm, $request) {
                if (empty($request['userSearch'])) {
                    throw new \Exception('Не указан получатель');
                }
                if (!((int) $request['userSearch'])) {
                    throw new \Exception('Не указан получатель');
                }
                $user = \Users\User::get((int) $request['userSearch']);
                if (!$user) {
                    throw new \Exception('Такой пользователь не найден');
                }
                if ($user->id == \Users\User::$cur->id) {
                    throw new \Exception('Нельзя выбрать себя в качестве получателя');
                }
                return true;
            },
            'amount' => function($activeForm, $request) {
                if (empty($request['amount'])) {
                    throw new \Exception('Не указана сумма');
                }
                if (!((float) $request['amount'])) {
                    throw new \Exception('Не указана сумма');
                }
                $amount = (float) $request['amount'];
                if (empty($request['wallets'])) {
                    throw new \Exception('Не указан кошелек');
                }
                if (!((int) $request['wallets'])) {
                    throw new \Exception('Не указан кошелек');
                }
                $wallets = \App::$cur->money->getUserWallets();
                if (empty($wallets[(int) $request['wallets']])) {
                    throw new \Exception('У вас нет такого кошелька');
                }
                $wallet = $wallets[(int) $request['wallets']];
                if (!$wallet->currency->transfer) {
                    throw new \Exception('Вы не можете переводить эту валюту');
                }
                if ($wallet->amount < $amount) {
                    throw new \Exception('У вас недостаточно средств на кошельке');
                }
                return true;
            },
            'commentClean' => function($activeForm, &$request) {
                $request['comment'] = trim(htmlspecialchars(urldecode($request['comment'])));
            }
        ];
    }

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'toUser' => [
                'model' => 'Users\User',
                'col' => 'to_user_id'
            ],
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ]
        ];
    }

    public function name() {
        return 'Перевод на сумму ' . $this->amount . ' ' . $this->currency->name . ' от ' . $this->user->name() . ' для ' . $this->toUser->name();
    }

    public function cancel() {
        if ($this->canceled || $this->complete) {
            return false;
        }

        $this->canceled = 1;
        $block = \Money\Wallet\Block::get('Money\Transfer:' . $this->id, 'data');
        if ($block) {
            $block->delete();
        }
        $wallets = \App::$cur->money->getUserWallets($this->user_id);
        $text = 'Отмена перевода средств';
        $wallets[$this->currency_id]->diff($this->amount, $text);
        \App::$cur->users->AddUserActivity($this->user_id, 4, $text . '<br />' . (float) $this->amount . ' ' . $wallets[$this->currency_id]->currency->acronym());
        $this->save();
        return true;
    }

    public function complete() {
        if ($this->canceled || $this->complete) {
            return false;
        }

        $this->complete = 1;
        $block = \Money\Wallet\Block::get('Money\Transfer:' . $this->id, 'data');
        if ($block) {
            $block->delete();
        }
        $wallets = \App::$cur->money->getUserWallets($this->to_user_id);
        $text = 'Перевод средств от ' . $this->user->name() . '.' . ($this->comment ? ' Комментарий:' . $this->comment : '');
        $wallets[$this->currency_id]->diff($this->amount, $text);
        \App::$cur->users->AddUserActivity($this->to_user_id, 4, $text . '<br />' . (float) $this->amount . ' ' . $wallets[$this->currency_id]->currency->acronym());
        $this->save();
        return true;
    }

}
