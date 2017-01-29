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
        'item_bage_id' => 'Наклейка',
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
        'imgs' => 'Фото',
        'date_create' => 'Дата создания'
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
        'item_bage_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'badge'],
        'deleted' => ['type' => 'bool'],
        //Системные
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'weight' => ['type' => 'number'],
        'sales' => ['type' => 'number'],
        'imported' => ['type' => 'text'],
        'tree_path' => ['type' => 'text'],
        'search_index' => ['type' => 'text', 'logging' => false],
        'date_create' => ['type' => 'dateTime'],
        'widget' => ['type' => 'text'],
        'view' => ['type' => 'text'],
        //Менеджеры
        'options' => ['type' => 'dataManager', 'relation' => 'options'],
        'offers' => ['type' => 'dataManager', 'relation' => 'offers'],
        'imgs' => ['type' => 'dataManager', 'relation' => 'images'],
    ];

    public static function simpleItemHandler($request) {
        if ($request) {
            $item = new Item();
            $item->name = $request['name'];
            $item->description = $request['description'];
            $item->category_id = $request['category'];
            $item->save();
            if (!empty($_FILES['ActiveForm_simpleItem']['tmp_name']['Ecommerce\Item']['image'])) {
                $file_id = \App::$primary->files->upload([
                    'tmp_name' => $_FILES['ActiveForm_simpleItem']['tmp_name']['Ecommerce\Item']['image'],
                    'name' => $_FILES['ActiveForm_simpleItem']['name']['Ecommerce\Item']['image'],
                    'type' => $_FILES['ActiveForm_simpleItem']['type']['Ecommerce\Item']['image'],
                    'size' => $_FILES['ActiveForm_simpleItem']['size']['Ecommerce\Item']['image'],
                    'error' => $_FILES['ActiveForm_simpleItem']['error']['Ecommerce\Item']['image'],
                        ], [
                    'upload_code' => 'activeForm:' . 'Ecommerce\Item' . ':' . $item->pk(),
                    'accept_group' => 'image'
                ]);
                if ($file_id) {
                    $item->image_file_id = $file_id;
                    $item->save();
                }
            }
            if (!empty($request['options']['option'])) {
                foreach ($request['options']['option'] as $key => $option_id) {
                    $param = new Item\Param();
                    $param->item_id = $item->id;
                    $param->value = $request['options']['value'][$key];
                    $param->item_option_id = $option_id;
                    $param->save();
                }
            }
            $offer = new Item\Offer();
            $offer->item_id = $item->id;
            $offer->save();
            if (!empty($request['offerOptions']['option'])) {
                foreach ($request['offerOptions']['option'] as $key => $option_id) {
                    $param = new Item\Offer\Param();
                    $param->item_offer_id = $offer->id;
                    $param->value = $request['offerOptions']['value'][$key];
                    $param->item_offer_option_id = $option_id;
                    $param->save();
                }
            }
            $price = new Item\Offer\Price();
            $price->price = $request['price'];
            $price->item_offer_id = $offer->id;
            $price->currency_id = $request['currency'];
            $price->save();
        }
    }

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
                'date_create'
            ],
            'categorys' => [
                'model' => 'Ecommerce\Category',
            ],
            'sortable' => ['date_create'],
            'preSort' => [
                'date_create' => 'desc'
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
                ['best', 'item_bage_id', 'image_file_id'],
                ['description'],
                ['imgs'],
                ['options'],
                ['offers'],
            ]
        ],
        'simpleItem' => [
            'options' => [
                'access' => [
                    'groups' => [
                        3
                    ]
                ],
            ],
            'name' => 'Простой товар с ценой',
            'inputs' => [
                'name' => ['type' => 'text'],
                'description' => ['type' => 'html'],
                'category' => ['type' => 'select', 'source' => 'model', 'model' => 'Ecommerce\Category', 'label' => 'Категория'],
                'image' => ['type' => 'image', 'label' => 'Изображение'],
                'price' => ['type' => 'text', 'label' => 'Цена'],
                'currency' => ['type' => 'select', 'source' => 'model', 'model' => 'Money\Currency', 'label' => 'Валюта'],
                'options' => ['type' => 'dynamicList', 'source' => 'options', 'options' => [
                        'inputs' => [
                            'option' => ['type' => 'select', 'source' => 'model', 'model' => 'Ecommerce\Item\Option', 'label' => 'Свойство'],
                            'value' => ['type' => 'dynamicType', 'typeSource' => 'selfMethod', 'selfMethod' => 'realType', 'label' => 'Значение'],
                        ]
                    ]
                ],
                'offerOptions' => ['type' => 'dynamicList', 'source' => 'options', 'options' => [
                        'inputs' => [
                            'option' => ['type' => 'select', 'source' => 'model', 'model' => 'Ecommerce\Item\Offer\Option', 'label' => 'Свойство предложения'],
                            'value' => ['type' => 'dynamicType', 'typeSource' => 'selfMethod', 'selfMethod' => 'realType', 'label' => 'Значение'],
                        ]
                    ], 'label' => 'Параметры предложения'
                ]
            ],
            'map' => [
                ['name', 'category'],
                ['description'],
                ['image'],
                ['price', 'currency'],
                ['options'],
                ['offerOptions'],
            ],
            'handler' => 'simpleItemHandler'
        ]
    ];

    public function realType() {
        if ($this->option && $this->option->type) {
            $type = $this->option->type;

            if ($type == 'select') {
                return [
                    'type' => 'select',
                    'source' => 'relation',
                    'relation' => 'option:items',
                ];
            }
            return $type;
        }
        return 'text';
    }

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
            'badge' => [
                'model' => 'Ecommerce\Item\Badge',
                'col' => 'item_bage_id'
            ],
            'category' => [
                'model' => 'Ecommerce\Category',
                'col' => 'category_id'
            ],
            'options' => [
                'type' => 'many',
                'model' => 'Ecommerce\Item\Param',
                'col' => 'item_id',
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