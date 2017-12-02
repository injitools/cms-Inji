<?php

/**
 * Log
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Log {

    public $log = [];
    public $lastLog = 0;
    public $run = true;
    public $startTime = 0;
    public $template_parsed = false;
    public $forceView = false;

    public function __construct() {
        if (!empty($_SERVER['REQUEST_TIME_FLOAT'])) {
            $this->startTime = $_SERVER['REQUEST_TIME_FLOAT'];
        } elseif (!empty($_SERVER['REQUEST_TIME'])) {
            $this->startTime = $_SERVER['REQUEST_TIME'];
        } else {
            $this->startTime = time();
        }
        $this->log[] = array('name' => 'System init', 'start' => $this->startTime, 'end' => microtime(true));
    }

    public function start($name) {
        if ($this->run) {
            $this->log[] = array('name' => $name, 'start' => microtime(true));
            end($this->log);
            return $this->lastLog = key($this->log);
        }
    }

    public function end($key = false) {
        if ($this->run) {
            if ($key === false) {
                $this->log[$this->lastLog]['end'] = microtime(true);
            } else {
                $this->log[$key]['end'] = microtime(true);
            }
        }
    }

    public function event($name, $status = 'info') {
        if ($this->run) {
            $this->log[] = array('name' => $name, 'status' => $status, 'time' => microtime(true));
        }
    }

    public function clean() {
        $this->log = [];
    }

    public function stop() {
        $this->run = false;
    }

    public function run() {
        $this->run = true;
    }

    public function view() {
        echo '<div onclick="var image = document.getElementById(\'Inji_debug_window\');
    image.style.display = (image.style.display == \'none\') ? \'block\' : \'none\';" style = "background:#fff;position:fixed;bottom:0;right:0;opacity:0.3;z-index:1000001;cursor:pointer;">debug</div>';
        echo '<div id = "Inji_debug_window" style = "background:#fff;position:fixed;top:0;left:0;display:none;z-index:1000000;width: 100%;height: 100%;overflow: auto;"><table border="1" class="table table-striped table-bordered"><tr><th>Name</th><th>Time</th></tr>';
        foreach ($this->log as $log) {
            if (!empty($log['status'])) {
                echo "<tr class = '{$log['status']}'><td>{$log['name']}</td><td>{$log['status']}</td></tr>";
            } else {
                if (empty($log['end'])) {
                    $log['end'] = microtime(true);
                }
                if (empty($log['start'])) {
                    $log['start'] = microtime(true);
                }
                echo "<tr><td>{$log['name']}</td><td" . (round(($log['end'] - $log['start']), 5) > 0.1 ? ' class ="danger"' : '') . ">" . round(($log['end'] - $log['start']), 5) . "</td></tr>";
            }
        }
        echo '<tr><th>Summary</th><th>' . round((microtime(true) - $this->startTime), 5) . '</th></tr>';
        echo '<tr><th>Memory</th><th>' . $this->convertSize(memory_get_peak_usage()) . ' of ' . ini_get('memory_limit') . '</th></tr></table></div>';
    }

    public function forceView($bool) {
        App::$cur->log->forceView = $bool;
    }

    public function convertSize($size) {

        if ($size < 1024) {
            return $size . "B";
        } elseif ($size < 1048576) {
            return round($size / 1024, 2) . "KB";
        } else {
            return round($size / 1048576, 2) . "MB";
        }
    }

    public function __destruct() {
        if ($this->forceView || ($this->run && $_SERVER['REMOTE_ADDR'] == '127.0.0.1' && $this->template_parsed)) {
            $this->view();
        }
    }

}
