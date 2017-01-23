<?php

/**
 * Helper for module methods
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;

class OptionsParser extends \Object {

    public static function parse($options = []) {
        $selectOptions = self::getDefault($options);

        //parse order options
        self::order($options, $selectOptions);

        //joined offers
        $selectOptions['join'][] = [Item\Offer::table(), Item::index() . ' = ' . Item\Offer::colPrefix() . Item::index(), 'inner'];

        //joined prices, check price configs
        $selectOptions['join'][] = [Item\Offer\Price::table(),
            Item\Offer::index() . ' = ' . Item\Offer\Price::colPrefix() . Item\Offer::index() .
            (empty(\App::$cur->Ecommerce->config['show_zero_price']) ? ' and ' . Item\Offer\Price::colPrefix() . 'price>0' : ''),
            empty(\App::$cur->Ecommerce->config['show_without_price']) ? 'inner' : 'left'];

        //join item types
        $selectOptions['join'][] = [
            Item\Offer\Price\Type::table(), Item\Offer\Price::colPrefix() . Item\Offer\Price\Type::index() . ' = ' . Item\Offer\Price\Type::index()
        ];

        //add filter for item type user roles by current user role
        $selectOptions['where'][] = [
            [Item\Offer\Price\Type::index(), null, 'is'],
            [
                [Item\Offer\Price\Type::colPrefix() . 'roles', '', '=', 'OR'],
                [Item\Offer\Price\Type::colPrefix() . 'roles', '%|' . \Users\User::$cur->role_id . '|%', 'LIKE', 'OR'],
            ],
        ];

        //add custom preset filters from config on where
        self::presetFilters($selectOptions);

        //parse filters
        self::filters($options, $selectOptions);

        //categories paths
        self::categories($options, $selectOptions);

        //search
        self::search($options, $selectOptions);

        self::warehouse($selectOptions);

        $selectOptions['group'] = Item::index();

        return $selectOptions;
    }

    public static function getDefault(&$options) {
        $selectOptions = [
            'where' => !empty($options['where']) ? $options['where'] : [],
            'distinct' => false,
            'join' => [],
            'order' => [],
            'start' => isset($options['start']) ? (int) $options['start'] : 0,
            'key' => isset($options['key']) ? $options['key'] : null,
            'limit' => !empty($options['count']) ? (int) $options['count'] : 0,
        ];

        //only not deleted items
        $selectOptions['where'][] = ['deleted', 0];

        //check config of view items without images
        if (empty(\App::$cur->Ecommerce->config['view_empty_image'])) {
            $selectOptions['where'][] = ['image_file_id', 0, '!='];
        }

        return $selectOptions;
    }

    public static function order(&$options, &$selectOptions) {
        if (empty($options['sort']) || !is_array($options['sort'])) {
            return;
        }
        foreach ($options['sort'] as $col => $direction) {
            $direction = strtolower($direction) == 'desc' ? 'desc' : 'asc';
            switch ($col) {
                case 'price':
                    $selectOptions['order'][] = [Item\Offer\Price::colPrefix() . 'price', $direction];
                    break;
                case 'new':
                    $selectOptions['order'][] = ['date_create', $direction];
                    break;
                case 'name':
                case 'sales':
                case 'weight':
                    $selectOptions['order'][] = [$col, $direction];
            }
        }
    }

    public static function presetFilters(&$selectOptions) {
        if (empty(\App::$cur->Ecommerce->config['view_filter'])) {
            return;
        }
        if (!empty(\App::$cur->Ecommerce->config['view_filter']['options'])) {
            foreach (\App::$cur->Ecommerce->config['view_filter']['options'] as $optionId => $optionValue) {
                $selectOptions['join'][] = [Item\Param::table(), Item::index() . ' = ' . 'option' . $optionId . '.' . Item\Param::colPrefix() . Item::index() . ' AND ' .
                    'option' . $optionId . '.' . Item\Param::colPrefix() . Item\Option::index() . ' = "' . (int) $optionId . '" AND ' .
                    'option' . $optionId . '.' . Item\Param::colPrefix() . 'value = "' . (int) $optionValue . '"',
                    'inner', 'option' . $optionId];
            }
        }
    }

    public static function filters(&$options, &$selectOptions) {
        if (empty($options['filters'])) {
            return;
        }
        foreach ($options['filters'] as $col => $filter) {
            switch ($col) {
                case 'price':
                    $colName = Item\Offer\Price::colPrefix() . 'price';
                    if (!empty($filter['min'])) {
                        $selectOptions['where'][] = [$colName, (float) $filter['min'], '>='];
                    }
                    if (!empty($filter['max'])) {
                        $selectOptions['where'][] = [$colName, (float) $filter['max'], '<='];
                    }
                    break;
                case 'options':
                case 'offerOptions':
                    if ($col == 'offerOptions') {
                        $paramPrefix = Item\Offer\Param::colPrefix();
                        $itemIndex = Item\Offer::index();
                        $optionIndex = Item\Offer\Option::index();
                        $table = Item\Offer\Param::table();
                    } else {
                        $paramPrefix = Item\Param::colPrefix();
                        $itemIndex = Item::index();
                        $optionIndex = Item\Option::index();
                        $table = Item\Param::table();
                    }
                    foreach ($filter as $optionId => $optionValue) {
                        $optionId = (int) $optionId;
                        if (is_array($optionValue)) {
                            $optionValueArr = [];
                            foreach ($optionValue as $val) {
                                $optionValueArr[] = \App::$cur->db->connection->pdo->quote($val);
                            }
                            $qstr = 'IN (' . implode(',', $optionValueArr) . ')';
                        } else {
                            $qstr = '= ' . \App::$cur->db->connection->pdo->quote($optionValue);
                        }
                        $selectOptions['join'][] = [$table, $itemIndex . ' = ' . 'option' . $optionId . '.' . $paramPrefix . $itemIndex . ' AND ' .
                            'option' . $optionId . '.' . $paramPrefix . $optionIndex . ' = "' . (int) $optionId . '" AND ' .
                            'option' . $optionId . '.' . $paramPrefix . 'value ' . $qstr . '',
                            'inner', 'option' . $optionId];
                    }
            }
        }
    }

    public static function categories(&$options, &$selectOptions) {
        if (empty($options['parent'])) {
            return;
        }
        if (is_array($options['parent']) || strpos($options['parent'], ',') !== false) {
            $list = is_array($options['parent']) ? $options['parent'] : explode(',', $options['parent']);
            $first = true;
            $where = [];
            foreach ($list as $categoryId) {
                if (!$categoryId) {
                    continue;
                }
                $category = Category::get($categoryId);
                $where[] = ['tree_path', $category->tree_path . (int) $categoryId . '/%', 'LIKE', $first ? 'AND' : 'OR'];
                $first = false;
            }
            $selectOptions['where'][] = $where;
        } else {
            $category = Category::get($options['parent']);
            if ($category) {
                $selectOptions['where'][] = ['tree_path', $category->tree_path . (int) $options['parent'] . '/%', 'LIKE'];
            }
        }
    }

    public static function search(&$options, &$selectOptions) {
        if (empty($options['search'])) {
            return;
        }
        $searchStr = preg_replace('![^A-zА-я0-9 ]!iSu', ' ', $options['search']);
        $searchArr = [];
        foreach (explode(' ', $searchStr) as $part) {
            $part = trim($part);
            if ($part && strlen($part) > 2) {
                $searchArr[] = ['search_index', '%' . $part . '%', 'LIKE'];
            }
        }
        if (!empty($searchArr)) {
            $selectOptions['where'][] = $searchArr;
        }
    }

    public static function warehouse(&$selectOptions) {
        if (!empty(\App::$cur->Ecommerce->config['view_empty_warehouse'])) {
            return;
        }
        $warehouseIds = self::getWarehouses();

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
    }

    public static function getWarehouses() {
        if (!\App::$cur->geography || !\Geography\City::$cur) {
            return [];
        }
        $warehouseIds = [];
        $warehouses = \Geography\City\Data::get([['code', 'warehouses'], ['city_id', \Geography\City::$cur->id]]);
        if ($warehouses && $warehouses->data) {
            foreach (explode(',', $warehouses->data) as $id) {
                $warehouseIds[$id] = $id;
            }
        }
        return $warehouseIds;
    }
}