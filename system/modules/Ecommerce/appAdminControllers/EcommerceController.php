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
        $options = [
            [
                'type' => 'checkbox',
                'name' => 'view_empty_warehouse',
                'label' => 'Продавать отсутствующие товары',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['sell_over_warehouse'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'view_empty_image',
                'label' => 'Показывать товары без изображения',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['view_empty_image'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'sell_empty_warehouse',
                'label' => 'Продавать без остатоков на складе',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['sell_empty_warehouse'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'sell_over_warehouse',
                'label' => 'Продавать сверх остатоков на складе',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['sell_over_warehouse'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'label' => 'Показывать товары с нулевой ценой',
                'name' => 'show_zero_price',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['show_zero_price'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_without_price',
                'label' => 'Показывать товары без цен',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['show_without_price'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'filtersInLast',
                'label' => 'Показывать Фильтры только текущей и дочерних категорий',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['filtersInLast'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'isset_sort',
                'label' => 'Возможность сортировки по наличию',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['isset_sort'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'list_all',
                'label' => 'Возможность вывести все товары',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['list_all'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'cartAddToggle',
                'label' => 'Удаление из корзины при повторном добавлении товара',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['cartAddToggle'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'defaultNeedDelivery',
                'label' => 'По умолчанию для всех товаров доставка осуществляется',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['defaultNeedDelivery'], false)
                ]
            ],
            [
                'type' => 'checkbox',
                'name' => 'catalogPresentPage',
                'label' => 'Заглавная страница магазина',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['catalogPresentPage'], false)
                ]
            ],
            [
                'type' => 'text',
                'name' => 'notify_mail',
                'label' => 'E-mail оповещений о новых заказах',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['notify_mail'], '')
                ]
            ],
            [
                'type' => 'select',
                'name' => 'defaultCategoryView',
                'label' => 'Стандартный вид категории',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['defaultCategoryView'], 'itemList'),
                    'values' => App::$cur->ecommerce->viewsCategoryList()
                ]
            ],
            [
                'type' => 'text',
                'name' => 'orderPrefix',
                'label' => 'Префикс для номеров заказов',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['orderPrefix'], '')
                ]
            ],
            [
                'type' => 'number',
                'name' => 'default_limit',
                'label' => 'Товаров на страницу',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['default_limit'], 18)
                ]
            ],
            [
                'type' => 'text',
                'name' => 'defaultCategoryTemplate',
                'label' => 'Шаблон магазина',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['defaultCategoryTemplate'], 'current')
                ]
            ],
        ];
        if (App::$cur->money) {
            $options[] = [
                'type' => 'select',
                'name' => 'defaultCurrency',
                'label' => 'Валюта по умолчанию',
                'options' => [
                    'value' => Tools::defValue(App::$cur->ecommerce->config['defaultCategoryView'], ''),
                    'values' => ['' => 'Выберите'] + \Money\Currency::getList()
                ]
            ];
        }
        if (!empty($_POST['config'])) {
            $config = App::$cur->ecommerce->config;
            foreach ($options as $option) {
                $config[$option['name']] = Tools::defValue($_POST['config'][$option['name']], false);
            }
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
        $this->view->page(['data' => compact('managers', 'options')]);
    }

    public function reCalcCategoriesAction() {
        set_time_limit(0);
        Model::$logging = false;
        ini_set('memory_limit', '-1');
        $categories = \Ecommerce\Category::getList();
        foreach ($categories as $category) {
            if (!$category->catalogs) {
                $time = microtime(true);
                echo $category->id . " start->";
                flush();
                $category->calcItemsCount(true, false);
                echo round(microtime(true) - $time, 2) . "<br />";
                flush();
            }
        }
        Tools::redirect('/admin/ecommerce/configure', 'Данные о кол-ве обновлены');
    }

    public function reSearchIndexAction($i = 0) {
        set_time_limit(0);
        Model::$logging = false;
        ini_set('memory_limit', '-1');
        $count = 100;
        $items = Ecommerce\Item::getList(['start' => $i * $count, 'limit' => $count]);
        if (!$items) {
            Tools::redirect('/admin/ecommerce/configure', 'Поисковый индекс обновлен');
        } else {
            $i++;
            foreach ($items as $key => $item) {
                $item->afterSave();
            }
            echo 'Происходит процесс индексации: проиндексировано ' . $i * $count;
            echo '<script>setTimeout(function(){window.location="/admin/ecommerce/reSearchIndex/' . $i . '"},100)</script>';
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