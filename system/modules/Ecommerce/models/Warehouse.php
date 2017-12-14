<?php

/**
 * Warehouse
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $addresses
 * @property string $contacts
 * @property int $city_id
 * @property string $date_create
 */
class Warehouse extends \Model {

    public static $objectName = 'Склад';
    public static $labels = [
        'name' => 'Название',
        'time' => 'Время работы',
        'type' => 'Тип',
        'addresses' => 'Адрес',
        'city_id' => 'Город',
        'contacts' => 'Контакты',
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'select', 'source' => 'array', 'sourceArray' => ['sale' => 'Продажи', 'local' => 'Внутренний']],
        'city_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'city'],
        'addresses' => ['type' => 'textarea'],
        'contacts' => ['type' => 'textarea'],
        'time' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Склады',
            'cols' => [
                'name',
                'city_id',
                'type',
                'addresses',
                'contacts',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'city'],
                ['type', 'time'],
                ['addresses'],
                ['contacts'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'city' => [
                'model' => 'Geography/City',
                'col' => 'city_id'
            ]
        ];
    }

}
