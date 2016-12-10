<div class ="materials-material content">
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