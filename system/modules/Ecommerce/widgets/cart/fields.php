<fieldset>
    <?php
    if (\Users\User::$cur->id) {
        $userAdds = Ecommerce\UserAdds::getList(['where' => ['user_id', \Users\User::$cur->id]]);
        $values = [];
        foreach ($userAdds as $userAdd) {
            $values[$userAdd->id] = $userAdd->values(['array' => true]);
        }
        if ($userAdds) {
            $form->input('select', 'userAddsId', 'Ваши контакты', ['values' => ['' => 'Выберите'] + Ecommerce\UserAdds::getList(['where' => ['user_id', \Users\User::$cur->id], 'forSelect' => true])]);
            echo '<hr />';
        }
        ?>
        <script>
          var userAddsValues = <?= json_encode($values); ?>;
          inji.onLoad(function () {
            $('[name="userAddsId"]').change(function () {
              var values = userAddsValues[$(this).val()];
              for (key in values) {
                var value = values[key];
                $('[name="userAdds[fields][' + value.useradds_value_useradds_field_id + ']"]').val(value.useradds_value_value);
              }
            });
          })
        </script>
        <?php
    }
    foreach (Ecommerce\UserAdds\Field::getList(['order' => ['weight', 'asc']]) as $field) {
        $options = [
            'required' => $field->required,
            'placeholder' => $field->placeholder
        ];
        $form->input($field->type, "userAdds[fields][{$field->id}]", $field->name, $options);
    }
    ?>
</fieldset>