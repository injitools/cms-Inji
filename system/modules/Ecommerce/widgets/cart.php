<?php
$cart = !empty($_SESSION['cart']['cart_id']) ? Ecommerce\Cart::get((int) $_SESSION['cart']['cart_id']) : false;
$count = $cart ? count($cart->cartItems) : 0;
$sum = $cart ? $cart->sum : 0;
?>
<a href='/ecommerce/cart'>
    В корзине <?= $count; ?> <?= Tools::getNumEnding($count, ['товар', 'товара', 'товаров']); ?> (<?= $sum; ?>р.)
</a>