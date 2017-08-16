<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce;


class DeliveryProvider {
    static $name = 'Unnamed';

    static function calcPrice($cart) {
        return new \Money\Sums([]);
    }
}