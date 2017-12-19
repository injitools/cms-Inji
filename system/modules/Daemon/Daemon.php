<?php

class Daemon extends Module {
    private $tasksDirResource;
    private $serializer;
    private $workDir;
    public $checked = false;

    function check() {
        $workDir = $this->workDir();
        $lock = fopen($workDir . '/daemon.lock', 'w+');
        if (flock($lock, LOCK_EX | LOCK_NB)) {
            flock($lock, LOCK_UN);
            fclose($lock);
            if (function_exists('pcntl_fork') && $pid = pcntl_fork() !== -1) {
                if ($pid) {
                    return $pid;
                } else {
                    $this->start(true);
                }
            } elseif (function_exists('fsockopen')) {
                $fp = fsockopen($_SERVER['SERVER_NAME'],
                    80,
                    $errno, $errstr, 30);
                $out = "GET /daemon/start HTTP/1.1\r\n";
                $out .= "Host: " . $_SERVER['SERVER_NAME'] . "\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                fclose($fp);
            } elseif (function_exists('curl_init')) {
                $ch = curl_init('http://' . $_SERVER['SERVER_NAME'] . '/daemon/start');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
                curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
                $content = curl_exec($ch);
                curl_close($ch);
            }
        }
    }

    function start($retry = false) {
        $workDir = $this->workDir();
        $lock = fopen($workDir . '/daemon.lock', 'w+');
        if (flock($lock, LOCK_EX | LOCK_NB)) {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            while (true) {
                $taskFile = $this->getNextTask();
                if (!$taskFile) {
                    for ($i = 0; $i < 358; $i++) {
                        sleep(1);
                        $taskFile = $this->getNextTask();
                        if ($taskFile) {
                            break;
                        }
                    }
                    if (!$taskFile) {
                        break;
                    }
                }
                $task = $this->unserialize(file_get_contents($taskFile));
                unlink($taskFile);
                if ($task) {
                    $task();
                }
            }
        } else {
            if ($retry) {
                sleep(1);
                $this->start(false);
            }
        }
    }

    function getNextTask() {
        $workDir = $this->workDir();
        if (!is_resource($this->tasksDirResource)) {
            $this->tasksDirResource = opendir($workDir . '/tasks/');
        }
        if ($this->tasksDirResource) {
            while (false !== ($entry = readdir($this->tasksDirResource))) {
                if ($entry != "." && $entry != "..") {
                    return $workDir . '/tasks/' . $entry;
                }
            }
        }
        return false;
    }

    function task($callback) {
        $keyLog = \App::$cur->log->start('add task');
        $workDir = $this->workDir();
        $taskFile = $workDir . '/tasks/' . microtime(true) . '.task';
        $keyLog2 = \App::$cur->log->start('serialize task');
        $serialize = $this->serialize($callback);
        \App::$cur->log->end($keyLog2);
        $keyLog3 = \App::$cur->log->start('put task to file');
        file_put_contents($taskFile, $serialize);
        \App::$cur->log->end($keyLog3);
        if (!$this->checked) {
            $this->checked = true;
            $this->check();
        }
        \App::$cur->log->end($keyLog);
        return $taskFile;
    }

    function workDir() {
        if ($this->workDir) {
            return $this->workDir;
        }
        $path = App::$primary->path . '/daemon';
        Tools::createDir(App::$primary->path . '/daemon/tasks');
        return $path;
    }

    function unserialize($item) {
        try {
            return $this->serializer()->unserialize($item);
        } catch (\SuperClosure\Exception\ClosureUnserializationException $e) {
            return false;
        }
    }

    function serialize($item) {
        return $this->serializer()->serialize($item);
    }

    function serializer() {
        if ($this->serializer) {
            return $this->serializer;
        }
        \ComposerCmd::requirePackage('jeremeamia/superclosure');
        return $this->serializer = new \SuperClosure\Serializer(new SuperClosure\Analyzer\TokenAnalyzer());
    }

}