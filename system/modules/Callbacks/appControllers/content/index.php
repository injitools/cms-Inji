<div class = 'callbacks'>
    <h1>Отзывы</h1>
    <?php
    $callbacks = Callbacks\Callback::getList(['where' => ['view', '1'], 'order' => ['weight', 'asc']]);
    foreach ($callbacks as $callback) {
        echo "<h3>{$callback->name}<div class = 'time'>{$callback->date_create}</div></h3>";
        echo "<p>" . nl2br($callback->text) . "</p>";
    }
    ?>
</div>
<h3>Можете и вы в свободной форме оставить свой отзыв</h3>
<?php
$this->widget('Callbacks\form');
