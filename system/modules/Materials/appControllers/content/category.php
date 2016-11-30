<div class ="materials-category">
    <div class="row">
        <div class="col-md-3">
          <?php
            \Ui\Tree::ul($category->getRoot(), 0, function($category) {
                echo "<a href='{$category->getHref()}'> {$category->name()}</a>";
            });
            ?>
        </div>
        <div class="col-md-9">
            <div class="content">
                <h2 class ='category-name'><?= $category->name; ?></h2>
                <div class="material-description">
                  <?= Ui\FastEdit::block($category, 'description', null, true); ?>
                </div>
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
            </div>
        </div>
    </div>
</div>