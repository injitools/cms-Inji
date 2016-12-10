<?php
return [
    'accessCheck' => function() {
        if (!class_exists('Users\User')) {
            return false;
        }
        $access = null;
        $path = [
            'accessTree',
            'appAdmin',
            'Dashboard',
            'siteConfig'
        ];
        if (isset(App::$cur->Dashboard->config['access'])) {
            $accesses = App::$cur->Dashboard->config['access'];
            $access = App::$cur->Access->resolvePath($accesses, $path, '_access');
        }
        if (is_null($access) && isset(App::$cur->Access->config['access'])) {
            $accesses = App::$cur->Access->config['access'];
            $access = App::$cur->Access->resolvePath($accesses, $path, '_access');
        }
        if (is_null($access)) {
            $access = [];
        }

        $user = Users\User::$cur;

        if (empty($access)) {
            return true;
        }

        if ((!$user->group_id && !empty($access)) || ($user->group_id && !empty($access) && !in_array($user->group_id, $access))) {
            return false;
        }
        return true;
    },
            'widget' => function() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Общие настройки сайта</div>
            <div class="panel-body">
                <p>Название: <?= !empty(App::$primary->config['site']['name']) ? App::$primary->config['site']['name'] : 'Не указано'; ?></p>
                <p>Email: <?= !empty(App::$primary->config['site']['email']) ? App::$primary->config['site']['email'] : 'Не указано'; ?></p>
            </div>
            <div class="panel-footer">
                <a href ="/admin/dashboard/siteConfig">Изменить</a>
            </div>
        </div>
        <?php
    }
        ];
        