<div class="items-table" <?= !empty($hide) ? 'style="display: none;"' : ''; ?>>	
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-striped table-hover">
            <?php
            $i = 0;
            foreach ($items as $item) {
                ?>
                <tr>	
                  <?php $this->widget('Ecommerce\items/item-tablerow', ['item' => $item]); ?>
                </tr>
                <?php
            }
            ?>
        </table></div>
</div>