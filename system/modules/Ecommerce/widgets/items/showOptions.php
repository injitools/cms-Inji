<?php
$query = $_GET;
$path = Controller::$cur->method != 'itemList' ? '/ecommerce/itemList' : '';
$limit = !empty(App::$cur->Ecommerce->config['default_limit']) ? App::$cur->Ecommerce->config['default_limit'] : 18;

function sortDirectionIcon($type) {
    if (!empty($_GET['sort'][$type])) {
        return ' <small class = "glyphicon glyphicon-triangle-' . ($_GET['sort'][$type] == 'asc' ? 'top' : 'bottom') . '"></small>';
    }
}
?>
<div class="ecommerce-showoptions">
    <div class="row">
        <div class="col-xs-6 ecommerce-showoptions-sort">
            <span class="caption">Сортировка:</span>
            <a href="<?= $path; ?>?<?= http_build_query(array_merge($query, ['sort' => ['price' => 'asc']])); ?>">По цене<?= sortDirectionIcon('price'); ?></a> 
            <a href="<?= $path; ?>?<?= http_build_query(array_merge($query, ['sort' => ['sales' => 'desc']])); ?>">По популярности<?= sortDirectionIcon('sales'); ?></a>
        </div>
        <div class="col-xs-6 text-right ecommerce-showoptions-view">
            <span class="caption">Вид:</span>
            <span class="group">
              <?php
              for ($i = 2; $i < 5; $i++) {
                  $curLimit = $limit * $i;
                  $curQuery = http_build_query(array_merge($query, ['limit' => $curLimit]));
                  echo " <a href='{$path}?{$curQuery}'>";
                  echo!empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '<b>' : '';
                  echo $curLimit;
                  echo!empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '</b>' : '';
                  echo "</a> ";
              }
              if (!empty(App::$cur->ecommerce->config['list_all'])) {
                  $curLimit = 'all';
                  $curQuery = http_build_query(array_merge($query, ['limit' => $curLimit]));
                  echo " <a href='{$path}?{$curQuery}'>";
                  echo !empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '<b>' : '';
                  echo 'Все';
                  echo !empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '</b>' : '';
                  echo "</a> ";
              }
              ?>
            </span>
            <span class="group">
                <a href ='#' onclick="inji.onLoad(function () {
                        $('.items-icons').show();
                        $('.items-table').hide();
                      });
                      return false;" class="glyphicon glyphicon-th-large"></a>
                <a href ='#' onclick="inji.onLoad(function () {
                        $('.items-table').show();
                        $('.items-icons').hide();
                      });
                      return false;" class="glyphicon glyphicon-th-list"></a>
            </span>
        </div>
    </div>
</div>