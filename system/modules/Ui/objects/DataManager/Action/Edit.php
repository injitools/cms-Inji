<?php

/**
 * Data manager edit action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\DataManager\Action;

class Edit extends \Inji\Ui\DataManager\Action {

    public static $name = 'Редактировать';
    public static $groupAction = false;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        $formParams = [
            'dataManagerParams' => $params,
            'formName' => !empty($dataManager->activeForm) ? $dataManager->activeForm : 'manager'
        ];
        $modelName = get_class($item);
        if (empty($modelName::$forms[$formParams['formName']])) {
            return '';
        }

        return '<a href ="#" onclick=\'inji.Ui.forms.popUp("' . addcslashes(get_class($item), '\\') . ':' . $item->pk() . '",' . json_encode($formParams) . ');
                                      return false;\'><i class="glyphicon glyphicon-edit"></i></a>';
    }

}
