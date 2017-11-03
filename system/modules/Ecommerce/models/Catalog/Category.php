<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce\Catalog;
class Category extends \Model {
    static $cols = [
        'catalog_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'catalog'],
        'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
    ];
    static $labels = [
        'catalog_id' => 'Раздел товаров'
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['category_id']
        ]
    ];
    static $forms = [
        'manager' => [
            'map' => [['category_id']]
        ]
    ];

    static function relations() {
        return [
            'catalog' => [
                'col' => 'catalog_id',
                'model' => 'Ecommerce\Catalog',
            ],
            'category' => [
                'col' => 'category_id',
                'model' => 'Ecommerce\Category',
            ]
        ];
    }
}