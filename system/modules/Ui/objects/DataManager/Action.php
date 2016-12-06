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
    public static $managerAction = false;

    /**
     * Must return button text for row in manager
     * 
     * @param \Ui\DataManager $dataManager
     * @param \Model $item
     * @param array $params
     * @param array $actionParams
     * @return string
     */
    public static function rowButton($dataManager, $item, $params, $actionParams) {
        return '';
    }

    /**
     * Must return button options array for manager actions
     * 
     * @param \Ui\DataManager $dataManager
     * @param array $formParams
     * @param array $actionParams
     * @return  array
     */
    public static function managerButton($dataManager, $formParams, $actionParams) {
        return '';
    }

    /**
     * Call if user choose rows and call action in manager. Return result msg.
     * 
     * @param \Ui\DataManager $dataManager
     * @param string $ids
     * @param array $actionParams
     * @param array $adInfo
     * @return string
     */
    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        return 'empty action';
    }
}