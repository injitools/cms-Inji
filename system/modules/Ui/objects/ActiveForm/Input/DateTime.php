<?php

/**
 * Active form input date time
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\ActiveForm\Input;

class DateTime extends \Inji\Ui\ActiveForm\Input {

    public function draw() {
        $inputName = $this->colName();
        $inputLabel = $this->colLabel();

        $inputOptions = $this->options;
        if (!empty($inputOptions['minDate'])) {
            if (strpos($inputOptions['minDate'], 'col:') === 0) {
                $colName = substr($inputOptions['minDate'], 4);
                $inputOptions['minDate'] = $this->activeForm->model->{$colName};
            }
        }
        $inputOptions['value'] = $this->value();
        $inputOptions['disabled'] = $this->readOnly();

        $preset = $this->preset();
        if ($preset !== null) {
            $inputOptions['disabled'] = true;
            $this->form->input('hidden', $inputName, '', $inputOptions);
            return true;
        }
        $classPath = explode('\\', get_called_class());
        $inputType = lcfirst(array_pop($classPath));
        $this->form->input($inputType, $inputName, $inputLabel, $inputOptions);
        return true;
    }
}