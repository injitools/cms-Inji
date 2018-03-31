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
/**
 * @property int $id
 * @property int $category_id
 * @property int $image_file_id
 * @property string $name
 * @property string $subtitle
 * @property string $alias
 * @property string $description
 * @property int $item_type_id
 * @property bool $best
 * @property int $item_badge_id
 * @property bool $deleted
 * @property int $user_id
 * @property int $weight
 * @property int $sales
 * @property string $imported
 * @property string $tree_path
 * @property string $search_index
 * @property string $date_create
 * @property string $widget
 * @property string $view
 *
 * @property-read \Ecommerce\Item\Badge $badge
 * @property-read \Ecommerce\Category $category
 * @property-read \Ecommerce\Item\Param[] $options
 * @method \Ecommerce\Item\Param[] options($options)
 * @property-read \Ecommerce\Item\Offer[] $offers
 * @method  \Ecommerce\Item\Offer[] offers($options)
 * @property-read \Ecommerce\Item\Type $type
 * @property-read \Files\File $image
 * @property-read \Ecommerce\Item\Image[] $images
 * @property-read \Users\User $user
 */
class Item extends \Model {

    public static $categoryModel = 'Ecommerce\Category';
    public static $objectName = 'Товар';
    public static $labels = [
        'name' => 'Название',
        'title' => 'Торговое название',
        'subtitle' => 'Подзаголовок',
        'alias' => 'Алиас',
        'item_badge_id' => 'Наклейка',
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
        'title' => ['type' => 'text'],
        'subtitle' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'description' => ['type' => 'html'],
        'item_type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
        'best' => ['type' => 'bool'],
        'item_badge_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'badge'],
        'deleted' => ['type' => 'bool'],
        //Системные
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'weight' => ['type' => 'number'],
        'sales' => ['type' => 'number', 'logging' => false],
        'visible' => ['type' => 'number', 'logging' => false],
        'imported' => ['type' => 'text'],
        'tree_path' => ['type' => 'text'],
        'search_index' => ['type' => 'text', 'logging' => false],
        'date_create' => ['type' => 'dateTime'],
        'widget' => ['type' => 'text'],
        'view' => ['type' => 'text', 'logging' => false],
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
            'filters' => [
                'id', 'name', 'best', 'deleted', 'date_create'
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'alias'],
                ['title', 'subtitle'],
                ['category_id', 'item_type_id', 'deleted'],
                ['widget', 'view'],
                ['best', 'item_badge_id', 'image_file_id'],
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

    public function isVisible() {
        if ($this->deleted) {
            return false;
        }
        if (empty(\App::$cur->Ecommerce->config['view_empty_image']) && !$this->image_file_id) {
            return false;
        }
        if (empty(\App::$cur->Ecommerce->config['view_empty_warehouse'])) {
            $warehouseIds = \Ecommerce\OptionsParser::getWarehouses();
            $selectOptions = ['where' => [['item_offer_item_id', $this->id]]];
            $selectOptions['where'][] = [
                '(
          (SELECT COALESCE(sum(`' . Item\Offer\Warehouse::colPrefix() . 'count`),0) 
            FROM ' . \App::$cur->db->table_prefix . Item\Offer\Warehouse::table() . ' iciw 
            WHERE iciw.' . Item\Offer\Warehouse::colPrefix() . Item\Offer::index() . ' = ' . Item\Offer::index() . '
                ' . ($warehouseIds ? ' AND iciw.' . Item\Offer\Warehouse::colPrefix() . Warehouse::index() . ' IN(' . implode(',', $warehouseIds) . ')' : '') . '
            )
          -
          (SELECT COALESCE(sum(' . Warehouse\Block::colPrefix() . 'count) ,0)
            FROM ' . \App::$cur->db->table_prefix . Warehouse\Block::table() . ' iewb
            inner JOIN ' . \App::$cur->db->table_prefix . Cart::table() . ' icc ON icc.' . Cart::index() . ' = iewb.' . Warehouse\Block::colPrefix() . Cart::index() . ' AND (
                (`' . Cart::colPrefix() . 'warehouse_block` = 1 and `' . Cart::colPrefix() . 'cart_status_id` in(2,3,6)) ||
                (`' . Cart::colPrefix() . Cart\Status::index() . '` in(0,1) and `' . Cart::colPrefix() . 'date_last_activ` >=subdate(now(),INTERVAL 30 MINUTE))
            )
            WHERE iewb.' . Warehouse\Block::colPrefix() . Item\Offer::index() . ' = ' . Item\Offer::index() . ')
          )',
                0,
                '>'
            ];
            if (!Item\Offer::getList($selectOptions)) {
                return false;
            }
        }
        $isset = empty(\App::$cur->Ecommerce->config['available_price_types']);
        $nozero = !empty(\App::$cur->Ecommerce->config['show_without_price']);
        if (!$isset || !$nozero) {
            $this->loadRelation('offers');
            foreach ($this->offers as $offer) {
                foreach ($offer->prices as $price) {
                    if (empty(\App::$cur->Ecommerce->config['available_price_types']) || in_array($price->item_offer_price_type_id, \App::$cur->Ecommerce->config['available_price_types'])) {
                        $isset = true;
                    }
                    if ($price->price > 0) {
                        $nozero = true;
                    }
                    if ($isset && $nozero) {
                        break 2;
                    }
                }
            }
            if (!$isset || !$nozero) {
                return false;
            }
        }
        return true;
    }

