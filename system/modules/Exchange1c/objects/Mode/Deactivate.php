<?php

/**
 * Mode Success
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

class Deactivate extends \Exchange1c\Mode {

    public function process() {
        $this->end();
    }

}
