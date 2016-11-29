<div class="ecommerce">
    <div class="row">
        <div class="col-md-3 item-sidebar">
            <div class="sidebar-block">
                <div class="items">
                  <?php $this->widget('Ecommerce\categorys'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="detail_item content">
                <div class="row">
                    <div class="col-sm-5">
                        <img src="<?= Statics::file($item->image ? $item->image->path : '/static/system/images/no-image.png', '350x800'); ?>" class="img-responsive"/>
                    </div>
                    <div class="col-sm-7">
                        <h1><?= $item->name(); ?></h1>
                        <ul class="item-options">
                          <?php
                          foreach ($item->options as $param) {
                              if (!$param->item_option_view || !$param->value)
                                  continue;
                              if ($param->item_option_type == 'select') {
                                  if (empty($param->option->items[$param->value]))
                                      continue;
                                  $value = $param->option->items[$param->value]->value;
                              } else {
                                  $value = $param->value;
                              }
                              $paramName = $param->item_option_name;
                              echo "<li>{$paramName}: {$value} {$param->item_option_postfix}</li>";
                          }
                          ?>
                        </ul>
                        <div class="item-actions">
                            <div class="item-price">
                                <span class="item-price-caption">Цена: </span>
                                <span class="item-price-amount"><?= number_format($item->getPrice()->price, 2, '.', ' '); ?></span>
                                <span class="item-price-currency">руб</span>
                            </div>
                            <div class="btn btn-primary item-addtocart" onclick="inji.Ecommerce.Cart.addItem(<?= $item->getPrice()->id; ?>, 1);">
                                <i class="glyphicon glyphicon-shopping-cart"></i> Добавить в корзину
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="item-description">
                            <?= $item->description; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>