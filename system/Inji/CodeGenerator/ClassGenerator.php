<?php

/**
 * Class generator
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\CodeGenerator;

class ClassGenerator extends \InjiObject {

    public $propertys = [];
    public $methods = [];
    public $name = 'class';
    public $extends = '';

    public function addProperty($name, $value = null, $static = false, $security = 'public') {
        $this->propertys[$name] = new Property();
        $this->propertys[$name]->name = $name;
        $this->propertys[$name]->value = $value;
        $this->propertys[$name]->static = $static;
        $this->propertys[$name]->security = $security;
    }

    public function addMethod($name, $body = '', $propertys = [], $static = false, $security = 'public') {
        $this->methods[$name] = new Method();
        $this->methods[$name]->name = $name;
        $this->methods[$name]->body = $body;
        $this->methods[$name]->propertys = $propertys;
        $this->methods[$name]->static = $static;
        $this->methods[$name]->security = $security;
    }

    public function generate() {
        $code = 'class ' . $this->name . ' ';
        if ($this->extends) {
            $code .= 'extends ' . $this->extends . ' ';
        }
        $code .= "{\n";
        foreach ($this->propertys as $property) {
            $code .= '    ' . str_replace("\n", "\n    ", $property->generate()) . "\n";
        }
        foreach ($this->methods as $method) {
            $code .= '    ' . str_replace("\n", "\n    ", $method->generate()) . "\n";
        }
        $code .= "}\n";
        return $code;
    }

}
