<?php

namespace Inji\Db;

use Inji\Ui\DataManager;

class OptionsSetupDataManager extends DataManager {
    public $name = 'setup';
    public $label = 'Базы данных';

    public $modelName = 'Inji\Db\Options';

    public $connection = 'injiStorage';
    public $dbOptions = ['share' => true];
    public $activeForm = 'setup';

    public $actions = [
        'actions' => [
            'Edit', 'Delete'
        ],
    ];

    public $cols = [
        'connect_name',
        'connect_alias',
        'db_name'
    ];
}