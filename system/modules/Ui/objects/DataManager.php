<?php
/**
 * Item name
 *
 * Info
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
    public $managerName = 'noNameManager';
    public $name = 'Менеджер данных';

    function __construct($modelName, $dataManager = 'manager') {
        $this->modelName = $modelName;
        if (is_string($dataManager)) {
            $this->managerName = $dataManager;
            $dataManager = \App::$cur->ui->getModelManager($modelName, $dataManager);
        }
        $this->managerOptions = $dataManager;

        if (!empty($modelName::$objectName)) {
            $this->name = 'Менеджер данных: ' . $modelName::$objectName;
        } else {
            $this->name = 'Менеджер данных: ' . $modelName;
        }
    }

    /**
     * Get buttons for monager
     * 
     * @param string $params
     * @param object $model
     */
    function getButtons($params = [], $model = null) {
        $formModelName = $modelName = $this->modelName;
        $formParams = [
            'dataManagerParams' => $params
        ];
        if ($model) {
            $formModelName = get_class($model);
            $relations = $formModelName::relations();
            $formParams['preset'] = [$relations[$params['relation']]['col'] => $model->pk()];
        }
        $buttons = [];
        $buttons[] = [
            'text' => 'Добавить элемент',
            'onclick' => 'inji.Ui.forms.popUp("' . str_replace('\\', '\\\\', $modelName) . '",' . json_encode($formParams) . ')',
        ];
        return $buttons;
    }

    /**
     * Get cols for manager
     * 
     * @return string
     */
    function getCols() {
        $modelName = $this->modelName;
        $cols = $this->managerOptions['cols'];
        foreach ($cols as $key => $col) {
            if (!empty($modelName::$labels[$col])) {
                $cols[$key] = $modelName::$labels[$col];
            }
        }
        $cols[] = "";
        return $cols;
    }

    /**
     * Get rows for manager
     * 
     * @param string $params
     * @param object $model
     * @return type
     */
    function getRows($params = [], $model = null) {
        if (!$this->chackAccess()) {
            $this->drawError('you not have access to "' . $this->modelName . '" manager with name: "' . $this->managerName . '"');
            return [];
        }
        $modelName = $this->modelName;
        if ($model && !empty($params['relation'])) {
            $items = $model->$params['relation'];
        } else {
            $items = $modelName::getList($params);
        }
        $rows = [];
        foreach ($items as $key => $item) {
            $row = [];
            foreach ($this->managerOptions['cols'] as $colName) {
                $relations = $modelName::relations();
                if (!empty($modelName::$cols[$colName]['relation']) && !empty($relations[$modelName::$cols[$colName]['relation']]['type']) && $relations[$modelName::$cols[$colName]['relation']]['type'] == 'many') {
                    switch ($relations[$modelName::$cols[$colName]['relation']]['type']) {
                        case'many':
                            $managerParams = ['relation' => $modelName::$cols[$colName]['relation']];
                            $count = $item->{$modelName::$cols[$colName]['relation']}(array_merge($params, ['count' => 1]));
                            $row[] = "<a class = 'btn btn-xs btn-primary' onclick = 'inji.Ui.dataManagers.popUp(\"" . str_replace('\\', '\\\\', $modelName) . ":" . $item->pk() . "\"," . json_encode(array_merge($params, $managerParams)) . ")'>{$count} Элементы</a>";
                            break;
                    }
                } else {

                    $row[] = $item->$colName;
                }
            }
            $row[] = $this->rowButtons($item, $params);
            $rows[] = $row;
        }
        return $rows;
    }

    function rowButtons($item, $params) {
        $modelName = $this->modelName;
        $formParams = [
            'dataManagerParams' => $params
        ];
        $buttons = '';
        $buttons .= "<a onclick='inji.Ui.forms.popUp(\"" . str_replace('\\', '\\\\', $modelName) . ":{$item->pk()}\"," . json_encode($formParams) . ");return false;' class = 'btn btn-success btn-xs'><i class='glyphicon glyphicon-edit'></i></a>";
        $buttons .= " <a onclick='inji.Ui.dataManagers.get(this).delRow({$item->pk()});return false;' class = 'btn btn-danger btn-xs'><i class='glyphicon glyphicon-remove'></i></a>";
        return $buttons;
    }

    function draw($params = [], $model = null) {


        $modelName = $this->modelName;

        $buttons = $this->getButtons($params, $model);
        $cols = $this->getCols();

        $table = new Table();
        $table->name = empty($this->managerOptions['categorys']) ? $this->name : false;
        $table->setCols($cols);
        foreach ($buttons as $button) {
            $table->addButton($button);
        }

        echo '<div '
        . 'id = "dataManager_' . $this->modelName . '_' . $this->managerName . '_' . \Tools::randomString() . '" '
        . 'class = "dataManager" '
        . 'data-params = \'' . json_encode($params) . '\' '
        . 'data-modelname = \'' . ($model ? get_class($model) : $this->modelName) . ($model && $model->pk() ? ':' . $model->pk() : '') . '\' '
        . 'data-managername = \'' . $this->managerName . '\''
        . '>';
        if (!empty($this->managerOptions['categorys'])) {
            ?>
            <h1><?= $this->name; ?></h1>
            <div class ="col-lg-2" style = 'overflow-x: auto;max-height:400px;'>
                <h3>Категории
                    <div class="pull-right">
                        <a class ='btn btn-xs btn-primary' onclick='<?= 'inji.Ui.forms.popUp("' . str_replace('\\', '\\\\', $this->managerOptions['categorys']['model']) . '");'; ?>'>Создать</a>
                    </div>
                </h3>
                <div class="categoryTree">
                    <?php
                    $this->drawCategorys();
                    ?>
                </div>
            </div>
            <div class ="col-lg-10">
                <?php
                $table->draw();
                ?>
            </div>
            <div class="clearfix"></div>
            <?php
        } else {
            $table->draw();
        }
        echo '</div>';
    }

    function drawCategorys() {
        ?>
        <ul class="nav nav-list-categorys" data-col='tree_path'>
            <?php
            $categoryModel = $this->managerOptions['categorys']['model'];
            $categorys = $categoryModel::get_list();
            foreach ($categorys as $category) {
                if ($category->parent_id == 0)
                    $this->showCategory($categorys, $category);
            }
            ?>
        </ul>
        <?php
    }

    function showCategory($categorys, $category) {
        $isset = false;
        $class = get_class($category);
        foreach ($categorys as $categoryChild) {
            if ($categoryChild->{$category->colPrefix() . 'parent_id'} == $category->{$category->index()}) {
                if (!$isset) {
                    $isset = true;
                    echo "<li>
                            <label class='nav-toggle nav-header'>
                                <span class='nav-toggle-icon glyphicon glyphicon-chevron-right'></span> 
                                <a href='#' onclick='switchCategory(this);return false;' data-path ='" . $category->tree_path . $category->pk() . "/'> " . $category->name . "</a>
                                <a href = '#' onclick = 'inji.Ui.forms.popUp(\"" . str_replace('\\', '\\\\', get_class($category)) . ':' . $category->pk() . "\")' class ='glyphicon glyphicon-edit'></a>&nbsp;    
                                <a onclick='inji.Ui.dataManagers.get(this).delCategory({$category->pk()});return false;' class ='glyphicon glyphicon-remove'></a>
                            </label>
                            <ul class='nav nav-list nav-left-ml'>";
                }
                $this->showCategory($categorys, $categoryChild);
            }
        }

        if ($isset) {
            echo '</ul>
                    </li>';
        } else {
            echo "<li>
            <label class='nav-header'>
                <span  class=' nav-toggle-icon fa fa-minus'></span>&nbsp;
                <a href='#' onclick='switchCategory(this);return false;' title = '" . $category->{$category->colPrefix() . 'name'} . "' data-path ='" . $category->{$category::colPrefix() . 'tree_path'} . "" . $category->{$category::index()} . "/'> " . $category->{$category->colPrefix() . 'name'} . "</a>
                <a href = '#' onclick = 'inji.Ui.forms.popUp(\"" . str_replace('\\', '\\\\', get_class($category)) . ':' . $category->pk() . "\")' class ='glyphicon glyphicon-edit'></a>&nbsp;    
                <a onclick='inji.Ui.dataManagers.get(this).delCategory({$category->pk()});return false;' class ='glyphicon glyphicon-remove'></a>
            </label></li>";
        }
    }

    /**
     * Draw error message
     * 
     * @param text $errorText
     */
    function drawError($errorText) {
        echo $errorText;
    }

    /**
     * Check access cur user to manager with name in param
     * 
     * @return boolean
     */
    function chackAccess() {
        $modelName = $this->modelName;
        if (empty($this->managerOptions)) {
            $this->drawError('"' . $this->modelName . '" manager with name: "' . $this->managerName . '" not found');
            return false;
        }

        if (!empty($this->managerOptions['options']['access']['groups']) && !in_array(\Users\User::$cur->group_id, $this->managerOptions['options']['access']['groups'])) {
            return false;
        }
        return true;
    }

}
