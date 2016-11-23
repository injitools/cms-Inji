<?php
App::$cur->libs->loadLib('JqueryUi');
$uid = Tools::randomString();

echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$attributes = [
    'type' => 'text',
    'name' => $name,
    'data-dateui' => $uid,
    'class' => !empty($options['class']) ? $options['class'] : 'form-control',
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
if (!empty($options['attributes'])) {
  $attributes = array_merge($attributes, $options['attributes']);
}
echo Html::el('input', $attributes, '', null);
echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>
<script>
  inji.onLoad(function () {
    $("[data-dateui='<?= $uid; ?>']").datetimepicker({
      changeMonth: true,
      changeYear: true,
      numberOfMonths: 1,
      dateFormat: 'yy-mm-dd',
      yearRange: "c-70:c+10",
      timeFormat: 'HH:mm:ss',
      beforeShow: function () {
        setTimeout(function () {
          $('.ui-datepicker').css('z-index', 99999999999999);
        }, 500);
      }
    });
  })
</script>