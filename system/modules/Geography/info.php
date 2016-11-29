<?php
return [
    'name' => 'География',
    'autoload' => true,
    'widgets' => [
        'Geography\cityData' => [
            'name' => 'Вариатор данных по городам',
            'params' => function() {
                App::$cur->Ui;
                \App::$cur->libs->loadLib('ckeditor');
                $dataSets = Geography\City\Data::getList(['order' => ['code']]);
                ?>
                <div class="form-group">
                    <label>Группа данных</label>
                    <select class ="form-control groupSelect" onchange="changeGroupSelect(this);">
                        <option value = 0>Выберите группу</option>
                        <?php
                        $isset = [];
                        $datas = [];
                        foreach ($dataSets as $set) {
                            $datas[$set->code][$set->city_id] = $set->data;
                            if (empty($isset[$set->code])) {
                                $isset[$set->code] = true;
                                echo "<option value = '{$set->code}'>{$set->code}</option>";
                            }
                        }
                        ?>
                        <option value = -1>Создать</option>
                    </select>
                </div>
                <script>
                    var changerData = <?= json_encode($datas); ?>;
                    function changeGroupSelect(select) {
                      var val = $(select).val();
                      if (val == "-1") {
                        $(this).data('skip', 1);
                        $('.newGroup').css('display', 'block');
                        $('.newGroup .form-control').data('skip', 0);
                      } else {
                        $(this).data('skip', 0);
                        $('.newGroup').css('display', 'none');
                        $('.newGroup .form-control').data('skip', 1);
                      }
                    }
                    function changeCitySelect(select) {
                      var instance = $('#params_Geography-cityData .htmleditor').next().attr('id').replace('cke_', '');
                      var data = '';
                      if ($('.groupSelect').data('skip') == 1) {
                        var group = $('.newGroup .form-control').val();
                      } else {
                        var group = $('.groupSelect').val();
                      }
                      var city = $('.citySelect .form-control').val();

                      if (changerData[group] && changerData[group][city]) {
                        data = changerData[group][city];
                      }

                      CKEDITOR.instances[instance].setData(data, function () {
                        CKEDITOR.instances[instance].updateElement();
                      });
                    }
                </script>
                <div class="form-group newGroup" style="display: none;">
                    <label>Код группы</label>
                    <input class="form-control" data-skip=1 />
                </div>
                <div class="form-group citySelect">
                    <label>Город</label>
                    <select class ="form-control" onchange="changeCitySelect(this);">
                        <option value = 0>Выберите город</option>
                        <?php
                        foreach (Geography\City::getList() as $city) {
                            echo "<option value = '{$city->id}'>{$city->name}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <textarea class="htmleditor"></textarea>
                </div>
                <?php
            }
                ],
            ]
        ];
        