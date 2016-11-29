<?php

/**
 * Block
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace TextBlocks;

class Block extends \Model {

    public static $objectName = "Текстовый блок";
    public static $cols = [
        'code' => [
            'type' => 'text'
        ],
        'name' => [
            'type' => 'text'
        ],
        'text' => [
            'type' => 'html'
        ],
        'date_create' => [
            'type' => 'currentDateTime'
        ]
    ];
    public static $labels = [
        'code' => 'код',
        'name' => 'Название',
        'text' => 'Текст',
        'date_create' => 'Дата создания'
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
                'name',
                'code',
                'date_create',
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'options' => [
                'access' => [
                    'groups' => [
                        3
                    ]
                ]
            ],
            'map' => [
                ['name', 'code'],
                ['text'],
            ]
        ]
    ];

    public static function relations() {
        return [
        ];
    }

}
