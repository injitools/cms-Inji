<?php

/**
 * Composer command tool
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class BowerCmd {

    public static function check() {
        if (!file_exists(App::$primary->path . '/bower.json')) {
            BowerCmd::initBower();
        }
    }

    public static function initBower($path = '') {
        if (!$path) {
            $path = App::$primary->path . '/';
        }

        $json = [
            "name" => get_current_user() . "/" . App::$primary->name,
            "config" => [
                "cache-dir" => "./cache/bower/"
            ],
            "authors" => [
                [
                    get_current_user() . ' <' . get_current_user() . "@" . INJI_DOMAIN_NAME . '>'
                ]
            ],
            'private' => true,
            "dependencies" => [],
        ];
        Tools::createDir($path);
        file_put_contents($path . '/bower.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if (!file_exists($path . '/.bowerrc')) {
            $json = [
                "directory" => 'static/bower',
                "interactive" => false
            ];
            Tools::createDir($path);
            file_put_contents($path . '/.bowerrc', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        ComposerCmd::requirePackage("injitools/bowerphp", "dev-master", '.');
        self::command('install', false, $path);
    }

    public static function command($command, $needOutput = true, $path = null) {
        ini_set('memory_limit', '2000M');
        include_once 'vendor/injitools/bowerphp/src/bootstrap.php';
        if ($needOutput) {
            $output = new Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));
        } else {
            $output = new Symfony\Component\Console\Output\NullOutput();
        }
        $path = str_replace('\\', '/', $path === null ? App::$primary->path . '/' : $path);
        $input = new Symfony\Component\Console\Input\StringInput($command);
        $app = new Bowerphp\Console\Application();
        $dir = getcwd();
        chdir($path);
        putenv('HOME=' . getcwd());
        $app->doRun($input, $output);
        chdir($dir);
    }

    public static function requirePackage($packageName, $version = '', $path = '') {
        if (!$path) {
            $path = App::$primary->path;
        }
        $bowerJson = json_decode(file_get_contents($path . '/bower.json'), true);
        if (strpos($packageName, 'github') !== false) {
            $needPackageName = basename($packageName);
        } else {
            $needPackageName = $packageName;
        }
        if (isset($bowerJson['dependencies'][$needPackageName]) && file_exists($path . '/static/bower/' . $needPackageName)) {
            return true;
        }

        self::command('install ' . $packageName . ($version ? '#' . $version : '') . ' --save', false, $path);
        return true;
    }
}