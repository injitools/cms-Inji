<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery\Provider;

class ConfigItem extends \Model {

    static $cols = [
        'name' => ['type' => 'text'],
        'value' => ['type' => 'textarea'],
        'delivery_provider_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'deliveryProvider'],
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['name', 'value']
            ]
        ]
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'value']
        ]
    ];

    static function relations() {
        return [
            'deliveryProvider' => [
                'model' => 'Ecommerce\Delivery\Provider',
                'col' => 'delivery_provider_id'
            ]
        ];
    }
}