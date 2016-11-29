<div class="money">
    <div class="content">
        <?php
        $transfers = Money\Transfer::getList(['where' => [
                        ['user_id', \Users\User::$cur->id],
                        ['complete', 0],
                        ['canceled', 0]
        ]]);
        if ($transfers) {
            echo "<h3>У вас есть незаконченные переводы</h3>";
            echo "<ul>";
            foreach ($transfers as $transfer) {
                echo "<li><a href = '/money/confirmTransfer/{$transfer->id}'>{$transfer->name()}</a></li>";
            }
            echo "</ul>";
        }
        $form->draw();
        ?>
    </div>
</div>

