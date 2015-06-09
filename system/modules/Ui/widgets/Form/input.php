<?php if ($type != 'hidden') { ?>
    <?php
    echo empty($options['noContainer']) ? '<div class="form-group">' : '';
    echo $label !== false ? "<label>{$label}</label>" : '';
    ?>
    <input  type ="<?= $type; ?>" placeholder="<?= !empty($options['placeholder']) ? $options['placeholder'] : ''; ?>" class="form-control" name = '<?= $name; ?>' value = '<?= !empty($options['value']) ? addcslashes($options['value'], "'") : ''; ?>' />
    <?php
    echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
    echo empty($options['noContainer']) ? '</div>' : '';
} else {
    ?>
    <input type ="hidden" name = '<?= $name; ?>' value = '<?= !empty($options['value']) ? addcslashes($options['value'], "'") : ''; ?>' />
    <?php
}
?>