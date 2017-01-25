<div class="ecommerce">
    <div class="row">
        <div class="col-md-3 category-sidebar">
            <div class="sidebar-block">
                <div class="head">Категории</div>
                <div class="items">
                  <?php $this->widget('Ecommerce\categorys', compact('category')); ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h2 class="category-name">Мои избранные товары</h2>
            <?php $this->widget('Ecommerce\items/icons', compact('items')); ?>
            <?php $this->widget('Ecommerce\items/table', ['items' => $items, 'hide' => true]); ?>
            <div class="text-center">
                <?= $pages->draw(); ?>
            </div>
        </div>
    </div>
</div>
