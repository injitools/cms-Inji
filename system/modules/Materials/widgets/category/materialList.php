<?php
if (!empty($category) || (!empty($params[0]) && $category = Materials\Category::get((int) $params[0]))) {
    $pages = new Ui\Pages($_GET, ['count' => Materials\Material::getCount(['where' => ['tree_path', $category->tree_path . $category->id . '/%', 'LIKE']]), 'limit' => 10]);
    $materials = Materials\Material::getList(['where' => ['tree_path', $category->tree_path . $category->id . '/%', 'LIKE'], 'order' => ['date_create', 'desc'], 'start' => $pages->params['start'], 'limit' => $pages->params['limit']]);
} else {
    $materials = [];
}
if (!$materials) {
    echo '<div class ="category-materials-empty"><h2 class="text-center">Данная категория пуста</h2></div>';
}
?>
<div class ="category-materials">
    <div class ="row">
      <?php
      $i = 0;
      foreach ($materials as $material) {
          ?>
            <div class = "col-sm-6 category-material">
                <a class="category-material-name" href ="<?= $material->getHref(); ?>"><h3><?= $material->name; ?></h3></a>
                <div class="category-material-preview"><?= $material->preview; ?></div>
                <div class="text-right category-material-more">
                    <a href ="<?= $material->getHref(); ?>"><strong>Читать далее <i class ='glyphicon glyphicon-forward'></i></strong></a>
                </div>
            </div>
            <?php
            if (!( ++$i % 2)) {
                echo '</div><hr /><div class ="row">';
            }
        }
        ?>
    </div>
    <?php
    $pages->draw();
    ?>
</div>