<?php

/**
 * Delivery user info save
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery;

class Save extends \Model {

  public static $cols = [
      'name' => ['type' => 'text'],
      'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user']
  ];

  public static function relations() {
    return [
        'values' => [
            'type' => 'many',
            'model' => 'Ecommerce\Delivery\Value',
            'col' => 'delivery_save_id',
            'resultKey' => 'delivery_field_id',
        ],
        'user' => [
            'model' => 'Users\User',
            'col' => 'user_id'
        ]
    ];
  }

}
