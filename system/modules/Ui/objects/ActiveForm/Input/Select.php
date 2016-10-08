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
        'values' => \Ui\ActiveForm::getOptionsList($this->colParams, $this->activeFormParams, $this->activeForm->modelName, $inputName)
    ];
    $modelName = '';
    switch ($inputParams['source']) {
      case 'model':
        $modelName = $inputParams['model'];
        break;
      case 'relation':
        if ($this->activeForm->modelName) {
          $itemModelName = $this->activeForm->modelName;
          $relation = $itemModelName::getRelation($inputParams['relation']);
          if ($relation['model'] && class_exists($relation['model'])) {
            $modelName = $relation['model'];
          }
        }
    }
    if (!empty($modelName)) {
      $inputOptions['createBtn'] = [
          'text' => 'Создать элемент',
          'onclick' => 'inji.Ui.forms.popUp(\'' . addslashes($modelName) . '\',{},function(elem){'
          . 'return function(data,modal){inji.Ui.forms.submitAjax($(elem).closest(\'form\')[0], {notSave: true});}}(this))'
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
