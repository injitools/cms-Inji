<?php

/**
 * Mode Init
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

use Exchange1c\Exchange;

class Info extends \Exchange1c\Mode
{

    public function process()
    {
        echo "moduleVersion=2.03";
        $this->end();
    }

}
