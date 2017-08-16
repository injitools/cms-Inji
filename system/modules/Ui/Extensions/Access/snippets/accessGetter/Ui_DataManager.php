<?php

/**
 * Controller access checker
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
return [
    'classes' => ['Ui\DataManager'],
    'get' => function ($element) {
        $access = null;
        $path = [
            'models',
            $element->modelName,
            'dataManager',
            $element->managerName
        ];
        $moduleName = explode('\\', $element->modelName)[0];
        if (isset(\App::$cur->{$moduleName}->config['access'])) {
            $accesses = \App::$cur->{$moduleName}->config['access'];
            $access = $this->resolvePath($accesses, $path, '_access');
        }
        if (is_null($access) && isset($this->config['access'])) {
            $accesses = $this->config['access'];
            $access = $this->resolvePath($accesses, $path, '_access');
        }
        return $access;
    }
];
