<?php

/**
 * Reward
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class Reward extends \Model {

    public static $cols = [
        'name' => ['type' => 'text'],
        'active' => ['type' => 'bool'],
        'lasthaveall' => ['type' => 'bool'],
        'block' => ['type' => 'bool'],
        'block_date_expired' => ['type' => 'text'],
        'round_type' => ['type' => 'text'],
        'round_precision' => ['type' => 'number'],
        'peruser' => ['type' => 'number'],
        'quantity' => ['type' => 'number'],
        'condition' => ['type' => 'dataManager', 'relation' => 'conditions'],
        'level' => ['type' => 'dataManager', 'relation' => 'levels']
    ];
    public static $labels = [
        'name' => 'Название',
        'active' => 'Активно',
        'lasthaveall' => 'Все излишки на последнего',
        'block' => 'Блокировка в случае не исполнения условий',
        'block_date_expired' => 'Срок блокировки',
        'round_type' => 'Тип округления',
        'round_precision' => 'Число после запятой',
        'peruser' => 'Лимит для одного',
        'quantity' => 'Лимит всего',
        'condition' => 'Условие',
        'level' => 'Уровни',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Вознаграждения',
            'cols' => [
                'name', 'condition', 'level', 'active', 'lasthaveall', 'block', 'block_date_expired', 'round_type', 'round_precision', 'peruser', 'quantity'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Вознаграждение',
            'map' => [
                ['name'],
                ['active', 'lasthaveall'],
                ['block', 'block_date_expired'],
                ['round_type', 'round_precision'],
                ['peruser', 'quantity']
            ]
        ]
    ];

    public function checkBlocked() {
        $blocked = Wallet\Block::getList(['where' => [
                        ['data', 'reward:' . $this->id],
                        [
                            ['date_expired', '0000-00-00 00:00:00'],
                            ['date_expired', date('Y-m-d H:i:s'), '>', 'OR']
                        ]
                    ]
        ]);
        $usersCompleted = [];
        foreach ($blocked as $block) {
            if (!isset($usersCompleted[$block->wallet->user_id])) {
                $complete = true;
                foreach ($this->conditions as $condition) {
                    if (!$condition->checkComplete($block->wallet->user_id)) {
                        $complete = false;
                        break;
                    }
                }
                if ($complete) {
                    $usersCompleted[$block->wallet->user_id] = true;
                } else {
                    $usersCompleted[$block->wallet->user_id] = false;
                }
            }
            if ($usersCompleted[$block->wallet->user_id]) {
                $block->wallet->diff($block->amount, $block->comment);
                $block->delete();
            }
        }
    }

    public static function relations() {
        return [
            'levels' => [
                'type' => 'many',
                'model' => 'Money\Reward\Level',
                'col' => 'reward_id'
            ],
            'conditions' => [
                'type' => 'relModel',
                'model' => 'Money\Reward\Condition',
                'relModel' => 'Money\Reward\ConditionRewardLnk'
            ]
        ];
    }

}
