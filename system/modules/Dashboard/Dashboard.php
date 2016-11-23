<?php

/**
 * Dashboard module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Dashboard extends Module {

  public function itemHref($item, $col, $colParam) {
    $modelName = $item->model;
    $relItem = $modelName::get($item->$col);
    if ($relItem) {
      return "<a href='/admin/" . $relItem->genViewLink() . "'>" . $relItem->name() . "</a>";
    }
    return 'Ресурс удален';
  }

}
