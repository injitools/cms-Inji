<?php

/**
 * Reward user recive
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward;

class Recive extends \Model {

    public static $cols = [
        'reward_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'reward'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'reward'],
        'amount' => ['type' => 'decimal']
    ];

    public static function relations() {
        return[
            'reward' => [
                'model' => 'Money\Reward',
                'col' => 'reward_id'
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
        ];
    }

}
