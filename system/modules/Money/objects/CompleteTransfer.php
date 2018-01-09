<?php
/**
 * Transfer cancel action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class CompleteTransfer extends \Ui\DataManager\Action {

    public static $name = 'Завершить';
    public static $groupAction = true;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        if ($item->canceled || $item->complete) {
            return '';
        }
        ob_start();
        ?>
        <a onclick="inji.Server.request({
                        url: '/admin/money/completeTransfer/<?= $item->id; ?>',
                        success: function () {
                          inji.Ui.dataManagers.reloadAll();
                        }});
                      return false;
           " href ='#' class="btn btn-xs btn-primary">Завершить</a>
        <?php
        $btn = ob_get_contents();
        ob_end_clean();
        return $btn;
    }

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        $transfers = \Money\Transfer::getList(['where' => [['id', $ids, 'IN'], ['canceled', 0], ['complete', 0]]]);
        foreach ($transfers as $transfer) {
            $transfer->confirm();
        }
        $count = count($transfers);
        return 'Завершено <b>' . $count . '</b> ' . \Inji\Tools::getNumEnding($count, ['перевод', 'перевода', 'переводов']);
    }

}
