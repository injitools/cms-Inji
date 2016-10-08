<?php

/**
 * Item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;

class Item extends \Model {

  public static $categoryModel = 'Ecommerce\Category';
  public static $objectName = 'Товар';
  public static $labels = [
      'name' => 'Название',
      'alias' => 'Алиас',
      'category_id' => 'Раздел',
      'description' => 'Описание',
      'item_type_id' => 'Тип товара',
      'image_file_id' => 'Изображение',
      'best' => 'Лучшее предложение',
      'options' => 'Параметры',
      'offers' => 'Торговые предложения',
      'widget' => 'Виджет для отображения в каталоге',
      'view' => 'Шаблон для отображения полной информации',
      'deleted' => 'Удален',
      'imgs' => 'Фото'
  ];
  public static $cols = [
      //Основные параметры
      'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
      'image_file_id' => ['type' => 'image'],
      'name' => ['type' => 'text'],
      'alias' => ['type' => 'text'],
      'description' => ['type' => 'html'],
      'item_type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
      'best' => ['type' => 'bool'],
      'deleted' => ['type' => 'bool'],
      //Системные
      'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
      'weight' => ['type' => 'number'],
      'sales' => ['type' => 'number'],
      'imported' => ['type' => 'text'],
      'tree_path' => ['type' => 'text'],
      'search_index' => ['type' => 'text'],
      'date_create' => ['type' => 'dateTime'],
      'widget' => ['type' => 'text'],
      'view' => ['type' => 'text'],
      //Менеджеры
      'options' => ['type' => 'dataManager', 'relation' => 'options'],
      'offers' => ['type' => 'dataManager', 'relation' => 'offers'],
      'imgs' => ['type' => 'dataManager', 'relation' => 'images'],
  ];
  public static $dataManagers = [
      'manager' => [
          'name' => 'Товары',
          'cols' => [
              'name',
              'imgs',
              'category_id',
              'item_type_id',
              'best',
              'deleted',
              'options',
              'offers',
          ],
          'categorys' => [
              'model' => 'Ecommerce\Category',
          ],
          'sortMode' => true
      ]
  ];
  public static $forms = [
      'manager' => [
          'map' => [
              ['name', 'alias'],
              ['category_id', 'item_type_id', 'deleted'],
              ['widget', 'view'],
              ['best', 'image_file_id'],
              ['description'],
              ['imgs'],
              ['options'],
              ['offers'],
          ]
  ]];

  public static function indexes() {
    return [
        'ecommerce_item_item_category_id' => [
            'type' => 'INDEX',
            'cols' => [
                'item_category_id'
            ]
        ],
        'inji_ecommerce_item_item_tree_path' => [
            'type' => 'INDEX',
            'cols' => [
                'item_tree_path(255)'
            ]
        ],
        'ecommerce_item_item_search_index' => [
            'type' => 'INDEX',
            'cols' => [
                'item_search_index(255)'
            ]
        ],
    ];
  }

  public function beforeSave() {

    if ($this->id) {
      $this->search_index = $this->name . ' ';
      if ($this->category) {
        $this->search_index .= $this->category->name . ' ';
      }
      if ($this->options) {
        foreach ($this->options as $option) {
          if ($option->item_option_searchable && $option->value) {
            if ($option->item_option_type != 'select') {
              $this->search_index .= $option->value . ' ';
            } elseif (!empty($option->option->items[$option->value])) {
              $option->option->items[$option->value]->value . ' ';
            }
          }
        }
      }
      if ($this->offers) {
        foreach ($this->offers as $offer) {
          if ($offer->options) {
            foreach ($offer->options as $option) {
              if ($option->item_offer_option_searchable && $option->value) {
                if ($option->item_offer_option_type != 'select') {
                  $this->search_index .= $option->value . ' ';
                } elseif (!empty($option->option->items[$option->value])) {
                  $option->option->items[$option->value]->value . ' ';
                }
              }
            }
          }
        }
      }
    }
  }

  public static function relations() {

    return [
        'category' => [
            'model' => 'Ecommerce\Category',
            'col' => 'category_id'
        ],
        'options' => [
            'type' => 'many',
            'model' => 'Ecommerce\Item\Param',
            'col' => 'item_id',
            //'resultKey' => 'code',
            'resultKey' => 'item_option_id',
            'join' => [Item\Option::table(), Item\Option::index() . ' = ' . Item\Param::colPrefix() . Item\Option::index()]
        ],
        'offers' => [
            'type' => 'many',
            'model' => 'Ecommerce\Item\Offer',
            'col' => 'item_id',
        ],
        'type' => [
            'model' => 'Ecommerce\Item\Type',
            'col' => 'item_type_id',
        ],
        'image' => [
            'model' => 'Files\File',
            'col' => 'image_file_id'
        ],
        'images' => [
            'type' => 'many',
            'model' => 'Ecommerce\Item\Image',
            'col' => 'item_id'
        ],
        'user' => [
            'model' => 'Users\User',
            'col' => 'user_id'
        ]
    ];
  }

  public function getPrice() {
    $offers = $this->offers(['key' => false]);
    $curPrice = null;

    foreach ($offers[0]->prices as $price) {
      if (!$price->type) {
        $curPrice = $price;
      } elseif (
              (!$price->type->roles && !$curPrice) ||
              ($price->type->roles && !$curPrice && strpos($price->type->roles, "|" . \Users\User::$cur->role_id . "|") !== false)
      ) {
        $curPrice = $price;
      }
    }
    return $curPrice;
  }

  public function name() {
    if (!empty(\App::$primary->ecommerce->config['item_option_as_name'])) {
      $param = Item\Param::get([['item_id', $this->id], ['item_option_id', \App::$primary->ecommerce->config['item_option_as_name']]]);
      if ($param && $param->value) {
        return $param->value;
      }
    }
    return $this->name;
  }

  public function beforeDelete() {
    if ($this->id) {
      if ($this->options) {
        foreach ($this->options as $option) {
          $option->delete();
        }
      }
      if ($this->offers) {
        foreach ($this->offers as $offer) {
          $offer->delete();
        }
      }
      if ($this->image) {
        $this->image->delete();
      }
    }
  }

  public function getHref() {
    return "/ecommerce/view/{$this->pk()}";
  }

}
