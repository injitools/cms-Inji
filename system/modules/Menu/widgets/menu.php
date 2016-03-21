<?php

if (!empty($params[0])) {
    $code = $params[0];
}
if (empty($code)) {
    $code = 'main';
}
$menu = \Menu\Menu::get($code, 'code');
if ($menu) {
    foreach ($menu->items(['where' => ['parent_id', 0], 'order' => ['weight', 'asc']]) as $item) {
        if (urldecode($_SERVER['REQUEST_URI']) == $item->href)
            $active = ' class = "active" ';
        else
            $active = '';
        echo "<li {$active}><a href = '{$item->href}'>{$item->name}</a>";
        if ($item->childs(['order' => ['weight', 'asc']])) {
            echo "<ul>";
            foreach ($item->childs as $item) {
                if (urldecode($_SERVER['REQUEST_URI']) == $item->href)
                    $active = ' class = "active" ';
                else
                    $active = '';
                echo "<li {$active}><a href = '{$item->href}'>{$item->name}</a>";
            }
            echo "</ul>";
        }
        echo "</li>";
    }
}
