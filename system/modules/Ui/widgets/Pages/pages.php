<ul class="<?= $class; ?>"><?php
    if ($pagesInstance->params['page'] > 1) {
        $getArr['page'] = $pagesInstance->params['page'] - 1;
        echo "<li><a href = '{$pagesInstance->options['url']}?" . http_build_query($getArr) . "'>&larr;</a></li>";
    }

    for ($i = 1; $i <= $pagesInstance->params['pages']; $i++) {
        if (($i >= $pagesInstance->params['page'] - 3 && $i <= $pagesInstance->params['page'] + 3) || $i == 1 || $i == $pagesInstance->params['pages']) {
            echo '<li ';
            if ($pagesInstance->params['page'] == $i) {
                            echo 'class = "active"';
            }
            echo ">";
            $getArr['page'] = $i;
            echo "<a href = '{$pagesInstance->options['url']}?" . http_build_query($getArr) . "'>{$i}</a></li>";
        } elseif ($i == $pagesInstance->params['page'] - 7 && $i > 1) {
            $getArr['page'] = round($pagesInstance->params['page'] / 2);
            echo "<li><a href = '{$pagesInstance->options['url']}?" . http_build_query($getArr) . "'>...</a></li>";
        } elseif ($i == $pagesInstance->params['page'] + 7 && $i < $pagesInstance->params['pages']) {
            $getArr['page'] = round(($pagesInstance->params['pages'] - $pagesInstance->params['page']) / 2) + $pagesInstance->params['page'];
            echo "<li><a href = '{$pagesInstance->options['url']}?" . http_build_query($getArr) . "'>...</a></li>";
        }
    }
    if ($pagesInstance->params['page'] < $pagesInstance->params['pages']) {
        $getArr['page'] = $pagesInstance->params['page'] + 1;
        echo "<li><a href = '{$pagesInstance->options['url']}?" . http_build_query($getArr) . "'>&rarr;</a></li>";
    }
    ?></ul>