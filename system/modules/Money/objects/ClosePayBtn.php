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

namespace Money;

class ClosePayBtn extends \Ui\DataManager\Action {

    public static $name = 'Оплачено';
    public static $groupAction = true;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams) {
        if ($item->pay_status_id != 1) {
            return '';
        }
        ob_start();
        ?>
        <a onclick="inji.Server.request({
                        url: '/admin/money/manualClosePay/<?= $item->id; ?>',
                        success: function () {
                          inji.Ui.dataManagers.reloadAll();
                        }});
                      return false;
           " href ='#' class="btn btn-xs btn-primary">Оплачено</a>
        <?php
        $btn = ob_get_contents();
        ob_end_clean();
        return $btn;
    }

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        $pays = Pay::getList(['where' => [['id', $ids, 'IN'], ['pay_status_id', 1]]]);
        foreach ($pays as $pay) {
            $pay->pay_status_id = 2;
            $pay->save();
            if ($pay->callback_module && $pay->callback_method) {
                \App::$primary->{$pay->callback_module}->{$pay->callback_method}(['status' => 'success', 'payId' => $pay->id, 'pay' => $pay]);
            }
        }
        $count = count($pays);
        return 'Оплачено <b>' . $count . '</b> ' . \Tools::getNumEnding($pays, ['счет', 'счета', 'счетов']);
    }

}
