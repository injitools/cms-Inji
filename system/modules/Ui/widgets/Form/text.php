<?php

echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$attributes = [
    'type' => 'text',
    'name' => $name,
    'class' => 'form-control',
    'value' => !empty($options['value']) ? addcslashes($options['value'], "'") : (!empty($form->userDataTree[$name]) ? addcslashes($form->userDataTree[$name], "'") : '')
];
if (!empty($options['disabled'])) {
    $attributes['disabled'] = 'disabled';
}
if (!empty($options['placeholder'])) {
    $attributes['placeholder'] = $options['placeholder'];
}
if (!empty($options['checked'])) {
    $attributes['checked'] = 'checked';
}
if (!empty($options['required'])) {
    $attributes['required'] = 'required';
}
if (!empty($options['attributes'])) {
    $attributes = array_merge($attributes, $options['attributes']);
}
echo Html::el('input', $attributes, '', null);
echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
