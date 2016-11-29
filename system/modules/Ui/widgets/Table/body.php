<?php if ($table->name || $table->buttons) { ?>
    <h1>
      <?= $table->name ? $table->name : ($table->buttons ? '&nbsp;' : ''); ?>
      <?php if ($table->buttons) { ?>
            <div class ='pull-right'>
                <div class="btn-group">
                    <?php
                    $html = '';
                    foreach ($table->buttons as $button) {
                        $html .= '<a class = "btn btn-primary btn-sm"';
                        if (!empty($button['href'])) {
                            $html .= " href = '{$button['href']}'";
                        }
                        if (!empty($button['onclick'])) {
                            $html .= " onclick = '{$button['onclick']}'";
                        }
                        $html .= ">{$button['text']}</a> ";
                    }
                    echo $html;
                    ?>
                </div>
            </div>
        <?php } ?>
    </h1>
<?php } ?>
<?= $table->afterHeader; ?>
<table 
<?php
echo $table->id ? " id = '{$table->id}' " : "";
echo $table->class ? " class = '{$table->class}' " : "";
foreach ($table->attributes as $attribute => $value) {
    echo " {$attribute} = '{$value}' ";
}
?>>
    <?php
    $this->widget('Ui\Table/head', compact('table'));
    echo '<tbody>';
    foreach ($table->rows as $row) {
        $this->widget('Ui\Table/row', compact('row'));
    }
    echo '</tbody>';
    $this->widget('Ui\Table/foot', compact('table'));
    ?>
</table>