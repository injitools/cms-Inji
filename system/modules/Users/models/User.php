<?php

/**
 * User
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users;
/**
 * @property int $id
 * @property string $login
 * @property string $mail
 * @property string $pass
 * @property int $parent_id
 * @property int $group_id
 * @property int $role_id
 * @property string $admin_text
 * @property string $activation
 * @property bool $blocked
 * @property string $date_last_active
 * @property string $date_create
 * @property \Users\Group $group
 * @property \Users\Role $role
 * @property \Users\User\Info $info
 * @property \Users\User\Inventory $inventory
 * @property \Users\User\Social[] $socials
 * @property \Users\User $parent
 * @property \Users\User[] $users
 */
class User extends \Model {
    /**
     * @var \Users\User
     */
    public static $cur;
    public static $objectName = "Пользователь";
    public static $labels = [
        'login' => 'Логин',
        'mail' => 'E-Mail',
        'pass' => 'Пароль',
        'parent_id' => 'Пригласивший',
        'group_id' => 'Группа',
        'role_id' => 'Роль',
        'admin_text' => 'Комментарий администратора',
        'activation' => 'Код активации',
        'blocked' => 'Заблокирован',
        'date_last_active' => 'Последняя активность',
        'date_create' => 'Дата регистрации',
    ];
    public static $cols = [
        'login' => ['type' => 'text', 'unique' => true],
        'mail' => ['type' => 'email', 'unique' => true],
        'pass' => ['type' => 'password', 'logging' => 'noValue'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'group_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'group'],
        'role_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'role'],
        'admin_text' => ['type' => 'html'],
        'activation' => ['type' => 'text', 'logging' => false],
        'blocked' => ['type' => 'bool'],
        'date_last_active' => ['type' => 'dateTime', 'logging' => false, 'null' => true, 'emptyValue' => null],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $dataManagers = [
        'manager' => [
            'options' => [
                'access' => [
                    'groups' => [
                        3
                    ]
                ]
            ],
            'cols' => [
                'mail',
                'info:first_name',
                'info:last_name',
                'group_id',
                'role_id',
                'date_last_active',
                'date_create',
            ],
            'sortable' => [
                'mail',
                'info:first_name',
                'info:last_name',
                'group_id',
                'role_id',
                'date_last_active',
                'date_create'
            ],
            'filters' => [
                'mail',
                'info:first_name',
                'info:last_name',
                'role_id',
                'date_last_active',
                'date_create',
            ],
            'searchableCols' => ['mail']
        ],
    ];
    public static $forms = [
        'manager' => [
            'inputs' => [
                'userSearch' => [
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'parent',
                    'label' => 'Пригласивший',
                    'cols' => [
                        'info:first_name',
                        'info:last_name',
                        'info:middle_name',
                        'mail'
                    ],
                    'col' => 'parent_id',
                ],
                'pass' => [
                    'type' => 'changePassword'
                ]
            ],
            'map' => [
                ['login', 'mail'],
                ['group_id', 'role_id'],
                ['userSearch', 'blocked'],
                ['pass'],
                ['activation'],
                ['admin_text'],
                ['form:info:manager']
            ]
        ],
        'profile' => [
            'options' => [
                'access' => [
                    'groups' => [
                        3
                    ],
                    'self' => true
                ]
            ],
            'inputs' => [
                'pass' => [
                    'type' => 'changePassword'
                ]
            ],
            'map' => [
                ['pass'],
                ['form:info:profile']
            ]
        ]
    ];

    public static function relations() {
        return [
            'group' => [
                'model' => 'Users\Group',
                'col' => 'group_id'
            ],
            'role' => [
                'model' => 'Users\Role',
                'col' => 'role_id'
            ],
            'info' => [
                'type' => 'one',
                'model' => 'Users\User\Info',
                'col' => 'user_id'
            ],
            'inventory' => [
                'type' => 'one',
                'model' => 'Users\User\Inventory',
                'col' => 'user_id'
            ],
            'socials' => [
                'type' => 'many',
                'model' => 'Users\User\Social',
                'col' => 'user_id'
            ],
            'parent' => [
                'model' => 'Users\User',
                'col' => 'parent_id'
            ],
            'users' => [
                'type' => 'many',
                'model' => 'Users\User',
                'col' => 'parent_id'
            ]
        ];
    }

    public function name() {
        if ($this->info) {
            return trim($this->info->name());
        } else {
            $this->id;
        }
    }

    public function isAdmin() {
        if ($this->group_id == 3) {
            return true;
        }
        return false;
    }

    public function beforeSave() {
        if (isset($this->_changedParams['user_parent_id'])) {
            $parenthistory = new User\ParentHistory([
                'user_id' => $this->id,
                'old' => $this->_changedParams['user_parent_id'],
                'new' => $this->parent_id
            ]);
            $parenthistory->save();
        }
    }

    public function beforeDelete() {
        if ($this->info) {
            $this->info->delete();
        }
        $sessions = Session::getList(['where' => ['user_id', $this->id]]);
        foreach ($sessions as $session) {
            $session->delete();
        }
        foreach ($this->socials as $social) {
            $social->delete();
        }
    }
}