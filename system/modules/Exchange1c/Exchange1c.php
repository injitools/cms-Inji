<?php

/**
 * Exchange1c module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Exchange1c extends Module {

    public function response($code, $text = '', $exit = true) {
        echo $code;
        if ($text) {
            echo "\n";
            echo $text;
        }
        if ($exit) {
            exit();
        }
    }

}
