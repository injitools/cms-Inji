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

  public function parseOptions($options = []) {
    $selectOptions = [
        'where' => !empty($options['where']) ? $options['where'] : [],
        'distinct' => false,
        'join' => [],
        'order' => [],
        'start' => isset($options['start']) ? (int) $options['start'] : 0,
        'key' => isset($options['key']) ? $options['key'] : null,
        'limit' => !empty($options['count']) ? (int) $options['count'] : 0,
    ];
    if (!empty($options['sort']) && is_array($options['sort'])) {
      foreach ($options['sort'] as $col => $direction) {
        switch ($col) {
          case 'price':
            $selectOptions['order'][] = [Ecommerce\Item\Offer\Price::colPrefix() . 'price', strtolower($direction) == 'desc' ? 'desc' : 'asc'];
            break;
          case 'name':
            $selectOptions['order'][] = ['name', strtolower($direction) == 'desc' ? 'desc' : 'asc'];
            break;
          case 'sales':
            $selectOptions['order'][] = ['sales', strtolower($direction) == 'desc' ? 'desc' : 'asc'];
            break;
          case 'weight':
            $selectOptions['order'][] = ['weight', strtolower($direction) == 'desc' ? 'desc' : 'asc'];
            break;
          case 'new':
            $selectOptions['order'][] = ['date_create', strtolower($direction) == 'desc' ? 'desc' : 'asc'];
            break;
        }
      }
    }
    $selectOptions['where'][] = ['deleted', 0];
    if (empty($this->config['view_empty_image'])) {
      $selectOptions['where'][] = ['image_file_id', 0, '!='];
    }

    $selectOptions['join'][] = [Ecommerce\Item\Offer::table(), Ecommerce\Item::index() . ' = ' . Ecommerce\Item\Offer::colPrefix() . Ecommerce\Item::index(), 'inner'];

    $selectOptions['join'][] = [Ecommerce\Item\Offer\Price::table(),
        Ecommerce\Item\Offer::index() . ' = ' . Ecommerce\Item\Offer\Price::colPrefix() . Ecommerce\Item\Offer::index() .
        (empty($this->config['show_zero_price']) ? ' and ' . Ecommerce\Item\Offer\Price::colPrefix() . 'price>0' : ''),
        empty($this->config['show_without_price']) ? 'inner' : 'left'];

    $selectOptions['join'][] = [
        Ecommerce\Item\Offer\Price\Type::table(), Ecommerce\Item\Offer\Price::colPrefix() . Ecommerce\Item\Offer\Price\Type::index() . ' = ' . Ecommerce\Item\Offer\Price\Type::index()
    ];

    $selectOptions['where'][] = [
        [Ecommerce\Item\Offer\Price\Type::index(), NULL, 'is'],
        [
            [Ecommerce\Item\Offer\Price\Type::colPrefix() . 'roles', '', '=', 'OR'],
            [Ecommerce\Item\Offer\Price\Type::colPrefix() . 'roles', '%|' . \Users\User::$cur->role_id . '|%', 'LIKE', 'OR'],
        ],
    ];


    if (!empty($this->config['view_filter'])) {
      if (!empty($this->config['view_filter']['options'])) {
        foreach ($this->config['view_filter']['options'] as $optionId => $optionValue) {
          $selectOptions['join'][] = [Ecommerce\Item\Param::table(), Ecommerce\Item::index() . ' = ' . 'option' . $optionId . '.' . Ecommerce\Item\Param::colPrefix() . Ecommerce\Item::index() . ' AND ' .
              'option' . $optionId . '.' . Ecommerce\Item\Param::colPrefix() . Ecommerce\Item\Option::index() . ' = "' . (int) $optionId . '" AND ' .
              'option' . $optionId . '.' . Ecommerce\Item\Param::colPrefix() . 'value = "' . (int) $optionValue . '"',
              'inner', 'option' . $optionId];
        }
      }
    }
    //filters
    if (!empty($options['filters'])) {
      foreach ($options['filters'] as $col => $filter) {
        switch ($col) {
          case 'price':
            if (!empty($filter['min'])) {
              $selectOptions['where'][] = [Ecommerce\Item\Offer\Price::colPrefix() . 'price', (float) $filter['min'], '>='];
            }
            if (!empty($filter['max'])) {
              $selectOptions['where'][] = [Ecommerce\Item\Offer\Price::colPrefix() . 'price', (float) $filter['max'], '<='];
            }
            break;
          case 'options':
            foreach ($filter as $optionId => $optionValue) {
              $optionId = (int) $optionId;
              if (is_array($optionValue)) {
                $optionValueArr = [];
                foreach ($optionValue as $val) {
                  $optionValueArr[] = \App::$cur->db->connection->pdo->quote($val);
                }
                $qstr = 'IN (' . implode(',', $optionValueArr) . ')';
              } else {
                $qstr = '= ' . \App::$cur->db->connection->pdo->quote($optionValue);
              }
              $selectOptions['join'][] = [Ecommerce\Item\Param::table(), Ecommerce\Item::index() . ' = ' . 'option' . $optionId . '.' . Ecommerce\Item\Param::colPrefix() . Ecommerce\Item::index() . ' AND ' .
                  'option' . $optionId . '.' . Ecommerce\Item\Param::colPrefix() . Ecommerce\Item\Option::index() . ' = "' . (int) $optionId . '" AND ' .
                  'option' . $optionId . '.' . Ecommerce\Item\Param::colPrefix() . 'value ' . $qstr . '',
                  'inner', 'option' . $optionId];
            }
            break;
          case 'offerOptions':
            //$selectOptions['join'][] = [Ecommerce\Item\Offer::table(), Ecommerce\Item::index() . ' = offer.' . Ecommerce\Item\Offer::colPrefix() . Ecommerce\Item::index(), 'left', 'offer'];
            foreach ($filter as $optionId => $optionValue) {
              $optionId = (int) $optionId;
              if (is_array($optionValue)) {
                $optionValueArr = [];
                foreach ($optionValue as $val) {
                  $optionValueArr[] = \App::$cur->db->connection->pdo->quote($val);
                }
                $qstr = 'IN (' . implode(',', $optionValueArr) . ')';
              } else {
                $qstr = '= ' . \App::$cur->db->connection->pdo->quote($optionValue);
              }
              $selectOptions['join'][] = [Ecommerce\Item\Offer\Param::table(), Ecommerce\Item\Offer::index() . ' = ' . 'offerOption' . $optionId . '.' . Ecommerce\Item\Offer\Param::colPrefix() . Ecommerce\Item\Offer::index() . ' AND ' .
                  'offerOption' . $optionId . '.' . Ecommerce\Item\Offer\Param::colPrefix() . Ecommerce\Item\Offer\Option::index() . ' = "' . (int) $optionId . '" AND ' .
                  'offerOption' . $optionId . '.' . Ecommerce\Item\Offer\Param::colPrefix() . 'value ' . $qstr,
                  'inner', 'offerOption' . $optionId];
            }
            break;
        }
      }
    }
    //parents
    if (!empty($options['parent']) && strpos($options['parent'], ',') !== false) {
      $first = true;
      $where = [];
      foreach (explode(',', $options['parent']) as $categoryId) {
        if (!$categoryId) {
          continue;
        }
        $category = \Ecommerce\Category::get($categoryId);
        $where[] = ['tree_path', $category->tree_path . (int) $categoryId . '/%', 'LIKE', $first ? 'AND' : 'OR'];
        $first = false;
      }
      $selectOptions['where'][] = $where;
    } elseif (!empty($options['parent'])) {
      $category = \Ecommerce\Category::get($options['parent']);
      $selectOptions['where'][] = ['tree_path', $category->tree_path . (int) $options['parent'] . '/%', 'LIKE'];
    }

    //search
    if (!empty($options['search'])) {
      $searchStr = preg_replace('![^A-zА-я0-9 ]!iSu', ' ', $options['search']);
      $searchArr = [];
      foreach (explode(' ', $searchStr) as $part) {
        $part = trim($part);
        if ($part && strlen($part) > 2) {
          $searchArr[] = ['search_index', '%' . $part . '%', 'LIKE'];
        }
      }
      if (!empty($searchArr)) {
        $selectOptions['where'][] = $searchArr;
      }
    }
    if (empty($this->config['view_empty_warehouse'])) {
      $warehouseIds = [];
      if (App::$cur->geography && \Geography\City::$cur) {
        $warehouses = \Geography\City\Data::get([['code', 'warehouses'], ['city_id', \Geography\City::$cur->id]]);
        if ($warehouses && $warehouses->data) {
          foreach (explode(',', $warehouses->data) as $id) {
            $warehouseIds[$id] = $id;
          }
        }
      }
      $selectOptions['where'][] = [
          '(
          (SELECT COALESCE(sum(`' . \Ecommerce\Item\Offer\Warehouse::colPrefix() . 'count`),0) 
            FROM ' . \App::$cur->db->table_prefix . \Ecommerce\Item\Offer\Warehouse::table() . ' iciw 
            WHERE iciw.' . \Ecommerce\Item\Offer\Warehouse::colPrefix() . \Ecommerce\Item\Offer::index() . ' = ' . \Ecommerce\Item\Offer::index() . '
                ' . ($warehouseIds ? ' AND iciw.' . \Ecommerce\Item\Offer\Warehouse::colPrefix() . \Ecommerce\Warehouse::index() . ' IN(' . implode(',', $warehouseIds) . ')' : '') . '
            )
          -
          (SELECT COALESCE(sum(' . \Ecommerce\Warehouse\Block::colPrefix() . 'count) ,0)
            FROM ' . \App::$cur->db->table_prefix . \Ecommerce\Warehouse\Block::table() . ' iewb
            inner JOIN ' . \App::$cur->db->table_prefix . \Ecommerce\Cart::table() . ' icc ON icc.' . \Ecommerce\Cart::index() . ' = iewb.' . \Ecommerce\Warehouse\Block::colPrefix() . \Ecommerce\Cart::index() . ' AND (
                (`' . \Ecommerce\Cart::colPrefix() . 'warehouse_block` = 1 and `' . \Ecommerce\Cart::colPrefix() . 'cart_status_id` in(2,3,6)) ||
                (`' . \Ecommerce\Cart::colPrefix() . \Ecommerce\Cart\Status::index() . '` in(0,1) and `' . \Ecommerce\Cart::colPrefix() . 'date_last_activ` >=subdate(now(),INTERVAL 30 MINUTE))
            )
            WHERE iewb.' . \Ecommerce\Warehouse\Block::colPrefix() . \Ecommerce\Item\Offer::index() . ' = ' . \Ecommerce\Item\Offer::index() . ')
          )',
          0,
          '>'
      ];
    }

    $selectOptions['group'] = Ecommerce\Item::index();

    return $selectOptions;
  }

  /**
   * Getting items params with params
   * 
   * @param array $params
   * @return array
   */
  public function getItemsParams($params = []) {
    $selectOptions = $this->parseOptions($params);

    $items = Ecommerce\Item::getList($selectOptions);
    if (!$items) {
      return [];
    }
    $items = Ecommerce\Item\Param::getList([
                'where' => ['item_id', array_keys($items), 'IN'],
                'join' => [[Ecommerce\Item\Option::table(), Ecommerce\Item\Option::index() . ' = ' . \Ecommerce\Item\Param::colPrefix() . Ecommerce\Item\Option::index() . ' and ' . \Ecommerce\Item\Option::colPrefix() . 'searchable = 1', 'inner']],
                'distinct' => \Ecommerce\Item\Option::index()
    ]);
    return $items;
  }

  /**
   * Getting items with params
   * 
   * @param array $params
   * @return array
   */
  public function getItems($params = []) {
    $selectOptions = $this->parseOptions($params);
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
    $selectOptions = $this->parseOptions($params);
    $selectOptions['distinct'] = \Ecommerce\Item::index();
    $counts = Ecommerce\Item::getCount($selectOptions);
    if (is_array($counts)) {
      $sum = 0;
      foreach ($counts as $count) {
        $sum +=$count['count'];
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
          case'currency':
            $currency = \Money\Currency::get($bonus->value);
            $wallets = App::$cur->money->getUserWallets($cart->user->id);
            $wallets[$currency->id]->diff($bonus->count, 'Бонус за покупку');
            break;
        }
      }
    }
    return $cart;
  }

  function sitemap() {
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

}
