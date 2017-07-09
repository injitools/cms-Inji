<div class="content">
    <h1>Поиск по сайту</h1>
    <form>
        <div class="form-group">
            <input type="text" name="search" value="<?= !empty($_GET['search']) ? $_GET['search'] : ''; ?>"/>
            <button class="btn btn-primary">Искать</button>
        </div>
    </form>

    <?php
    foreach ($map as $pice) {
        echo "<h2>{$pice['name']}";
        if (!empty($pice['detailSearch'])) {
            echo " <small><a href='{$pice['detailSearch']}'>К подробному поиску</a></small></h2>";
        }
        echo '</h2>';
        if ($pice['result']) {
            echo "<p>Было найдено <b>{$pice['count']}</b> результатов</p>";
            foreach ($pice['result'] as $item) {
                echo "<p>";
                echo "<h3><a href='{$item['href']}'>{$item['title']}</a></h3>";
                echo $item['details'];
                echo "</p>";
            }
            echo "<a class='btn btn-primary btn-block' href ='{$pice['detailSearch']}'>Показать все результаты</a>";
        } else {
            echo "<p><b>Ничего не было найдено</b></p>";
        }

    }
    ?>
</div>
