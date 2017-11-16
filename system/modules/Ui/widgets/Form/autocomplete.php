<?php

$id = 'autocomplete_' . Tools::randomString();
echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$value = !empty($options['value']) ? addcslashes($options['value'], "'") : (!empty($form->userDataTree[$name]) ? addcslashes($form->userDataTree[$name], "'") : '');
$displayValue = '';
if ($value && isset($options['snippet']) && is_string($options['snippet'])) {
    $snippets = App::$cur->Ui->getSnippets('autocomplete');
    if (isset($snippets[$options['snippet']])) {
        $displayValue = $snippets[$options['snippet']]['getValueText']($value, $options['snippetParams']);
    }
}
?>
<div style="position: relative;<?= !$value ? 'display:none' : ''; ?>" class="custominput-clear"
     onclick="$(this).next()[0].__inji_autocomplete.clear();$(this).next()[0].focus();">
    <span class="btn btn-primary btn-xs" style="position: absolute;right: 7px;top: 7px;">Изменить <?= $label; ?></span>
</div>
<input <?= !empty($options['required']) ? 'required' : ''; ?>
        id='<?= $id; ?>'
        type="text"
        autocomplete="off"
        placeholder="<?= !empty($options['placeholder']) ? $options['placeholder'] : ''; ?>"
        class="form-control"
        name='query-<?= $name; ?>'
        value='<?= $displayValue; ?>'
/>

<div class="form-search-cur">Выбрано: <?= $displayValue; ?></div>
<?php
$attributes = [
    'type' => 'hidden',
    'name' => $name,
    'value' => $value
];
if (!empty($options['attributes'])) {
    $attributes = array_merge($attributes, $options['attributes']);
}
echo Html::el('input', $attributes, '', null);
?>

<div class="form-search-results"></div>
<script>
  inji.onLoad(function () {
    setTimeout(function () {
      console.log(inji.Ui);
      inji.Ui.autocomplete.bind(inji.get('#<?=$id;?>'), '<?=$options['snippet'];?>', <?=json_encode($options['snippetParams']);?>);
    }, 100);
  });
</script>
<?php
echo !empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>
