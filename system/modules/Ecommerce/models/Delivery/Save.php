<?php

/**
 * Delivery user info save
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Delivery;

/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property string $date_create
 *
 * @property \Ecommerce\Delivery\Value[] $values
 * @property \Users\User $user
 *
 * @method \Ecommerce\Delivery\Value[] values($options)
 * @method \Users\User user($options)
 */
class Save extends \Inji\Model {
    public static $logging = false;
    public static $cols = [
        'name' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user']
    ];

    public static function relations() {
        return [
            'values' => [
                'type' => 'many',
                'model' => 'Inji\Ecommerce\Delivery\Value',
                'col' => 'delivery_save_id',
                'resultKey' => 'delivery_field_id',
            ],
            'user' => [
                'model' => 'Inji\Users\User',
                'col' => 'user_id'
            ]
        ];
    }

}
