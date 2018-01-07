<?php

/**
 * INJI CMS 5.0.0 dev
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
//set locale
setlocale(LC_ALL, 'ru_RU.UTF-8', 'rus_RUS.UTF-8', 'Russian_Russia.65001');
setlocale(LC_NUMERIC, 'C');
//default timezone
date_default_timezone_set('Asia/Krasnoyarsk');
// time start
define('INJI_TIME_START', microtime(true));
// system files dir
define('INJI_SYSTEM_DIR', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/system');
// apps files dir
define('INJI_PROGRAM_DIR', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/program');
// base dir for outputing system files and dirs
define('INJI_BASE_DIR', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/');

// check base config
if (!file_exists(INJI_SYSTEM_DIR) || !is_dir(INJI_SYSTEM_DIR)) {
    INJI_SYSTEM_ERROR('Error in base config: INJI_SYSTEM_DIR not correct', true);
}
if (!file_exists(INJI_PROGRAM_DIR) || !is_dir(INJI_PROGRAM_DIR)) {
    INJI_SYSTEM_ERROR('Error in base config: INJI_PROGRAM_DIR not correct', true);
}
foreach ($_SERVER as $key => $item) {
    if (strpos($key, 'HTTP_AUTHORIZATION') !== false) {
        $auth = explode(':', base64_decode(substr($item, 6)));
        $_SERVER['PHP_AUTH_USER'] = $auth[0];
        $_SERVER['PHP_AUTH_PW'] = isset($auth[1]) ? $auth[1] : '';
    }
}

require_once(INJI_SYSTEM_DIR . '/init.php');

/**
 * System error messages
 */
function INJI_SYSTEM_ERROR($msg, $fatal = false) {
    if ($fatal) {
        exit("<div style = 'text-align:center;font-size:20px;margin-top:25%;'>{$msg}</div>");
    } else {
        echo "<div style = 'text-align:center;font-size:20px;margin-top:25%;'>{$msg}</div>";
    }
}
