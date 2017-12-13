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
 * @property string $date_create
 */
class Warehouse extends \Model {

    public static $objectName = 'Склад';
    public static $labels = [
        'name' => 'Название',
        'time' => 'Время работы',
        'type' => 'Тип',
        'addresses' => 'Адрес',
        'contacts' => 'Контакты',
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'select', 'source' => 'array', 'sourceArray' => ['sale' => 'Продажи', 'local' => 'Внутренний']],
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
                'type',
                'addresses',
                'contacts',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name'],
                ['type', 'time'],
                ['addresses'],
                ['contacts'],
            ]
        ]];

}
