<div class="item">		
  <?php
  if ($item->badge && $item->badge->image) {
      echo "<img class = 'item-icon-badge' src = '{$item->badge->image->path}' />";
  }
  ?>
    <h4 class="item-name">
        <a href="/ecommerce/view/<?= $item->id; ?>">
          <?= $item->name(); ?>
        </a>
    </h4>
    <a href="/ecommerce/view/<?= $item->id; ?>">
        <img src="<?= Statics::file($item->image ? $item->image->path :false, '200x200', 'q'); ?>" class="img-responsive" style = 'margin:0 auto;'/>
    </a>
    <div class="item-actions">
        <div class="btn-group btn-group-justified">
            <a class="btn btn-default item-price"><?= number_format($item->getPrice()->price, 2, '.', ' '); ?> руб</a>
            <a class="btn btn-primary item-addtocart" onclick="inji.Ecommerce.Cart.addItem(<?= $item->getPrice()->id; ?>, 1);"><i class="glyphicon glyphicon-shopping-cart"></i></a>
        </div>
    </div>
</div>