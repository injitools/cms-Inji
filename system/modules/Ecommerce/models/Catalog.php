<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce;


class Catalog extends \Model {
    static $cols = [
        'name' => ['type' => 'text'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent']
    ];

    static function relations() {
        return [
            'parent' => [
                'model' => 'Ecommerce\Catalog',
                'col' => 'parent_id'
            ],
            'categories'=>[
                'type'=>'many',
                'col'=>'catalog_id',
                'model'=>'Ecommerce\Catalog\Category'
            ]
        ];
    }
}