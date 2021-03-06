<?php

echo empty($options['noContainer']) ? '<div class="radio">' : '';
echo $label !== false ? "<label>" : '';
$attributes = [
    'type' => 'radio',
    'name' => $name,
    'value' => $options['value']
];
if (!empty($options['disabled'])) {
    $attributes['disabled'] = 'disabled';
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
echo $label !== false ? " {$label}</label>" : '';
echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
