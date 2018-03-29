<?php

/**
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User;
/**
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property string $country
 * @property string $city
 * @property int $user_id
 * @property int $sex
 * @property int $photo_file_id
 * @property string $bday
 */
class Info extends \Model {

    public static $objectName = "Расширенная информация";
    public static $cols = [
        'first_name' => ['type' => 'text'],
        'last_name' => ['type' => 'text'],
        'middle_name' => ['type' => 'text'],
        'phone' => ['type' => 'text'],
        'country' => ['type' => 'text'],
        'city' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user', 'showCol' => 'name'],
        'sex' => [
            'type' => 'select',
            'source' => 'array',
            'sourceArray' => [
                0 => 'Не определился',
                1 => 'Мужчина',
                2 => 'Женщина'
            ]
        ],
        'photo_file_id' => ['type' => 'image'],
        'bday' => ['type' => 'date', 'null' => true],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['first_name', 'middle_name', 'last_name'],
                ['country', 'city'],
                ['sex', 'photo_file_id'],
                ['bday', 'phone']
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
            'map' => [
                ['first_name', 'middle_name', 'last_name'],
                ['country', 'city'],
                ['sex', 'photo_file_id'],
                ['bday', 'phone']
            ]
        ]
    ];
    public static $labels = [
        'first_name' => 'Имя',
        'last_name' => 'Фамилия',
        'middle_name' => 'Отчество',
        'sex' => 'Пол',
        'photo_file_id' => 'Фото',
        'bday' => 'Дата рождения',
        'phone' => 'Мобильный телефон',
        'country' => 'Страна',
        'city' => 'Город',
        'user_id' => 'Пользователь'
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'photo' => [
                'model' => 'Files\File',
                'col' => 'photo_file_id'
            ]
        ];
    }

    public function name() {
        if ($this->first_name . $this->last_name . $this->middle_name) {
            $name = '';
            if ($this->first_name) {
                $name .= $this->first_name;
            }
            if ($this->middle_name) {
                $name .= ($name ? ' ' : '') . $this->middle_name;
            }
            if ($this->last_name) {
                $name .= ($name ? ' ' : '') . $this->last_name;
            }
            return $name;
        } else {
            return $this->user_id;
        }
    }

}
