
<?php
echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}" . (!empty($options['required']) ? ' <span class="required-star">*</span>' : '') . "</label>" : '';
?>
<div class="row">
    <div class="col-xs-6">
        <input <?= !empty($options['required']) ? 'required' : ''; ?> 
          type ="password" 
          placeholder="Новый пароль" 
          class="form-control" 
          name = '<?= $name; ?>[pass]' 
          />
    </div>
    <div class="col-xs-6">
        <input <?= !empty($options['required']) ? 'required' : ''; ?> 
          type ="password" 
          placeholder="Повторите пароль" 
          class="form-control" 
          name = '<?= $name; ?>[repeat]' 
          />
    </div>
</div>
<div class="help-block">Для сменя пароля, введите новый пароль в первом поле и повторите его во втором</div>

<?php
echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>