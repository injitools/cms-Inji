<?php

$tree = new Ui\Tree();
$tree->itemBodyFn = function($category) {
    return Html::el('a', ['href' => $category->getHref()], $category->name());
};
$tree->itemActiveCheck = function($curCategory) use($category) {
    return $curCategory->id == $category->id || strpos($curCategory->tree_path, "/{$category->id}/") !== false;
};
$tree->draw($category->getRoot());
