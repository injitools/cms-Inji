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
            echo 'started';
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
            while ($taskFile = $this->getNextTask()) {
                $task = $this->unserialize(file_get_contents($taskFile));
                $task();
                unlink($taskFile);
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
        $workDir = $this->workDir();
        $taskFile = $workDir . '/tasks/' . microtime(true) . '.task';
        file_put_contents($taskFile, $this->serialize($callback));
        $this->needCheck = true;
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
        return $this->serializer = new \SuperClosure\Serializer();
    }

    function __destruct() {
        if ($this->needCheck) {
            $this->check();
        }
    }

}