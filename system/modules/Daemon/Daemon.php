<?php

class Daemon extends Module {
    private $tasksDirResource;
    private $serializer;
    private $workDir;
    public $needCheck = false;

    function check() {
        $workDir = $this->workDir();
        $lock = fopen($workDir . '/daemon.lock', 'w+');
        if (flock($lock, LOCK_EX | LOCK_NB)) {
            flock($lock, LOCK_UN);
            fclose($lock);
            $fp = fsockopen($_SERVER['SERVER_NAME'],
                80,
                $errno, $errstr, 30);

            $out = "GET /daemon/start HTTP/1.1\r\n";
            $out .= "Host: " . $_SERVER['SERVER_NAME'] . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose($fp);
        }
    }

    function start() {
        $workDir = $this->workDir();
        $lock = fopen($workDir . '/daemon.lock', 'w+');
        if (flock($lock, LOCK_EX | LOCK_NB)) {
            ignore_user_abort(true);
            set_time_limit(0);
            while (true) {
                $taskFile = $this->getNextTask();
                if (!$taskFile) {
                    for ($i = 0; $i < 60; $i++) {
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
                $task();
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
        $this->needCheck = true;
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
        return $this->serializer()->unserialize($item);
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

    function __destruct() {
        if ($this->needCheck) {
            $this->check();
        }
    }

}