<?php

return function($step = NULL, $params = []) {

    $groups = [
        [
            'name' => 'Гости',
        ], [
            'name' => 'Зарегистрированые',
        ], [
            'name' => 'Администрация'
        ]
    ];
    $roles = [
        [
            'name' => 'Гость',
        ], [
            'name' => 'Пользователь',
        ], [
            'name' => 'Администратор',
        ]
    ];
    foreach ($groups as $key => $group) {
        $groupObject = new \Users\Group($group);
        $groupObject->save();
        $roleObject = new Users\Role($roles[$key]);
        $roleObject->group_id = $groupObject->id;
        $roleObject->save();
    }

    if (!empty($params['user'])) {
        $user = new Users\User(array(
            'user_login' => $params['user']['user_login'],
            'user_mail' => $params['user']['user_mail'],
            'user_pass' => password_hash($params['user']['user_pass'], PASSWORD_DEFAULT),
            'user_group_id' => $groupObject->id,
            'user_role_id' => $roleObject->id,
        ));
        $user->save();
        $userInfo = new Users\User\Info([
            'user_id' => $user->id,
            'first_name' => 'Администратор'
        ]);
        $userInfo->save();
    } else {
        $user = new Users\User(array(
            'user_login' => 'admin',
            'user_mail' => 'admin@' . idn_to_utf8(INJI_DOMAIN_NAME),
            'user_pass' => password_hash('admin', PASSWORD_DEFAULT),
            'user_group_id' => $groupObject->id,
            'user_role_id' => $roleObject->id,
        ));
        $user->save();
        $userInfo = new Users\User\Info([
            'user_id' => $user->id,
            'first_name' => 'Администратор'
        ]);
        $userInfo->save();
    }
    $socials = [
        [
            'name' => 'Вконтакте',
            'code' => 'vk',
            'object_name' => 'Vk'
        ],
        [
            'name' => 'Google+',
            'code' => 'google',
            'object_name' => 'Google'
        ],
        [
            'name' => 'Facebook',
            'code' => 'facebook',
            'object_name' => 'Facebook'
        ],
        [
            'name' => 'Twitter',
            'code' => 'twitter',
            'object_name' => 'Twitter'
        ],
    ];
    $socialsConfig = [
        [
            [
                'name' => 'appId'
            ],
            [
                'name' => 'secret'
            ]
        ],
        [
            [
                'name' => 'client_id'
            ],
            [
                'name' => 'secret'
            ]
        ],
        [
            [
                'name' => 'appId'
            ],
            [
                'name' => 'secret'
            ]
        ],
        [
            [
                'name' => 'consumer_key'
            ],
            [
                'name' => 'consumer_secret'
            ]
        ],
    ];
    foreach ($socials as $key => $social) {
        $socialObject = new Users\Social($social);
        $socialObject->save();
        foreach ($socialsConfig[$key] as $config) {
            $configObject = new \Users\Social\Config($config);
            $configObject->social_id = $socialObject->id;
            $configObject->save();
        }
    }
};
