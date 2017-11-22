<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery;

/**
 * Class Provider
 * @property int $id
 * @property string $name
 * @property string $object
 * @property bool $active
 * @property \Ecommerce\Delivery\Provider\ConfigItem[] $configs
 */
class Provider extends \Model {
    static $objectName = 'Провайдер доставки';
    static $cols = [
        'name' => ['type' => 'text'],
        'object' => ['type' => 'text'],
        'active' => ['type' => 'bool'],
        'config' => ['type' => 'dataManager', 'relation' => 'configs'],
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['name', 'object', 'active'],
                ['config']
            ]
        ]
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'active', 'object', 'config']
        ]
    ];

    static function relations() {
        return [
            'configs' => [
                'type' => 'many',
                'model' => 'Ecommerce\Delivery\Provider\ConfigItem',
                'col' => 'delivery_provider_id'
            ]
        ];
    }
}