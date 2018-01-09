<?php

/**
 * Reward condition
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward;

class Condition extends \Inji\Model {

    public static $cols = [
        'reward_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'reward'],
        'name' => ['type' => 'text'],
        'active' => ['type' => 'bool'],
        'item' => ['type' => 'dataManager', 'relation' => 'items']
    ];
    public static $labels = [
        'reward_id' => 'Вознаграждение',
        'name' => 'Название',
        'active' => 'Активно',
        'item' => 'Условия',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Условия',
            'cols' => ['name', 'active', 'item']
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Условие',
            'map' => [
                ['name', 'active'],
            ]
        ]
    ];

    public function checkComplete($userId = 0) {
        $userId = $userId ? $userId : \Users\User::$cur->id;
        if (!$userId) {
            return false;
        }
        foreach ($this->items as $item) {
            if (!$item->checkComplete($userId)) {
                return false;
            }
        }
        return true;
    }

    public static function relations() {
        return [
            'rewards' => [
                'type' => 'relModel',
                'relModel' => 'Money\Reward\ConditionRewardLnk',
                'model' => 'Money\Reward',
            ],
            'items' => [
                'type' => 'many',
                'model' => 'Money\Reward\Condition\Item',
                'col' => 'reward_condition_id'
            ]
        ];
    }

}
