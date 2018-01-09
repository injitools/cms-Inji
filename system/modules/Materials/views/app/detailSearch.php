<div class="content">
    <h1>Поиск по материалам</h1>
    <form>
        <div class="form-group">
            <input type="text" name="search" value="<?= !empty($_GET['search']) ? $_GET['search'] : ''; ?>"/>
            <button class="btn btn-primary">Искать</button>
        </div>
    </form>

    <?php
    if (!empty($result['items'])) {
        echo "<p>Было найдено <b>{$result['count']}</b> результатов</p>";
        foreach ($result['items'] as $item) {
            echo "<p>";
            echo "<h3><a href='/materials/view/{$item->id}'>{$item->name()}</a></h3>";
            $details = '<div>';
            $shortdes = mb_substr(strip_tags($item->text), 0, 300);
            $shortdes = mb_substr($shortdes, 0, mb_strrpos($shortdes, ' '));
            $details .= $shortdes;
            if (mb_strlen($item->description) > $shortdes) {
                $details .= '...';
            }
            $details .= '</div>';
            echo $details;
            echo "</p>";
        }
        $result['pages']->draw();
    } else {
        echo "<p><b>Ничего не было найдено</b></p>";
    }
    ?>
</div>
