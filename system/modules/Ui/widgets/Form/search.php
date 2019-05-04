<?php
echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$value = !empty($options['value']) ? addcslashes($options['value'], "'") : (!empty($form->userDataTree[$name]) ? addcslashes($form->userDataTree[$name], "'") : '');
$displayValue = '';
if (!empty($options['values'][$value])) {
    if (!empty($options['inputObject']->colParams['showCol'])) {
        if (is_array($options['inputObject']->colParams['showCol'])) {
            switch ($options['inputObject']->colParams['showCol']['type']) {
                case 'staticMethod':
                    $calssName = $options['inputObject']->colParams['showCol']['class'];
                    $displayValue = $calssName::{$options['inputObject']->colParams['showCol']['method']}($options['values'][$value]);
                    break;
            }
        } else {
            $displayValue = $options['values'][$value]->{$options['inputObject']}->colParams['showCol'];
        }
    } else {
        $displayValue = $options['values'][$value]->name();
    }
}
?>
<input <?= !empty($options['required']) ? 'required' : ''; ?> 
  type ="text" 
  autocomplete="off"
  placeholder="<?= !empty($options['placeholder']) ? $options['placeholder'] : ''; ?>" 
  class="form-control" 
  name = 'query-<?= $name; ?>' 
  value = '<?= $displayValue; ?>'
  />

<div class="form-search-cur">Выбрано: <?= $displayValue; ?></div>
<input 
  type="hidden" 
  name = '<?= $name; ?>'
  value = '<?= $value; ?>'
  />

<div class="form-search-results"></div>
<?php
echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>