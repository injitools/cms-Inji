<?php
/**
 * Reader xml
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Reader;
class Xml extends \Migrations\Reader {

    public function loadData($source = '') {
        $this->source = $source;
        $this->data = new \SimpleXMLElement(file_get_contents($source));
        return true;
    }

    public function readPath($path = '/') {
        foreach ($this->data->attributes() as $code => $item) {
            $reader = new Xml();
            $reader->source = $this->source;
            $reader->data = $item;
            yield $code => $reader;
        }
        foreach ($this->data as $code => $item) {
            $reader = new Xml();
            $reader->source = $this->source;
            $reader->data = $item;
            yield $code => $reader;
        }
    }

    public function getArray() {
        $key = \App::$cur->log->start('encode xml');
        $string = json_encode($this->data);
        \App::$cur->log->end($key);
        $key = \App::$cur->log->start('decode xml');
        $array = json_decode($string, true);
        \App::$cur->log->end($key);
        return $array;
    }

    public function __toString() {
        return (string) $this->data;
    }

    public function __isset($name) {
        return isset($this->data->$name) || isset($this->data[$name]);
    }

    public function __get($name) {
        return ($this->data->$name) ? (string) ($this->data->$name) : (string) $this->data[$name];
    }

}
