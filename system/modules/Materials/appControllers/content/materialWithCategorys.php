<div class ="materials-material">
    <div class="row">
        <div class="col-md-3">
          <?php
            $category = $material->category;
            \Ui\Tree::ul($category->getRoot(), 0, function($category) {
                echo "<a href='{$category->getHref()}'> {$category->name()}</a>";
            });
            ?>
        </div>
        <div class="col-md-9">
            <div class="content">
                <h2 class="material-name"><?= $material->name; ?></h2>
                <div class="material-text">
                    <?= Ui\FastEdit::block($material, 'text', null, true); ?>
                </div>
                <?php
                if ($material->links) {
                    echo '<ul class = "material-links">';
                    foreach ($material->links as $materialLink) {
                        $href = $materialLink->linkedMaterial->alias;
                        if ($href == '') {
                            $href = '/';
                        }
                        $name = $materialLink->name ? $materialLink->name : $materialLink->linkedMaterial->name;
                        echo "<li><a href = '{$href}'>{$name}</a></li>";
                    }
                    echo '</ul>';
                }
                ?>
            </div>
        </div>
    </div>
</div>