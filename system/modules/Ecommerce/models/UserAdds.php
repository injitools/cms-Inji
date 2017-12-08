<?php

/**
 * UserAdds
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;
/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property string $date_create
 *
 * @property \Ecommerce\UserAdds\Value[] $values
 * @property \Users\User $user
 *
 * @method \Ecommerce\UserAdds\Value[] values($options)
 * @method \Users\User user($options)
 */
class UserAdds extends \Model {
    public static $logging = false;
    public static $labels = [
        'value' => 'Информация'
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'value' => ['type' => 'dataManager', 'relation' => 'values'],
    ];

    public static function relations() {
        return [
            'values' => [
                'type' => 'many',
                'model' => 'Ecommerce\UserAdds\Value',
                'col' => 'useradds_id',
                'resultKey' => 'useradds_field_id'
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id',
            ],
        ];
    }

}
