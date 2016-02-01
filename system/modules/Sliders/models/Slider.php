<?php
/**
 * Slider
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
namespace Sliders;

class Slider extends \Model
{
    public static $objectName = "Слайдер";
    public static $cols = [
        'name' => [
            'type' => 'text'
        ],
        'description' => [
            'type' => 'html'
        ],
        'user_id' => [
            'type' => 'select',
            'source' => 'relation',
            'relation' => 'user'
        ],
        'image_file_id' => [
            'type' => 'image'
        ],
        'date_create' => [
            'type' => 'dateTime'
        ],
        'slide' => [
            'type' => 'select',
            'source' => 'relation',
            'relation' => 'slides'
        ],
    ];
    public static $labels = [
        'name' => 'Название',
        'date_create' => 'Дата создания слайдера',
        'slide' => 'Слайды',
        'description' => 'Описание',
        'user_id' => 'Создатель',
        'image_file_id' => 'Изображение',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Слайдеры',
            'cols' => [
                'name', 'slide', 'user_id', 'date_create'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'image_file_id'],
                ['description']
            ]
        ]
    ];

    public static function relations()
    {
        return [
            'slides' => [
                'type' => 'many',
                'model' => 'Sliders\Slide',
                'col' => 'slider_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ]
        ];
    }

}
