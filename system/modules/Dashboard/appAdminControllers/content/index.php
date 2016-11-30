<div class="dashboard-widgets">
    <h4 class ='dashboard-widgets-welcome'>Добро Пожаловать в панель управления<?= class_exists('\Users\User') ? ', ' . \Users\User::$cur->name() : ''; ?></h4>
    <div class = "row">
      <?php
        $rowSum = 0;
        foreach ($sections as $section) {
            if (empty($section['widget'])) {
                continue;
            }

            $widgetSize = !empty($section['size']) ? $section['size'] : 1;
            $rowSum += $widgetSize;
            ?>
            <div class="col-sm-<?= $widgetSize * 4; ?>" style="margin-bottom: 10px;"><?= $section['widget'](); ?></div>
            <?php
            if ($rowSum >= 3) {
                $rowSum = 0;
                echo '</div><div class = "row">';
            }
        }
        ?>
    </div>
</div>