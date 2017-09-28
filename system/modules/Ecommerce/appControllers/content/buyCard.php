<div class="ecommerce">
    <div class="cart-order_page">
        <?php
        $form = new Ui\Form;
        $form->action = "/ecommerce/buyCard";
        $form->begin();
        ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="order_page-info">
                    <?php if (!Users\User::$cur->id) { ?>
                        <fieldset id="account">
                            <h4>Аккаунт</h4>
                            <?php $this->widget('Ecommerce\cart/fastLogin', ['form' => $form]); ?>
                        </fieldset>
                    <?php } ?>
                    <fieldset id="address">
                        <h4>Информация для доставки</h4>
                        <?php $this->widget('Ecommerce\cart/fields', ['form' => $form]); ?>
                    </fieldset>
                </div>
            </div>

            <div class="col-sm-8">
                <div class="order_page-details">
                    <div class="table-responsive">
                        <table class="table table-bordered order_page-cartItems">
                            <thead>
                            <tr>
                                <td class="text-left" colspan="2">Карта</td>
                                <td class="text-left">Уровни</td>
                                <td class="text-left">Стоимость</td>
                            </tr>
                            </thead>
                            <tbody class="cartitems">
                            <?php
                            $first = true;
                            foreach (\Ecommerce\Card::getList() as $card) {
                                $checked = $first;
                                $first = false;
                                ?>
                                <tr>
                                    <td>
                                        <input id="cardId" type="radio" name="card_id"
                                               value="<?= $card->id; ?>" <?= $checked ? 'checked' : ''; ?> />
                                    </td>
                                    <td>
                                        <h4><?= $card->name; ?></h4>
                                        <img class="img-responsive" src="<?= $card->image->path; ?>?resize=200x200"/>
                                    </td>
                                    <td>
                                        <ul>
                                            <?php
                                            foreach ($card->levels as $level) {
                                                ?>
                                                <li><?= $level->name; ?> - <?= $level->discount->name; ?></li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <?= $card->price; ?>&nbsp;руб.
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="checkout-content confirm-section">
                        <h3>Вы можете добавить комментарий</h3>
                        <div class="form-group">
                            <textarea name="comment" rows="5"
                                      class="form-control"><?= (!empty($_POST['comment'])) ? $_POST['comment'] : ''; ?></textarea>
                        </div>
                        <div class="confirm-order">
                            <button data-loading-text="Подождите.." class="btn btn-primary">Подтверждаю покупку карты
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php $form->end(false); ?>
    </div>
</div>