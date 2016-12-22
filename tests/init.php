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
define('INJI_PROGRAM_DIR', __DIR__ . '/tmp/program');

session_start();

define('INJI_DOMAIN_NAME', 'test.app');

spl_autoload_register(function($class_name) {
    if (file_exists(INJI_SYSTEM_DIR . '/Inji/' . $class_name . '.php')) {
        include_once INJI_SYSTEM_DIR . '/Inji/' . $class_name . '.php';
    }
});

//load core
Inji::$inst = new Inji();
Inji::$config = Config::system();
Inji::$inst->listen('Config-change-system', 'systemConfig', function($event) {
    Inji::$config = $event['eventObject'];
    return $event['eventObject'];
});
spl_autoload_register('Router::findClass');

//Make default app params
$appConfig = [
    'name' => INJI_DOMAIN_NAME,
    'dir' => INJI_DOMAIN_NAME,
    'installed' => true,
    'default' => true,
    'route' => INJI_DOMAIN_NAME,
];
App::$cur = new App($appConfig);

App::$cur->type = 'app';
App::$cur->path = INJI_PROGRAM_DIR . '/' . App::$cur->dir;
App::$cur->params = [];
App::$cur->config = Config::app(App::$cur);
App::$primary = App::$cur;
Inji::$inst->listen('Config-change-app-' . App::$cur->name, 'curAppConfig', function($event) {
    App::$cur->config = $event['eventObject'];
    return $event['eventObject'];
});
$dbConf = Db\Options::get('local', 'connect_allias');
if (!$dbConf) {
    $dbConf = new Db\Options([
        'connect_name' => 'local',
        'connect_alias' => 'local',
        'driver' => 'Mysql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'pass' => '',
        'db_name' => 'test',
        'encoding' => 'utf8',
        'table_prefix' => 'inji_',
        'port' => ''
    ]);
    $dbConf->save();
}
App::$cur->db->init();
putenv('COMPOSER_HOME=' . __DIR__ . '/tmp');
putenv('COMPOSER_CACHE_DIR=' . __DIR__ . '/tmp/cache/composer');
ComposerCmd::check();
if (!function_exists('idn_to_utf8')) {
    ComposerCmd::requirePackage("mabrahamde/idna-converter", "dev-master", getenv('COMPOSER_HOME'));

    function idn_to_utf8($domain) {
        if (empty(Inji::$storage['IdnaConvert'])) {
            Inji::$storage['IdnaConvert'] = new \idna_convert(array('idn_version' => 2008));
        }
        return Inji::$storage['IdnaConvert']->decode($domain);
    }
}
if (file_exists(__DIR__ . '/tmp/vendor/autoload.php')) {
    include_once __DIR__ . '/tmp/vendor/autoload.php';
}
if (file_exists(App::$primary->path . '/vendor/autoload.php')) {
    include_once App::$primary->path . '/vendor/autoload.php';
}
