<?php
$pages = new Ui\Pages($_GET, ['count' => Ecommerce\Cart::getCount(['where' => ['user_id', Users\User::$cur->id]]), 'limit' => 10]);
$carts = Ecommerce\Cart::getList(['where' => ['user_id', Users\User::$cur->id], 'order' => ['date_create', 'desc'], 'start' => $pages->params['start'], 'limit' => $pages->params['limit']]);
$prefix = isset(App::$cur->ecommerce->config['orderPrefix']) ? $config = App::$cur->ecommerce->config['orderPrefix'] : '';
?>
<h3>История заказов</h3>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-condensed list">
        <thead>
            <tr>
                <td class="text-right">№ Заказа</td>
                <td class="text-left">Статус</td>

                <td class="text-right">Товары</td>
                <td class="text-right">Всего</td>
                <td class="text-left">Оформлено</td>
                <td>Оплата</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
          <?php
          foreach ($carts as $cart) {
              $sums = [];
              foreach ($cart->cartItems as $cartItem) {
                  $currency_id = $cartItem->price && $cartItem->price->currency ? $cartItem->price->currency->id : (!empty(\App::$cur->ecommerce->config['defaultCurrency']) ? \App::$cur->ecommerce->config['defaultCurrency'] : 0);
                  if (empty($sums[$currency_id])) {
                      $sums[$currency_id] = $cartItem->final_price * $cartItem->count;
                  } else {
                      $sums[$currency_id] += $cartItem->final_price * $cartItem->count;
                  }
              }
              if ($cart->delivery && $cart->delivery->price) {
                  $currency_id = $cart->delivery->currency_id;
                  if (empty($sums[$currency_id])) {
                      $sums[$currency_id] = $cart->delivery->price;
                  } else {
                      $sums[$currency_id] += $cart->delivery->price;
                  }
              }
              foreach ($cart->extras as $extra) {
                  $currency_id = $extra->currency ? $extra->currency->id : \App::$cur->ecommerce->config['defaultCurrency'];
                  if (empty($sums[$currency_id])) {
                      $sums[$currency_id] = $extra->price * $extra->count;
                  } else {
                      $sums[$currency_id] += $extra->price * $extra->count;
                  }
              }
              ?>
                <tr>
                    <td class="text-right"><?= $prefix ?><?= $cart->id; ?></td>
                    <td class="text-left"><?= $cart->status ? $cart->status->name : 'Наполняется'; ?></td>
                    <td class="text-right"><?= count($cart->cartItems); ?></td>
                    <td class="text-right"><?php
                      if ($sums) {
                          foreach ($sums as $currency_id => $sum) {
                              if (!$sum) {
                                  continue;
                              }
                              echo number_format($sum, 2, '.', ' ');
                              if (App::$cur->money) {
                                  $currency = Money\Currency::get($currency_id);
                                  if ($currency) {
                                      echo '&nbsp;' . $currency->acronym();
                                  } else {
                                      echo '&nbsp;р.';
                                  }
                              } else {
                                  echo '&nbsp;р.';
                              }
                              echo '<br />';
                          }
                      }
                      ?></td>
                    <td class="text-left"><?= $cart->complete_data; ?></td>
                    <td><?php
                      if ($cart->payed) {
                          echo 'Оплачено';
                      } elseif (!App::$cur->money) {
                          echo 'Не оплачено';
                      } else {
                          $handlers = App::$cur->ecommerce->getSnippets('payTypeHandler');
                          $redirect = ['/ecommerce/cart/success'];
                          if ($cart->payType && !empty($handlers[$cart->payType->handler]['handler'])) {
                              $newRedirect = $handlers[$cart->payType->handler]['handler']($cart);
                          }
                          if (!empty($newRedirect[0])) {
                              echo '<a class="btn btn-warning btn-sm" href = "' . $newRedirect[0] . '">Оплатить</a>';
                          } else {
                              echo 'Не оплачено';
                          }
                      }
                      ?></td>
                    <td class="text-right">
                      <?php
                      if ($cart->cart_status_id < 2) {
                          ?>
                            <a title="Продолжить покупки" href="/ecommerce/cart/continue/<?= $cart->id; ?>" data-toggle="tooltip" title="" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-chevron-right"></i></a>
                            <a title="Удалить заказ" onclick="return confirm('Вы уверены?')" href="/ecommerce/cart/delete/<?= $cart->id; ?>" data-toggle="tooltip" title="" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
                            <?php
                        }
                        if ($cart->cart_status_id >= 2) {
                            ?>
                            <a title="Просмотр" href="/ecommerce/cart/orderDetail/<?= $cart->id; ?>" data-toggle="tooltip" title="" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-eye-open"></i></a>
                            <a title="Заказать повторно" href="/ecommerce/cart/refill/<?= $cart->id; ?>" data-toggle="tooltip" title="" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-refresh"></i></a>
                                <?php
                            }
                            ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php
$pages->draw();
?>
