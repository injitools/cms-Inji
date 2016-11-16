<?php

/**
 * Fast edit
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui;

class FastEdit extends \Object {

  public static function block($object, $col, $value = null, $parse = false) {

    $str = "<div class = 'fastEdit' ";
    if ($object) {
      $str .= "data-model='" . get_class($object) . "' data-col='{$col}' data-key='" . $object->pk() . "'";
    }
    $str .= ">";
    $value = $value !== null ? $value : ($object ? $object->$col : '');
    if ($parse) {
      \App::$cur->view->parseSource($value);
    } else {
      $str .= $value;
    }

    $str .= "</div>";
    return $str;
  }

}
