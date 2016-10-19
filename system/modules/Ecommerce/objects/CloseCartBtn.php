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

namespace Ecommerce;

class CloseCartBtn extends \Ui\DataManager\Action
{
    public static $name = 'Завершить';
    public static $groupAction = true;
    public static $rowAction = true;

    public static function rowButton($dataManager, $item, $params, $actionParams)
    {
        if (\App::$cur->Exchange1c) {
            return '';
        }
        ob_start();
        ?>
        <a onclick="inji.Server.request({
                        url: '/admin/ecommerce/closeCart/<?= $item->id; ?>',
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

    public static function groupAction($dataManager, $ids, $actionParams, $adInfo)
    {
        if (\App::$cur->Exchange1c) {
            throw new \Exception('Недоступно при подключенной 1с');
        }
        $carts = Cart::getList(['where' => [['id', $ids, 'IN'], ['cart_status_id', 5, '!=']]]);
        foreach ($carts as $cart) {
            $cart->cart_status_id = 5;
            $cart->save();
        }
        $count = count($carts);
        return 'Завершено <b>' . $count . '</b> ' . \Tools::getNumEnding($count, ['корзина', 'корзины', 'корзин']);
    }

}
