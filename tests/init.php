<?php

setlocale(LC_ALL, 'ru_RU.UTF-8', 'rus_RUS.UTF-8', 'Russian_Russia.65001');
setlocale(LC_NUMERIC, 'C');
//default timezone
date_default_timezone_set('Asia/Krasnoyarsk');
// time start
define('INJI_TIME_START', microtime(true));
// system files dir
define('INJI_SYSTEM_DIR', __DIR__ . '/../system');
// apps files dir
define('INJI_PROGRAM_DIR', __DIR__ . '/../program');
session_start();

include_once INJI_SYSTEM_DIR . '/Inji/Inji.php';
include_once INJI_SYSTEM_DIR . '/Inji/Router.php';

spl_autoload_register('Inji\Router::findClass');

Inji\Router::addPath(INJI_SYSTEM_DIR . '/Inji/', 'Inji\\');
Inji\Router::addPath(INJI_SYSTEM_DIR . '/modules/', 'Inji\\', 10);
Inji\Router::addPath(INJI_SYSTEM_DIR . '/modules/', 'Inji\\', 20, 1, ['models', 'objects', 'controllers']);

define('INJI_DOMAIN_NAME', 'injitest.localhost');

//load core
Inji::$inst = new Inji();
Inji::$config = Inji\Config::system();
Inji::$inst->listen('Config-change-system', 'systemConfig', function ($event) {
    Inji::$config = $event['eventObject'];
    return $event['eventObject'];
});

//Make default app params
$appConfig = [
    'name' => INJI_DOMAIN_NAME,
    'dir' => INJI_DOMAIN_NAME,
    'installed' => true,
    'default' => true,
    'route' => INJI_DOMAIN_NAME,
    'namespace' => 'InjiTest'
];
Inji\App::$cur = new Inji\App($appConfig);

Inji\App::$cur->type = 'app';
Inji\App::$cur->path = INJI_PROGRAM_DIR . '/apps/' . Inji\App::$cur->dir;
Inji\App::$cur->params = [];
Inji\App::$cur->config = Inji\Config::app(Inji\App::$cur);
Inji\App::$primary = Inji\App::$cur;