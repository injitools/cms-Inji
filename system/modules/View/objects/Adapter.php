<?php

/**
 * Adapter interface
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Adapter {

    public function el($name, $option = []) {
        $className = get_called_class() . '\\' . $name;
        if (class_exists($className)) {
            return new $className();
        }
    }

    public function __get($name) {
        return $this->el($name);
    }

    public function __call($name, $arguments) {
        return $this->el($name, $arguments);
    }

}
