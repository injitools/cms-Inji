<div class="ecommerce">
    <div class="content">
      <?php
        $cart = Ecommerce\Cart::getList([
                    'where' => [
                        ['user_id', Users\User::$cur->id],
                    ],
                    'order' => ['complete_data', 'desc'],
                    'limit' => 1
        ]);
        $cart_id = isset(array_values($cart)[0]) ? array_values($cart)[0]->id : '';
        $prefix = isset(App::$cur->ecommerce->config['orderPrefix']) ? $config = App::$cur->ecommerce->config['orderPrefix'] : '';
        $text = "<p>История заказа находится в <a href='/users/cabinet'>Личном кабинете</a>. Для просмотра истории, перейдите по ссылке <a href=\"/users/cabinet/ecommerceOrdersHistory\">История заказов</a>.</p>
             <p>Если у Вас возникли вопросы, пожалуйста <a href='/materials/contacts'>свяжитесь с нами</a>.</p>";

        if (isset(array_values($cart)[0])) {
            ?>
            <h1 class="heading-title">Ваш заказ номер <?= $prefix; ?><?= $cart_id; ?> принят!</h1>
            <p>Ваш заказ принят!</p>
            <?= $text ?>
            <p>Спасибо за покупки в нашем интернет-магазине!</p>
            <?php
        } else {
            ?>
            <h1 class="heading-title">У вас нет недавних заказов</h1>
            <p>У вас нет недавних заказов</p>
            <?= $text ?>
            <?php
        }
        ?>
    </div>
</div>