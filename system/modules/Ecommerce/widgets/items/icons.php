<div class="items-icons">
    <div class="row">
        <?php
        $i = 0;
        foreach ($items as $item) {
            ?>
            <div class="col-xs-6 col-sm-4">
                <?php $this->widget('Ecommerce\items/item-icon', ['item' => $item]); ?>
            </div>
            <?php
            ++$i;
            if (!($i % 3)) {
                echo '<div class="clearfix hidden-xs"></div>';
            }
            if (!($i % 2)) {
                echo '<div class="clearfix visible-xs"></div>';
            }
        }
        ?>
    </div>
</div>