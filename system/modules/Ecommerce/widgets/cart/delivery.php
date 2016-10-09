<div class="row">
  <div class="col-md-4">
    <ul class="nav nav-pills nav-stacked">
      <?php
      $hiddenId = Tools::randomString();
      foreach ($deliverys as $delivery) {
        if ((!empty($_POST['delivery']) && $_POST['delivery'] == $delivery->id) || ($cart->delivery && $delivery->id == $cart->delivery->id)) {
          $checked = 'checked';
        } else {
          $checked = '';
        }
        echo '<li' . ($checked ? ' class="active"' : '') . '><a href = "#" onclick = "document.getElementById(\'' . $hiddenId . '\').value=\'' . $delivery->id . '\';inji.Ecommerce.Cart.calcSum();return false;">';
        echo $delivery->name;
        echo '</a></li>';
      }
      $form->input('hidden', "delivery", '', [
          'value' => $cart->delivery_id,
          'attributes' => [
              'id' => $hiddenId
          ],
      ]);
      ?>
    </ul>
  </div>
  <div class="col-md-8">
    <?php
    if ($cart->delivery) {
      echo "<h4>Информация о доставке</h4>";
      if ($cart->delivery->price_text || $cart->delivery->price) {
        echo "<div>Стоимость доставки: <b>" . ($cart->delivery->price_text ? $cart->delivery->price_text : ( $cart->delivery->price . ' ' . ($cart->delivery->currency ? $cart->delivery->currency->acronym() : 'руб.') )) . '</b></div>';
      }
      if ((float) $cart->delivery->max_cart_price) {
        echo '<div>При заказе товаров на сумму от ' . $cart->delivery->max_cart_price . ' руб - бесплатно</div>';
      }
      echo $cart->delivery->info;
      if ($cart->delivery->fields) {
        echo '<hr />';
        foreach ($cart->delivery->fields as $field) {
          $options = ['required' => $field->required];
          if ($field->type == 'radio') {
            $values = $field->fieldItems(['forSelect' => true]);
            foreach ($values as $key => $value) {
              $options['value'] = $key;
              $form->input($field->type, "deliveryFields[{$field->id}]", $value, $options);
            }
          } else {
            if ($field->type == 'select') {
              $options['values'] = $field->fieldItems(['forSelect' => true]);
            }
            $form->input($field->type, "deliveryFields[{$field->id}]", $field->name, $options);
          }
        }
      }
    } else {
      echo "<h4>Выберите способ доставки</h4>";
    }
    ?>
  </div>
</div>