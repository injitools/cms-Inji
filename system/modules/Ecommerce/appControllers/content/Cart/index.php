<div class="ecommerce">
    <div class="content">
        <div class="cart-order_page">
            <h2>Быстрое оформление заказа</h2>
            <?php
            if (!$cart || !$cart->cartItems) {
                            echo "<h1>Ваша корзина пуста</h1>";
            } else {
                $form = new Ui\Form;
                $form->action = "/ecommerce/cart";
                $form->begin();
                ?>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="order_page-info">
                          <?php if (!Users\User::$cur->id) { ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Аккаунт</h3>
                                    </div>
                                    <div class="panel-body">
                                      <?php $this->widget('Ecommerce\cart/fastLogin', ['form' => $form, 'cart' => $cart]); ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (Ecommerce\Card::getList()) { ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Дисконтная карта</h3>
                                    </div>
                                    <div class="panel-body">
                                      <?php $this->widget('Ecommerce\cart/cardSelect', ['form' => $form, 'cart' => $cart]); ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Контактная информация</h3>
                                </div>
                                <div class="panel-body">
                                  <?php $this->widget('Ecommerce\cart/fields', ['form' => $form, 'cart' => $cart]); ?>
                                </div>
                            </div>
                        </div>                               
                    </div>
                    <div class="col-sm-8">
                        <div class="order_page-options">
                            <div class="order_page-delivery">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Способ доставки</h3>
                                    </div>
                                    <div class="panel-body">
                                      <?php $this->widget('Ecommerce\cart/delivery', compact('form', 'cart', 'deliverys')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-content checkout-payment-methods">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Способ оплаты</h3>
                                    </div>
                                    <div class="panel-body">
                                      <?php $this->widget('Ecommerce\cart/payType', compact('form', 'cart', 'payTypes')); ?>
                                    </div>
                                </div>      
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="order_page-details">
                            <h3>Корзина товаров</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered order_page-cartItems">
                                    <thead>
                                        <tr>
                                            <td colspan="2">Название товара</td>
                                            <td style="width:1%;min-width:150px;">Количество</td>
                                            <td>Цена</td>
                                            <?= $cart->hasDiscount() ? '<td>Скидка</td>' : ''; ?>
                                            <td>Итого</td>
                                            <td style="width:1%"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                        if (class_exists('Money\Currency')) {
                                            $defaultCurrency = Money\Currency::get(\App::$cur->ecommerce->config['defaultCurrency']);
                                        } else {
                                            $defaultCurrency = '';
                                        }
                                        foreach ($cart->cartItems as $cartItem) {
                                            $path = $cartItem->item->image ? $cartItem->item->image->path : '/static/system/images/no-image.png';
                                            ?>
                                            <tr class="cart_item_id<?= $cartItem->id; ?> item" data-cart_item_id = '<?php echo $cartItem->id; ?>' data-priceam = '<?php echo $cartItem->price->price; ?>' data-item_offer_price_id = '<?php echo $cartItem->price->id; ?>'>
                                                <td class="text-center image">                            
                                                    <a href="/ecommerce/view/<?php echo $cartItem->item->id; ?>">
                                                        <img src="<?= $path; ?>?resize=50x50" />
                                                    </a>
                                                </td>
                                                <td class="text-left name">
                                                    <a href="/ecommerce/view/<?= $cartItem->item->id; ?>"><?= $cartItem->item->name() . ($cartItem->price->offer->name && $cartItem->price->offer->name != $cartItem->item->name() ? ' (' . $cartItem->price->offer->name . ')' : ''); ?></a>
                                                </td>
                                                <td class="text-left quantity">
                                                  <?php $this->widget('Ecommerce\cart/ranger', compact('form', 'cart', 'cartItem')); ?>
                                                </td>
                                                <td class="text-right price"><?= number_format($cartItem->price->price, 2, '.', '&nbsp;'); ?></td>
                                                <?php if ($cart->hasDiscount()) { ?>
                                                    <td class="text-right discount"><?= new Money\Sums([$cartItem->price->currency_id => $cartItem->discount()]); ?></td>
                                                <?php } ?>
                                                <td class="text-right total"><?= new Money\Sums([$cartItem->price->currency_id => $cartItem->price->price * $cartItem->count - $cartItem->discount()]); ?></td>
                                                <td class="text-right actions">
                                                    <div class="btn-group-vertical" role="group" aria-label="...">
                                                        <a type="button" class="btn btn-primary btn-update btn-sm" onclick="inji.Ecommerce.Cart.calcSum();"><i class="glyphicon glyphicon-refresh"></i></a>
                                                        <a type="button" class="btn btn-danger  btn-delete btn-sm" onclick="inji.Ecommerce.Cart.delItem(<?php echo $cartItem->id; ?>);"><i class="glyphicon glyphicon-remove"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                      <?php
                                        $colspan = $cart->hasDiscount() ? 5 : 4;
                                        ?>
                                        <tr class="order_page-sum">
                                            <td colspan="<?= $colspan; ?>" class="text-right">Сумма:</td>
                                            <td colspan="2" class="text-right"><?= $cart->itemsSum(); ?></td>
                                        </tr>
                                        <?php
                                        if ($cart->hasDiscount()) {
                                            ?>
                                            <tr class="order_page-discountSum">
                                                <td colspan="<?= $colspan; ?>" class="text-right">Скидка:</td>
                                                <td colspan="2" class="text-right"><?= $cart->discountSum(); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        if ($cart->delivery) {
                                            ?>
                                            <tr class="order_page-deliverySum">
                                                <td colspan="<?= $colspan; ?>" class="text-right"><?= $cart->delivery->name; ?>:</td>
                                                <td colspan="2" class="text-right"><?php
                                                    if ($cart->delivery && $cart->delivery->price_text) {
                                                        echo $cart->delivery->price_text;
                                                    } else {
                                                        echo $cart->deliverySum();
                                                    }
                                                    ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <tr class="order_page-total">
                                            <td colspan="<?= $colspan; ?>" class="text-right">Итого:</td>
                                            <td colspan="2" class="text-right"><?= $cart->finalSum(); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr />
                            <div class="clearfix"></div>
                            <div class="order_page-finish">
                              <?php
                                $form->input('textarea', 'comment', 'Вы можете добавить комментарий к своему заказу', ['value' => (!empty($_POST['comment'])) ? $_POST['comment'] : '']);
                                ?>
                                <div class="order_page-orderBtn">
                                    <button name ="action" value ="order" data-loading-text="Подождите.." class="btn btn-primary">Подтверждение заказа</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $form->end(false);
            }
            ?>
        </div>
    </div>
</div>