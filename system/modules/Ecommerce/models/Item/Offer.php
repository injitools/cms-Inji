<?php

/**
 * Item offer
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Item;

/**
 * Class Offer
 *
 * @property int $id
 * @property int $item_id
 * @property string $name
 * @property string $article
 * @property int $weight
 * @property string $date_create
 *
 * @property-read \Ecommerce\Item\Offer\Warehouse[] $warehouses
 * @property-read \Ecommerce\Item\Offer\Price[] $prices
 * @property-read \Ecommerce\Item\Offer\Bonus[] $bonuses
 * @property-read \Ecommerce\Item\Offer\Param[] $options
 * @property-read \Ecommerce\Item $item
 *
 * @method \Ecommerce\Item\Offer\Warehouse[] warehouses($options)
 * @method \Ecommerce\Item\Offer\Price[] prices($options)
 * @method \Ecommerce\Item\Offer\Bonus[] bonuses($options)
 * @method \Ecommerce\Item\Offer\Param[] options($options)
 * @method \Ecommerce\Item item($options)
 */
class Offer extends \Inji\Model {

    public static $objectName = 'Торговое предложение';
    public static $cols = [
        //Основные параметры
        'item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'item'],
        'name' => ['type' => 'text'],
        'article' => ['type' => 'text'],
        //Системные
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'warehouse' => ['type' => 'dataManager', 'relation' => 'warehouses'],
        'price' => ['type' => 'dataManager', 'relation' => 'prices'],
        'option' => ['type' => 'dataManager', 'relation' => 'options'],
    ];
    public static $labels = [
        'name' => 'Название',
        'article' => 'Артикул',
        'warehouse' => 'Наличие на складах',
        'price' => 'Цены',
        'option' => 'Параметры предложения'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'article', 'warehouse', 'price', 'option'
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'article'],
                ['warehouse'],
                ['price'],
                ['option'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'warehouses' => [
                'type' => 'many',
                'model' => 'Inji\Ecommerce\Item\Offer\Warehouse',
                'col' => 'item_offer_id'
            ],
            'prices' => [
                'type' => 'many',
                'model' => 'Inji\Ecommerce\Item\Offer\Price',
                'col' => 'item_offer_id',
            ],
            'bonuses' => [
                'type' => 'many',
                'model' => 'Inji\Ecommerce\Item\Offer\Bonus',
                'col' => 'item_offer_id',
            ],
            'options' => [
                'type' => 'many',
                'model' => 'Inji\Ecommerce\Item\Offer\Param',
                'col' => 'item_offer_id',
                'resultKey' => 'item_offer_option_id',
                'join' => [Offer\Option::table(), Offer\Option::index() . ' = ' . Offer\Param::colPrefix() . Offer\Option::index()]
            ],
            'item' => [
                'model' => 'Inji\Ecommerce\Item',
                'col' => 'item_id'
            ]
        ];
    }

    public function changeWarehouse($count) {
        $warehouse = Offer\Warehouse::get([['count', '0', '>'], ['item_offer_id', $this->id]]);
        if ($warehouse) {
            $warehouse->count += (float) $count;
            $warehouse->save();
        } else {
            $warehouse = Offer\Warehouse::get([['item_offer_id', $this->id]]);
            if ($warehouse) {
                $warehouse->count += (float) $count;
                $warehouse->save();
            }
        }
    }

    public function warehouseCount($cart_id = 0) {
        $warehouseIds = [];
        if (\Inji\App::$cur->geography && \Inji\Geography\City::$cur) {
            $warehouses = \Inji\Geography\City\Data::get([['code', 'warehouses'], ['city_id', \Inji\Geography\City::$cur->id]]);
            if ($warehouses && $warehouses->data) {
                foreach (explode(',', $warehouses->data) as $id) {
                    $warehouseIds[$id] = $id;
                }
            }
        }
        if ($warehouseIds) {
            \Inji\App::$cur->db->where(\Ecommerce\Item\Offer\Warehouse::colPrefix() . \Inji\Ecommerce\Warehouse::index(), $warehouseIds, 'IN');
        }
        \Inji\App::$cur->db->where(\Ecommerce\Item\Offer\Warehouse::colPrefix() . \Ecommerce\Item\Offer::index(), $this->id);
        \Inji\App::$cur->db->cols = 'COALESCE(sum(' . \Ecommerce\Item\Offer\Warehouse::colPrefix() . 'count),0) as `sum` ';
        $warehouse = \Inji\App::$cur->db->select(\Ecommerce\Item\Offer\Warehouse::table())->fetch();

        \Inji\App::$cur->db->cols = 'COALESCE(sum(' . \Inji\Ecommerce\Warehouse\Block::colPrefix() . 'count) ,0) as `sum` ';
        \Inji\App::$cur->db->where(\Inji\Ecommerce\Warehouse\Block::colPrefix() . \Ecommerce\Item\Offer::index(), $this->id);
        if ($cart_id) {
            \Inji\App::$cur->db->where(\Inji\Ecommerce\Warehouse\Block::colPrefix() . \Inji\Ecommerce\Cart::index(), (int) $cart_id, '!=');
        }
        $on = '
            ' . \Inji\Ecommerce\Cart::index() . ' = ' . \Inji\Ecommerce\Warehouse\Block::colPrefix() . \Inji\Ecommerce\Cart::index() . ' AND (
            (`' . \Inji\Ecommerce\Cart::colPrefix() . 'warehouse_block` = 1 and `' . \Inji\Ecommerce\Cart::colPrefix() . 'cart_status_id` in(2,3,6)) || 
            (`' . \Inji\Ecommerce\Cart::colPrefix() . 'cart_status_id` in(0,1) and `' . \Inji\Ecommerce\Cart::colPrefix() . 'date_last_activ` >=subdate(now(),INTERVAL 30 MINUTE))
            )
        ';
        \Inji\App::$cur->db->join(\Ecommerce\Cart::table(), $on, 'inner');


        $blocked = \Inji\App::$cur->db->select(\Inji\Ecommerce\Warehouse\Block::table())->fetch();
        return (float) $warehouse['sum'] - (float) $blocked['sum'];
    }

    /**
     * @param bool|\Ecommerce\Cart $cart
     * @return Offer\Price|null
     */
    public function getPrice($cart = false) {
        $where = [];
        if (empty(\Inji\App::$cur->Ecommerce->config['show_zero_price'])) {
            $where[] = ['price', 0, '>'];
        }
        if ($cart) {
            $types = $cart->availablePricesTypes();
        } else {
            $types = \Inji\App::$primary->Ecommerce->availablePricesTypes();
        }
        if ($types !== true) {
            $where[] = ['item_offer_price_type_id', array_values($types), 'IN'];
        }

        $prices = $this->prices(['where' => $where, 'order' => ['type:weight', 'ASC'], 'limit' => 1, 'key' => false]);
        if ($prices) {
            return $prices[0];
        }
        return null;
    }

    public function beforeDelete() {
        if ($this->id) {
            if ($this->warehouses) {
                foreach ($this->warehouses as $warehouse) {
                    $warehouse->delete();
                }
            }
            if ($this->prices) {
                foreach ($this->prices as $price) {
                    $price->delete();
                }
            }
            if ($this->bonuses) {
                foreach ($this->bonuses as $bonus) {
                    $bonus->delete();
                }
            }
        }
    }

}
