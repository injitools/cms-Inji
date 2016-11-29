<div class="ecommerce-sidebar-categorys">
    <?php
    if (empty($category)) {
        $category = [];
    }
    $tree = new Ui\Tree();
    $tree->ul('\Ecommerce\Category', 0, function($categoryItem) use($category) {
        if ($category && $category->id == $categoryItem->id) {
            $class = 'active';
        } else {
            $class = '';
        }
        return [
            'attributes' => [
                'class' => $class,
            ],
            'text' => "<a {$class} href = '/ecommerce/itemList/{$categoryItem->id}'>{$categoryItem->name}</a>"
        ];
    });
    ?>
</div>