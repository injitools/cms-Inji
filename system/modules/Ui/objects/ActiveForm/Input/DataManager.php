<?php

/**
 * Active form input Data Manager
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\ActiveForm\Input;

class DataManager extends \Inji\Ui\ActiveForm\Input {

    public function draw() {
        $inputOptions = [];
        $modelName = $this->modelName;
        $inputOptions['relation'] = $modelName::getRelation($this->colParams['relation']);
        $inputOptions['input'] = $this;
        $this->form->input('dataManager', '', '', $inputOptions);
    }

}
