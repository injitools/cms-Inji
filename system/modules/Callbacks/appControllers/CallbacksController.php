<?php

/**
 * Callbacks app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class CallbacksController extends Controller {

    public function indexAction($categoryCode = '') {
        $category = null;
        if ($categoryCode) {
            $category = Callbacks\Category::get($categoryCode, 'alias');
            if (!$category) {
                $category = Callbacks\Category::get($categoryCode);
            }
        }
        if ($category) {
            $callbacks = $category->callbacks(['where' => [['view', 1]], 'order' => ['weight', 'asc']]);
        } else {
            $callbacks = Callbacks\Callback::getList(['where' => [['category_id', 0], ['view', 1]], 'order' => ['weight', 'asc']]);
        }
        $this->view->setTitle($category ? $category->name : 'Отзывы');
        $this->view->page([
            'page' => $category ? $category->resolveTemplate() : 'current',
            'content' => $category ? $category->resolveViewer() : 'index',
            'data' => compact('category', 'callbacks')
        ]);
    }

    public function viewAction($callbackId) {
        $callback = Callbacks\Callback::get((int) $callbackId);
        if (!$callback) {
            Tools::header(404);
            Tools::redirect('/', 'Отзыв не найден', 'danger');
        }
        $this->view->setTitle('Отзыв: ' . $callback->name);
        $this->view->page(['data' => compact('callback')]);
    }

}
