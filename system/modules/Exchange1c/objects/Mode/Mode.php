<?php

/**
 * Mode
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c;

class Mode extends \InjiObject {

    /**
     * @var \Exchange1c\Exchange
     */
    public $exchange;

    /**
     * @var \Exchange1c\Exchange\Log
     */
    public $log;

    public function process() {
        $this->end();
    }

    public function end($status = 'success') {
        $this->log->status = $status;
        $this->log->date_end = date('Y-m-d H:i:s');
        $this->log->save();
    }

}
