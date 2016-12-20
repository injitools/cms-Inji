<?php
return [
    'widget' => function () {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Онлайн-магазин</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <p>Всего товаров/отображаемых: <?= Ecommerce\Item::getCount(); ?>
                            / <?= \App::$cur->ecommerce->getItemsCount(); ?></p>
                        <p>Новых товаров/отображаемых
                            сегодня: <?= Ecommerce\Item::getCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?>
                            / <?= \App::$cur->ecommerce->getItemsCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?></p>
                    </div>
                    <div class="col-lg-6">
                        <p>Всего Корзин/Заказов: <?= Ecommerce\Cart::getCount(); ?>
                            / <?= Ecommerce\Cart::getCount(['where' => ['cart_status_id', 1, '>']]); ?></p>
                        <p>Новых Корзин/Заказов
                            сегодня: <?= Ecommerce\Cart::getCount(['where' => [['date_create', date('Y-m-d 00:00:00'), '>']]]); ?>
                            / <?= Ecommerce\Cart::getCount(['where' => [['cart_status_id', 1, '>'], ['complete_data', date('Y-m-d 00:00:00'), '>']]]); ?></p>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <a href="/admin/Ecommerce/Item">Товары</a> |
                <a href="/admin/Ecommerce/Cart?datamanagerFilters[cart_status_id][value]=2">Заказы</a>
            </div>
        </div>
        <?php
    }
];
        