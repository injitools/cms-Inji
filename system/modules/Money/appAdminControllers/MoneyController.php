<?php

/**
 * Money admin Controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MoneyController extends adminController {

    public function manualClosePayAction($payId) {
        $pay = \Money\Pay::get((int) $payId);
        $result = new Server\Result();
        if ($pay && $pay->pay_status_id == 1) {
            $pay->pay_status_id = 2;
            $pay->save();
            if ($pay->callback_module && $pay->callback_method) {
                \App::$primary->{$pay->callback_module}->{$pay->callback_method}(['status' => 'success', 'payId' => $pay->id, 'pay' => $pay]);
            }
            $result->successMsg = 'Счет был проведен';
            $result->send();
        }
        $result->success = false;
        $result->content = 'Такой счет не найден';
        $result->send();
    }

    public function cancelTransferAction($transferId) {
        $transfer = Money\Transfer::get($transferId);
        $result = new Server\Result();

        $result->success = $transfer->cancel();

        $result->successMsg = 'Перевод был отменен';
        $result->content = 'Не удалось отменить перевод';
        $result->send();
    }

    public function completeTransferAction($transferId) {
        $transfer = Money\Transfer::get($transferId);

        $result = new Server\Result();

        $result->success = $transfer->complete();

        $result->successMsg = 'Перевод был завершен';
        $result->content = 'Не удалось завершить перевод';
        $result->send();
    }

}
