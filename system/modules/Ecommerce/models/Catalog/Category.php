<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Catalog;
class Category extends \Inji\Model {
    static $cols = [
        'catalog_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'catalog'],
        'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
    ];
    static $labels = [
        'category_id' => 'Раздел товаров'
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

    public function afterSave() {
        $this->catalog->calcItemsCount();
    }
    public function afterDelete() {
        $this->catalog->calcItemsCount();
    }

    static function relations() {
        return [
            'catalog' => [
                'col' => 'catalog_id',
                'model' => 'Inji\Ecommerce\Catalog',
            ],
            'category' => [
                'col' => 'category_id',
                'model' => 'Inji\Ecommerce\Category',
            ]
        ];
    }
}