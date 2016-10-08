<div class="filters">
  <form>
    <?php
    $min = App::$cur->ecommerce->getItems(['sort' => ['price' => 'asc'], 'count' => 1, 'key' => false]);
    $max = App::$cur->ecommerce->getItems(['sort' => ['price' => 'desc'], 'count' => 1, 'key' => false]);
    if ($min && $min[0]->getPrice() && $max && $max[0]->getPrice()) {
      ?>
      <h4>Фильтр по цене</h4>
      <div class="form-group">      
        <div class="row">
          <div class="col-sm-6">от <input type="text" name = 'filters[price][min]' value ="<?= !empty($_GET['filters']['price']['min']) ? $_GET['filters']['price']['min'] : $min[0]->getPrice()->price; ?>" class="form-control" /></div>
          <div class="col-sm-6">до <input type="text" name = 'filters[price][max]' value ="<?= !empty($_GET['filters']['price']['max']) ? $_GET['filters']['price']['max'] : $max[0]->getPrice()->price; ?>" class="form-control" /></div>
        </div>
      </div>
      <?php
    }
    foreach ($options as $option) {
      ?>
      <div class="filter">  

        <?php
        switch ($option->type) {
          case 'radio':
            echo "<label>{$option->name}</label>";
            foreach ($option->items as $item) {
              $this->widget('Ui\Form/' . $option->type, [
                  'label' => $item->name,
                  'name' => "filters[options][{$option->id}]",
                  !empty($_GET['filters']['options'][$option->id]) && $_GET['filters']['options'][$option->id] == $item->id ? 'checked' : false,
                  'options' => [
                      'value' => $item->id,
                  ]
              ]);
            }
            break;
          case 'select':
            echo "<label>{$option->name}</label>";
            foreach ($option->items as $item) {
              ?>
              <div class="radio">
                <label>
                  <input type="checkbox" name = 'filters[options][<?= $option->id; ?>][]' value ="<?= $item->id; ?>" <?= !empty($_GET['filters']['options'][$option->id]) && in_array($item->id, $_GET['filters']['options'][$option->id]) ? 'checked' : ''; ?>>
                  <?= $item->value; ?>
                </label>
              </div>
              <?php
            }
            break;
          default:
            $this->widget('Ui\Form/' . ($option->type ? $option->type : 'text'), [
                'label' => $option->name,
                'name' => "filters[options][{$option->id}]",
                'options' => [
                    'value' => !empty($_GET['filters']['options'][$option->id]) ? $_GET['filters']['options'][$option->id] : '',
                ]
            ]);
        }
        ?>
      </div>
      <?php
    }
    ?>
    <button class="btn btn-primary">Применить</button>
  </form>
</div>