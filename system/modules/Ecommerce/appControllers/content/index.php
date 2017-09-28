<div class="ecommerce">
    <div class="row">
        <div class="col-md-3 category-sidebar">
            <div class="sidebar-block">
                <div class="items">
                    <?php $this->widget('Ecommerce\categorys', compact('category')); ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ecommerce-presentpage">
                <?php $this->widget('Sliders\slider', ['alias' => 'ecommerce-slider']); ?>
                <div class="ecommerce-best">
                    <h2 class='caption'><span>Рекомендумые товары</span></h2>
                    <?php
                    $bestItems = App::$cur->ecommerce->getItems(['where' => [['best', '1']], 'sort' => ['sales' => 'desc'], 'start' => 0, 'count' => 3]);
                    if (count($bestItems) < 3) {
                        $bestItems = array_merge($bestItems, App::$cur->ecommerce->getItems(['sort' => ['sales' => 'desc'], 'start' => 0, 'count' => 3]));
                        $bestItems = array_slice($bestItems, 0, 3);
                    }
                    $this->widget('Ecommerce\items/icons', ['items' => $bestItems]);
                    $this->widget('Ecommerce\items/table', ['items' => $bestItems, 'hide' => true]);
                    ?>
                </div>
            </div>
            <?php
            $this->widget('Ecommerce\items/showOptions');

            $bestItems = App::$cur->ecommerce->getItems(['sort' => ['sales' => 'desc'], 'start' => 0, 'count' => 6]);
            $this->widget('Ecommerce\items/icons', ['items' => $bestItems]);
            $this->widget('Ecommerce\items/table', ['items' => $bestItems, 'hide' => true]);
            ?>
            <a class="ecommerce-showmore" href="/ecommerce/itemList">Показать больше товаров</a>
        </div>
    </div>
</div>