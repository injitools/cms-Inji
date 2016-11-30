<?php

/**
 * Msg
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Msg extends Module {

    /**
     * Add message to query
     * 
     * @param string $text
     * @param string $status
     */
    public static function add($text = false, $status = 'info') {
        if ($text !== false) {
            if (!empty($_SESSION['_INJI_MSG'])) {
                foreach ($_SESSION['_INJI_MSG'] as $key => $msg) {
                    if ($msg['text'] == $text) {
                        $msg['count']++;
                        return true;
                    }
                }
            }
            $_SESSION['_INJI_MSG'][] = [
                'text' => $text,
                'status' => $status,
                'count' => 1
            ];
        }
        return true;
    }

    /**
     * Show messages query
     */
    public static function show() {
        App::$cur->view->widget('msgList');
    }

    /**
     * Get cur messages query
     * 
     * @param boolean $clean
     * @return array
     */
    public static function get($clean = false) {
        if (empty($_SESSION['_INJI_MSG']))
            return [];
        $msgs = $_SESSION['_INJI_MSG'];
        if ($clean) {
            $_SESSION['_INJI_MSG'] = [];
        }
        return $msgs;
    }

    /**
     * Clean messages query
     */
    public static function flush() {
        $_SESSION['_INJI_MSG'] = [];
    }

}