    public function beforeSave() {
        $this->visible = $this->isVisible();
    }

    public function afterSave() {
        $itemId = $this->id;
        if ($this->category_id && isset($this->_changedParams['item_visible'])) {
            $categoryId = $this->category_id;
            \App::$primary->daemon->task(function () use ($categoryId) {
                $category = \Ecommerce\Category::get($categoryId);
                if ($category) {
                    $category->calcItemsCount();
                }
            });
        }
        \App::$primary->daemon->task(function () use ($itemId) {
            $item = \Ecommerce\Item::get($itemId);
            if (!$item) {
                return;
            }
            $item->buildSearchIndex();
            $item->save(['disableAfterTrigger' => true]);
        });
    }

    public function buildSearchIndex() {
        $this->search_index = $this->name . ' ' . $this->description . ' ';
        if ($this->category) {
            $this->search_index .= $this->category->name . ' ';
        }
        $category = $this->category;
        if ($category) {
            $categoryOptions = $category->options(['key' => 'item_option_id']);
        } else {
            $categoryOptions = [];
        }
        foreach ($this->options(['key' => false]) as $option) {
            if ($option->item_option_searchable && $option->value) {
                if ($option->item_option_type != 'select') {
                    $this->search_index .= 'option' . $option->option->id . ':' . $option->value . ' ';
                } elseif (!empty($option->option->items[$option->value])) {
                    $this->search_index .= 'option' . $option->option->id . ':' . $option->option->items(['where' => ['id', $option->value]])[$option->value]->value . ' ';
                }
            }
            if ($this->category && $option->item_option_view && !isset($categoryOptions[$option->item_option_id])) {
                $this->category->addRelation('options', $option->item_option_id);
                $categoryOptions = $this->category->options(['key' => 'item_option_id']);
            } elseif (!$option->item_option_view && isset($categoryOptions[$option->item_option_id])) {
                $categoryOptions[$option->item_option_id]->delete();
                unset($categoryOptions[$option->item_option_id]);
            }
        }
        if ($this->offers) {
            foreach ($this->offers(['key' => false]) as $offer) {
                if ($offer->options) {
                    foreach ($offer->options as $option) {
                        if ($option->item_offer_option_searchable && $option->value) {
                            if ($option->item_offer_option_type != 'select') {
                                $this->search_index .= 'offerOption' . $option->option->id . ':' . $option->value . ' ';
                            } elseif (!empty($option->option->items[$option->value])) {
                                $this->search_index .= 'offerOption' . $option->option->id . ':' . $option->option->items[$option->value]->value . ' ';
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
                'col' => 'item_badge_id'
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

    /**
     * @param int $offerId
     * @param bool|\Ecommerce\Cart $cart
     * @return bool|Item\Offer\Price|null
     */
    public function getPrice($offerId = 0, $cart = false) {
        if ($offerId) {
            return $this->offers ? $this->offers[$offerId]->getPrice($cart) : false;
        }
        return $this->offers(['key' => false]) ? $this->offers(['key' => false])[0]->getPrice($cart) : false;
    }

    public function name() {
        if (!empty(\App::$primary->ecommerce->config['item_option_as_name'])) {
            $param = Item\Param::get([['item_id', $this->id], ['item_option_id', \App::$primary->ecommerce->config['item_option_as_name']]]);
            if ($param && $param->value) {
                return $param->value;
            }
        }
        return $this->title ? $this->title : $this->name;
    }

    public function afterDelete() {
        if (!$this->id) {
            return;
        }
        $itemId = $this->id;
        \App::$primary->daemon->task(function () use ($itemId) {
            $item = \Ecommerce\Item::get($itemId);
            foreach ($item->options as $option) {
                $option->delete();
            }
            foreach ($item->offers as $offer) {
                $offer->delete();
            }
            foreach ($item->images as $image) {
                $image->delete();
            }
            if ($item->image) {
                $item->image->delete();
            }
        });
    }

    public function getHref() {
        return "/ecommerce/view/{$this->pk()}";
    }

    public function inFav() {
        if (\Users\User::$cur->id) {
            $fav = \Ecommerce\Favorite::get([['user_id', \Users\User::$cur->id], ['item_id', $this->id]]);
            return (bool)$fav;
        } else {
            $favs = !empty($_COOKIE['ecommerce_favitems']) ? json_decode($_COOKIE['ecommerce_favitems'], true) : [];
            return in_array($this->id, $favs);
        }
    }
}