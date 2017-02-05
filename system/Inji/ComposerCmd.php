<?php

/**
 * Composer command tool
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class ComposerCmd {

    public static $appInstance = null;

    public static function getInstance() {
        if (!self::$appInstance) {
            self::$appInstance = new Composer\Console\Application();
        }
        return self::$appInstance;
    }

    public static function check() {
        if (!file_exists(getenv('COMPOSER_HOME') . '/composer/vendor/autoload.php')) {
            self::installComposer(getenv('COMPOSER_HOME'));
        }
        if (!file_exists(getenv('COMPOSER_HOME') . '/vendor/autoload.php')) {
            self::initComposer(getenv('COMPOSER_HOME'));
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function installComposer($path) {
        while (!Inji::$inst->blockParallel()) {
            sleep(2);
        }
        ini_set('memory_limit', '1000M');
        if (file_exists($path . '/composer/bin/composer')) {
            return true;
        }
        Tools::createDir($path . '/composer');
        if (!file_exists($path . '/composer/composer.phar')) {
            file_put_contents($path . '/composer/composerInstall.php', str_replace('process(is_array($argv) ? $argv : array());', '', file_get_contents('https://getcomposer.org/installer')));
            include_once $path . '/composer/composerInstall.php';

            $quiet = false;
            $channel = 'stable';
            $disableTls = false;
            $installDir = $path . '/composer/';
            $version = false;
            $filename = 'composer.phar';
            $cafile = false;
            setUseAnsi([]);
            ob_start();
            $installer = new Installer($quiet, $disableTls, $cafile);
            $installer->run($version, $installDir, $filename, $channel);
            ob_end_clean();
        }
        $composer = new Phar($path . '/composer/composer.phar');
        $composer->extractTo($path . '/composer/');
        $composer = null;
        gc_collect_cycles();
        Inji::$inst->unBlockParallel();
        return true;
    }

    public static function initComposer($path = '') {
        if (!$path) {
            $path = getenv('COMPOSER_HOME');
        }
        if (!file_exists($path . '/composer.json')) {
            $json = [
                "name" => get_current_user() . "/" . $_SERVER['SERVER_NAME'],
                "config" => [
                    "cache-dir" => Cache::folder() . "composer/"
                ],
                "authors" => [
                    [
                        "name" => get_current_user(),
                        "email" => get_current_user() . "@" . $_SERVER['SERVER_NAME']
                    ]
                ],
                "require" => [
                    "php" => ">=5.5.0"
                ]
            ];
            Tools::createDir($path);
            file_put_contents($path . '/composer.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        self::command('install', false, $path);
    }

    /**
     * @param string $command
     * @param string $path
     */
    public static function command($command, $needOutput = true, $path = null) {
        while (!Inji::$inst->blockParallel()) {
            sleep(2);
        }
        ini_set('memory_limit', '1000M');
        include_once getenv('COMPOSER_HOME') . '/composer/vendor/autoload.php';
        if ($needOutput) {
            $output = new Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));
        } else {
            $output = null;
        }
        $path = str_replace('\\', '/', $path === null ? App::$primary->path . '/' : $path);
        $input = new Symfony\Component\Console\Input\StringInput($command . ' -d ' . $path);
        $app = self::getInstance();
        $app->setAutoExit(false);
        $dir = getcwd();
        $app->run($input, $output);
        $output = null;
        $input = null;
        chdir($dir);
        gc_collect_cycles();
        Inji::$inst->unBlockParallel();
    }

    public static function requirePackage($packageName, $version = '', $path = '') {
        if (!$path) {
            $path = getenv('COMPOSER_HOME');
        }
        if (file_exists($path . '/composer.lock')) {
            $lockFile = json_decode(file_get_contents($path . '/composer.lock'), true);
        }
        if (!empty($lockFile['packages'])) {
            foreach ($lockFile['packages'] as $package) {
                if ($package['name'] == $packageName) {
                    return true;
                }
            }
        }

        self::command('require ' . $packageName . ($version ? ':' . $version : ''), false, $path);
        return true;
    }
}