<?php

/**
 * Form
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui;

class Form extends \Object {

    public $id = null;
    public $method = 'POST';
    public $action = '';
    public $inputs = [];
    public $map = [];
    public $userDataTree = [];
    public $options = ['widgetsDir' => 'Form'];

    public function __construct($options = []) {
        $this->options = array_merge($this->options, $options);
        $this->genUserDataTree($_POST);
    }

    public function genUserDataTree($data, $treeKey = '') {
        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $this->genUserDataTree($item, $treeKey ? $treeKey . "[{$key}]" : $key);
            } else {
                $this->userDataTree[$treeKey ? $treeKey . "[{$key}]" : $key] = $item;
            }
        }
    }

    public function begin($header = '', $options = [], $params = []) {
        $params = compact('header', 'options', 'params');
        $params['form'] = $this;
        \App::$cur->view->widget('Ui\\' . $this->options['widgetsDir'] . '/begin', $params);
    }

    /**
     * Draw form input
     *
     * @param string $type
     * @param string $name
     * @param string|boolean $label
     * @param array $options
     */
    public function input($type, $name, $label = '', $options = []) {
        switch ($type) {
            case 'html':
                \App::$cur->libs->loadLib('ckeditor');
                $params = compact('name', 'label', 'options');
                $params['form'] = $this;
                if (empty($params['options']['class'])) {
                    $params['options']['class'] = 'htmleditor';
                } else {
                    $params['options']['class'] .= ' htmleditor';
                }
                \App::$cur->view->widget('Ui\\' . $this->options['widgetsDir'] . '/textarea', $params);
                break;
            case 'map':
                $params = compact('name', 'label', 'options');
                $params['form'] = $this;
                \App::$cur->libs->loadLib('yandexMap');
                \App::$cur->view->widget('Ui\\' . $this->options['widgetsDir'] . '/map', $params);
                break;
            default:
                $params = compact('name', 'label', 'options');
                $params['form'] = $this;
                if (strpos($type, '\\')) {
                    \App::$cur->view->widget($type, $params);
                } else {
                    \App::$cur->view->widget('Ui\\' . $this->options['widgetsDir'] . '/' . $type, $params);
                }


        }
    }

    /**
     * Draw form end
     *
     * @param boolean|string $btnText
     * @param array $attributs
     */
    public function end($btnText = 'Отправить', $attributs = [], $options = []) {
        $params = compact('btnText', 'attributs', 'options');
        $params['form'] = $this;
        \App::$cur->view->widget('Ui\\' . $this->options['widgetsDir'] . '/end', $params);
    }

}
