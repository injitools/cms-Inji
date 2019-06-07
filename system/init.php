<?php

/**
 * Start system core
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
session_start();

spl_autoload_register(function ($class_name) {
    if (file_exists(INJI_SYSTEM_DIR . '/Inji/' . $class_name . '.php')) {
        include_once INJI_SYSTEM_DIR . '/Inji/' . $class_name . '.php';
    }
});
//load core
Inji::$inst = new Inji();
Inji::$config = Config::system();
Inji::$inst->listen('Config-change-system', 'systemConfig', function ($event) {
    Inji::$config = $event['eventObject'];
    return $event['eventObject'];
});
spl_autoload_register('Router::findClass');

putenv('COMPOSER_HOME=' . getcwd());
putenv('COMPOSER_CACHE_DIR=' . getcwd() . DIRECTORY_SEPARATOR . 'cache/composer');
ComposerCmd::check();
if (!function_exists('idn_to_utf8')) {
    ComposerCmd::requirePackage("mabrahamde/idna-converter", "dev-master", '.');

    function idn_to_utf8($domain) {
        if (empty(Inji::$storage['IdnaConvert'])) {
            Inji::$storage['IdnaConvert'] = new \idna_convert(array('idn_version' => 2008));
        }
        return Inji::$storage['IdnaConvert']->decode($domain);
    }
}

BowerCmd::check();

if (file_exists('vendor/autoload.php')) {
    include 'vendor/autoload.php';
}

$domain = $_SERVER['SERVER_NAME'];
if (strpos($domain, 'www.') === 0) {
    $domain = substr($domain, 4);
}
define('INJI_DOMAIN_NAME', $domain);


$apps = Apps\App::getList();
//Make default app params
$finalApp = [
    'name' => App::$cur->getDomain(true),
    'dir' => App::$cur->getDomain(),
    'installed' => false,
    'default' => true,
    'route' => App::$cur->getDomain(true),
];
foreach ($apps as $app) {
    if ($app->default) {
        $finalApp = $app->_params;
    }
    if ($app->route && preg_match("!{$app->route}!i", App::$cur->getDomain(true))) {
        $finalApp = $app->_params;
        break;
    }
}
App::$cur = new App($finalApp);
$params = Tools::uriParse($_SERVER['REQUEST_URI']);

App::$cur->type = 'app';
App::$cur->path = INJI_PROGRAM_DIR . '/' . App::$cur->dir;
App::$cur->params = $params;
App::$cur->config = Config::app(App::$cur);
App::$primary = App::$cur;
if (!empty($params[0]) && file_exists(INJI_SYSTEM_DIR . '/program/' . $params[0] . '/')) {
    App::$primary->params = [];

    App::$cur = new App();
    App::$cur->name = $params[0];
    App::$cur->system = true;
    App::$cur->staticPath = "/" . App::$cur->name . "/static";
    App::$cur->templatesPath = "/" . App::$cur->name . "/static/templates";
    App::$cur->path = INJI_SYSTEM_DIR . '/program/' . App::$cur->name;
    App::$cur->type = 'app' . ucfirst(strtolower(App::$cur->name));
    App::$cur->installed = true;
    App::$cur->params = array_slice($params, 1);
    App::$cur->config = Config::app(App::$cur);

    Inji::$inst->listen('Config-change-app-' . App::$primary->name, 'primaryAppConfig', function ($event) {
        App::$primary->config = $event['eventObject'];
        return $event['eventObject'];
    });
}
App::$cur->log = new \Log();
App::$cur->log->run = defined('LOG_ENABLED');
Inji::$inst->listen('Config-change-app-' . App::$cur->name, 'curAppConfig', function ($event) {
    App::$cur->config = $event['eventObject'];
    return $event['eventObject'];
});
$shareConfig = Config::share();
if (empty($shareConfig['installed']) && App::$cur->name != 'setup' && (empty(App::$cur->params[0]) || App::$cur->params[0] != 'static')) {
    Tools::redirect('/setup');
}

Module::$cur = Module::resolveModule(App::$cur);

if (Module::$cur === null) {
    INJI_SYSTEM_ERROR('Module not found', true);
}

Controller::$cur = Module::$cur->findController();
if (Controller::$cur === null) {
    INJI_SYSTEM_ERROR('Controller not found', true);
}
if (!empty(App::$primary->config['autoloadModules'])) {
    foreach (App::$primary->config['autoloadModules'] as $module) {
        App::$cur->$module;
    }
}
if (App::$primary !== App::$cur) {
    foreach (App::$cur->config['autoloadModules'] as $module) {
        App::$cur->$module;
    }
}
Controller::$cur->run();
