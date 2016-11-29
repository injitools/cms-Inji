<?php

/**
 * Active form input
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm;

class Input extends \Object {

    public $form = null;
    public $activeForm = null;
    public $activeFormParams = [];
    public $modelName = '';
    public $colName = '';
    public $colParams = [];
    public $options = [];

    public function draw() {
        $inputName = $this->colName();
        $inputLabel = $this->colLabel();

        $inputOptions = $this->options;
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

    public function parseRequest($request) {
        $colName = empty($this->colParams['col']) ? $this->colName : $this->colParams['col'];
        if (isset($request[$this->colName])) {
            $this->activeForm->model->{$colName} = $request[$this->colName];
        } else {
            $this->activeForm->model->{$colName} = 0;
            $this->activeForm->model->{$colName} = '';
        }
    }

    public function value() {
        $value = isset($this->colParams['default']) ? $this->colParams['default'] : '';
        if ($this->activeForm) {
            $colName = empty($this->colParams['col']) ? $this->colName : $this->colParams['col'];
            $value = ($this->activeForm && $this->activeForm->model && isset($this->activeForm->model->{$colName})) ? $this->activeForm->model->{$colName} : $value;
        }
        $value = isset($this->colParams['value']) ? $this->colParams['value'] : $value;
        return $value;
    }

    public function preset() {
        $preset = !empty($this->activeForm->form['preset'][$this->colName]) ? $this->activeForm->form['preset'][$this->colName] : [];
        if (!empty($this->activeForm->form['userGroupPreset'][\Users\User::$cur->group_id][$this->colName])) {
            $preset = array_merge($preset, $this->activeForm->form['userGroupPreset'][\Users\User::$cur->group_id][$this->colName]);
        }
        if ($preset) {
            $value = '';
            if (!empty($preset['value'])) {
                $value = $preset['value'];
            } elseif (!empty($preset['userCol'])) {
                if (strpos($preset['userCol'], ':')) {
                    $rel = substr($preset['userCol'], 0, strpos($preset['userCol'], ':'));
                    $param = substr($preset['userCol'], strpos($preset['userCol'], ':') + 1);
                    $value = \Users\User::$cur->$rel->$param;
                }
            }
            return $value;
        }
        return null;
    }

    public function colName() {
        return "{$this->activeForm->requestFormName}[{$this->activeForm->modelName}]" . (stristr($this->colName, '[') ? $this->colName : "[{$this->colName}]");
    }

    public function colLabel() {
        $modelName = $this->modelName;
        return isset($this->colParams['label']) ? $this->colParams['label'] : (($this->activeForm->model && !empty($modelName::$labels[$this->colName])) ? $modelName::$labels[$this->colName] : $this->colName);
    }

    public function readOnly() {
        return !empty($this->activeForm->form['userGroupReadonly'][\Users\User::$cur->group_id]) && in_array($this->colName, $this->activeForm->form['userGroupReadonly'][\Users\User::$cur->group_id]);
    }

    public function validate(&$request) {
        if (empty($request[$this->colName]) && !empty($this->colParams['required'])) {
            throw new \Exception('Вы не заполнили: ' . $this->colLabel());
        }
        if (!empty($this->colParams['validator'])) {
            $modelName = $this->modelName;
            $validator = $modelName::validator($this->colParams['validator']);
            $validator($this->activeForm, $request);
        }
    }

}
