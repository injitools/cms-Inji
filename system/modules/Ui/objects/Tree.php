<?php
/**
 * Tree
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui;

class Tree {

    /**
     * Function for generate item body html
     *
     * @var closure|null
     */
    public $itemBodyFn = null;

    /**
     * Function for generate item body html
     *
     * @var closure|null
     */
    public $itemActiveCheck = null;

    /**
     * Active item class name
     *
     * @var string
     */
    public $itemActiveClass = 'active';

    public function __construct() {
        
    }

    /**
     * Draw tree
     * 
     * @param Model|string $objectRoot
     * @param integer $maxDeep
     * @param array $order
     * @return integer
     */
    public function draw($objectRoot, $maxDeep = 0, $order = []) {
        return Tree::ul($objectRoot, $maxDeep, $this->itemBodyFn, $order, $this->itemActiveCheck, $this->itemActiveClass);
    }

    /**
     * Start generating items tree from root item
     * item must has parent_id col for generating tree by this coll
     * 
     * @param Model|string $objectRoot
     * @param integer $maxDeep
     * @param closure|null $hrefFunc
     * @param array $order
     * @return integer
     */
    public static function ul($objectRoot, $maxDeep = 0, $hrefFunc = null, $order = [], $activeFunc = '', $activeClass = 'active') {
        $count = 0;
        if (!$hrefFunc) {
            $hrefFunc = function($object) {
                return "<a href='#'> {$object->name()}</a>";
            };
        }
        ?>
        <ul class="treeview" data-col='tree_path'>
            <?php
            if (is_string($objectRoot)) {
                $items = $objectRoot::getList(['where' => ['parent_id', 0], 'order' => $order]);
            } else {
                $class = get_class($objectRoot);
                $items = $class::getList(['where' => ['parent_id', $objectRoot->pk()], 'order' => $order]);
            }
            $count += count($items);
            foreach ($items as $objectChild) {
                $count += static::showLi($objectChild, 1, $maxDeep, $hrefFunc, $order, $activeFunc, $activeClass);
            }
            ?>
        </ul>
        <?php
        return $count;
    }

    public static function showLi($object, $deep = 1, $maxDeep = 0, $hrefFunc = null, $order = [], $activeFunc = '', $activeClass = 'active') {
        $count = 0;
        $isset = false;
        $class = get_class($object);
        $item = $hrefFunc ? $hrefFunc($object) : "<a href='#'> {$object->name()}</a> ";
        $attributes = [];
        if ($activeFunc && $activeFunc($object)) {
            $attributes['class'] = $activeClass;
        }

        if (is_array($item)) {
            $attributes = $item['attributes'];
            $item = $item['text'];
        }
        if (!isset($attributes['id'])) {
            $attributes['id'] = str_replace('\\', '_', get_class($object)) . "-{$object->pk()}";
        }
        if (!$maxDeep || $deep < $maxDeep) {
            $items = $class::getList(['where' => ['parent_id', $object->pk()], 'order' => $order]);
            $count += count($items);
            foreach ($items as $objectChild) {
                if (!$isset) {
                    $isset = true;
                    if ($activeFunc && $activeFunc($objectChild)) {
                        $attributes['class'] = $activeClass;
                    }
                    echo \Html::el('li', $attributes, $item, true);
                    echo '<ul>';
                }
                $count += static::showLi($objectChild, $deep + 1, $maxDeep, $hrefFunc, $order, $activeFunc, $activeClass);
            }
        }
        if ($isset) {
            echo '</ul></li>';
        } else {
            echo \Html::el('li', $attributes, $item);
        }
        return $count;
    }
}