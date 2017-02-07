<?php

/**
 * Materials app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MaterialsController extends Controller {

    public function indexAction() {
        $args = func_get_args();
        $category = null;
        $material = null;
        $path = trim(implode('/', $args));

        if (is_numeric($path)) {
            $material = Materials\Material::get([['id', (int) $path], ['date_publish', null, 'IS NOT']]);
        }
        if (!$material) {
            foreach ($args as $key => $alias) {
                $category = Materials\Category::get([['parent_id', $category ? $category->id : 0], ['alias', $alias]]);
                if (!$category || $key + 2 == count($args)) {
                    break;
                }
            }
        }
        if (!$material && $path) {
            if ($category) {
                $where = [
                    ['category_id', $category->id],
                    ['alias', $args[count($args) - 1]],
                    ['date_publish', null, 'IS NOT']
                ];
            } else {
                $where = [['alias', $path], ['date_publish', null, 'IS NOT']];
            }
            $material = Materials\Material::get($where);
            if (!$material) {
                if ($category) {
                    $where = [
                        ['category_id', $category->id],
                        ['id', (int) $args[count($args) - 1]],
                        ['date_publish', null, 'IS NOT']
                    ];
                } else {
                    $where = [['alias', $path], ['date_publish', null, 'IS NOT']];
                }
                $material = Materials\Material::get($where);
            }
            if (!$material) {
                $category = Materials\Category::get($path, 'alias');
                if ($category) {
                    $this->categoryAction($category->id);
                }
            }
        } elseif(!$material) {
            $material = Materials\Material::get(1, 'default');
        }
        if ($material) {
            $this->viewAction($material->id);
        } elseif (!$category && !$material) {
            Tools::header('404');
            $this->view->page([
                'content' => '404',
                'data' => ['text' => 'Такой страницы не найдено']
            ]);
        }
    }

    public function categoryAction() {
        $args = func_get_args();
        $path = trim(implode('/', $args));
        $category = null;
        if (is_numeric($path)) {
            $category = Materials\Category::get((int) $path);
        }
        if (!$category) {
            foreach ($args as $alias) {
                $category = Materials\Category::get([['parent_id', $category ? $category->id : 0], ['alias', $alias]]);
                if (!$category) {
                    break;
                }
            }
        }
        if (!$category) {
            $category = Materials\Category::get($path, 'alias');
        }
        if (!$category) {
            Tools::header('404');
            $this->view->page([
                'content' => '404',
                'data' => ['text' => 'Такой страницы не найдено']
            ]);
        } else {
            $this->view->setTitle($category->name);

            $pages = new Ui\Pages($_GET, ['count' => Materials\Material::getCount(['where' => [['tree_path', $category->tree_path . $category->id . '/%', 'LIKE'], ['date_publish', null, 'IS NOT']]]), 'limit' => 10]);
            $materials = Materials\Material::getList(['where' => [['tree_path', $category->tree_path . $category->id . '/%', 'LIKE'], ['date_publish', null, 'IS NOT']], 'order' => ['date_create', 'desc'], 'start' => $pages->params['start'], 'limit' => $pages->params['limit']]);

            $this->view->page(['page' => $category->resolveTemplate(), 'content' => $category->resolveViewer(), 'data' => compact('materials', 'pages', 'category')]);
        }
    }

    public function viewAction() {
        $args = func_get_args();
        $alias = trim(implode('/', $args));
        $material = false;
        if ($alias) {
            if (is_numeric($alias)) {
                $material = Materials\Material::get([['id', (int) $alias], ['date_publish', null, 'IS NOT']]);
            }
            if (!$material) {
                $material = Materials\Material::get([['alias', $alias], ['date_publish', null, 'IS NOT']]);
                if (!$material) {
                    Tools::header('404');
                    $this->view->page([
                        'content' => '404',
                        'data' => ['text' => 'Такой страницы не найдено']
                    ]);
                    exit();
                }
            }
        }
        if ($material->keywords) {
            $this->view->addMetaTag(['name' => 'keywords', 'content' => $material->keywords]);
        }
        if ($material->description) {
            $this->view->addMetaTag(['name' => 'description', 'content' => $material->description]);
        }
        $this->view->addMetaTag(['property' => 'og:title', 'content' => $material->name]);
        $this->view->addMetaTag(['property' => 'og:url', 'content' => 'http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/' . $material->alias]);
        if ($material->description) {
            $this->view->addMetaTag(['property' => 'og:description', 'content' => 'http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/' . $material->description]);
        }
        if ($material->image) {
            $this->view->addMetaTag(['property' => 'og:image', 'content' => 'http://' . idn_to_utf8(INJI_DOMAIN_NAME) . $material->image->path]);
        } elseif ($logo = Files\File::get('site_logo', 'code')) {
            $this->view->addMetaTag(['property' => 'og:image', 'content' => 'http://' . idn_to_utf8(INJI_DOMAIN_NAME) . $logo->path]);
        }
        $this->view->setTitle($material->name);
        $bread = [];
        $bread[] = ['text' => $material->name, 'href' => '/' . $material->alias];
        $this->view->page([
            'page' => $material->resolveTemplate(),
            'content' => $material->resolveViewer(),
            'data' => compact('material', 'bread'),
        ]);
    }
}