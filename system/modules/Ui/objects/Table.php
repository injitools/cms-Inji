<?php

/**
 * Table
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui;

class Table extends \Object {

    public $cols = [];
    public $rows = [];
    public $buttons = [];
    public $name = '';
    public $afterHeader = '';
    public $id = '';
    public $class = 'table table-condensed table-hover';
    public $attributes = [];
    public $indexCol = null;

    public function setCols($cols) {
        $this->cols = $cols;
    }

    public function draw() {
        \App::$cur->view->widget('Ui\Table/body', ['table' => $this]);
    }

    public static function drawRow($row) {
        \App::$cur->view->widget('Ui\Table/row', ['row' => $row]);
    }

    public function addRow($row) {
        $this->rows[] = $row;
    }

    public function addButton($button) {
        $this->buttons[] = $button;
    }

}
