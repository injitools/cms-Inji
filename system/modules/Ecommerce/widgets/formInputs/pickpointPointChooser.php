<div class="form-group">
    <?= $label ? "<label>{$label}</label>" : ''; ?>
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
    <div style="position: relative;padding-right: 75px;">
        <a style="position: absolute;right: 7px;top: 7px;" href="#"
           onclick="PickPoint.open(my_function, {fromcity:'Москва'});return false"
           id="addressbtn"
           class="btn btn-primary btn-xs"><?= $address ? 'изменить' : 'выбрать'; ?></a>
    </div>
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
        document.getElementById('addressbtn').innerHTML = 'Изменить';
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
