<?php

/**
 * Parser
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations;

class Parser {

    public $data;
    public $param;
    public $model;
    public $object;
    /**
     * @var \Migrations\Walker
     */
    public $walker;

    public function parse() {
        
    }

    public function editor() {
        return [
            '' => 'Выберите',
        ];
    }

}
