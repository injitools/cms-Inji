<tr>		
    <td class="item-image">
        <a href="/ecommerce/view/<?= $item->id; ?>">
            <img src="<?= Statics::file($item->image ? $item->image->path : '/static/system/images/no-image.png', '50x50'); ?>" />
        </a>
    </td>
    <td class="item-name">
        <a href="/ecommerce/view/<?= $item->id; ?>">
          <?= $item->name(); ?>
        </a>
    </td>

    <td class="item-price">
        <?= number_format($item->getPrice()->price, 2, '.', '&nbsp;'); ?>&nbsp;руб
    </td>
    <td class="item-toCart">
        <a class="btn btn-primary" onclick="inji.Ecommerce.Cart.addItem(<?= $item->getPrice()->id; ?>, 1);"><i class="glyphicon glyphicon-shopping-cart"></i></a>
    </td>
</tr>