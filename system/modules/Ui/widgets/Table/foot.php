<tfoot>
    <tr>
      <?php
        foreach ($table->cols as $col) {
            if (is_string($col)) {
                echo "<th>{$col}</th>";
            } else {
                echo Html::el('th', !empty($col['attributes']) ? $col['attributes'] : [], $col['text']);
            }
        }
        ?>
    </tr>
</tfoot>