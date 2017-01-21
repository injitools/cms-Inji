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

    public static $appInstance = null;

    public static function getInstance() {
        if (!self::$appInstance) {
            self::$appInstance = new Bowerphp\Console\Application();
        }
        return self::$appInstance;
    }

    public static function check() {
        if (!file_exists(INJI_BASE_DIR . Cache::folder() . "static/bowerLibs/bower.json")) {
            BowerCmd::initBower();
        }
    }

    public static function initBower($path = '') {
        while (!Inji::$inst->blockParallel()) {
            sleep(2);
        }
        if (!$path) {
            $path = INJI_BASE_DIR . Cache::folder() . "static/bowerLibs/";
        }
        $json = [
            "name" => get_current_user() . "/" . App::$primary->name,
            "config" => [
                "cache-dir" => INJI_BASE_DIR . Cache::folder() . "bower/"
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
        file_put_contents($path . 'bower.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if (!file_exists($path . '.bowerrc')) {
            $json = [
                "directory" => './',
                "interactive" => false
            ];
            Tools::createDir($path);
            file_put_contents($path . '.bowerrc', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        self::command('install', false, $path);
        gc_collect_cycles();
        Inji::$inst->unBlockParallel();
    }

    public static function command($command, $needOutput = true, $path = null) {
        while (!Inji::$inst->blockParallel()) {
            sleep(2);
        }
        ini_set('memory_limit', '2000M');
        ComposerCmd::requirePackage("injitools/bowerphp", "dev-master", '.');
        include_once 'vendor/injitools/bowerphp/src/bootstrap.php';
        if ($needOutput) {
            $output = new Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));
        } else {
            $output = new Symfony\Component\Console\Output\NullOutput();
        }
        $path = str_replace('\\', '/', $path === null ? INJI_BASE_DIR . Cache::folder() . "static/bowerLibs/" : $path);
        $input = new Symfony\Component\Console\Input\StringInput($command);
        $app = self::getInstance();
        chdir($path);
        putenv('HOME=' . getcwd());
        $app->doRun($input, $output);
        $output = null;
        $input = null;
        chdir(INJI_BASE_DIR);
        gc_collect_cycles();
        Inji::$inst->unBlockParallel();
    }

    public static function requirePackage($packageName, $version = '', $path = '') {
        if (!$path) {
            $path = INJI_BASE_DIR . Cache::folder() . "static/bowerLibs/";
        }
        $bowerJson = json_decode(file_get_contents($path . 'bower.json'), true);
        if (strpos($packageName, 'github') !== false) {
            $needPackageName = basename($packageName);
        } else {
            $needPackageName = $packageName;
        }
        if (file_exists(Cache::folder() . 'static/bowerLibs/' . $needPackageName)) {
            return true;
        }

        self::command('install ' . $packageName . ($version ? '#' . $version : '') . ' --save', false, $path);
        return true;
    }
}