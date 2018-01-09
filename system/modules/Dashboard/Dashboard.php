<?php
namespace Inji;
/**
 * Dashboard module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Dashboard extends \Inji\Module {

    public function itemHref($item, $col) {
        $modelName = $item->model;
        $relItem = $modelName::get($item->$col);
        if ($relItem) {
            return "<a href='/admin/" . $relItem->genViewLink() . "'>" . $relItem->name() . "</a>";
        }
        return 'Ресурс удален';
    }

    public function moduleHref($item, $col) {
        if (!$item->$col) {
            return 'Модуль не задан';
        }
        if (!Module::installed($item->$col, \Inji\App::$primary)) {
            return 'Модуль ' . $item->$col . ' не установлен';
        }
        $moduleInfo = Module::getInfo($item->$col);
        return !empty($moduleInfo['name']) ? $moduleInfo['name'] : $item->$col;
    }

    public function modelHref($item, $col) {
        if (!$item->$col) {
            return 'Модель не задана';
        }
        if (!class_exists($item->$col)) {
            return 'Модель ' . $item->$col . ' несуществует';
        }
        $modelName = $item->$col;
        return $modelName::$objectName ? $modelName::$objectName : $modelName;
    }

}
