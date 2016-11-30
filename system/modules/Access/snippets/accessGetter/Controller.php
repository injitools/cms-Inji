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
    'classes' => ['Controller'],
    'get' => function($element) {
$access = null;
$path = [
    'accessTree',
    $element->module->app->type,
    $element->name,
    $element->method
];
if (isset($element->module->config['access'])) {
    $accesses = $element->module->config['access'];
    $access = $this->resolvePath($accesses, $path, '_access');
}
if (is_null($access) && isset($this->config['access'])) {
    $accesses = $this->config['access'];
    $access = $this->resolvePath($accesses, $path, '_access');
}
return $access;
}
];
