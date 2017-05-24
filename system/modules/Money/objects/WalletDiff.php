<?php

/**
 * Data manager delete action
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class WalletDiff extends \Ui\DataManager\Action {
    public static $name = 'Списать/пополнить';
    public static $groupAction = true;

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo) {
        if (empty($adInfo['amount'])) {
            throw new \Exception('Необходимо указать сумму');
        }
        $wallets = Wallet::getList(['where' => ['id', $ids, 'IN']]);
        foreach ($wallets as $wallet) {
            $wallet->diff($adInfo['amount'], $adInfo['comment']);
        }
    }

}
