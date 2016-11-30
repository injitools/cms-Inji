<?php
$rewards = Money\Reward::getList(['where' => ['active', 1]]);
$itemTypes = [
    'event' => 'Событие'
];
$types = App::$cur->money->getSnippets('rewardType');
foreach ($rewards as $reward) {
    ?>
    <h2><?= $reward->name; ?></h2>
    <div class="row">
        <div class="col-sm-6">
            <h3>Уровни начислений</h3>
            <ul>
              <?php
                foreach ($reward->levels(['order' => ['level', 'asc']]) as $level) {
                    ?>

                    <li><?= !$level->level ? 'Личный' : $level->level; ?>. <?= $types[$level->type]['viewer']($level); ?></li>
                    <?php
                }
                ?>

            </ul>
        </div>
        <div class="col-sm-6">
            <h3>Условия получения</h3>
            <?php
            if (!$reward->conditions) {
                echo '<h4 class="text-success">Нет особых условий для получения вознаграждения</h4>';
            }
            foreach ($reward->conditions as $condition) {
                $complete = $condition->checkComplete();
                ?>
                <h4 class="<?= $complete ? 'text-success' : 'text-danger'; ?>"><?= $condition->name(); ?></h4>
                <ul>
                  <?php
                    foreach ($condition->items as $item) {
                        $itemComplete = $item->checkComplete();
                        switch ($item->type) {
                            case 'event':
                              $name = \Events\Event::get($item->value, 'event')->name();
                                break;
                        }
                        ?>
                        <li> 
                            <b class="<?= $itemComplete ? 'text-success' : 'text-danger'; ?>"><?= $name; ?> <?= $item->recivedCount(); ?></b>/<?= $item->count; ?> <br />
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
