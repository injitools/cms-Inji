<?php

if (App::$cur->users->config['invites']) {
    return [
        'name' => 'Мои партнеры',
        'fullWidget' => 'Users\cabinet/usersTree'
    ];
}
return [];
