<script>
    function exist(widget) {
      var params = widget.replace(/^{WIDGET:/ig, '').replace(/}$/g, '').split(':');
      $('#widgetChooser option[data-filename="' + params[0] + '"]')[0].selected = true;
      $('#widgetChooser').change();
      i = 1;
      $.each($('#params' + $('#widgetChooser').val() + ' .form-control'), function () {
        if (params[i]) {
          $(this).val(decodeURIComponent(params[i++]));
        }
      })
    }
    function genCode() {
      if ($('#widgetChooser')) {
        var code = '{WIDGET:' + ($('#widgetChooser option:selected').data('filename'));
        $.each($('#params' + $('#widgetChooser').val() + ' .form-control'), function () {
          code += ':' + encodeURIComponent($(this).val());
        })
        code += '}';
        return code;
      }
      return '';
    }
    function selectChange(select) {
      $('.widgetParams').hide();
      $('#params_' + $(select).val().replace(/\\/g, '-')).show();
    }
</script>
<select class ='form-control' id = 'widgetChooser' onchange="selectChange(this);">
    <option value = ''>Выберите виджет</option>
    <?php
    foreach ($widgets as $code => $options) {
        $name = is_string($options) ? $options : $options['name'];
        echo "<option value = '{$code}' data-filename='{$code}'>{$name}</option>";
    }
    ?>
</select>
<?php
foreach ($widgets as $code => $options) {
    echo "<div id = 'params_" . str_replace('\\', '-', $code) . "' class = 'widgetParams' style='display:none;'>";
    if (isset($options['params']) && !is_array($options['params'])) {
        $options['params']();
    } elseif (isset($options['params'])) {
        $params = $options['params'];
        if ($params) {
            echo "<h3>Параметры</h3>";
            foreach ($params as $param) {
                if ($param['type'] == 'select') {
                    echo "<div class = 'form-group'>
                        <label>{$param['name']}</label>
                        <select name = 'params[" . str_replace('\\', '-', $code) . "][]' class = 'form-control' >";
                    foreach ($param['model']::get_list() as $item) {
                        echo "<option value = '{$item->pk()}'>{$item->$param['showCol']}</option>";
                    }
                    echo "</select>
                        </div>";
                } elseif ($param['type'] == 'textarea') {
                    echo "<div class = 'form-group'>
                        <label>{$param['name']}</label>
                        <textarea name = 'params[" . str_replace('\\', '-', $code) . "][]' class = 'form-control'></textarea>
                        </div>";
                } else {
                    echo "<div class = 'form-group'>
                        <label>{$param['name']}</label>
                        <input name = 'params[" . str_replace('\\', '-', $code) . "][]' class = 'form-control' />
                        </div>";
                }
            }
        }
    }
    echo "</div>";
}
?>