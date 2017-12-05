<?php

/**
 * Active form input select
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm\Input;

class Select extends \Ui\ActiveForm\Input {

    public function draw() {
        $inputName = $this->colName();
        $inputLabel = $this->colLabel();
        $inputParams = $this->colParams;

        $inputOptions = [
            'value' => $this->value(),
            'disabled' => $this->readOnly(),
            'values' => \Ui\ActiveForm::getOptionsList($this->colParams, $this->activeFormParams, !empty($this->modelName) ? $this->modelName : $this->activeForm->modelName, $inputName)
        ];
        $modelName = '';

        switch ($inputParams['source']) {
            case 'model':
                $modelName = $inputParams['model'];
                break;
            case 'relation':
                if ($this->activeForm->modelName) {
                    $itemModelName = $this->activeForm->modelName;

                    if (strpos($inputParams['relation'], ':')) {
                        $relPaths = explode(':', $inputParams['relation']);
                        $model = $this->activeForm->model;
                        foreach ($relPaths as $key => $path) {
                            $relation = $itemModelName::getRelation($path);
                            if (!$relation) {
                                break;
                            }
                            if ($key + 1 == count($relPaths)) {
                                if ($model) {
                                    $inputOptions['values'] = $model->{$path}(['forSelect' => true]);
                                }
                            } else {
                                if ($model) {
                                    $model = $model->{$path};
                                }
                            }
                            $itemModelName = $relation['model'];
                        }
                    } else {
                        $relation = $itemModelName::getRelation($inputParams['relation']);
                    }
                    if ($relation && $relation['model'] && class_exists($relation['model'])) {
                        $modelName = $relation['model'];
                    }
                }
        }
        if (!empty($modelName)) {
            $inputOptions['createBtn'] = [
                'text' => 'Создать',
                'onclick' => 'inji.Ui.forms.popUp(\'' . addslashes($modelName) . '\',{},function(elem){'
                    . 'return function(data,modal){inji.Ui.forms.submitAjax($(elem).closest(\'form\')[0], {notSave: true});}}(this));return false;'
            ];
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
        $this->form->input('select', $inputName, $inputLabel, $inputOptions);
        return true;
    }

}
