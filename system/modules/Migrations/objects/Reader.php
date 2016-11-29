<?php

/**
 * Reader
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations;

class Reader extends \Object {

    public $data = NULL;
    public $source = '';

    public function loadData($source = '') {
        $this->source = $source;
        return FALSE;
    }

    public function readPath($path = '/') {
        return [];
    }

    public function __toString() {
        return '';
    }

}
