<?php

namespace Inji\Db;

use Inji\Ui\ActiveForm;

class OptionsSetupActiveForm extends ActiveForm {
    public $name = 'setup';
    public $label = 'База данных';

    public $modelName = 'Inji\Db\Options';

    public $connection = 'injiStorage';
    public $dbOptions = ['share' => true];

    public $map = [
        ['connect_name', 'connect_alias', 'driver'],
        ['host', 'user'],
        ['pass', 'db_name'],
        ['encoding', 'table_prefix', 'port']
    ];
}