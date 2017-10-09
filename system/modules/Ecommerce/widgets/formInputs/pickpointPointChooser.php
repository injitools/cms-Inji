<div class="form-group">
    <a href="#" onclick="PickPoint.open(my_function, {fromcity:'Москва'});return false"
       class="pull-right btn btn-success btn-xs">Выбрать</a>
    <label><?= $label; ?></label>
    <?php
    $id = 'pickpoint' . \Tools::randomString();
    $address = '';
    if (!empty($options['value'])) {
        $points = \Cache::get('PickPointPostamts', []);
        if (!$points) {
            $points = file_get_contents('https://e-solution.pickpoint.ru/api/postamatlist');
            \Cache::set('PickPointPostamts', [], $points, 24 * 60 * 60);
        }
        $points = json_decode($points, true);
        $data = [];
        foreach ($points as $item) {
            if ($item['Number'] == $options['value']) {
                $data = $item;
                break;
            }
        }
        if ($data) {
            $address = $data['Name'] . ' ' . $data['Number'] . '<br />' . $data['PostCode'] . ', ' . $data['Region'] . ', ' . $data['CitiName'] . ', ' . $data['Address'];// . '<br /><small>' . $data['OutDescription'].'</small>';
        }
    }
    ?>
    <script type="text/javascript" src="http://pickpoint.ru/select/postamat.js"></script>
    <div id="address"><?= $address; ?></div>

    <!-- в это поле поместится ID постамата или пункта выдачи -->
    <script type="text/javascript">
      function my_function(result) {
        console.log(result);
// устанавливаем в скрытое поле ID терминала
        document.getElementById('<?=$id;?>').value = result.id;
// показываем пользователю название точки и адрес доствки
        document.getElementById('address').innerHTML = result['name'] + '<br />' + result['address'];
        inji.onLoad(function () {
          inji.Ecommerce.Cart.calcSum();
        })

      }
    </script>
    <?php

    $attributes = [
        'id' => $id,
        'type' => 'hidden',
        'name' => $name,
        'value' => !empty($options['value']) ? $options['value'] : ''
    ];
    if (!empty($options['attributes'])) {
        $attributes = array_merge($attributes, $options['attributes']);
    }
    echo Html::el('input', $attributes, '', null);
    ?>
    <div class="clearfix"></div>
</div>
