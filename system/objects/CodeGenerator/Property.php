<?php

/**
 * Property
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace CodeGenerator;

class Property extends \Object {

    public $security = 'public';
    public $static = false;
    public $name = 'property';
    public $value = 'value';

    public function generate() {
        $code = $this->security . ' ';
        $code .= $this->static ? 'static ' : '';
        $code .= '$' . $this->name . ' = ';
        if (is_array($this->value)) {
            $code .= \CodeGenerator::genArray($this->value);
        } else {
            $code .= '"' . str_replace('"', '\"', $this->value) . '";';
        }
        return $code;
    }

}
