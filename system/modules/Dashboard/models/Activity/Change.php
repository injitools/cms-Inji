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

namespace Dashboard\Activity;

class Change extends \Model {

  static $logging = false;
  static $cols = [
      'activity_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'activity'],
      'col' => ['type' => 'text'],
      'old' => ['type' => 'textarea'],
      'new' => ['type' => 'textarea'],
      'date_create' => ['type' => 'dateTime']
  ];

  static function relations() {
    return [
        'activity' => [
            'col' => 'activity_id',
            'model' => 'Dashboard\Activity'
        ]
    ];
  }

}
