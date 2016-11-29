<?php

/**
 * Parser Item Offer WarehouseSum
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Parser\Item\Offer;

class WarehouseSum extends \Migrations\Parser {

    public function parse() {
        $count = (string) $this->data;
        $warehouse = \Ecommerce\Warehouse::get(['name', 'Общий склад 1с']);
        if (!$warehouse) {
            $warehouse = new \Ecommerce\Warehouse();
            $warehouse->name = 'Общий склад 1с';
            $warehouse->save();
        }
        $modelName = get_class($this->object->model);

        $warehouseOffer = \Ecommerce\Item\Offer\Warehouse::get([
                    [$modelName::index(), $this->object->model->pk()],
                    [\Ecommerce\Warehouse::index(), $warehouse->id]
        ]);
        if (!$warehouseOffer) {
            $warehouseOffer = new \Ecommerce\Item\Offer\Warehouse([
                $modelName::index() => $this->object->model->pk(),
                \Ecommerce\Warehouse::index() => $warehouse->id,
                'count' => $count
            ]);
        } else {
            $warehouseOffer->count = $count;
        }
        $warehouseOffer->save();
    }

}
