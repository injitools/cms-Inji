<?php

/**
 * Active form input search
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\ActiveForm\Input;

class Autocomplete extends \Inji\Ui\ActiveForm\Input {

    public function draw() {
        $inputName = $this->colName();
        $inputLabel = $this->colLabel();
        $modelName = $this->activeForm->modelName;

        $inputOptions = [
            'value' => $this->value(),
            'disabled' => $this->readOnly(),
            'values' => []
        ];
        $relation = $modelName::getRelation($this->colParams['relation']);
        if ($relation && class_exists($relation['model']) && $inputOptions['value']) {
            $inputOptions['values'][$this->value()] = $relation['model']::get($inputOptions['value']);
        }

        if (!empty($inputOptions['values'][$this->activeForm->model->{$this->colName}]) &&
                is_array($inputOptions['values'][$this->activeForm->model->{$this->colName}]) &&
                !empty($inputOptions['values'][$this->activeForm->model->{$this->colName}]['input'])) {
            $aditionalCol = $inputOptions['values'][$this->activeForm->model->{$this->colName}]['input']['name'];
            $inputOptions['aditionalValue'] = $this->activeForm->model->$aditionalCol;
        }

        $preset = $this->preset();

        if ($preset !== null) {
            $inputOptions['disabled'] = true;
            $this->form->input('hidden', $inputName, '', $inputOptions);
            return true;
        }
        $inputOptions['inputObject'] = $this;
        $this->form->input('search', $inputName, $inputLabel, $inputOptions);
        return true;
    }

}
