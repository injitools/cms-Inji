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

class Open extends \Inji\Ui\DataManager\Action {

    public static $name = 'Просмотр';
    public static $groupAction = false;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        if (\Inji\App::$cur->name != 'admin') {
            return '';
        }
        $query = [
            'formName' => !empty($dataManager->activeForm) ? $dataManager->activeForm : 'manager',
            'redirectUrl' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : str_replace('\\', '/', $dataManager->modelName)
        ];
        return "<a href='/admin/{$item->genViewLink()}?" . http_build_query($query) . "'><i class='glyphicon glyphicon-eye-open'></i></a>";
    }

}
