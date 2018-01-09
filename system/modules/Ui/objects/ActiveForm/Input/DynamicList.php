<?php

/**
 * Active form input dynamic list
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\ActiveForm\Input;

class DynamicList extends \Inji\Ui\ActiveForm\Input {

    public function draw() {
        $inputName = $this->colName();
        $inputLabel = $this->colLabel();
        $inputOptions = [
            'activeForm' => $this->activeForm,
            'cols' => $this->getCols(),
            'values' => $this->value(),
            'source' => !empty($this->colParams['source']) ? $this->colParams['source'] : 'relation',
            'modelPk' => $this->activeForm->model->pk()
        ];
        $this->form->input('dynamicList', $inputName, $inputLabel, $inputOptions);
        return true;
    }

    public function parseRequest($request) {
        $modelName = $this->modelName;
        switch ($this->colParams['source']) {
            case 'options':
                break;
            default:
                $rels = [];
                $relation = $modelName::getRelation($this->colParams['relation']);
                if ($this->activeForm->model->pk()) {
                    switch ($relation['type']) {
                        case 'relModel':
                            foreach ($request[$this->colName] as $row) {
                                $rels[$row['relItem']] = true;
                            }
                            $relModels = $relation['relModel']::getList(['where' => [$modelName::index(), $this->activeForm->model->pk()], 'key' => $relation['model']::index()]);
                            foreach ($relModels as $model) {
                                if (empty($rels[$model->{$relation['model']::index()}])) {
                                    $model->delete();
                                } else {
                                    unset($rels[$model->{$relation['model']::index()}]);
                                }
                            }
                            foreach ($rels as $relId => $trash) {
                                $model = new $relation['relModel']([
                                    $modelName::index() => $this->activeForm->model->pk(),
                                    $relation['model']::index() => $relId
                                ]);
                                $model->save();
                            }
                            break;

                        case 'many':
                            $requestData = [];
                            if (!empty($request[$this->colName])) {
                                foreach ($request[$this->colName] as $colName => $items) {
                                    foreach ($request[$this->colName][$colName] as $key => $data) {
                                        $requestData[$key][$colName] = $data;
                                    }
                                }
                            }
                            foreach ($requestData as $key => $row) {
                                if (!empty($row['id'])) {
                                    $rels[$row['id']] = $row;
                                    unset($requestData[$key]);
                                }
                            }
                            $relModels = $this->activeForm->model->{$this->colParams['relation']};
                            foreach ($relModels as $model) {
                                if (empty($rels[$model->pk()])) {
                                    $model->delete();
                                } else {
                                    $model->setParams($rels[$model->pk()]);
                                    $model->save();
                                }
                            }
                            foreach ($requestData as $row) {
                                $row[$relation['col']] = $this->activeForm->model->pk();
                                $model = new $relation['model']($row);
                                $model->save();
                            }
                            $this->activeForm->model->loadRelation($this->colParams['relation']);
                    }
                }
        }
    }

    public function value() {
        $values = [];
        switch ($this->colParams['source']) {
            case 'options':
                break;
            default:
                if ($this->activeForm->model) {
                    $items = $this->activeForm->model->{$this->colParams['relation']};
                    foreach ($items as $key => $item) {
                        $value = ['id' => $item->id];
                        foreach ($this->colParams['options']['cols'] as $colName) {
                            $value[$colName] = $item->$colName;
                        }
                        $values[] = $value;
                    }
                }
        }

        return $values;
    }

    public function getCols() {
        $modelName = $this->modelName;
        $cols = [];
        switch ($this->colParams['source']) {
            case 'options':
                foreach ($this->colParams['options']['inputs'] as $colName => $col) {
                    $inputClassName = '\Ui\ActiveForm\Input\\' . ucfirst($col['type']);
                    $input = new $inputClassName();
                    $input->form = $this->form;
                    $input->activeForm = $this->activeForm;
                    $input->activeFormParams = $this->activeFormParams;
                    $input->modelName = $this->modelName;
                    $input->colName = "[{$this->colName}]";
                    $input->colParams = $col;
                    $input->options = !empty($col['options']) ? $col['options'] : [];
                    $cols[$colName] = ['input' => $input, 'col' => $col];
                }
                break;
            default:
                $relation = $modelName::getRelation($this->colParams['relation']);
                switch ($relation['type']) {
                    case 'relModel':
                        $cols['relItem'] = [
                            'col' => [
                                'label' => $relation['model']::objectName(),
                                'type' => 'select',
                                'options' => [
                                    'values' => $relation['model']::getList(['forSelect' => true])
                                ]
                            ]
                        ];
                        break;
                    case 'many':
                        $inputClassName = '\Ui\ActiveForm\Input\Hidden';
                        $input = new $inputClassName();
                        $input->form = $this->form;
                        $input->activeForm = $this->activeForm;
                        $input->activeFormParams = $this->activeFormParams;
                        $input->modelName = $relation['model'];
                        $input->colName = "[{$this->colName}]";
                        $input->colParams = [];
                        $input->options = [];
                        $cols['id'] = ['input' => $input, 'hidden' => true];
                        foreach ($this->colParams['options']['cols'] as $colName) {
                            $col = $relation['model']::getColInfo($colName);
                            $inputClassName = '\Ui\ActiveForm\Input\\' . ucfirst($col['colParams']['type']);
                            $input = new $inputClassName();
                            $input->form = $this->form;
                            $input->activeForm = $this->activeForm;
                            $input->activeFormParams = $this->activeFormParams;
                            $input->modelName = $relation['model'];
                            $input->colName = "[{$this->colName}]";
                            $input->colParams = $col['colParams'];
                            $input->options = !empty($col['options']) ? $col['options'] : [];
                            $cols[$colName] = ['input' => $input, 'col' => $col];
                        }
                        break;
                }
        }
        return $cols;
    }

}
