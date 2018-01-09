<?php

/**
 * Reward level
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward;

class Level extends \Inji\Model {

    public static $cols = [
        'reward_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'reward'],
        'level' => ['type' => 'number'],
        'type' => ['type' => 'text'],
        'param' => ['type' => 'dataManager', 'relation' => 'params'],
        'nocondition' => ['type' => 'bool']
    ];
    public static $labels = [
        'reward_id' => 'Вознаграждение',
        'level' => 'Уровень',
        'type' => 'Тип',
        'param' => 'Параметры',
        'nocondition' => 'Не требуется выполнение условий',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Уровни',
            'cols' => ['level', 'type', 'nocondition', 'param']
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Уровень',
            'map' => [
                ['level', 'reward_id'],
                ['type', 'nocondition'],
                ['param']
            ]
        ]
    ];

    public static function relations() {
        return [
            'reward' => [
                'model' => 'Money\Reward',
                'col' => 'reward_id'
            ],
            'params' => [
                'type' => 'many',
                'model' => 'Money\Reward\Level\Param',
                'col' => 'reward_level_id',
                'resultKey' => 'param'
            ]
        ];
    }

}
