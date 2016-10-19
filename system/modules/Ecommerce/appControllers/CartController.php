<?php

/**
 * Ecommerce Cart app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class CartController extends Controller {

  public function indexAction() {
    $cart = '';
    $deliverys = \Ecommerce\Delivery::getList(['order' => ['weight', 'ASC']]);
    $payTypes = \Ecommerce\PayType::getList(['order' => ['weight', 'ASC']]);
    if (!empty($_SESSION['cart']['cart_id'])) {
      $cart = Ecommerce\Cart::get($_SESSION['cart']['cart_id']);
      if (!empty($_POST)) {
        $error = false;
        if (!Users\User::$cur->id) {
          $user_id = $this->Users->registration($_POST, true);
          if (!$user_id) {
            $error = true;
          } else {
            $user = Users\User::get($user_id);
          }
        } else {
          $user = Users\User::$cur;
        }
        $ids = [];
        if (!empty($_POST['cartItems'])) {
          foreach ($_POST['cartItems'] as $cartItemId => $cartItemCont) {
            $cartItem = \Ecommerce\Cart\Item::get((int) $cartItemId);
            if (!$cartItem) {
              continue;
            }
            if ($cartItem->cart_id != $cart->id) {
              continue;
            }
            $count = (float) $cartItemCont;
            if ($count < 0.001) {
              $count = 1;
            }
            $cartItem->count = $count;
            $cartItem->save();
            $ids[] = $cartItemId;
          }
        }
        foreach ($cart->cartItems as $cartItem) {
          if (!in_array($cartItem->id, $ids)) {
            $cartItem->delete();
          }
        }
        $cart = Ecommerce\Cart::get($cart->id);
        if (!$cart->cartItems) {
          $error = true;
          //Tools::redirect('/ecommerce', 'Ваша корзина пуста');
        }
        if (empty($this->module->config['sell_over_warehouse'])) {
          foreach ($cart->cartItems as $cartitem) {
            $warecount = $cartitem->price->offer->warehouseCount($cart->id);
            if ($cartitem->count > $warecount) {
              $error = true;
              Msg::add('Вы заказали <b>' . $cartitem->item->name . '</b> больше чем есть на складе. на складе: <b>' . $warecount . '</b>', 'danger');
            }
          }
        }
        if ($deliverys && empty($deliverys[$_POST['delivery']])) {
          $error = 1;
          Msg::add('Выберите способ доставки', 'danger');
        } elseif ($deliverys && !empty($deliverys[$_POST['delivery']])) {
          $cart->delivery_id = $_POST['delivery'];
          foreach ($deliverys[$cart->delivery_id]->fields as $field) {
            if (empty($_POST['deliveryFields'][$field->id]) && $field->required) {
              $error = 1;
              Msg::add('Вы не указали: ' . $field->name, 'danger');
            }
          }
        }
        if ($payTypes && (empty($_POST['payType']) || empty($payTypes[$_POST['payType']]))) {
          $error = 1;
          Msg::add('Выберите способ оплаты', 'danger');
        } elseif ($payTypes && !empty($payTypes[$_POST['payType']])) {
          $payType = $payTypes[$_POST['payType']];
          $cart->paytype_id = $payType->id;
        } else {
          $payType = null;
        }
        foreach (\Ecommerce\UserAdds\Field::getList() as $field) {
          if (empty($_POST['userAdds']['fields'][$field->id]) && $field->required) {
            $error = 1;
            Msg::add('Вы не указали: ' . $field->name, 'danger');
          }
        }
        if (!empty($_POST['discounts']['card_item_id'])) {
          $userCard = \Ecommerce\Card\Item::get((int) $_POST['discounts']['card_item_id']);
          if (!$userCard) {
            $error = true;
            Msg::add('Такой карты не существует', 'danger');
          } elseif ($userCard->user_id != $user->id) {
            $error = true;
            Msg::add('Это не ваша карта', 'danger');
          } else {
            $cart->card_item_id = $userCard->id;
          }
        }
        $cart->save();
        if (!$error && !empty($_POST['action']) && $_POST['action'] = 'order') {
          $cart->user_id = $user->user_id;
          $this->module->parseFields($_POST['userAdds']['fields'], $cart);
          if ($payTypes && !empty($payTypes[$cart->paytype_id]) && !empty($_POST['deliveryFields'])) {
            $this->module->parseDeliveryFields($_POST['deliveryFields'], $cart, $deliverys[$cart->delivery_id]->fields);
          }
          $cart->cart_status_id = 2;
          $cart->comment = !empty($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';
          $cart->date_status = date('Y-m-d H:i:s');
          $cart->complete_data = date('Y-m-d H:i:s');
          $cart->warehouse_block = 1;
          $cart->save();

          $cart = \Ecommerce\Cart::get($cart->id);
          foreach ($cart->cartItems as $cartItem) {
            $cartItem->discount = $cartItem->discount();
            $cartItem->final_price = $cartItem->price->price - $cartItem->discount;
            $cartItem->save();
          }
          $cart = \Ecommerce\Cart::get($cart->id);
          if (!empty(\App::$cur->ecommerce->config['notify_mail'])) {
            $text = 'Перейдите в админ панель чтобы просмотреть новый заказ <a href = "http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/admin/ecommerce/Cart">Админ панель</a>';
            $title = 'Новый заказ в интернет магазине на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME);
            \Tools::sendMail('noreply@' . INJI_DOMAIN_NAME, \App::$cur->ecommerce->config['notify_mail'], $title, $text);
          }
          if ($this->notifications) {
            $notification = new Notifications\Notification();
            $notification->name = 'Новый заказ в интернет магазине на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME);
            $notification->text = 'Перейдите в админ панель чтобы просмотреть новый заказ';
            $notification->chanel_id = $this->notifications->getChanel('Ecommerce-orders')->id;
            $notification->save();
          }
          $handlers = $this->ecommerce->getSnippets('payTypeHandler');
          $redirect = ['/ecommerce/cart/success'];
          if ($payType && !empty($handlers[$payType->handler]['handler'])) {
            $newRedirect = $handlers[$payType->handler]['handler']($cart);
            if (!empty($newRedirect)) {
              $redirect = $newRedirect;
            }
          }
          unset($_SESSION['cart']['cart_id']);
          call_user_func_array(['Tools', 'redirect'], $redirect);
        }
      }
    }
    $this->view->setTitle('Корзина');
    $bread = [];
    $bread[] = [
        'text' => 'Каталог',
        'href' => '/ecommerce'
    ];
    $bread[] = [
        'text' => 'Корзина',
        'href' => '/ecommerce/cart'
    ];
    $this->view->page(['data' => compact('cart', 'items', 'deliverys', 'payTypes', 'packItem', 'bread')]);
  }

  public function orderDetailAction($id = 0) {
    $cart = Ecommerce\Cart::get((int) $id);
    if ($cart->user_id != Users\User::$cur->id) {
      $this->url->redirect('/', 'Это не ваша корзина');
    }
    $bread[] = [
        'text' => 'Каталог',
        'href' => '/ecommerce'
    ];
    $bread[] = [
        'text' => 'Корзина',
        'href' => '/ecommerce/cart'
    ];
    $bread[] = [
        'text' => 'Заказ: №' . $cart->id,
        'href' => '/ecommerce/cart/orderDetail/' . $cart->id
    ];
    $this->view->setTitle('Заказ №' . $cart->id);
    $this->view->page(['data' => compact('cart', 'bread')]);
  }

  public function continueAction($id = 0) {
    $cart = \Ecommerce\Cart::get((int) $id);
    if ($cart->user_id != Users\User::$cur->id) {
      Tools::redirect('/', 'Это не ваша корзина');
    }
    if ($cart->cart_status_id > 1) {
      Tools::redirect('/', 'Корзина уже оформлена');
    }
    $_SESSION['cart']['cart_id'] = $cart->id;
    Tools::redirect('/ecommerce/cart');
  }

  public function deleteAction($id = 0) {
    $cart = \Ecommerce\Cart::get((int) $id);
    if ($cart->user_id != Users\User::$cur->id) {
      Tools::redirect('/', 'Это не ваша корзина');
    }
    if ($cart->cart_status_id > 1) {
      Tools::redirect('/', 'Корзина уже оформлена');
    }
    if (!empty($_SESSION['cart']['cart_id']) && $_SESSION['cart']['cart_id'] == $cart->id) {
      unset($_SESSION['cart']['cart_id']);
    }
    $cart->delete();
    Tools::redirect('/users/cabinet/ecommerceOrdersHistory', 'Корзина была удалена', 'success');
  }

  public function refillAction($id = 0) {
    $cart = \Ecommerce\Cart::get((int) $id);
    if ($cart->user_id != Users\User::$cur->id) {
      Tools::redirect('/', 'Это не ваша корзина');
    }
    if (!empty($_SESSION['cart']['cart_id'])) {
      unset($_SESSION['cart']['cart_id']);
    }
    $newCart = $this->ecommerce->getCurCart();
    foreach ($cart->cartItems as $cartitem) {
      $newCart->addItem($cartitem->item_offer_price_id, $cartitem->count);
    }

    $newCart->save();

    Tools::redirect('/ecommerce/cart/');
  }

  public function successAction() {
    $bread = [];
    $bread[] = [
        'text' => 'Каталог',
        'href' => '/ecommerce'
    ];
    $bread[] = [
        'text' => 'Корзина',
        'href' => '/ecommerce/cart'
    ];
    $bread[] = [
        'text' => 'Заказ принят',
        'href' => '/ecommerce/cart/success'
    ];
    $this->view->setTitle('Заказ принят');
    $this->view->page(['data' => compact('bread')]);
  }

  public function addAction() {
    $result = new Server\Result();
    if (empty($_GET['itemOfferPriceId'])) {
      $result->success = false;
      $result->content = 'Произошла непредвиденная ошибка при добавлении товара';
      $result->send();
    }
    $price = \Ecommerce\Item\Offer\Price::get((int) $_GET['itemOfferPriceId']);
    if (!$price) {
      $result->success = false;
      $result->content = 'Такой цены не найдено';
      $result->send();
    }
    $item = $price->offer->item;

    if (!$item) {
      $result->success = false;
      $result->content = 'Такого товара не существует';
      $result->send();
    }

    $item->sales ++;
    $item->save();

    if (empty($_GET['count']))
      $count = 1;
    else
      $count = (float) $_GET['count'];

    $cart = $this->ecommerce->getCurCart();
    $stages = Ecommerce\Cart\Stage::getList();
    if (empty($this->module->config['sell_over_warehouse']) && $price->offer->warehouseCount() < $count) {
      $result->success = false;
      $result->content = 'На складе недостаточно товара! Доступно: ' . $price->offer->warehouseCount();
      $result->send();
    }

    $isset = false;
    foreach ($cart->cartItems as $cartItem) {
      if ($cartItem->item_id == $item->id && $cartItem->item_offer_price_id == $price->id) {
        $cartItem->count += $count;
        $cartItem->save();
        $isset = true;
        break;
      }
    }
    if (!$isset) {
      $cart->addItem($price->id, $count);
    }
    $cart->date_last_activ = date('Y-m-d H:i:s');
    $cart->calc();
    $result->successMsg = '<a href="/ecommerce/view/' . $item->id . '">' . $item->name() . ($price->offer->name() ? ' (' . $price->offer->name() . ')' : '') . '</a> добавлен <a href="/ecommerce/cart">в корзину покупок</a>!';
    $result->send();
  }

  public function getcartAction() {
    $result = new Server\Result();
    ob_start();
    $this->view->widget('Ecommerce\cart');
    $result->content = ob_get_contents();
    ob_end_clean();
    $result->send();
  }

}
