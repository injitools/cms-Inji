<?php
/**
 * Data manager
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui;

class DataManager extends \Object {

    public $modelName = '';
    public $managerOptions = [];
    public $managerName = 'customManager';
    public $name = 'Менеджер данных';
    public $limit = 30;
    public $page = 1;
    public $table = null;
    public $joins = [];
    public $predraw = false;
    public $cols = [];
    public $managerId = '';

    /**
     * Construct new data manager
     * 
     * @param string|array $modelNameOrOptions
     * @param string $managerName
     * @throws Exception
     */
    public function __construct($modelNameOrOptions, $managerName = 'manager') {
        $this->managerName = $managerName;

        if (!is_array($modelNameOrOptions)) {
            if (!class_exists($modelNameOrOptions)) {
                throw new \Exception("model {$modelNameOrOptions} not exists");
            }
            $this->modelName = $modelNameOrOptions;
            $this->managerOptions = !empty($modelNameOrOptions::$dataManagers[$managerName]) ? $modelNameOrOptions::$dataManagers[$managerName] : [];
            if (isset($modelNameOrOptions::$objectName)) {
                $this->name = $modelNameOrOptions::$objectName;
            } else {
                $this->name = $modelNameOrOptions;
            }
        } else {
            $this->managerOptions = $modelNameOrOptions;
        }

        if (!$this->managerOptions || !is_array($this->managerOptions)) {
            throw new \Exception('empty DataManager');
        }

        if (!empty($this->managerOptions['name'])) {
            $this->name = $this->managerOptions['name'];
        }

        $this->managerId = str_replace('\\', '_', 'dataManager_' . $this->modelName . '_' . $this->managerName . '_' . \Tools::randomString());
    }

    /**
     * Get buttons for manager
     * 
     * @param string $params
     * @param object $model
     */
    public function getButtons($params = [], $model = null) {
        $modelName = $this->modelName;

        $formParams = [
            'dataManagerParams' => $params,
            'formName' => !empty($this->managerOptions['editForm']) ? $this->managerOptions['editForm'] : 'manager'
        ];
        if ($model) {
            $formModelName = get_class($model);
            $relations = $formModelName::relations();
            $type = !empty($relations[$params['relation']]['type']) ? $relations[$params['relation']]['type'] : 'to';
            switch ($type) {
                case 'relModel':
                    $formParams['preset'] = [
                        $formModelName::index() => $model->pk()
                    ];
                    break;
                default:
                    $formParams['preset'] = [
                        $relations[$params['relation']]['col'] => $model->pk()
                    ];
            }
        }

        $buttons = [];
        if (!empty($this->managerOptions['sortMode'])) {
            $buttons[] = [
                'class' => 'modeBtn',
                'data-mode' => 'sort',
                'text' => 'Сортировать',
            ];
        }
        if (!empty($this->managerOptions['filters'])) {
            $buttons[] = [
                'text' => 'Фильтры',
                'onclick' => '  var modal = $("#' . $this->managerId . '_filters");
                modal.modal("show");',
            ];
        }
        if (!empty($modelName::$forms['simpleItem'])) {
            $formParams['formName'] = 'simpleItem';
            $buttons[] = [
                'text' => '<i class = "glyphicon glyphicon-send"></i> Быстрое создание',
                'onclick' => 'inji.Ui.dataManagers.get(this).newItem("' . str_replace('\\', '\\\\', $modelName) . '",' . json_encode($formParams) . ');',
            ];
        }
        $formParams['formName'] = !empty($this->managerOptions['editForm']) ? $this->managerOptions['editForm'] : 'manager';
        $actions = $this->getActions(false, true);
        foreach ($actions as $action) {
            $btn = $action['className']::managerButton($this, $formParams, $action);
            if ($btn) {
                $buttons[] = $btn;
            }
        }

        return $buttons;
    }

    public function getActions($groupActions = false, $managerActions = false) {
        $actions = [
            'Open' => ['className' => 'Open'], 'Create' => ['className' => 'Create'], 'Edit' => ['className' => 'Edit'], 'Delete' => ['className' => 'Delete']
        ];
        if (isset($this->managerOptions['actions'])) {
            $actions = array_merge($actions, $this->managerOptions['actions']);
        }
        $return = [];
        foreach ($actions as $key => $action) {
            if ($action === false) {
                continue;
            }
            if (is_array($action)) {
                if (!empty($action['access']['groups']) && !in_array(\Users\User::$cur->group_id, $action['access']['groups'])) {
                    continue;
                }
                if (empty($action['className'])) {
                    $action['className'] = $key;
                }
                $return[$key] = $action;
            } else {
                $key = $action;
                $return[$key] = [
                    'className' => $action
                ];
            }
            $return[$key]['className'] = strpos($return[$key]['className'], '\\') === false && class_exists('Ui\DataManager\Action\\' . $return[$key]['className']) ? 'Ui\DataManager\Action\\' . $return[$key]['className'] : $return[$key]['className'];
            if (!class_exists($return[$key]['className']) || ($groupActions && !$return[$key]['className']::$groupAction) || ($managerActions && !$return[$key]['className']::$managerAction)) {
                unset($return[$key]);
            }
        }
        return $return;
    }

    /**
     * Get cols for manager
     * 
     * @return string
     */
    public function getCols() {
        $actions = $this->getActions();
        ob_start();
        ?>
        <div class="dropdown">
            <a id="dLabel" data-target="#" href="" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-cog"></i>
                <span class="caret"></span>
            </a>

            <ul class="dropdown-menu" aria-labelledby="dLabel">
                <li><a href ='' onclick='inji.Ui.dataManagers.get(this).rowSelection("selectAll");return false;'>Выделить все</a></li>
                <li><a href ='' onclick='inji.Ui.dataManagers.get(this).rowSelection("unSelectAll");return false;'>Снять все</a></li>
                <li><a href ='' onclick='inji.Ui.dataManagers.get(this).rowSelection("inverse");return false;'>Инвертировать</a></li>
                <li role="separator" class="divider"></li>
                    <?php
                    foreach ($actions as $action => $actionParams) {
                        if (class_exists($actionParams['className']) && $actionParams['className']::$groupAction) {
                            echo "<li><a role='button' href ='#' onclick='inji.Ui.dataManagers.get(this).groupAction(\"" . str_replace('\\', '\\\\', $action) . "\");return false;'>{$actionParams['className']::$name}</a></li>";
                        }
                    }
                    ?>
            </ul>
        </div>
        <?php
        $dropdown = ob_get_contents();
        ob_end_clean();

        $cols = [];
        $cols[] = ['label' => $dropdown];
        $cols['id'] = ['label' => '№', 'sortable' => true];

        $modelName = $this->modelName;
        foreach ($this->managerOptions['cols'] as $key => $col) {
            if (is_array($col)) {
                $colName = $key;
                $colOptions = $col;
            } else {
                $colName = $col;
                $colOptions = [];
            }
            $colInfo = [];
            if ($modelName) {
                $colInfo = $modelName::getColInfo($colName);
            }
            if (empty($colOptions['label']) && !empty($colInfo['label'])) {
                $colOptions['label'] = $colInfo['label'];
            } elseif (empty($colOptions['label'])) {
                $colOptions['label'] = $colName;
            }
            $cols[$colName] = $colOptions;
        }
        return $cols;
    }

    /**
     * Get rows for manager
     * 
     * @param array $params
     * @param object $model
     * @return type
     */
    public function getRows($params = [], $model = null) {
        $modelName = $this->modelName;
        if (!class_exists($modelName)) {
            return [];
        }
        if (!$this->checkAccess()) {
            $this->drawError('you not have access to "' . $this->modelName . '" manager with name: "' . $this->managerName . '"');
            return [];
        }
        $modelName = $this->modelName;
        $queryParams = [];
        if (empty($params['all'])) {
            if (!empty($params['limit'])) {
                $this->limit = (int) $params['limit'];
            }
            if (!empty($params['page'])) {
                $this->page = (int) $params['page'];
            }
            $queryParams['limit'] = $this->limit;
            $queryParams['start'] = $this->page * $this->limit - $this->limit;
        }
        if (!empty($params['categoryPath']) && $modelName::$categoryModel) {
            $queryParams['where'][] = ['tree_path', $params['categoryPath'] . '%', 'LIKE'];
        }
        if (!empty($params['appType'])) {
            $queryParams['appType'] = $params['appType'];
        }
        if ($this->joins) {
            $queryParams['joins'] = $this->joins;
        }
        if (!empty($this->managerOptions['userGroupFilter'][\Users\User::$cur->group_id]['getRows'])) {
            foreach ($this->managerOptions['userGroupFilter'][\Users\User::$cur->group_id]['getRows'] as $colName => $colOptions) {
                if (!empty($colOptions['userCol'])) {
                    $queryParams['where'][] = [$colName, \Model::getColValue(\Users\User::$cur, $colOptions['userCol'])];
                } elseif (isset($colOptions['value'])) {
                    if (is_array($colOptions['value'])) {
                        foreach ($colOptions['value'] as $key => $value) {
                            if ($key === 'userCol') {
                                $colOptions['value'][$key] = \Model::getColValue(\Users\User::$cur, $value);
                            }
                        }
                    }
                    $queryParams['where'][] = [$colName, $colOptions['value'], is_array($colOptions['value']) ? 'IN' : '='];
                }
            }
        }
        if (!empty($this->managerOptions['filters'])) {
            foreach ($this->managerOptions['filters'] as $col) {
                $colInfo = $modelName::getColInfo($col);
                switch ($colInfo['colParams']['type']) {
                    case 'select':
                        if (empty($params['filters'][$col]['value'])) {
                            continue;
                        }
                        if (is_array($params['filters'][$col]['value'])) {
                            foreach ($params['filters'][$col]['value'] as $key => $value) {
                                if ($value === '') {
                                    unset($params['filters'][$col]['value'][$key]);
                                }
                            }
                        }
                        if (!$params['filters'][$col]['value']) {
                            continue;
                        }
                        $queryParams['where'][] = [$col, $params['filters'][$col]['value'], is_array($params['filters'][$col]['value']) ? 'IN' : '='];
                        break;
                    case 'bool':
                        if (!isset($params['filters'][$col]['value']) || $params['filters'][$col]['value'] === '') {
                            continue;
                        }
                        $queryParams['where'][] = [$col, $params['filters'][$col]['value']];
                        break;
                    case 'dateTime':
                    case 'date':
                        if (empty($params['filters'][$col]['min']) && empty($params['filters'][$col]['max'])) {
                            continue;
                        }
                        if (!empty($params['filters'][$col]['min'])) {
                            $queryParams['where'][] = [$col, $params['filters'][$col]['min'], '>='];
                        }
                        if (!empty($params['filters'][$col]['max'])) {
                            if ($colInfo['colParams']['type'] == 'dateTime' && !strpos($params['filters'][$col]['max'], ' ')) {

                                $date = $params['filters'][$col]['max'] . ' 23:59:59';
                            } else {
                                $date = $params['filters'][$col]['max'];
                            }
                            $queryParams['where'][] = [$col, $date, '<='];
                        }
                        break;
                    case 'number':
                        if (empty($params['filters'][$col]['min']) && empty($params['filters'][$col]['max'])) {
                            continue;
                        }
                        if (!empty($params['filters'][$col]['min'])) {
                            $queryParams['where'][] = [$col, $params['filters'][$col]['min'], '>='];
                        }
                        if (!empty($params['filters'][$col]['max'])) {
                            $queryParams['where'][] = [$col, $params['filters'][$col]['max'], '<='];
                        }
                        break;
                    case 'email':
                    case 'text':
                    case 'textarea':
                    case 'html':
                        if (empty($params['filters'][$col]['value'])) {
                            continue;
                        }
                        switch ($params['filters'][$col]['compareType']) {
                            case 'contains':
                                $queryParams['where'][] = [$col, '%' . $params['filters'][$col]['value'] . '%', 'LIKE'];
                                break;
                            case 'equals':
                                $queryParams['where'][] = [$col, $params['filters'][$col]['value']];
                                break;
                            case 'starts_with':
                                $queryParams['where'][] = [$col, $params['filters'][$col]['value'] . '%', 'LIKE'];
                                break;
                            case 'ends_with':
                                $queryParams['where'][] = [$col, '%' . $params['filters'][$col]['value'], 'LIKE'];
                                break;
                        }
                        break;
                }
            }
        }
        if (!empty($params['mode']) && $params['mode'] == 'sort') {
            $queryParams['order'] = ['weight', 'asc'];
        } elseif (!empty($params['sortered']) && !empty($this->managerOptions['sortable'])) {
            foreach ($params['sortered'] as $colName => $sortType) {
                if ($colName && in_array($colName, $this->managerOptions['sortable'])) {
                    $sortType = in_array($sortType, ['desc', 'asc']) ? $sortType : 'desc';
                    $queryParams['order'][] = [$colName, $sortType];
                }
            }
        }
        if ($model && !empty($params['relation'])) {
            $relation = $model::getRelation($params['relation']);
            $items = $model->$params['relation']($queryParams);
        } else {
            $relation = false;
            $items = $modelName::getList($queryParams);
        }
        $rows = [];
        foreach ($items as $item) {
            if ($relation && !empty($relation['relModel'])) {
                $item = $relation['relModel']::get([[$item->index(), $item->id], [$model->index(), $model->id]]);
            }
            $row = [];
            if (empty($params['download'])) {
                $row[] = '<input type ="checkbox" name = "pk[]" value =' . $item->pk() . '>';
                $redirectUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin/' . str_replace('\\', '%5C', get_class($this));
                $row[] = "<a href ='/admin/" . $item->genViewLink() . "?redirectUrl={$redirectUrl}'>{$item->pk()}</a>";
            } else {
                $row[] = $item->pk();
            }
            foreach ($this->managerOptions['cols'] as $key => $colName) {
                if (!empty($params['download'])) {
                    $row[] = \Model::getColValue($item, is_array($colName) ? $key : $colName, true, false);
                } else {
                    $row[] = DataManager::drawCol($item, is_array($colName) ? $key : $colName, $params, $this);
                }
            }
            if (empty($params['download'])) {
                $row[] = $this->rowButtons($item, $params);
            }
            $rows[] = $row;
        }
        return $rows;
    }

    public static function drawCol($item, $colName, $params = [], $dataManager = null, $originalCol = '', $originalItem = null) {
        $modelName = get_class($item);
        if (!class_exists($modelName)) {
            return false;
        }

        if (!$originalCol) {
            $originalCol = $colName;
        }
        if (!$originalItem) {
            $originalItem = $item;
        }

        $relations = $modelName::relations();
        if (strpos($colName, ':') !== false && !empty($relations[substr($colName, 0, strpos($colName, ':'))])) {
            $rel = substr($colName, 0, strpos($colName, ':'));
            $col = substr($colName, strpos($colName, ':') + 1);
            if ($item->$rel) {
                return DataManager::drawCol($item->$rel, $col, $params, $dataManager, $originalCol, $originalItem);
            } else {
                return 'Не указано';
            }
        }
        if (!empty($modelName::$cols[$colName]['relation'])) {
            $type = !empty($relations[$modelName::$cols[$colName]['relation']]['type']) ? $relations[$modelName::$cols[$colName]['relation']]['type'] : 'to';
            switch ($type) {
                case 'relModel':
                    $managerParams = ['relation' => $modelName::$cols[$colName]['relation']];
                    $count = $item->{$modelName::$cols[$colName]['relation']}(array_merge($params, ['count' => 1]));
                    $count = $count ? $count : 'Нет';
                    return "<a class = 'btn btn-xs btn-primary' onclick = 'inji.Ui.dataManagers.popUp(\"" . str_replace('\\', '\\\\', $modelName) . ":" . $item->pk() . "\"," . json_encode(array_merge($params, $managerParams)) . ")'>{$count}</a>";
                case 'many':
                    $managerParams = ['relation' => $modelName::$cols[$colName]['relation']];
                    if (!empty($modelName::$cols[$colName]['manager'])) {
                        $managerParams['managerName'] = $modelName::$cols[$colName]['manager'];
                    }
                    $count = $item->{$modelName::$cols[$colName]['relation']}(array_merge($params, ['count' => 1]));
                    $count = $count ? $count : 'Нет';
                    return "<a class = 'btn btn-xs btn-primary' onclick = 'inji.Ui.dataManagers.popUp(\"" . str_replace('\\', '\\\\', $modelName) . ":" . $item->pk() . "\"," . json_encode(array_merge($params, $managerParams)) . ")'>{$count}</a>";
                default:
                    if ($item->{$modelName::$cols[$colName]['relation']}) {
                        if (\App::$cur->name == 'admin') {
                            $href = "<a href ='/admin/" . $item->{$modelName::$cols[$colName]['relation']}->genViewLink() . "'>";
                            if (!empty($modelName::$cols[$colName]['showCol'])) {
                                $href .= $item->{$modelName::$cols[$colName]['relation']}->{$modelName::$cols[$colName]['showCol']};
                            } else {

                                $href .= $item->{$modelName::$cols[$colName]['relation']}->name();
                            }
                            $href .= '</a>';
                            return $href;
                        } else {
                            return $item->{$modelName::$cols[$colName]['relation']}->name();
                        }
                    } else {
                        return $item->$colName;
                    }
            }
        } else {
            if (!empty($modelName::$cols[$colName]['view']['type'])) {
                switch ($modelName::$cols[$colName]['view']['type']) {
                    case 'widget':
                        ob_start();
                        \App::$cur->view->widget($modelName::$cols[$colName]['view']['widget'], ['item' => $item, 'colName' => $colName, 'colParams' => $modelName::$cols[$colName]]);
                        $content = ob_get_contents();
                        ob_end_clean();
                        return $content;
                    case 'moduleMethod':
                        return \App::$cur->{$modelName::$cols[$colName]['view']['module']}->{$modelName::$cols[$colName]['view']['method']}($item, $colName, $modelName::$cols[$colName]);
                    case 'many':
                        $managerParams = ['relation' => $modelName::$cols[$colName]['relation']];
                        if (!empty($modelName::$cols[$colName]['manager'])) {
                            $managerParams['managerName'] = $modelName::$cols[$colName]['manager'];
                        }
                        $count = $item->{$modelName::$cols[$colName]['relation']}(array_merge($params, ['count' => 1]));
                        return "<a class = 'btn btn-xs btn-primary' onclick = 'inji.Ui.dataManagers.popUp(\"" . str_replace('\\', '\\\\', $modelName) . ":" . $item->pk() . "\"," . json_encode(array_merge($params, $managerParams)) . ")'>{$count} " . \Tools::getNumEnding($count, ['Элемент', 'Элемента', 'Элементов']) . "</a>";
                    default:
                        return $item->$colName;
                }
            } elseif (!empty($modelName::$cols[$colName]['type'])) {
                if (\App::$cur->name == 'admin' && $originalCol == 'name' || ($dataManager && !empty($dataManager->managerOptions['colToView']) && $dataManager->managerOptions['colToView'] == $originalCol)) {
                    $formName = $dataManager && !empty($dataManager->managerOptions['editForm']) ? $dataManager->managerOptions['editForm'] : 'manager';
                    $redirectUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin/' . str_replace('\\', '/', get_class($originalItem));
                    return "<a href ='/admin/{$originalItem->genViewLink()}?formName={$formName}&redirectUrl={$redirectUrl}'>{$item->$colName}</a>";
                } elseif (\App::$cur->name == 'admin' && $colName == 'name') {
                    $redirectUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/admin/' . str_replace('\\', '/', get_class($originalItem));
                    return "<a href ='/admin/{$item->genViewLink()}?redirectUrl={$redirectUrl}'>{$item->$colName}</a>";
                } elseif ($modelName::$cols[$colName]['type'] == 'html') {
                    $uid = \Tools::randomString();
                    $script = "<script>inji.onLoad(function(){
            var el{$uid}=$('#{$uid}');
            var height{$uid} = el{$uid}.height();
            el{$uid}.css('maxHeight','none');
            function el{$uid}Toggle(){
              console.log($('#{$uid}').css('height'));
                
              if( $('#{$uid}').css('height')=='44px'){
                $('#{$uid}').css('height','auto');
                  var height = $('#{$uid}').height();
                  $('#{$uid}').css('height','44px');
                  $('#{$uid}').animate({height:height});
                  $('#{$uid}').next().text('Свернуть' )
                }
                else {
                  $('#{$uid}').next().text('Развернуть')
                  $('#{$uid}').animate({height:'44px'});
                }
            }
            window['el{$uid}Toggle']= el{$uid}Toggle;
            if(el{$uid}.height()>height{$uid}){
              el{$uid}.css('height','44px');
                
              el{$uid}.after('<a href=\"#\" onclick=\"el{$uid}Toggle();return false;\">Развернуть</a>');
            }
            })</script>";
                    return "<div id = '{$uid}' style='max-height:44px;overflow:hidden;'>{$item->$colName}</div>" . $script;
                } else {
                    return \Model::resloveTypeValue($item, $colName);
                }
            } else {
                return $item->$colName;
            }
        }
    }

    public function rowButtons($item, $params) {
        $modelName = $this->modelName;
        if (!class_exists($modelName)) {
            return false;
        }
        ob_start();
        $widgetName = !empty($this->managerOptions['rowButtonsWidget']) ? $this->managerOptions['rowButtonsWidget'] : 'Ui\DataManager/rowButtons';
        \App::$cur->view->widget($widgetName, [
            'dataManager' => $this,
            'item' => $item,
            'params' => $params
        ]);
        $buttons = ob_get_contents();
        ob_end_clean();
        return $buttons;
    }

    public function getPages($params = [], $model = null) {
        $modelName = $this->modelName;
        if (!class_exists($modelName)) {
            return [];
        }
        if (!$this->checkAccess()) {
            $this->drawError('you not have access to "' . $this->modelName . '" manager with name: "' . $this->managerName . '"');
            return [];
        }
        if (!empty($params['limit'])) {
            $this->limit = (int) $params['limit'];
        }
        if (!empty($params['page'])) {
            $this->page = (int) $params['page'];
        }
        $queryParams = [
            'count' => true
        ];
        $modelName = $this->modelName;
        if (!empty($params['categoryPath']) && $modelName::$categoryModel) {
            $queryParams['where'][] = ['tree_path', $params['categoryPath'] . '%', 'LIKE'];
        }
        if (!empty($this->managerOptions['userGroupFilter'][\Users\User::$cur->group_id]['getRows'])) {
            foreach ($this->managerOptions['userGroupFilter'][\Users\User::$cur->group_id]['getRows'] as $colName => $colOptions) {
                if (!empty($colOptions['userCol'])) {
                    $queryParams['where'][] = [$colName, \Model::getColValue(\Users\User::$cur, $colOptions['userCol'])];
                } elseif (isset($colOptions['value'])) {
                    if (is_array($colOptions['value'])) {
                        foreach ($colOptions['value'] as $key => $value) {
                            if ($key === 'userCol') {
                                $colOptions['value'][$key] = \Model::getColValue(\Users\User::$cur, $value);
                            }
                        }
                    }
                    $queryParams['where'][] = [$colName, $colOptions['value'], is_array($colOptions['value']) ? 'IN' : '='];
                }
            }
        }
        $modelName = $this->modelName;
        if (!empty($this->managerOptions['filters'])) {
            foreach ($this->managerOptions['filters'] as $col) {
                $colInfo = $modelName::getColInfo($col);
                switch ($colInfo['colParams']['type']) {
                    case 'select':
                        if (empty($params['filters'][$col]['value'])) {
                            continue;
                        }
                        if (is_array($params['filters'][$col]['value'])) {
                            foreach ($params['filters'][$col]['value'] as $key => $value) {
                                if ($value === '') {
                                    unset($params['filters'][$col]['value'][$key]);
                                }
                            }
                        }
                        if (!$params['filters'][$col]['value']) {
                            continue;
                        }
                        $queryParams['where'][] = [$col, $params['filters'][$col]['value'], is_array($params['filters'][$col]['value']) ? 'IN' : '='];
                        break;
                    case 'bool':
                        if (empty($params['filters'][$col]['value'])) {
                            continue;
                        }
                        $queryParams['where'][] = [$col, '1'];
                        break;
                    case 'dateTime':
                    case 'date':
                        if (empty($params['filters'][$col]['min']) && empty($params['filters'][$col]['max'])) {
                            continue;
                        }
                        if (!empty($params['filters'][$col]['min'])) {
                            $queryParams['where'][] = [$col, $params['filters'][$col]['min'], '>='];
                        }
                        if (!empty($params['filters'][$col]['max'])) {
                            if ($colInfo['colParams']['type'] == 'dateTime' && !strpos($params['filters'][$col]['max'], ' ')) {

                                $date = $params['filters'][$col]['max'] . ' 23:59:59';
                            } else {
                                $date = $params['filters'][$col]['max'];
                            }
                            $queryParams['where'][] = [$col, $date, '<='];
                        }
                        break;
                    case 'number':
                        if (empty($params['filters'][$col]['min']) && empty($params['filters'][$col]['max'])) {
                            continue;
                        }
                        if (!empty($params['filters'][$col]['min'])) {
                            $queryParams['where'][] = [$col, $params['filters'][$col]['min'], '>='];
                        }
                        if (!empty($params['filters'][$col]['max'])) {
                            $queryParams['where'][] = [$col, $params['filters'][$col]['max'], '<='];
                        }
                        break;
                    case 'email':
                    case 'text':
                    case 'textarea':
                    case 'html':
                        if (empty($params['filters'][$col]['value'])) {
                            continue;
                        }
                        switch ($params['filters'][$col]['compareType']) {
                            case 'contains':
                                $queryParams['where'][] = [$col, '%' . $params['filters'][$col]['value'] . '%', 'LIKE'];
                                break;
                            case 'equals':
                                $queryParams['where'][] = [$col, $params['filters'][$col]['value']];
                                break;
                            case 'starts_with':
                                $queryParams['where'][] = [$col, $params['filters'][$col]['value'] . '%', 'LIKE'];
                                break;
                            case 'ends_with':
                                $queryParams['where'][] = [$col, '%' . $params['filters'][$col]['value'], 'LIKE'];
                                break;
                        }
                        break;
                }
            }
        }
        if ($model && !empty($params['relation'])) {
            $count = $model->$params['relation']($queryParams);
        } else {
            $count = $modelName::getCount($queryParams);
        }
        $pages = new Pages([
            'limit' => $this->limit,
            'page' => $this->page,
                ], [
            'count' => $count,
            'dataManager' => $this
        ]);
        return $pages;
    }

    public function preDraw($params = [], $model = null) {
        $this->predraw = true;

        $cols = $this->getCols();

        $this->table = new Table();
        $tableCols = [];
        foreach ($cols as $colName => $colOptions) {
            $tableCols[] = [
                'attributes' => ['class' => $this->managerId . '_colname_' . $colName, 'data-colname' => $colName],
                'text' => !empty($colOptions['label']) ? $colOptions['label'] : $colName
            ];
        }
        $tableCols[] = '';
        $this->table->class .= ' datamanagertable';
        $this->table->setCols($tableCols);
    }

    public function draw($params = [], $model = null) {
        if (!$this->predraw) {
            $this->preDraw($params, $model);
        }
        \App::$cur->view->widget('Ui\DataManager/DataManager', [
            'dataManager' => $this,
            'model' => $model,
            'table' => $this->table,
            'params' => $params
        ]);
    }

    public function drawCategorys() {
        if (!class_exists($this->modelName)) {
            return false;
        }
        if (!$this->checkAccess()) {
            $this->drawError('you not have access to "' . $this->modelName . '" manager with name: "' . $this->managerName . '"');
            return [];
        }
        $tree = new Tree();
        $tree->ul($this->managerOptions['categorys']['model'], 0, function($category) {
            $path = $category->tree_path . ($category->pk() ? $category->pk() . "/" : '');
            $cleanClassName = str_replace('\\', '\\\\', get_class($category));
            return "<a href='#' onclick='inji.Ui.dataManagers.get(this).switchCategory(this);return false;' data-index='{$category->index()}' data-path ='{$path}' data-id='{$category->pk()}' data-model='{$this->managerOptions['categorys']['model']}'> {$category->name}</a> 
                
                    <a href = '#' class ='glyphicon glyphicon-edit'   onclick = 'inji.Ui.forms.popUp(\"{$cleanClassName}:{$category->pk()}\")'></a>&nbsp;
                    <a href = '#' class ='glyphicon glyphicon-remove' onclick = 'inji.Ui.dataManagers.get(this).delCategory({$category->pk()});return false;'></a>";
        });
        ?>
        <?php
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
     * Check access cur user to manager with name in param
     * 
     * @return boolean
     */
    public function checkAccess() {
        if (\App::$cur->Access && !\App::$cur->Access->checkAccess($this)) {
            return false;
        }

        if (!empty($this->managerOptions['options']['access']['apps']) && !in_array(\App::$cur->name, $this->managerOptions['options']['access']['apps'])) {
            return false;
        }
        if (!empty($this->managerOptions['options']['access']['groups'])) {
            return in_array(\Users\User::$cur->group_id, $this->managerOptions['options']['access']['groups']);
        }
        if ($this->managerName == 'manager' && !\Users\User::$cur->isAdmin()) {
            return false;
        }
        return true;
    }
}