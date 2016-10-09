<?php

/**
 * Link between delivery and link
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Delivery;

class Price extends \Model {

  public static $labels = [
      'delivery_id' => 'Тип доставки',
      'cart_price' => 'Сумма корзины',
      'price' => 'Стоимость доставки',
      'currency_id' => 'Валюта',
  ];
  public static $cols = [
      'delivery_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'delivery'],
      'cart_price' => ['type' => 'number'],
      'price' => ['type' => 'number'],
      'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
      'date_create' => ['type' => 'dateTime']
  ];
  public static $dataManagers = [
      'manager' => [
          'name' => 'Цены для стоимости корзин',
          'cols' => ['delivery_id', 'cart_price', 'price', 'currency_id'],
      ]
  ];
  public static $forms = [
      'manager' => [
          'map' => [
              ['delivery_id', 'currency_id'],
              ['cart_price', 'price'],
          ]
      ]
  ];

  public static function relations() {
    return [
        'delivery' => [
            'model' => 'Ecommerce\Delivery',
            'col' => 'delivery_id'
        ],
        'currency' => [
            'model' => 'Money\Currency',
            'col' => 'currency_id'
        ],
    ];
  }

}
