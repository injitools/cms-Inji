<?php
$query = $_GET;
$path = Controller::$cur->method != 'itemList' ? '/ecommerce/itemList' : '';
$limit = !empty(App::$cur->Ecommerce->config['default_limit']) ? App::$cur->Ecommerce->config['default_limit'] : 18;

function sortDirectionIcon($type) {
    if (!empty($_GET['sort'][$type])) {
        return ' <small class = "glyphicon glyphicon-triangle-' . ($_GET['sort'][$type] == 'asc' ? 'top' : 'bottom') . '"></small>';
    }
}

function sortToggler($type, $default) {
    return empty($_GET['sort'][$type]) ? $default : ($_GET['sort'][$type] == 'desc' ? 'asc' : 'desc');
}

?>
<div class="ecommerce-showoptions">
    <div class="row">
        <div class="col-sm-7 ecommerce-showoptions-sort">
            <span class="caption">Сортировка:</span>
            <a rel="nofollow"
               href="<?= $path; ?>?<?= http_build_query(array_merge($query, ['sort' => ['price' => sortToggler('price', 'asc')]])); ?>">По
                цене<?= sortDirectionIcon('price'); ?></a>
            <a rel="nofollow"
               href="<?= $path; ?>?<?= http_build_query(array_merge($query, ['sort' => ['sales' => sortToggler('sales', 'desc')]])); ?>">По
                популярности<?= sortDirectionIcon('sales'); ?></a>
            <?php
            if (!empty(App::$cur->ecommerce->config['isset_sort'])) {
                ?>
                <a rel="nofollow"
                   href="<?= $path; ?>?<?= http_build_query(array_merge($query, ['sort' => ['isset' => sortToggler('isset', 'desc')]])); ?>">По
                    наличию<?= sortDirectionIcon('isset'); ?></a>
                <?php
            }
            ?>
        </div>
        <div class="col-sm-5 text-right ecommerce-showoptions-view">
            <span class="caption">Вид:</span>
            <span class="group">
              <?php
              for ($i = 2; $i < 5; $i++) {
                  $curLimit = $limit * $i;
                  $curQuery = http_build_query(array_merge($query, ['limit' => $curLimit]));
                  echo " <a rel=\"nofollow\" href='{$path}?{$curQuery}'>";
                  echo !empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '<b>' : '';
                  echo $curLimit;
                  echo !empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '</b>' : '';
                  echo "</a> ";
              }
              if (!empty(App::$cur->ecommerce->config['list_all'])) {
                  $curLimit = 'all';
                  $curQuery = http_build_query(array_merge($query, ['limit' => $curLimit]));
                  echo " <a rel=\"nofollow\" href='{$path}?{$curQuery}'>";
                  echo !empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '<b>' : '';
                  echo 'Все';
                  echo !empty($_GET['limit']) && $_GET['limit'] == $curLimit ? '</b>' : '';
                  echo "</a> ";
              }
              ?>
            </span>
            <span class="group">
                <a rel="nofollow" href='#' onclick="inji.onLoad(function () {
                        $('.items-icons').show();
                        $('.items-table').hide();
                      });
                      return false;" class="glyphicon glyphicon-th-large"></a>
                <a rel="nofollow" href='#' onclick="inji.onLoad(function () {
                        $('.items-table').show();
                        $('.items-icons').hide();
                      });
                      return false;" class="glyphicon glyphicon-th-list"></a>
            </span>
        </div>
    </div>
</div>