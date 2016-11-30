<?php
$aditionalInputs = [];
$showedInput = false;
$optionsHtml = '';

foreach ($options['values'] as $key => $value) {
    $selected = '';

    $primaryValue = isset($options['value']) ? $options['value'] : null;
    $primaryValue = is_array($primaryValue) && isset($primaryValue['primary']) ? $primaryValue['primary'] : $primaryValue;
    if (is_numeric($key) && !is_array($primaryValue) && $primaryValue !== '') {
        $primaryValue = (int) $primaryValue;
    }
    if (
            (!is_array($primaryValue) && ($key === $primaryValue || (isset($form->userDataTree[$name]) && $form->userDataTree[$name] === $key))) ||
            (is_array($primaryValue) && (in_array($key, $primaryValue) || (isset($form->userDataTree[$name]) && in_array($key, $form->userDataTree[$name]))))
    ) {
        $selected = ' selected="selected"';
    }
    if (is_array($value)) {
        $aditionalInputs[] = $value['input'];
        if ($selected) {
            $showedInput = count($aditionalInputs) - 1;
            $aditionValue = !empty($options['aditionalValue']) ? $options['aditionalValue'] : '';
        }
        $optionsHtml .= "<option data-aditionalInput='" . (count($aditionalInputs) - 1) . "' value ='{$key}'{$selected}>{$value['text']}</option>";
    } else {
        $optionsHtml .= "<option value ='{$key}'{$selected}>{$value}</option>";
    }
}
?>
<?= empty($options['noContainer']) ? '<div class="form-group">' : ''; ?>
<?= $label !== false ? "<label>{$label}" : ''; ?>
<?php
if ($label !== false && !empty($options['createBtn'])) {
    echo ' (<a href="" onclick="' . $options['createBtn']['onclick'] . ';this.disabled=true;return false;">' . $options['createBtn']['text'] . '</a>)';
}
?>
<?= $label !== false ? "</label>" : ''; ?>
<select <?= !empty($options['multiple']) ? 'multiple ' : ''; ?><?= ($showedInput !== false) ? 'data-aditionalEnabled="1"' : ''; ?> <?= !empty($options['disabled']) ? 'disabled="disabled"' : ''; ?> onchange="inji.Ui.forms.checkAditionals(this);" class="form-control <?= !empty($options['class']) ? $options['class'] : ''; ?>" name = '<?= $name; ?>'>
    <?= $optionsHtml; ?>
</select>
<?php
foreach ($aditionalInputs as $key => $input) {
    $input['options']['noContainer'] = true;

    if ($key !== $showedInput) {
        $input['options']['disabled'] = true;
        $input['options']['class'] = !empty($input['options']['class']) ? $input['options']['class'] . ' hidden' : 'hidden';
    } else {
        $input['options']['value'] = empty($input['options']['value']) ? $aditionValue : $input['options']['value'];
    }
    if ($input['type'] == 'select') {
        $input['options']['values'] = \Ui\ActiveForm::getOptionsList($input);
    }
    $form->input($input['type'], empty($input['name']) ? $name . '[aditional]' : $input['name'], false, $input['options']);
}
?>
<?= empty($options['noContainer']) ? '</div>' : ''; ?>