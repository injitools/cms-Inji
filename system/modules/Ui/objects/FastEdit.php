<?php

/**
 * Fast edit
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui;

class FastEdit extends \Inji\InjiObject {

    public static function block($object, $col, $value = null, $parse = false) {

        $str = "<div class = 'fastEdit' ";
        if ($object) {
            $str .= "data-model='" . get_class($object) . "' data-col='{$col}' data-key='" . $object->pk() . "'";
        }
        $str .= ">";
        $value = $value !== null ? $value : ($object ? $object->$col : '');
        if ($parse) {
            ob_start();
            \Inji\App::$cur->view->parseSource($value);
            $str .= ob_get_contents();
            ob_end_clean();
        } else {
            $str .= $value;
        }

        $str .= "</div>";
        return $str;
    }
}