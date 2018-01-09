<?php

/**
 * City Model
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Geography;
/**
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property bool $default
 * @property int $country_id
 * @property string $date_create
 * @property \Geography\City\Data[] $datas
 * @property \Geography\Country $country
 */
class City extends \Inji\Model {
    /**
     * @var City
     */
    public static $cur = null;
    public static $objectName = 'Город';
    public static $labels = [
        'name' => 'Название',
        'alias' => 'Алиас',
        'default' => 'По умолчанию',
        'country_id' => 'Страна',
        'data' => 'Данные',
        'date_create' => 'Дата создания',
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'default' => ['type' => 'bool'],
        'country_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'country'],
        'data' => ['type' => 'dataManager', 'relation' => 'datas'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'alias', 'default', 'data', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'alias'],
                ['default', 'country_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'country' => [
                'model' => 'Geography\Country',
                'col' => 'country_id'
            ],
            'datas' => [
                'type' => 'many',
                'model' => 'Geography\City\Data',
                'col' => 'city_id',
                'resultKey' => 'code'
            ]
        ];
    }

}
