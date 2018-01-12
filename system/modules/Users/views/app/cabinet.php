<div class="users">
    <div class="content">
        <div class="users-cabinet">
            <h2 class ='users-cabinet-welcome'>Добро Пожаловать в личный кабинет, <?= \Users\User::$cur->name(); ?></h2>
            <div class = "row">
                <div class = "col-sm-3">
                    <ul class = "nav nav-pills nav-stacked">
                        <li <?= !$activeSection ? 'class="active"' : ''; ?>>
                            <a href="/users/cabinet">Мой кабинет</a>
                        </li>
                        <?php
                        foreach ($sections as $sectionName => $section) {
                            if (!empty($section['name']) && (!empty($section['fullWidget']) || !empty($section['href']))) {
                                ?>
                                <li <?= $activeSection == $sectionName ? 'class="active"' : ''; ?>>
                                    <a href="<?= !empty($section['href']) ? $section['href'] : "/users/cabinet/{$sectionName}"; ?>"><?= $section['name']; ?></a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                        <li>
                            <a href="?logout">Выход</a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-9">
                  <?php
                    if (empty($activeSection) || empty($sections[$activeSection]['fullWidget'])) {
                        foreach ($sections as $section) {
                            if (!empty($section['smallWidget'])) {
                                $widgetName = is_array($section['smallWidget']) ? $section['smallWidget']['widget'] : $section['smallWidget'];
                                $widgetSize = !empty($section['smallWidget']['size']) ? $section['smallWidget']['size'] : 1;
                                ?>
                                <div class="col-sm-<?= $widgetSize * 4; ?>" style="margin-bottom: 10px;"><?= $this->widget($widgetName); ?></div>
                                <?php
                            }
                        }
                    } else {
                        $this->widget($sections[$activeSection]['fullWidget']);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>