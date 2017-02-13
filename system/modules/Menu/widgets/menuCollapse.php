<?php
if (!empty($params[0])) {
    $code = $params[0];
}
if (empty($code)) {
    $code = 'main';
}
$uid = Tools::randomString();
$childDraws = function($item, $childDraws, $uid, $activeFind) {
    if ($item->childs(['order' => ['weight', 'asc']])) {
        echo "<ul class ='dropdown-menu'>";

        foreach ($item->childs(['order' => ['weight', 'asc']]) as $item) {
            if (urldecode($_SERVER['REQUEST_URI']) == $item->href || $activeFind($item, $activeFind)) {
                $active = ' class = "active" ';
            } else {
                $active = '';
            }
            $childs = $item->childs(['order' => ['weight', 'asc']]);
            echo "<li {$active} " . ($childs ? 'class="dropdown"' : '') . "><a href = '{$item->href}' " . ($childs ? 'data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="multitogle' . $uid . '"' : '') . ">{$item->name}</a>";
            $childDraws($item, $childDraws, $uid, $activeFind);
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
        $childs = $item->childs(['order' => ['weight', 'asc']]);
        echo "<li {$active} " . ($childs ? 'class="dropdown"' : '') . "><a href = '{$item->href}' " . ($childs ? 'data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"' : '') . ">{$item->name}</a>";
        $childDraws($item, $childDraws, $uid, $activeFind);
        echo "</li>";
    }
}
?>
<script>
    inji.onLoad(function () {
      $('a.multitogle<?= $uid; ?>').on("click", function (e) {
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
      });
    });
</script>