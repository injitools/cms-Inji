<?php

namespace Inji\Apps;

use Inji\Ui\ActiveForm;

class AppSetupActiveForm extends ActiveForm {
    public $name = 'setup';
    public $label = 'Приложение';

    public $modelName = 'Inji\Apps\App';

    public $connection = 'injiStorage';
    public $dbOptions = ['share' => true];

    public $map = [
        ['name', 'dir'],
        ['installed', 'default'],
        ['route'],
    ];
}