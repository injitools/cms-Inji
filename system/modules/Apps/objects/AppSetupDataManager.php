<?php

namespace Inji\Apps;

use Inji\Ui\DataManager;

class AppSetupDataManager extends DataManager {
    public $name = 'setup';
    public $label = 'Приложения';

    public $modelName = 'Inji\Apps\App';

    public $connection = 'injiStorage';
    public $dbOptions = ['share' => true];
    public $activeForm = 'setup';

    public $actions = [
        'actions' => [
            'manage' => [
                'className' => 'Href',
                'href' => '/setup/apps/configure',
                'text' => '<i class = "glyphicon glyphicon-cog"></i>'
            ],
            'Edit', 'Delete'
        ],
    ];

    public $cols = [
        'name',
        'dir',
        'installed',
        'default',
        'route',

    ];
}