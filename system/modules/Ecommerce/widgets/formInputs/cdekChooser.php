<div class="form-group">
    <?php
    $cart = \App::$cur->ecommerce->getCurCart(false);
    $field = \Ecommerce\Delivery\Field::get('cdektype', 'code');

    if ($cart && $field && isset($cart->deliveryInfos[$field->id])) {
        if ($field->fieldItems[$cart->deliveryInfos[$field->id]->value]->value === 'До двери') {
            if (empty($options['value'])) {
                $fieldInfo = \Ecommerce\UserAdds\Field::get('deliveryfield_city', 'code');
                if ($fieldInfo && isset($cart->infos[$fieldInfo->id]) && \Ecommerce\Delivery\Field\Item::get($cart->infos[$fieldInfo->id]->value)) {
                    $options['value'] = \Ecommerce\Delivery\Field\Item::get($cart->infos[$fieldInfo->id]->value)->value . ', ';
                }
            }
            $attributes = [
                'type' => 'text',
                'name' => $name,
                'value' => !empty($options['value']) ? $options['value'] : '',
                'class' => 'form-control'
            ];
            if (!empty($options['attributes'])) {
                $attributes = array_merge($attributes, $options['attributes']);
            }
            echo Html::el('input', $attributes, '', null);
        } else {
            $fieldInfo = \Ecommerce\UserAdds\Field::get('deliveryfield_city', 'code');
            if ($fieldInfo && isset($cart->infos[$fieldInfo->id]) && \Ecommerce\Delivery\Field\Item::get($cart->infos[$fieldInfo->id]->value)) {
                $city = \Ecommerce\Delivery\Field\Item::get($cart->infos[$fieldInfo->id]->value)->value;
                $pointsField = \Ecommerce\Delivery\Field::get('cdekPVZ', 'code');
                $points = \Ecommerce\Delivery\Field\Item::getList(['where' => [
                    ['delivery_field_id', $pointsField->id],
                    [\Ecommerce\Delivery\Field\Item::colPrefix() . 'data->\'$."Город"\'', $city]
                ]]);
                ?>
                <select class="form-control" name="<?= $name; ?>">
                    <?php
                    foreach ($points as $point) {
                        $selected = (isset($cart->deliveryInfos[$pointsField->id]) && $cart->deliveryInfos[$pointsField->id]->value == $point->value) ? ' selected="selected"' : '';
                        echo "<option{$selected}>{$point->value}</option>";
                    }
                    ?>
                </select>
                <?php
            } else {
                echo '<b>Заполните поле: Город</b>';
            }
        }

    }
    ?>
</div>
