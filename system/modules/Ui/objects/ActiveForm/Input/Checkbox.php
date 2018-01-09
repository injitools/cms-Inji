<?php

/**
 * Active form input bool
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\ActiveForm\Input;

class Checkbox extends \Inji\Ui\ActiveForm\Input {

    public function draw() {
        $inputName = $this->colName();
        $inputLabel = $this->colLabel();
        $inputOptions = [
            'value' => $this->value(),
            'disabled' => $this->readOnly()
        ];
        $preset = $this->preset();
        if ($preset !== null) {
            $inputOptions['disabled'] = true;
            $this->form->input('hidden', $inputName, '', $inputOptions);
            return true;
        }
        $this->form->input('checkbox', $inputName, $inputLabel, $inputOptions);
        return true;
    }

}
