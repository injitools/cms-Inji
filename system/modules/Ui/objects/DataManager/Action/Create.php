<?php

/**
 * Data manager open action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\DataManager\Action;

class Create extends \Inji\Ui\DataManager\Action {

    public static $name = 'Создать';
    public static $managerAction = true;

    public static function managerButton($dataManager, $formParams, $actionParams) {
        $modelName = $dataManager->modelName;
        $name = 'Элемент';
        if (!empty($modelName::$forms[$formParams['formName']])) {
            $aform = new \Inji\Ui\ActiveForm(new $modelName, $formParams['formName']);
            if ($aform->checkAccess()) {
                if ($modelName::$objectName) {
                    $name = $modelName::$objectName;
                }
                return [
                    'text' => !empty($actionParams['text']) ? $actionParams['text'] : ('Создать ' . $name),
                    'onclick' => 'inji.Ui.dataManagers.get(this).newItem("' . str_replace('\\', '\\\\', $modelName) . '",' . json_encode($formParams) . ');',
                ];
            }
        }
        return [];
    }
}