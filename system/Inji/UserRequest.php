<?php

/**
 * User Request parser
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class UserRequest {

    public static function get($key, $type, $default) {
        if (!isset($_GET[$key])) {
            return $default;
        }
        if ($type == 'array') {
            return !is_array($_GET[$key]) ? [] : $_GET[$key];
        }
        return eval('return (' . $type . ') $_GET[$key];');
    }

}
