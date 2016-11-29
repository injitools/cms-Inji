<?php
$options = $cartItem->item->options(['key' => 'item_option_id']);
$price = $cartItem->item->getPrice();
if (empty(App::$cur->ecommerce->config['sell_over_warehouse'])) {
    $max = $price->offer->warehouseCount((!empty($_SESSION['cart']['cart_id']) ? $_SESSION['cart']['cart_id'] : 0));
} else {
    $max = 100;
}
if (!empty($options[16]) && $options[16]->value) {
    echo '<div style="min-width:200px;">';
    $price = $cartItem->price;
    $step = preg_replace('![^0-9]!', '', $options[16]->value) / 1000;
    ?>
    Примерный вес
    <input type = "text" data-miltiple="1000" class ="combineRanger item-counter" data-step ="<?= $step; ?>" data-max="<?= $max; ?>" data-price ="<?= $price->price; ?>" name="cartItems[<?php echo $cartItem->id; ?>]" value ="<?= $cartItem->count; ?>" />
    <?php
    echo '</div>';
} else {
    ?>
    <div class="input-group number-spinner" >
        <span class="input-group-btn">
            <button type="button" class="btn btn-default btn-sm btn-number" data-type="minus" data-field="cartItems[<?php echo $cartItem->id; ?>]"><span class="glyphicon glyphicon-minus"></span></button>
        </span>
        <input type="text" name="cartItems[<?php echo $cartItem->id; ?>]" class="form-control text-center input-sm input-number" value="<?php echo (float) $cartItem->count; ?>" min="1" max="<?= $max; ?>">
        <span class="input-group-btn">
            <button type="button" class="btn btn-default btn-sm btn-number" data-type="plus" data-field="cartItems[<?php echo $cartItem->id; ?>]"><span class="glyphicon glyphicon-plus"></span></button>
        </span>
    </div>
    <?php
}
?>