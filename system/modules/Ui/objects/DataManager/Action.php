<?php

/**
 * Data manager action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\DataManager;

class Action extends \Object {

    public static $name = '';
    public static $groupAction = false;
    public static $rowAction = false;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        return '';
    }

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        return 'empty action';
    }

}
