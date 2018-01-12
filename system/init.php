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

include_once INJI_SYSTEM_DIR . '/Inji/Inji.php';
include_once INJI_SYSTEM_DIR . '/Inji/Router.php';
include_once INJI_SYSTEM_DIR . '/Inji/Router/Folder.php';
include_once INJI_SYSTEM_DIR . '/Inji/Router/Path.php';

spl_autoload_register('Inji\Router::findClass');

Inji\Router::addPath(INJI_SYSTEM_DIR . '/Inji/', 'Inji\\');
Inji\Router::addPath(INJI_SYSTEM_DIR . '/modules/', 'Inji\\', 10);
Inji\Router::addPath(INJI_SYSTEM_DIR . '/modules/', 'Inji\\', 20, 1, ['models', 'objects', 'controllers']);

//load core
Inji::$inst = new Inji();
Inji::$config = Inji\Config::system();
Inji::$inst->listen('Config-change-system', 'systemConfig', function ($event) {
    Inji::$config = $event['eventObject'];
    return $event['eventObject'];
});


putenv('COMPOSER_HOME=' . getcwd());
putenv('COMPOSER_CACHE_DIR=' . getcwd() . DIRECTORY_SEPARATOR . 'cache/composer');
Inji\ComposerCmd::check();
if (!function_exists('idn_to_utf8')) {
    Inji\ComposerCmd::requirePackage("mabrahamde/idna-converter", "dev-master", '.');

    function idn_to_utf8($domain) {
        if (empty(Inji::$storage['IdnaConvert'])) {
            Inji::$storage['IdnaConvert'] = new \idna_convert(array('idn_version' => 2008));
        }
        return Inji::$storage['IdnaConvert']->decode($domain);
    }
}

Inji\BowerCmd::check();

if (file_exists('vendor/autoload.php')) {
    include 'vendor/autoload.php';
}

$domain = idn_to_utf8($_SERVER['SERVER_NAME']);
if (strpos($domain, 'www.') === 0) {
    $domain = substr($domain, 4);
}
define('INJI_DOMAIN_NAME', $domain);


//Make default app params
$finalApp = [
    'name' => INJI_DOMAIN_NAME,
    'dir' => INJI_DOMAIN_NAME,
    'installed' => false,
    'default' => true,
    'route' => INJI_DOMAIN_NAME,
];
Inji\App::$primary = Inji\App::$cur = new Inji\App($finalApp);
$apps = Inji\Apps\App::connection('injiStorage')->setDbOption('share', true)->getList();
foreach ($apps as $app) {
    if ($app->default) {
        $finalApp = $app->_params;
    }
    if (preg_match("!{$app->route}!i", INJI_DOMAIN_NAME)) {
        $finalApp = $app->_params;
        break;
    }
}
Inji\App::$cur = new Inji\App($finalApp);
$params = Inji\Tools::uriParse($_SERVER['REQUEST_URI']);

Inji\App::$cur->type = 'app';
Inji\App::$cur->path = INJI_PROGRAM_DIR . '/' . Inji\App::$cur->dir;
Inji\App::$cur->params = $params;
Inji\App::$cur->config = Inji\Config::app(Inji\App::$cur);
if (!Inji\App::$cur->namespace) {
    Inji\App::$cur->namespace = ucfirst(Inji\App::$cur->name);
}
Inji\App::$primary = Inji\App::$cur;

if (!empty($params[0]) && file_exists(INJI_SYSTEM_DIR . '/program/' . $params[0] . '/')) {

    Inji\App::$primary->params = [];

    Inji\App::$cur = new Inji\App();
    Inji\App::$cur->name = $params[0];
    Inji\App::$cur->namespace = 'Inji\\' . ucfirst($params[0]);
    Inji\App::$cur->system = true;
    Inji\App::$cur->staticPath = "/" . Inji\App::$cur->name . "/static";
    Inji\App::$cur->templatesPath = "/" . Inji\App::$cur->name . "/static/templates";
    Inji\App::$cur->path = INJI_SYSTEM_DIR . '/program/' . Inji\App::$cur->name;
    Inji\App::$cur->type = 'app' . ucfirst(strtolower(Inji\App::$cur->name));
    Inji\App::$cur->installed = true;
    Inji\App::$cur->params = array_slice($params, 1);
    Inji\App::$cur->config = Inji\Config::app(Inji\App::$cur);

    Inji::$inst->listen('Config-change-app-' . Inji\App::$primary->name, 'primaryAppConfig', function ($event) {
        Inji\App::$primary->config = $event['eventObject'];
        return $event['eventObject'];
    });

    Inji\Router::addPath(Inji\App::$cur->path . '/objects/', Inji\App::$cur->namespace . '\\', 60);
    Inji\Router::addPath(Inji\App::$cur->path . '/modules/', Inji\App::$cur->namespace . '\\', 70);
    Inji\Router::addPath(Inji\App::$cur->path . '/modules/', Inji\App::$cur->namespace . '\\', 80, 2, ['models', 'objects', 'controllers']);

}
if (!empty(Inji\App::$primary->namespace)) {
    Inji\Router::addPath(Inji\App::$primary->path . '/objects/', Inji\App::$primary->namespace . '\\', 30);
    Inji\Router::addPath(Inji\App::$primary->path . '/modules/', Inji\App::$primary->namespace . '\\', 40);
    Inji\Router::addPath(Inji\App::$primary->path . '/modules/', Inji\App::$primary->namespace . '\\', 50, 1, ['models', 'objects', 'controllers']);
}
Inji\App::$cur->log = new Inji\Log();
Inji\App::$cur->log->run = defined('LOG_ENABLED');
Inji::$inst->listen('Config-change-app-' . Inji\App::$cur->name, 'curAppConfig', function ($event) {
    Inji\App::$cur->config = $event['eventObject'];
    return $event['eventObject'];
});
$shareConfig = Inji\Config::share();
if (empty($shareConfig['installed']) && Inji\App::$cur->name != 'setup' && (empty(Inji\App::$cur->params[0]) || Inji\App::$cur->params[0] != 'static')) {
    Inji\Tools::redirect('/setup');
}
Inji\Module::$cur = Inji\Module::resolveModule(Inji\App::$cur);

if (Inji\Module::$cur === null) {
    INJI_SYSTEM_ERROR('Module not found', true);
}

Inji\Controller::$cur = Inji\Module::$cur->findController();
if (Inji\Controller::$cur === null) {
    INJI_SYSTEM_ERROR('Controller not found', true);
}
if (!empty(Inji\App::$primary->config['autoloadModules'])) {
    foreach (Inji\App::$primary->config['autoloadModules'] as $module) {
        Inji\App::$cur->$module;
    }
}
if (Inji\App::$primary !== Inji\App::$cur) {
    foreach (Inji\App::$cur->config['autoloadModules'] as $module) {
        Inji\App::$cur->$module;
    }
}
Inji\Controller::$cur->run();
