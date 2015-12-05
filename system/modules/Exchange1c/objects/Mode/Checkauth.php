<?php

/**
 * Mode Checkauth
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

class Checkauth extends \Exchange1c\Mode
{
    function process()
    {
        $_SESSION['auth'] = true;
        \App::$cur->exchange1c->response('success', session_name() . "\n" . session_id(), false);
        $this->end();
    }

}
