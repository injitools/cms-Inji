<?php

/**
 * Data manager delete action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\DataManager\Action;

class Delete extends \Inji\Ui\DataManager\Action {

    public static $name = 'Удалить';
    public static $groupAction = true;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        return '<a href ="#" onclick=\'inji.Ui.dataManagers.get(this).delRow(' . $item->pk() . ');
                                      return false;\'><i class="glyphicon glyphicon-remove"></i></a>';
    }

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        $count = 0;
        if ($ids) {
            $modelName = $dataManager->modelName;
            $models = $modelName::getList(['where' => [['id', $ids, 'IN']]]);
            foreach ($models as $model) {
                $model->delete();
            }
            $count = count($models);
        }
        return 'Удалено <b>' . $count . '</b> ' . \Inji\Tools::getNumEnding($count, ['запись', 'записи', 'записей']);
    }
}