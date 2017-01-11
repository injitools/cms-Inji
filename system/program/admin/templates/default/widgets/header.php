<div id="header-wrapper">
    <?php
    if (class_exists('Users\User')) {
        App::$cur->ui;
        ?>
        <div class="userWidget pull-right">
            <img
                src="<?= Statics::file(Users\User::$cur->info->photo ? Users\User::$cur->info->photo->path : '', '29x29', 'q'); ?>"
                class="img-circle user-avatar"/>

            <a href="#"
               onclick="inji.Ui.forms.popUp('Users\\User:<?= Users\User::$cur->id; ?>', {formName: 'profile'});return false;"><?= Users\User::$cur->name(); ?></a>

            <a href="?logout">Выйти</a>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <?php
        $where[] = ['group_id', Users\User::$cur->group_id];
        if (Users\User::$cur->group_id == 3) {
            $where[] = ['group_id', 0, '=', 'OR'];
        }
    }
    ?>

</div>