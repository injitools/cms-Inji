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

    public static function check() {
        if (!file_exists('composer/vendor/autoload.php')) {
            self::installComposer();
        }
        if (!file_exists('vendor/autoload.php')) {
            ;
            self::initComposer('./');
        }
        if (!file_exists(App::$primary->path . '/vendor/autoload.php')) {
            self::initComposer();
        }
    }

    public static function installComposer() {
        if (!file_exists('composer/bin/composer')) {
            Tools::createDir('composer');
            if (!file_exists('composer/composer.phar')) {
                file_put_contents('composer/composerInstall.php', file_get_contents('https://getcomposer.org/installer'));
                $argv = ['install', '--install-dir', 'composer/'];
                header("Location: " . filter_input(INPUT_SERVER, 'REQUEST_URI'));
                include_once 'composer/composerInstall.php';
            }
            $composer = new Phar('composer/composer.phar');
            $composer->extractTo('composer/');
        }
    }

    public static function initComposer($path = '') {
        if (!$path) {
            $path = App::$primary->path . '/';
        }
        if (!file_exists($path . 'composer.json')) {
            $json = [
                "name" => get_current_user() . "/" . App::$primary->name,
                "config" => [
                    "cache-dir" => "./composerCache/"
                ],
                "authors" => [
                    [
                        "name" => get_current_user(),
                        "email" => get_current_user() . "@" . INJI_DOMAIN_NAME
                    ]
                ],
                "require" => [
                    "php" => ">=5.5.0"
                ]
            ];
            Tools::createDir($path);
            file_put_contents($path . 'composer.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        self::command('install', false, $path);
    }

    public static function command($command, $needOutput = true, $path = null) {
        ini_set('memory_limit', '2000M');
        include_once 'composer/vendor/autoload.php';
        if ($needOutput) {
            $output = new Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));
        } else {
            $output = null;
        }
        $path = str_replace('\\', '/', $path === null ? App::$primary->path . '/' : $path);
        $input = new Symfony\Component\Console\Input\StringInput($command . ' -d ' . $path);
        $app = new Composer\Console\Application();
        $app->setAutoExit(false);
        $dir = getcwd();
        $app->run($input, $output);
        chdir($dir);
    }

    public static function requirePackage($packageName, $version = '', $path = '') {
        if (!$path) {
            $path = App::$primary->path . '/';
        }
        if (file_exists($path . 'composer.lock')) {
            $lockFile = json_decode(file_get_contents($path . 'composer.lock'), true);
        }
        if (!empty($lockFile['packages'])) {
            foreach ($lockFile['packages'] as $package) {
                if ($package['name'] == $packageName) {
                    return true;
                }
            }
        }

        ComposerCmd::command('require ' . $packageName . ($version ? ':' . $version : ''), false, $path);
        return true;
    }

}
