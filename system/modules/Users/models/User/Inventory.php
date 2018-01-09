<?php

/**
 * User inventory
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User;

class Inventory extends \Inji\Model {

    public static $objectName = "Инвентарь";
    public static $cols = [
        'public' => ['type' => 'bool'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user', 'showCol' => 'name'],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['public', 'user_id'],
            ]
        ],
    ];
    public static $labels = [
        'public' => 'Публичный',
        'user_id' => 'Пользователь'
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Inji\Users\User',
                'col' => 'user_id'
            ],
            'items' => [
                'model' => 'Inji\Users\User\Inventory\Item',
                'col' => 'user_inventory_id'
            ]
        ];
    }

    public function name() {
        if ($this->first_name . $this->last_name . $this->middle_name) {
            $name = '';
            if ($this->first_name) {
                $name .= $this->first_name;
            }
            if ($this->middle_name) {
                $name .= ($name ? ' ' : '') . $this->middle_name;
            }
            if ($this->last_name) {
                $name .= ($name ? ' ' : '') . $this->last_name;
            }
            return $name;
        } else {
            return $this->user_id;
        }
    }

}
