<?php

namespace Inji\Db;

class Migration extends \Inji\Model {

    public static $cols = [
        'code' => ['type' => 'text'],
        'version' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime']
    ];

}