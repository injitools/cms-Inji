  <?php

  echo empty($options['noContainer']) ? '<div class="form-group">' : '';
  echo $label !== false ? "<label>{$label}</label>" : '';
  $attributes = [
      'name' => $name,
      'class' => "form-control " . (!empty($options['class']) ? $options['class'] : '')
  ];
  if (!empty($options['required'])) {
    $attributes['required'] = 'required';
  }
  if (!empty($options['attributes'])) {
    $attributes = array_merge($attributes, $options['attributes']);
  }
  echo Html::el('textarea', $attributes, !empty($options['value']) ? $options['value'] : (!empty($form->userDataTree[$name]) ? addcslashes($form->userDataTree[$name], "'") : ''));
  echo empty($options['noContainer']) ? '</div>' : '';
  