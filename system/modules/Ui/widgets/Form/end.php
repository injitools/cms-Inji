<?php
if (empty($options['activeForm']) || $options['activeForm']->parent === null) {
    if ($btnText !== false) {
        ?>
        <div class="form-group">
            <?= Html::el('button', array_merge(['class' => 'btn btn-primary'], $attributs), $btnText); ?>
        </div>
        <?php
    }
    ?>
    </form>
    <?php
}
?>