<?php

if (!empty($params[0])) {
    $code = $params[0];
}
if (empty($code)) {
    $code = 'main';
}
$childDraws = function($item, $childDraws, $activeFind) {
    if ($item->childs(['order' => ['weight', 'asc']])) {
        echo "<ul class ='list-unstyled'>";
        foreach ($item->childs(['order' => ['weight', 'asc']]) as $item) {
            if (urldecode($_SERVER['REQUEST_URI']) == $item->href || $activeFind($item, $activeFind)) {
                $active = ' class = "active" ';
            } else {
                $active = '';
            }
            echo "<li {$active}><a href = '{$item->href}'>{$item->name}</a>";
            $childDraws($item, $childDraws, $activeFind);
        }
        echo "</ul>";
    }
};
$activeFind = function($item, $activeFind) {
    foreach ($item->childs(['order' => ['weight', 'asc']]) as $item) {
        if (urldecode($_SERVER['REQUEST_URI']) == $item->href) {
            return true;
        }
        return $activeFind($item, $activeFind);
    }
    return false;
};

$menu = \Menu\Menu::get($code, 'code');
if ($menu) {
    foreach ($menu->items(['where' => ['parent_id', 0], 'order' => ['weight', 'asc']]) as $item) {
        if (urldecode($_SERVER['REQUEST_URI']) == $item->href || $activeFind($item, $activeFind)) {
            $active = ' class = "active" ';
        } else {
            $active = '';
        }
        echo "<li {$active}><a href = '{$item->href}'>{$item->name}</a>";
        $childDraws($item, $childDraws, $activeFind);
        echo "</li>";
    }
}

