<?php

/**
 * Data manager delete action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\DataManager\Action;

class ChangeParams extends \Ui\DataManager\Action {

    public static $name = 'Изменить параметр';
    public static $groupAction = true;

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        $count = 0;
        if ($ids) {
            $modelName = $dataManager->modelName;
            $models = $modelName::getList(['where' => [[$modelName::index(), $ids, 'IN']]]);
            foreach ($models as $model) {
                foreach ($actionParams['params'] as $col => $value) {
                    $model->$col = $value;
                }
                $model->save();
                $count++;
            }
        }
        return 'Измненено <b>' . $count . '</b> ' . \Tools::getNumEnding($count, ['запись', 'записи', 'записей']);
    }

}
