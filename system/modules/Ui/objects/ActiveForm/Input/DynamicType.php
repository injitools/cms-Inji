<?php

/**
 * Active form input dynamic type
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm\Input;

class DynamicType extends \Ui\ActiveForm\Input {

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
        $inputType = 'text';

        switch ($this->colParams['typeSource']) {
            case'selfMethod':
                $type = $this->activeForm->model->{$this->colParams['selfMethod']}();
                if (is_array($type)) {
                    $inputType = $type['type'];
                    if (strpos($type['relation'], ':')) {
                        $relationPath = explode(':', $type['relation']);
                        $relationName = array_pop($relationPath);
                        $item = $this->activeForm->model;
                        foreach ($relationPath as $path) {
                            $item = $item->$path;
                        }

                        $inputOptions['values'] = $item->{$relationName}(['forSelect' => true]);
                    } else {
                        $inputOptions['values'] = $this->activeForm->model->{$type['relation']}(['forSelect' => true]);
                    }
                }
                break;
        }
        $this->form->input($inputType, $inputName, $inputLabel, $inputOptions);
        return true;
    }

}
