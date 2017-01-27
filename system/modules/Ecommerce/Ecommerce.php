<?php

/**
 * Ecommerce module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Ecommerce extends Module {

    public function init() {
        App::$primary->view->customAsset('js', '/moduleAsset/Ecommerce/js/cart.js');
        if (class_exists('Users\User') && Users\User::$cur->id) {
            $favs = !empty($_COOKIE['ecommerce_favitems']) ? json_decode($_COOKIE['ecommerce_favitems'], true) : [];
            if ($favs) {
                foreach ($favs as $itemId) {
                    $fav = \Ecommerce\Favorite::get([['user_id', Users\User::$cur->id], ['item_id', (int) $itemId]]);
                    if (!$fav) {
                        $item = \Ecommerce\Item::get((int) $itemId);
                        if ($item) {
                            $fav = new \Ecommerce\Favorite([
                                'user_id' => Users\User::$cur->id,
                                'item_id' => $itemId
                            ]);
                            $fav->save();
                        }
                    }
                }
                if (!headers_sent()) {
                    setcookie("ecommerce_favitems", json_encode([]), time() + 360000, "/");
                }
            }
        }
    }

    public function getPayTypeHandlers($forSelect = false) {
        if (!$forSelect) {
            return $this->getSnippets('payTypeHandler');
        }
        $handlers = ['' => 'Не выбрано'];
        foreach ($this->getSnippets('payTypeHandler') as $key => $handler) {
            if (empty($handler)) {
                continue;
            }
            $handlers[$key] = $handler['name'];
        }
        return $handlers;
    }

    public function cartPayRecive($data) {
        $cart = Ecommerce\Cart::get($data['pay']->data);
        if ($cart) {
            $payed = true;
            foreach ($cart->pays as $pay) {
                if ($pay->pay_status_id != 2) {
                    $payed = false;
                    break;
                }
            }
            $cart->payed = $payed;
            $cart->save();
        }
    }

    public function parseFields($data, $cart) {
        $fields = \Ecommerce\UserAdds\Field::getList();
        $name = '';
        foreach ($fields as $field) {
            if ($field->save && !empty($data[$field->id])) {
                $name .= htmlspecialchars($data[$field->id]) . ' ';
            }
        }
        $name = trim($name);

        $userAdds = Ecommerce\UserAdds::get([['user_id', $cart->user->id], ['name', $name]]);
        if (!$userAdds) {
            $userAdds = new Ecommerce\UserAdds();
            $userAdds->user_id = $cart->user->id;
            $userAdds->name = $name;
            $userAdds->save();
            foreach ($fields as $field) {
                if (!$field->save) {
                    continue;
                }
                $userAddsValue = new Ecommerce\UserAdds\Value();
                $userAddsValue->value = htmlspecialchars($data[$field->id]);
                $userAddsValue->useradds_field_id = $field->id;
                $userAddsValue->useradds_id = $userAdds->id;
                $userAddsValue->save();
            }
        }
        $user = \Users\User::get($cart->user_id);
        foreach ($fields as $field) {
            $info = new \Ecommerce\Cart\Info();
            $info->name = $field->name;
            $info->value = htmlspecialchars($data[$field->id]);
            $info->useradds_field_id = $field->id;
            $info->cart_id = $cart->id;
            $info->save();
            $relations = [];
            if ($field->userfield) {
                if (strpos($field->userfield, ':')) {
                    $path = explode(':', $field->userfield);
                    if (!$user->{$path[0]}->{$path[1]}) {
                        $user->{$path[0]}->{$path[1]} = $info->value;
                        $relations[$path[0]] = $path[0];
                    }
                } else {
                    if (!$user->{$field->userfield}) {
                        $user->{$field->userfield} = $info->value;
                    }
                }
            }
            foreach ($relations as $rel) {
                $user->$rel->save();
            }
            $user->save();
        }
        return $userAdds;
    }

    public function parseDeliveryFields($data, $cart, $fields) {
        $name = '';
        foreach ($fields as $field) {
            if ($field->save && !empty($data[$field->id])) {
                $name .= htmlspecialchars($data[$field->id]) . ' ';
            }
        }
        $name = trim($name);

        $save = Ecommerce\Delivery\Save::get([['user_id', $cart->user->id], ['name', $name]]);
        if (!$save) {
            $save = new Ecommerce\Delivery\Save();
            $save->user_id = $cart->user->id;
            $save->name = $name;
            $save->save();
            foreach ($fields as $field) {
                if (!$field->save) {
                    continue;
                }
                $saveValue = new Ecommerce\Delivery\Value();
                $saveValue->value = htmlspecialchars($data[$field->id]);
                $saveValue->delivery_field_id = $field->id;
                $saveValue->delivery_save_id = $save->id;
                $saveValue->save();
            }
        }
        $user = \Users\User::get($cart->user_id);
        foreach ($fields as $field) {
            $info = new \Ecommerce\Cart\DeliveryInfo();
            $info->name = $field->name;
            $info->value = htmlspecialchars($data[$field->id]);
            $info->delivery_field_id = $field->id;
            $info->cart_id = $cart->id;
            $info->save();
            $relations = [];
            if ($field->userfield) {
                if (strpos($field->userfield, ':')) {
                    $path = explode(':', $field->userfield);
                    if (!$user->{$path[0]}->{$path[1]}) {
                        $user->{$path[0]}->{$path[1]} = $info->value;
                        $relations[$path[0]] = $path[0];
                    }
                } else {
                    if (!$user->{$field->userfield}) {
                        $user->{$field->userfield} = $info->value;
                    }
                }
            }
            foreach ($relations as $rel) {
                $user->$rel->save();
            }
            $user->save();
        }
        return $save;
    }

    public function getCurCart($create = true) {
        $cart = false;
        if (!empty($_SESSION['cart']['cart_id'])) {
            $cart = Ecommerce\Cart::get((int) $_SESSION['cart']['cart_id']);
        }
        if (!$cart && $create) {
            $cart = new Ecommerce\Cart();
            $cart->cart_status_id = 1;
            $cart->user_id = Users\User::$cur->id;
            $userCard = \Ecommerce\Card\Item::get(\Users\User::$cur->id, 'user_id');
            if ($userCard) {
                $cart->card_item_id = $userCard->id;
            }
            $cart->save();
            $_SESSION['cart']['cart_id'] = $cart->id;
        }
        return $cart;
    }

    /**
     * Getting items params with params
     * 
     * @param array $params
     * @return array
     */
    public function getItemsParams($params = [], $saveFilterOptions = []) {
        $filtersOptions = !empty($params['filters']['options']) ? $params['filters']['options'] : [];
        $params['filters'] = [];
        foreach ($filtersOptions as $optionId => $filter) {
            if (in_array($optionId, $saveFilterOptions)) {
                $params['filters']['options'][$optionId] = $filter;
            }
        }
        $selectOptions = Ecommerce\OptionsParser::parse($params);
        $selectOptions['array'] = true;
        $items = Ecommerce\Item::getList($selectOptions);
        if (!$items) {
            return [];
        }
        $cols = array_keys(App::$cur->db->getTableCols(\Ecommerce\Item\Option::table()));
        $cols[] = \Ecommerce\Item\param::colPrefix() . \Ecommerce\Item::index();
        $selectOptions = ['where' => ['view', 1],
            'join' => [
                [Ecommerce\Item\Param::table(), \Ecommerce\Item\Option::index() . ' = ' . Ecommerce\Item\Param::colPrefix() . \Ecommerce\Item\Option::index() . ' and ' . Ecommerce\Item\Param::colPrefix() . Ecommerce\Item::index() . ' IN (' . implode(',', array_keys($items)) . ')', 'inner'],
            ],
            'distinct' => true,
            'cols' => implode(',', $cols)
        ];
        $options = Ecommerce\Item\Option::getList($selectOptions);
        return $options;
    }

    /**
     * Getting items with params
     * 
     * @param array $params
     * @return array
     */
    public function getItems($params = []) {
        $selectOptions = Ecommerce\OptionsParser::parse($params);
        $items = Ecommerce\Item::getList($selectOptions);
        return $items;
    }

    /**
     * Return count of items with params
     * 
     * @param array $params
     * @return int
     */
    public function getItemsCount($params = []) {
        $selectOptions = Ecommerce\OptionsParser::parse($params);
        $selectOptions['distinct'] = \Ecommerce\Item::index();
        $counts = Ecommerce\Item::getCount($selectOptions);
        if (is_array($counts)) {
            $sum = 0;
            foreach ($counts as $count) {
                $sum += $count['count'];
            }
            return $sum;
        }
        return $counts;
    }

    public function viewsCategoryList($inherit = true) {
        $return = [];
        if ($inherit) {
            $return['inherit'] = 'Как у родителя';
        }
        $return['itemList'] = 'Список товаров';
        $conf = App::$primary->view->template->config;
        if (!empty($conf['files']['modules']['Ecommerce'])) {
            foreach ($conf['files']['modules']['Ecommerce'] as $file) {
                if ($file['type'] == 'Category') {
                    $return[$file['file']] = $file['name'];
                }
            }
        }
        return $return;
    }

    public function templatesCategoryList() {
        $return = [
            'inherit' => 'Как у родителя',
            'current' => 'Текущая тема'
        ];

        $conf = App::$primary->view->template->config;

        if (!empty($conf['files']['aditionTemplateFiels'])) {
            foreach ($conf['files']['aditionTemplateFiels'] as $file) {
                $return[$file['file']] = '- ' . $file['name'];
            }
        }
        return $return;
    }

    public function cartStatusDetector($event) {
        $cart = $event['eventObject'];
        if (!empty($cart->_changedParams['cart_cart_status_id'])) {
            $cart->date_status = date('Y-m-d H:i:s');
            $event = new Ecommerce\Cart\Event(['cart_id' => $cart->id, 'user_id' => \Users\User::$cur->id, 'cart_event_type_id' => 5, 'info' => $cart->cart_status_id]);
            $event->save();

            $prev_status_id = $cart->_changedParams['cart_cart_status_id'];
            $now_status_id = $cart->cart_status_id;

            $status = Ecommerce\Cart\Status::getList(['where' => ['id', implode(',', [$prev_status_id, $now_status_id]), 'IN']]);

            $prefix = isset(App::$cur->ecommerce->config['orderPrefix']) ? $config = App::$cur->ecommerce->config['orderPrefix'] : '';
            \App::$cur->users->AddUserActivity($cart->user_id, 3, "Статус вашего заказа номер {$prefix}{$cart->id} изменился с {$status[$prev_status_id]->name} на {$status[$now_status_id]->name}");

            if ($cart->cart_status_id == 5) {
                Inji::$inst->event('ecommerceCartClosed', $cart);
            }
        }
        return $cart;
    }

    public function cardTrigger($event) {
        $cart = $event['eventObject'];
        if ($cart->card) {
            $sum = 0;
            foreach ($cart->cartItems as $cartItem) {
                $sum += $cartItem->final_price * $cartItem->count;
            }
            $cardItemHistory = new Ecommerce\Card\Item\History();
            $cardItemHistory->amount = $sum;
            $cardItemHistory->card_item_id = $cart->card_item_id;
            $cardItemHistory->save();
            $cart->card->sum += $sum;
            $cart->card->save();
        }
        return $cart;
    }

    public function bonusTrigger($event) {
        $cart = $event['eventObject'];
        foreach ($cart->cartItems as $cartItem) {
            foreach ($cartItem->price->offer->bonuses as $bonus) {
                if ($bonus->limited && $bonus->left <= 0) {
                    continue;
                } elseif ($bonus->limited && $bonus->left > 0) {
                    $bonus->left -= 1;
                    $bonus->save();
                }
                switch ($bonus->type) {
                    case 'currency':
                        $currency = \Money\Currency::get($bonus->value);
                        $wallets = App::$cur->money->getUserWallets($cart->user->id);
                        $wallets[$currency->id]->diff($bonus->count, 'Бонус за покупку');
                        break;
                }
            }
        }
        return $cart;
    }

    public function sitemap() {
        $map = [];
        $zeroItems = \Ecommerce\Item::getList(['where' => ['category_id', 0]]);
        foreach ($zeroItems as $item) {
            $map[] = [
                'name' => $item->name,
                'url' => [
                    'loc' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . INJI_DOMAIN_NAME . ($item->getHref())
                ],
            ];
        }

        $categorys = \Ecommerce\Category::getList(['where' => ['parent_id', 0]]);
        $scan = function($category, $scan) {
            $map = [];

            foreach ($category->items as $item) {
                $map[] = [
                    'name' => $item->name,
                    'url' => [
                        'loc' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . INJI_DOMAIN_NAME . ($item->getHref())
                    ],
                ];
            }
            foreach ($category->catalogs as $child) {
                $map = array_merge($map, $scan($child, $scan));
            }
            return $map;
        };
        foreach ($categorys as $category) {
            $map = array_merge($map, $scan($category, $scan));
        }
        return $map;
    }

    public function getFavoriteCount() {
        if (Users\User::$cur->id) {
            return \Ecommerce\Favorite::getCount(['user_id', Users\User::$cur->id]);
        } else {
            $favs = !empty($_COOKIE['ecommerce_favitems']) ? json_decode($_COOKIE['ecommerce_favitems'], true) : [];
            return count($favs);
        }
    }
}