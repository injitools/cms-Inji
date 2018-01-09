<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li class="text-center" style="text-indent: 0"><a href="/" style="font-size:16px;">Перейти на сайт</a></li>
        <div class="sidebar-brand">
            <div class="col-xs-4">
                <img
                    src="<?= Inji\Statics::file(!empty(\Inji\App::$primary->config['site']['site_logo']) ? \Inji\App::$primary->config['site']['site_logo'] : ''); ?>"
                    class="img-responsive"/>
            </div>
            <div class="col-xs-8">
                <?= !empty(\Inji\App::$primary->config['site']['name']) ? \Inji\App::$primary->config['site']['name'] : 'Название не задано'; ?>
                <br/>
                <?= !empty(\Inji\App::$primary->config['site']['email']) ? \Inji\App::$primary->config['site']['email'] : 'E-mail не задан'; ?>
                <?php
                $resolved = Inji\Router::resolvePath('/admin/dashboard/siteConfig');
                if (isset($resolved['controller']) && $resolved['controller']->checkAccess()) {
                    echo "<br/><a href=\"/admin/dashboard/siteConfig\">Редактировать</a>";
                }
                ?>
            </div>
            <div class="clearfix"></div>

        </div>
        <?php

        $where = [];
        if (class_exists('Users\User')) {
            $where[] = ['group_id', Inji\Users\User::$cur->group_id];
            if (Inji\Users\User::$cur->group_id == 3) {
                $where[] = ['group_id', 0, '=', 'OR'];
            }
        } else {
            $where[] = ['group_id', 0, '='];
        }
        ?>
        <li>
            <a href="/admin">Панель управления</a>
        </li>
        <?php
        $menu = Inji\Menu\Menu::get([['code', 'sidebarMenu'], $where]);
        if ($menu) {
            foreach ($menu->items(['where' => ['parent_id', 0], 'order' => ['weight', 'asc']]) as $item) {
                echo "<li><a href = '{$item->href}'>{$item->name}</a>";
                $childItems = Menu\Item::getList(['where' => ['parent_id', $item->id]]);
                if ($childItems) {
                    echo "<ul>";
                    foreach ($childItems as $item) {
                        echo "<li><a href = '{$item->href}'>{$item->name}</a>";
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
        }
        ?>
        <li>
            <a href="?logout">Выйти</a>
        </li>
    </ul>
</div>