<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\DataManager\Action;

class Href extends \Inji\Ui\DataManager\Action {

    public static $name = 'Ссылка';
    public static $groupAction = false;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        $query = [
            'item_pk' => $item->pk(),
            'time' => time()
        ];
        if (!empty($actionParams['query'])) {
            $query = $query + $actionParams['query'];
        }
        return "<a class='" . (isset($actionParams['class']) ? $actionParams['class'] : '') . "' href='{$actionParams['href']}?" . http_build_query($query) . "'>{$actionParams['text']}</a>";
    }

}
