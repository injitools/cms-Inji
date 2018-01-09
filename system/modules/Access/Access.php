<?php

namespace Inji;
/**
 * Access module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Access extends Module {
    public $name = 'Access';
    public $accessCheckers = [];

    public function init() {
        $this->accessCheckers = $this->getSnippets('accessGetter');
    }

    public function getDeniedRedirect($app = false) {
        if (!$app) {
            $app = $this->app->type;
        }
        $url = '/';
        if (!empty($this->config['access']['accessTree'][$app]['deniedUrl'])) {
            $url = $this->config['access']['accessTree'][$app]['deniedUrl'];
        }
        $url .= '?' . http_build_query(['ref' => $_SERVER['REQUEST_URI']]);
        return $url;
    }

    public function checkAccess($element, $user = null) {
        $access = null;
        foreach ($this->accessCheckers as $getter) {
            foreach ($getter['classes'] as $className) {
                if ($element instanceof $className) {
                    $access = $getter['get']($element);
                    break;
                }
            }
        }
        if (is_null($access)) {
            $access = [];
        }
        if (empty($access)) {
            return true;
        }
        if (is_null($user)) {
            $user = Users\User::$cur;
        }

        if ((!$user->group_id && !empty($access)) || ($user->group_id && !empty($access) && !in_array($user->group_id, $access))) {
            return false;
        }

        return true;
    }

    public function resolvePath($array, $path, $element) {
        while ($path) {
            $result = $this->pathWalker($array, array_merge($path, [$element]));
            if ($result !== null) {
                return $result;
            }
            $path = array_slice($path, 0, -1);
        }
        return null;
    }

    public function pathWalker($array, $path) {
        if ($path && isset($array[$path[0]])) {
            return $this->pathWalker($array[$path[0]], array_slice($path, 1));
        } elseif (!$path) {
            return $array;
        } else {
            return null;
        }
    }

}
