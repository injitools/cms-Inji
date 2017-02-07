<?php

namespace Db;

class Migration extends \Model {

    public static $cols = [
        'code' => ['type' => 'text'],
        'version' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime']
    ];

}