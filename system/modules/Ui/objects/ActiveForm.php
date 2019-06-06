<?php

/**
 * Active form
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui;

class ActiveForm {

    /**
     * @var \Model
     */
    public $model = null;
    public $modelName = '';
    public $header = "";
    public $action = "";
    public $form = [];
    public $inputs = [];
    public $formName = 'noNameForm';
    public $requestFormName = '';
    public $requestFullFormName = '';
    public $parent = null;

    public function __construct($model, $form = '') {
        if (is_array($model)) {
            $this->form = $model;
            if (is_string($form)) {
                $this->formName = $form;
            }
        } else {
            $this->model = $model;
            $this->modelName = get_class($model);
            if (is_array($form)) {
                if (empty($form)) {
                    throw new \Exception('empty form');
                }
                $this->form = $form;
            } else {
                $this->formName = $form;
                $this->form = \App::$cur->ui->getModelForm($this->modelName, $form);
                if (empty($this->form)) {
                    throw new \Exception('empty form ' . $form);
                }
                $this->inputs = $this->getInputs();
            }
        }
        $this->requestFormName = "ActiveForm_{$this->formName}";
        $modeName = $this->modelName;

        if (!empty($this->form['name'])) {
            $this->header = $this->form['name'];
        } elseif (!empty($modeName::$objectName)) {
            $this->header = $modeName::$objectName;
        } else {
            $this->header = $this->modelName;
        }
    }

    public function getInputs() {
        $inputs = !empty($this->form['inputs']) ? $this->form['inputs'] : [];
        $modelName = $this->modelName;
        foreach ($this->form['map'] as $row) {
            foreach ($row as $col) {
                if (!$col || !empty($inputs[$col])) {
                    continue;
                }
                if (strpos($col, 'form:') === 0) {
                    $colPath = explode(':', $col);
                    if ($this->model->{$colPath[1]}) {
                        $inputs[$col] = new ActiveForm($this->model->{$colPath[1]}, $colPath[2]);
                    } else {
                        $relOptions = $modelName::getRelation($colPath[1]);
                        if (!isset($this->model->_params[$modelName::index()])) {
                            $this->model->_params[$modelName::index()] = 0;
                        }
                        $relOptions['model']::fixPrefix($relOptions['col']);
                        $inputs[$col] = new ActiveForm(new $relOptions['model'](), $colPath[2]);
                    }
                    $inputs[$col]->parent = $this;

                    $inputs[$col]->parent = $this;
                } elseif (!empty($modelName::$cols[$col])) {
                    $inputs[$col] = $modelName::$cols[$col];
                }
            }
        }
        return $inputs;
    }

    public function checkRequest($params = [], $ajax = false) {
        if (!$this->checkAccess()) {
            $this->drawError('you not have access to "' . $this->modelName . '" manager with name: "' . $this->formName . '"');
            return [];
        }
        $successId = 0;
        $modelName = $this->model;
        if (!empty($_POST[$this->requestFormName][$this->modelName]) || !empty($_FILES[$this->requestFormName]['tmp_name'][$this->modelName])) {
            $request = !empty($_POST[$this->requestFormName][$this->modelName]) ? $_POST[$this->requestFormName][$this->modelName] : [];
            if ($this->model) {
                if (!empty($this->form['handler']) && empty($_GET['notSave'])) {

                    $modelName::{$this->form['handler']}($request);
                    $text = 'Новый элемент был успешно добавлен';
                    \Msg::add($text, 'success');
                    \Msg::show();
                } else {
                    $presets = !empty($this->form['preset']) ? $this->form['preset'] : [];
                    if (!empty($this->form['userGroupPreset'][\Users\User::$cur->group_id])) {
                        $presets = array_merge($presets, $this->form['userGroupPreset'][\Users\User::$cur->group_id]);
                    }
                    $afterSave = [];
                    $error = false;
                    foreach ($this->inputs as $col => $param) {
                        if (!empty($presets[$col])) {
                            continue;
                        }
                        if (is_object($param)) {
                            $afterSave[$col] = $param;
                            continue;
                        }
                        if (!empty($this->form['userGroupReadonly'][\Users\User::$cur->group_id]) && in_array($col, $this->form['userGroupReadonly'][\Users\User::$cur->group_id])) {
                            continue;
                        }
                        $type = ucfirst($param['type']);
                        if ($type == 'DynamicType') {
                            switch ($param['typeSource']) {
                                case 'selfMethod':
                                    $type = $this->model->{$param['selfMethod']}();
                                    if (is_array($type)) {
                                        $param = $type;
                                        $type = $type['type'];
                                    }
                                    break;
                            }
                        }
                        $inputClassName = '\Ui\ActiveForm\Input\\' . (ucfirst($type) == 'Bool' ? 'BoolInput' : ucfirst($type));
                        /** @var \Ui\ActiveForm\Input $input */
                        $input = new $inputClassName();
                        $input->activeForm = $this;
                        $input->activeFormParams = $params;
                        $input->modelName = $this->modelName;
                        $input->colName = $col;
                        $input->colParams = $param;
                        try {
                            $input->validate($request);
                            $input->parseRequest($request);
                        } catch (\Exception $exc) {
                            \Msg::add($exc->getMessage(), 'danger');
                            $error = true;
                        }
                    }
                    if (!$error && empty($_GET['notSave'])) {
                        foreach ($presets as $col => $preset) {
                            if (!empty($preset['value'])) {
                                $this->model->$col = $preset['value'];
                            } elseif (!empty($preset['userCol'])) {
                                if (strpos($preset['userCol'], ':')) {
                                    $rel = substr($preset['userCol'], 0, strpos($preset['userCol'], ':'));
                                    $param = substr($preset['userCol'], strpos($preset['userCol'], ':') + 1);
                                    $this->model->$col = \Users\User::$cur->$rel->$param;
                                } else {
                                    $this->model->$col = \Users\User::$cur->{$preset['userCol']};
                                }
                            }
                        }
                        if (!$this->parent) {
                            if (!empty($this->form['successText'])) {
                                $text = $this->form['successText'];
                            } else {
                                $text = $this->model->pk() ? 'Изменения были успешно сохранены' : 'Новый элемент был успешно добавлен';
                            }
                            \Msg::add($text, 'success');
                        }

                        $this->model->save(!empty($params['dataManagerParams']) ? $params['dataManagerParams'] : []);
                        foreach ($afterSave as $col => $form) {
                            if (strpos($col, 'form:') === 0) {
                                $colPath = explode(':', $col);
                                if (!$this->model->{$colPath[1]}) {
                                    $relOptions = $modelName::getRelation($colPath[1]);
                                    if (!isset($this->model->_params[$modelName::index()])) {
                                        $this->model->_params[$modelName::index()] = 0;
                                    }
                                    $relOptions['model']::fixPrefix($relOptions['col']);
                                    $form->model->{$relOptions['col']} = $this->model->_params[$modelName::index()];
                                }
                            }
                            $form->checkRequest();
                        }
                        if ($ajax) {
                            \Msg::show();
                        } elseif (!empty($_GET['redirectUrl'])) {
                            \Tools::redirect($_GET['redirectUrl'] . (!empty($_GET['dataManagerHash']) ? '#' . $_GET['dataManagerHash'] : ''));
                        }
                        $successId = $this->model->pk();
                    }
                }
            }
            if (!is_array($params) && is_callable($params)) {
                $params($request);
            }
        }
        return $successId;
    }

    public function draw($params = [], $ajax = false) {
        if (!$this->checkAccess()) {
            $this->drawError('you not have access to "' . $this->modelName . '" form with name: "' . $this->formName . '"');
            return [];
        }
        $form = new Form(!empty($this->form['formOptions']) ? $this->form['formOptions'] : []);
        \App::$cur->view->widget('Ui\ActiveForm', ['form' => $form, 'activeForm' => $this, 'ajax' => $ajax, 'params' => $params]);
    }

    public function drawCol($colName, $options, $form, $params = []) {
        if (is_object($options)) {
            $options->draw();
        } else {
            $type = ucfirst($options['type']);
            if ($type == 'DynamicType') {
                switch ($options['typeSource']) {
                    case 'selfMethod':
                        $type = $this->model->{$options['selfMethod']}();
                        if (is_array($type)) {
                            $options = $type;
                            $type = $type['type'];
                        }
                        break;
                }
            }
            $inputClassName = '\Ui\ActiveForm\Input\\' . (ucfirst($type) == 'Bool' ? 'BoolInput' : ucfirst($type));
            $input = new $inputClassName();
            $input->form = $form;
            $input->activeForm = $this;
            $input->activeFormParams = $params;
            $input->modelName = $this->modelName;
            $input->colName = $colName;
            $input->colParams = $options;
            $input->options = !empty($options['options']) ? $options['options'] : [];
            $input->draw();
        }
        return true;
    }

    public static function getOptionsList($inputParams, $params = [], $modelName = '', $aditionalInputNamePrefix = 'aditional', $options = [],$model=false) {
        $values = [];
        switch ($inputParams['source']) {
            case 'model':
                $values = $inputParams['model']::getList(['forSelect' => true]);
                break;
            case 'array':
                $values = $inputParams['sourceArray'];
                break;
            case 'method':
                if (!empty($inputParams['params'])) {
                    $values = call_user_func_array([\App::$cur->{$inputParams['module']}, $inputParams['method']], $inputParams['params']+[$model]);
                } else {
                    $values = \App::$cur->$inputParams['module']->{$inputParams['method']}($model);
                }
                break;
            case 'relation':
                if (!$modelName) {
                    return [];
                }
                $relation = $modelName::getRelation($inputParams['relation']);
                if (!empty($params['dataManagerParams']['appType'])) {
                    $options['appType'] = $params['dataManagerParams']['appType'];
                }
                $items = [];
                if (class_exists($relation['model'])) {
                    $filters = $relation['model']::managerFilters();
                    if (!empty($filters['getRows']['where'])) {
                        $options['where'][] = $filters['getRows']['where'];
                    }
                    if (!empty($relation['where'])) {
                        $options['where'][] = $relation['where'];
                    }
                    if (!empty($relation['order'])) {
                        $options['order'] = $relation['order'];
                    }
                    if (!empty($inputParams['itemName'])) {
                        $options['itemName'] = $inputParams['itemName'];
                    }
                    $items = $relation['model']::getList($options);
                }
                if (!empty($params['noEmptyValue'])) {
                    $values = [];
                } else {
                    $values = [0 => 'Не задано'];
                }
                foreach ($items as $key => $item) {
                    if (!empty($inputParams['showCol'])) {
                        if (is_array($inputParams['showCol'])) {
                            switch ($inputParams['showCol']['type']) {
                                case 'staticMethod':
                                    $values[$key] = $inputParams['showCol']['class']::{$inputParams['showCol']['method']}($item);
                                    break;
                            }
                        } else {
                            $values[$key] = $item->{$inputParams['showCol']};
                        }
                    } else {
                        $values[$key] = $item->name();
                    }
                }
                break;
        }
        foreach ($values as $key => $value) {
            if (is_array($value) && !empty($value['input']) && empty($value['input']['noprefix'])) {
                $values[$key]['input']['name'] = $aditionalInputNamePrefix . "[{$value['input']['name']}]";
            }
        }
        return $values;
    }

    /**
     * Draw error message
     *
     * @param string $errorText
     */
    public function drawError($errorText) {
        echo $errorText;
    }

    /**
     * Check access cur user to form with name in param and $model
     *
     * @return boolean
     */
    public function checkAccess() {
        if (empty($this->form)) {
            $this->drawError('"' . $this->modelName . '" form with name: "' . $this->formName . '" not found');
            return false;
        }
        if (\App::$cur->Access && !\App::$cur->Access->checkAccess($this)) {
            return false;
        }
        if (!empty($this->form['options']['access']['apps']) && !in_array(\App::$cur->name, $this->form['options']['access']['apps'])) {
            return false;
        }
        if (!empty($this->form['options']['access']['groups']) && in_array(\Users\User::$cur->group_id, $this->form['options']['access']['groups'])) {
            return true;
        }
        if ($this->model && !empty($this->form['options']['access']['self']) && \Users\User::$cur->id == $this->model->user_id) {
            return true;
        }
        if ($this->formName == 'manager' && !\Users\User::$cur->isAdmin()) {
            return false;
        }
        return true;
    }
}
