<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Dashboard;

/**
 * Description of Activity
 *
 * @author benzu
 */
class Activity extends \Model {

  static $logging = false;
  static $cols = [
      'type' => ['type' => 'select', 'source' => 'array',
          'sourceArray' => [
              'changes' => 'Изменение',
              'new' => 'Создание',
              'delete' => 'Удаление'
          ]
      ],
      'item_id' => ['type' => 'number'],
      'module' => ['type' => 'text'],
      'model' => ['type' => 'text'],
      'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
      'changes_text' => ['type' => 'text'],
      'date_create' => ['type' => 'dateTime']
  ];

  static function relations() {
    return [
        'user' => [
            'col' => 'user_id',
            'model' => 'Users\User'
        ],
        'changes' => [
            'type' => 'many',
            'col' => 'activity_id',
            'model' => 'Dashboard\Activity\Change'
        ]
    ];
  }

}
