<?php
$path = $cartItem->item->image ? $cartItem->item->image->path : '/static/system/images/no-image.png';

$itemName = $cartItem->item->name();
?>
<tr class="cart_item_id<?= $cartItem->id; ?> item" data-cart_item_id = '<?php echo $cartItem->id; ?>' data-priceam = '<?php echo $cartItem->price->price; ?>' data-item_offer_price_id = '<?php echo $cartItem->price->id; ?>'>
    <td class="text-center">                            
        <a href="/ecommerce/view/<?php echo $cartItem->item->id; ?>">
            <img src="<?= $path; ?>?resize=50x50" alt="<?= $itemName; ?>" title="<?= $itemName; ?>" class="img-thumbnail" />
        </a>
    </td>
    <td class="text-left">
        <a href="/ecommerce/view/<?= $cartItem->item->id; ?>"><?= $itemName; ?></a>
    </td>
    <td class="text-left">
      <?php
        if (!empty($cartItem->item->options['7fc7a4d1-b26a-11e4-9490-80c16e818121']) && $cartItem->item->options['7fc7a4d1-b26a-11e4-9490-80c16e818121']->cip_value) {
            echo '<div style="min-width:200px;">';
            $price = $cartItem->price->price;
            $max = $cartItem->price->offer->warehouseCount((!empty($_SESSION['cart']['cart_id']) ? $_SESSION['cart']['cart_id'] : 0)) * 1000;
            $step = preg_replace('![^0-9]!', '', $cartItem->item->options['7fc7a4d1-b26a-11e4-9490-80c16e818121']->cip_value);
            ?>
            Примерный вес
            <input name="items[<?php echo $cartItem->id; ?>]" type = "text" class ="combineRanger item-counter cart-couner rangerCount" data-step ="<?= $step; ?>" data-max="<?= $max; ?>" data-price ="<?= $price; ?>" value="<?php echo (float) $cartItem->count * 1000; ?>" />
            <?php
            echo '</div>';
        } else {
            ?>
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default btn-number btn-sm" data-type="minus" data-field="items[<?php echo $cartItem->id; ?>]">
                        <span class="glyphicon glyphicon-minus"></span>
                    </button>
                </span>
                <input style ="min-width:50px;float:none;line-height: 16px;margin:0;vertical-align: middle;" type="text" name="items[<?php echo $cartItem->id; ?>]" class="form-control input-number input-sm cart-couner" value="<?php echo (float) $cartItem->count; ?>" min="1" max="<?= $cartItem->price->offer->warehouseCount((!empty($_SESSION['cart']['cart_id']) ? $_SESSION['cart']['cart_id'] : 0)); ?>">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default btn-number btn-sm" data-type="plus" data-field="items[<?php echo $cartItem->id; ?>]">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </span>
            </div>
            <?php
        }
        ?>
    </td>
    <td class="text-left">
        <span class="input-group-btn">
            <button onclick="inji.Ecommerce.Cart.calcSum()" type="button" data-toggle="tooltip" title="Обновить" class="btn btn-primary"><i class="glyphicon glyphicon-refresh"></i></button>
            <button onclick="inji.Ecommerce.Cart.delItem(<?php echo $cartItem->id; ?>);"type="button" data-toggle="tooltip" title="Удалить" class="btn btn-danger" ><i class="glyphicon glyphicon-remove"></i></button>
        </span>
    </td>
    <td class="text-right price"><?php echo $cartItem->price->price; ?>&nbsp;руб.</td>
    <td class="text-right total"><?= $cartItem->price->price * $cartItem->count; ?>&nbsp;руб.</td>
</tr>
