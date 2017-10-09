<?php
$cart = App::$cur->ecommerce->getCurCart(false);
$count = $cart ? count($cart->cartItems) : 0;
$sum = $cart ? $cart->sum : 0;
?>
<a href='/ecommerce/cart'>
    В корзине <?= $count; ?> <?= Tools::getNumEnding($count, ['товар', 'товара', 'товаров']); ?> (<?= $sum; ?>р.)
</a>