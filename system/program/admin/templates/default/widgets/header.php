<div id="header-wrapper">
    <?php
    if (class_exists('Users\User')) {
        Inji\App::$cur->ui;
        ?>
        <div class="userWidget pull-right">
            <img
                src="<?= Inji\Statics::file(Inji\Users\User::$cur->info->photo ? Inji\Users\User::$cur->info->photo->path : '', '29x29', 'q'); ?>"
                class="img-circle user-avatar"/>

            <a href="#"
               onclick="inji.Ui.forms.popUp('Users\\User:<?= Inji\Users\User::$cur->id; ?>', {formName: 'profile'});return false;"><?= Inji\Users\User::$cur->name(); ?></a>

            <a href="?logout">Выйти</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <?php
        $where[] = ['group_id', Inji\Users\User::$cur->group_id];
        if (Inji\Users\User::$cur->group_id == 3) {
            $where[] = ['group_id', 0, '=', 'OR'];
        }
    }
    ?>

</div>