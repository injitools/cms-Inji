<?php

/**
 * Ecommerce admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class EcommerceController extends adminController {

    public function dashboardAction() {
        $this->view->setTitle('Онлайн магазин');
        $this->view->page();
    }

    public function configureAction() {
        if (!empty($_POST['config'])) {
            $config = App::$cur->ecommerce->config;
            $config['view_empty_warehouse'] = empty($_POST['config']['view_empty_warehouse']) ? false : true;
            $config['view_empty_image'] = empty($_POST['config']['view_empty_image']) ? false : true;
            $config['sell_empty_warehouse'] = empty($_POST['config']['sell_empty_warehouse']) ? false : true;
            $config['sell_over_warehouse'] = empty($_POST['config']['sell_over_warehouse']) ? false : true;
            $config['notify_mail'] = $_POST['config']['notify_mail'];
            $config['defaultCategoryView'] = $_POST['config']['defaultCategoryView'];
            $config['defaultCurrency'] = $_POST['config']['defaultCurrency'];
            $config['orderPrefix'] = $_POST['config']['orderPrefix'];
            $config['show_zero_price'] = empty($_POST['config']['show_zero_price']) ? false : true;
            $config['show_without_price'] = empty($_POST['config']['show_without_price']) ? false : true;
            $config['filtersInLast'] = empty($_POST['config']['filtersInLast']) ? false : true;
            $config['defaultNeedDelivery'] = empty($_POST['config']['defaultNeedDelivery']) ? false : true;
            $config['catalogPresentPage'] = empty($_POST['config']['catalogPresentPage']) ? false : true;
            Config::save('module', $config, 'Ecommerce');
            Tools::redirect('/admin/ecommerce/configure', 'Настройки были изменены', 'success');
        }
        $managers = [
            'Ecommerce\Delivery',
            'Ecommerce\PayType',
            'Ecommerce\Warehouse',
            'Ecommerce\Unit',
            'Ecommerce\Card',
            'Ecommerce\Discount',
            'Ecommerce\Cart\Stage',
            'Ecommerce\Item\Type',
            'Ecommerce\Item\Option',
            'Ecommerce\Item\Offer\Option',
            'Ecommerce\Item\Offer\Price\Type',
            'Ecommerce\UserAdds\Field',
            'Ecommerce\Cart\Status',
            'Ecommerce\Delivery\Field',
            'Ecommerce\Delivery\Provider',
        ];
        $this->view->setTitle('Настройки магазина');
        $this->view->page(['data' => compact('managers')]);
    }

    public function reBlockIndexAction() {
        set_time_limit(0);
        $carts = Cart::getList();
        foreach ($carts as $cart) {
            $cart->save();
        }
        Tools::redirect('/admin/ecommerce/configure', 'Данные о блокировках обновлены');
    }

    public function reSearchIndexAction($i = 0) {
        set_time_limit(0);
        Model::$logging = false;
        $count = 100;
        $items = Ecommerce\Item::getList(['start' => $i * $count, 'limit' => $count]);
        if (!$items) {
            Tools::redirect('/admin/ecommerce/configure', 'Поисковый индекс обновлен');
        } else {
            $i++;
            foreach ($items as $key => $item) {
                $item->save();
                unset($items[$key]);
                unset($item);
            }
            echo 'Происходит процесс индексации: проиндексировано ' . $i * $count;
            Tools::redirect('/admin/ecommerce/reSearchIndex/' . $i);
        }
    }

    public function newOrdersSubscribeAction() {
        $this->Notifications->subscribe('Ecommerce-orders');
    }

    public function closeCartAction($cartId = 0) {
        $cart = Ecommerce\Cart::get((int) $cartId);
        $result = new Server\Result();
        if ($cart && $cart->cart_status_id != 5) {
            $cart->cart_status_id = 5;
            $cart->save();
            $result->successMsg = 'Заказ был завершен';
            $result->send();
        }
        $result->success = false;
        $result->content = 'Такая корзина не найдена';
        $result->send();
    }

}