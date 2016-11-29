<?php

/**
 * Active form input files
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm\Input;

class Map extends \Ui\ActiveForm\Input {

    public function parseRequest($request) {
        $colName = empty($this->colParams['col']) ? $this->colName : $this->colParams['col'];
        if (isset($request[$this->colName])) {
            $this->activeForm->model->{$colName} = json_encode($request[$this->colName]);
        } else {
            $this->activeForm->model->{$colName} = 0;
            $this->activeForm->model->{$colName} = '';
        }
    }

}
