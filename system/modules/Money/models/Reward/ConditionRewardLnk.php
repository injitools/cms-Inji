<?php

/**
 * Reward conditions relations
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward;

class ConditionRewardLnk extends \Model {

    public static $cols = [
        'reward_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'reward'],
        'reward_condition_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'condition'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['reward_id', 'reward_condition_id']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['reward_id', 'reward_condition_id']
            ]
        ]
    ];

    public static function relations() {
        return [
            'reward' => [
                'model' => 'Money\Reward',
                'col' => 'reward_id'
            ],
            'condition' => [
                'model' => 'Money\Reward\Condition',
                'col' => 'reward_condition_id'
            ],
        ];
    }

}
