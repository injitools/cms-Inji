<div class="ecommerce">
    <div class="content">
        <h1 class="heading-title">Заказ</h1>
        <table class="table table-bordered table-hover list">
            <thead>
                <tr>
                    <td class="text-left" colspan="2">Детали заказа</td>
                </tr>
            </thead>
            <?php
            $orderDeatilCols = [
                'id' => '№ Заказа',
                'complete_data' => 'Дата заказа'
            ];
            ?>
            <tbody>
                <tr>
                    <td class="text-left" style="width: 50%;">              
                      <?php
                      foreach ($orderDeatilCols as $col => $label) {
                          echo "<b>{$label}</b> " . $cart->$col . "<br>";
                      }
                      ?>
                    </td>
                    <td class="text-left">              
                        <b>Способ оплаты</b> <?= $cart->payType ? $cart->payType->name : '' ?><br>
                        <b>Способ доставки</b> <?= $cart->delivery ? $cart->delivery->name : '' ?>              
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered table-hover list">
            <thead>
                <tr>
                    <td class="text-left">Информация о доставке</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-left">
                      <?php
                      if ($cart->infos) {
                          foreach ($cart->infos as $info) {
                              echo "<b>{$info->field->name()}</b> " . $info->value . "<br>";
                          }
                      }
                      ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="table-responsive">
            <table class="table table-bordered table-hover list">
                <thead>

                    <tr>
                        <td class="text-left">Название товара</td>
                        <td class="text-right">Количество</td>
                        <td class="text-right">Цена</td>
                        <td class="text-right">Всего</td>
                        <td style="width: 20px;"></td>
                    </tr>


                </thead>
                <tbody>
                  <?php
                  $sums = [];
                  foreach ($cart->cartItems as $cartItem) {
                      $item = $cartItem->item;
                      $itemName = $item->name();
                      if (!isset($sums[$cartItem->price->currency_id])) {
                          $sums[$cartItem->price->currency_id] = $cartItem->price->price;
                      } else {
                          $sums[$cartItem->price->currency_id] += $cartItem->price->price;
                      }
                      ?>
                        <tr>
                            <td class="text-left"><?= $itemName; ?></td>
                            <td class="text-right"><?= $cartItem->count; ?></td>
                            <td class="text-right"><?= $cartItem->price->price; ?> 
                              <?php
                              if (App::$cur->money) {
                                  $currency = Money\Currency::get($cartItem->price->currency_id);
                                  if ($currency) {
                                      echo $currency->acronym();
                                  } else {
                                      echo 'руб.';
                                  }
                              } else {
                                  echo 'руб.';
                              }
                              ?></td>
                            <td class="text-right"><?= ($cartItem->price->price * $cartItem->count); ?>
                              <?php
                              if (App::$cur->money) {
                                  $currency = Money\Currency::get($cartItem->price->currency_id);
                                  if ($currency) {
                                      echo $currency->acronym();
                                  } else {
                                      echo 'руб.';
                                  }
                              } else {
                                  echo 'руб.';
                              }
                              ?></td>
                            <td class="text-right" style="white-space: nowrap;">
                                <a onclick = 'inji.Ecommerce.Cart.addItem(<?= $item->getPrice()->id; ?>, 1);
                                          return false;' data-original-title="Добавить в корзину" href="#" data-toggle="tooltip" title="" class="btn btn-primary"><i class="glyphicon glyphicon-shopping-cart"></i></a>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><b>Сумма</b></td>
                        <td class="text-right"><?php
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
                                      echo '&nbsp;руб.';
                                  }
                              } else {
                                  echo '&nbsp;руб.';
                              }
                              echo '<br />';
                          }
                          ?></td>
                        <td></td>
                    </tr>
                    <?php
                    $deliveryCurrency = $cart->delivery ? $cart->delivery->currency_id : 0;
                    $deliveryPrice = $cart->delivery->price;
                    if (!isset($sums[$deliveryCurrency])) {
                        $sums[$deliveryCurrency] = $deliveryPrice;
                    } else {
                        $sums[$deliveryCurrency] += $deliveryPrice;
                    }
                    $delCurrency = false;
                    if (App::$cur->money) {
                        $delCurrency = Money\Currency::get($deliveryPrice);
                    }
                    ?>
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><b><?= $cart->delivery ? $cart->delivery->name : 'Доставка'; ?></b></td>
                        <td class="text-right"><?= $deliveryPrice; ?><?= $delCurrency ? $delCurrency->acronym() : '&nbsp;руб.'; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><b>Итого</b></td>
                        <td class="text-right"><?php
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
                                      echo '&nbsp;руб.';
                                  }
                              } else {
                                  echo '&nbsp;руб.';
                              }
                              echo '<br />';
                          }
                          ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <h3>События корзины</h3>
        <table class="table table-bordered table-hover list">
            <thead>
                <tr>
                    <td class="text-left">Добавлено</td>
                    <td class="text-left">Тип</td>
                    <td class="text-left">Значение</td>
                </tr>
            </thead>
            <tbody>
              <?php
              $statuses = Ecommerce\Cart\Event::getList(['where' => [['cart_id', $cart->id]], 'order' => ['date_create', 'desc']]);
              foreach ($statuses as $status) {
                  ?>
                    <tr>
                        <td class="text-left"><?= $status->date_create; ?></td>
                        <td class="text-left"><?= $status->type->name; ?></td>
                        <td class="text-left">
                            <?php
                            switch ($status->cart_event_type_id) {
                                case'1':
                                case'2':
                                    $price = Ecommerce\Item\Offer\Price::get($status->info);
                                    if ($price) {
                                        echo "<a href = '/ecommerce/view/{$item->id}'>" . $price->offer->item->name() . "</a>";
                                    } else {
                                        echo 'Товар удален';
                                    }
                                    break;
                                case '4':
                                    $info = explode('|', $status->info);
                                    $price = Ecommerce\Item\Offer\Price::get($info[0]);
                                    if ($price) {
                                        echo "<a href = '/ecommerce/view/{$item->id}'>" . $price->offer->item->name() . "</a> " . ($info[1] > 0 ? '+' . $info[1] : $info[1]);
                                    } else {
                                        echo 'Товар удален';
                                    }
                                    break;
                                case '5':
                                    echo Ecommerce\Cart\Status::get($status->info)->name;
                                    break;
                                default:
                                    echo $status->info;
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
</div>
