<?php

/**
 * Delivery
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce;
/**
 * Class Delivery
 *
 * @property int $id
 * @property int $delivery_provider_id
 * @property string $name
 * @property string $price_type
 * @property number $price
 * @property int $currency_id
 * @property string $price_text
 * @property number $max_cart_price
 * @property int $icon_file_id
 * @property string $info
 * @property bool $disabled
 * @property bool $default
 * @property number $weight
 * @property string $date_create
 * @property \Files\File $icon
 * @property \Money\Currency $currency
 * @property \Ecommerce\Delivery\Field[] $fields
 * @property \Ecommerce\Delivery\Price[] $prices
 */
class Delivery extends \Inji\Model {

    public static $objectName = 'Доставка';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'delivery_provider_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'provider'],
        'price' => ['type' => 'decimal'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'price_text' => ['type' => 'textarea'],
        'max_cart_price' => ['type' => 'decimal'],
        'icon_file_id' => ['type' => 'image'],
        'info' => ['type' => 'html'],
        'disabled' => ['type' => 'bool'],
        'default' => ['type' => 'bool'],
        //Системные
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'citiesMgr' => ['type' => 'dataManager', 'relation' => 'cities'],
        'field' => ['type' => 'dataManager', 'relation' => 'fields'],
        'priceChanger' => ['type' => 'dataManager', 'relation' => 'prices']
    ];
    public static $labels = [
        'name' => 'Название',
        'price_type' => 'Тип расчета стоимости',
        'price' => 'Стоимость',
        'price_text' => 'Текстовое описание стоимости (отображается вместо цены)',
        'max_cart_price' => 'Басплатно при',
        'icon_file_id' => 'Иконка',
        'currency_id' => 'Валюта',
        'info' => 'Дополнительная информация',
        'priceChanger' => 'Градация стоимости',
        'field' => 'Поля',
        'disabled' => 'Отключено',
        'default' => 'По умолчанию'
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Варианты доставки',
            'cols' => [
                'name',
                'delivery_provider_id',
                'price',
                'currency_id',
                'max_cart_price',
                'disabled',
                'default',
                'field',
                'priceChanger',
                'citiesMgr'
            ],
            'sortMode' => true
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'delivery_provider_id'],
                ['default', 'disabled'],
                ['price', 'currency_id'],
                ['max_cart_price', 'icon_file_id'],
                ['price_text'],
                ['info'],
                ['priceChanger'],
                ['field'],
                ['citiesMgr']
            ]
        ]
    ];

    public static function relations() {
        return [
            'icon' => [
                'model' => 'Inji\Files\File',
                'col' => 'icon_file_id'
            ],
            'currency' => [
                'model' => 'Inji\Money\Currency',
                'col' => 'currency_id'
            ],
            'fields' => [
                'type' => 'relModel',
                'model' => 'Inji\Ecommerce\Delivery\Field',
                'relModel' => 'Ecommerce\Delivery\DeliveryFieldLink'
            ],
            'cities' => [
                'type' => 'relModel',
                'model' => 'Inji\Geography\City',
                'relModel' => 'Ecommerce\Delivery\CityLink'
            ],
            'prices' => [
                'type' => 'many',
                'model' => 'Inji\Ecommerce\Delivery\Price',
                'col' => 'delivery_id'
            ],
            'provider' => [
                'model' => 'Inji\Ecommerce\Delivery\Provider',
                'col' => 'delivery_provider_id'
            ]
        ];
    }

    public function providerHelper() {
        if ($this->provider) {
            return 'Ecommerce\DeliveryProvider\\' . $this->provider->object;
        }
        return false;
    }

    function beforeSave() {
        if ($this->default) {
            Delivery::update(['default' => 0]);
        }
    }
}
