<?php

/**
 * Parser Item Offer Warehouse
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Parser\Item\Offer;

class WarehouseNew extends \Migrations\Parser {

    public function parse() {
        if (!\Tools::isAssoc($this->data['Остаток'])) {
            foreach ($this->data['Остаток'] as $warehouseCount) {
                $this->parseWarehouse($warehouseCount['Склад']);
            }
        } elseif (is_array($this->data['Остаток'])) {
            $this->parseWarehouse($this->data['Остаток']['Склад']);
        }
    }

    function parseWarehouse($warehouseCount) {
        $count = $warehouseCount['Количество'];
        $objectId = \App::$cur->migrations->findObject((string) $warehouseCount['Ид'], 'Ecommerce\Warehouse');
        if ($objectId) {
            $modelName = get_class($this->model);
            $warehouse = \Ecommerce\Item\Offer\Warehouse::get([[$modelName::index(), $this->model->pk()], [\Ecommerce\Warehouse::index(), $objectId->object_id]]);
            if (!$warehouse) {
                $warehouse = new \Ecommerce\Item\Offer\Warehouse([
                    $modelName::index() => $this->model->pk(),
                    \Ecommerce\Warehouse::index() => $objectId->object_id,
                    'count' => $count
                ]);
                $warehouse->save();
            } else {
                if ($warehouse->count != $count) {
                    $warehouse->count = $count;
                    $warehouse->save();
                }
            }
        }

    }

}
