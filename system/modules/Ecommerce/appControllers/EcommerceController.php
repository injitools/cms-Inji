<?php

/**
 * Ecommerce app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class ecommerceController extends Controller {

    public function submitReviewAction() {
        $result = new \Server\Result();
        if (!empty($_POST['review']['item_id']) && !empty($_POST['review']['name']) && !empty($_POST['review']['text'])) {
            $item = Ecommerce\Item::get((int)$_POST['review']['item_id']);
            if (!$item) {
                $result->success = false;
                $result->content = ['errorText' => 'Товар не найден'];
                return $result->send();
            }
            $review = new Ecommerce\Item\Review([
                'item_id' => $item->id,
                'user_id' => \Users\User::$cur->id,
                'name' => htmlspecialchars($_POST['review']['name']),
                'rating' => (int)$_POST['review']['rating'],
                'text' => htmlspecialchars($_POST['review']['text']),
                'mail' => !empty($_POST['review']['email']) ? htmlspecialchars($_POST['review']['email']) : '',
                'file_id' => !empty($_FILES['review']['tmp_name']['file']) ? App::$cur->files->upload([
                    'tmp_name' => $_FILES['review']['tmp_name']['file'],
                    'name' => $_FILES['review']['name']['file']
                ]) : 0
            ]);
            $review->save();
            $result->successMsg = 'Отзыв успешно оставлен, он появится после модерации';
            return $result->send();
        }
        $result->success = false;
        $result->content = ['errorText' => 'Не все поля были заполнены'];
        return $result->send();
    }

    public function buyCardAction() {
        $this->view->setTitle('Покупка карты');
        $bread = [];
        $bread[] = ['text' => 'Покупка карты'];
        $user = Users\User::$cur;
        if (!empty($_POST) && !empty($_POST['card_id'])) {
            $error = false;
            $card = \Ecommerce\Card::get((int)$_POST['card_id']);
            if (!$card) {
                $error = true;
                Msg::add('Такой карты не существует', 'danger');
            }

            if (!Users\User::$cur->id) {
                $user_id = $this->Users->registration($_POST, true);
                if (!$user_id) {
                    $error = true;
                    $user = null;
                } else {
                    $user = Users\User::get($user_id);
                }
            }
            $userCard = \Ecommerce\Card\Item::get([['card_id', $card->id], ['user_id', $user->id]]);
            if ($userCard) {
                $error = true;
                Msg::add('У вас уже есть такая карта', 'danger');
            }

            $fields = \Ecommerce\UserAdds\Field::getList();
            foreach ($fields as $field) {
                if (empty($_POST['userAdds']['fields'][$field->id]) && $field->required) {
                    $error = 1;
                    Msg::add('Вы не указали: ' . $field->name);
                }
            }
            if (!$error) {
                $cardItem = new \Ecommerce\Card\Item();
                $cardItem->card_id = $card->id;
                $cardItem->user_id = $user->id;
                $cardItem->save();

                $cart = new \Ecommerce\Cart();
                $cart->user_id = $user->user_id;
                $cart->cart_status_id = 2;
                $cart->comment = htmlspecialchars($_POST['comment']);
                $cart->date_status = date('Y-m-d H:i:s');
                $cart->complete_data = date('Y-m-d H:i:s');
                if (!empty($_SESSION['cart']['cart_id'])) {
                    $cart->card_item_id = $cardItem->id;
                }
                $cart->save();

                $this->module->parseFields($_POST['userAdds']['fields'], $cart);


                $extra = new \Ecommerce\Cart\Extra();
                $extra->name = $card->name;
                $extra->price = $card->price;
                $extra->count = 1;
                $extra->cart_id = $cart->id;
                $extra->info = 'card:' . $card->id . '|cardItem:' . $cardItem->id;
                $extra->save();
                Tools::redirect('/ecommerce/cart/success');
            }
        }
        $this->view->page(['data' => compact('bread')]);
    }

    public function autoCompleteAction() {
        $return = Cache::get('itemsAutocomplete');
        if (!$return) {
            $items = $this->ecommerce->getItems();
            $return = [];
            foreach ($items as $item) {
                $return[] = ['name' => $item->name(), 'search' => $item->search_index . ' ' . $item->name];
            }
            $return = json_encode($return);
            Cache::set('itemsAutocomplete', [], $return);
        }
        echo $return;
    }

    public function indexAction() {
        if (empty($this->module->config['catalogPresentPage'])) {
            Tools::redirect('/ecommerce/itemList');
        }
        $this->view->page();
    }

    public function itemListAction($category_id = 0) {
        //search
        if (!empty($_GET['search'])) {
            if (!empty($_GET['inCatalog'])) {
                $category_id = (int)$_GET['inCatalog'];
            }
            $search = $_GET['search'];
        } else {
            $search = '';
        }

        //sort
        if (!empty($_GET['sort'])) {
            $sort = $_GET['sort'];
        } elseif (!empty($this->ecommerce->config['defaultSort'])) {
            $sort = $this->ecommerce->config['defaultSort'];
        } else {
            $sort = ['name' => 'asc'];
        }

        //category
        $category = null;
        if ($category_id) {
            if (is_numeric($category_id)) {
                $category = \Ecommerce\Category::get($category_id);
            }
            if (!$category) {
                $category = \Ecommerce\Category::get($category_id, 'alias');
            }
            if ($category) {
                $category_id = $category->id;
            } else {
                $category_id = 0;
            }
        } else {
            $category_id = 0;
        }
        if($category){
            $category->views++;
            $category->save();
        }
        $active = $category_id;
        if (!empty($_GET['categorys'])) {
            $categorysList = $_GET['categorys'];
        } else {
            $categorysList = $category_id;
        }

        //items pages
        $pages = new \Ui\Pages($_GET, ['count' => $this->ecommerce->getItemsCount([
            'parent' => $categorysList,
            'search' => trim($search),
            'filters' => !empty($_GET['filters']) ? $_GET['filters'] : []
        ]),
            'limit' => !empty($this->Ecommerce->config['default_limit']) ? $this->Ecommerce->config['default_limit'] : 18,
        ]);
        if (!empty(App::$cur->ecommerce->config['list_all']) && !empty($_GET['limit']) && $_GET['limit'] == 'all') {
            $pages->params['start'] = 0;
            $pages->params['limit'] = 0;
            $pages->params['pages'] = 1;
        }

        //bread
        $bread = [];
        if (!$category || !$category->name) {
            $bread[] = array('text' => 'Каталог');
            $this->view->setTitle('Каталог');
        } else {
            $bread[] = array('text' => 'Каталог', 'href' => '/ecommerce');
            $categoryIds = array_values(array_filter(explode('/', $category->tree_path)));
            foreach ($categoryIds as $id) {
                $cat = Ecommerce\Category::get($id);
                $bread[] = array('text' => $cat->name, 'href' => '/ecommerce/itemList/' . $cat->id);
            }
            $bread[] = array('text' => $category->name);
            $this->view->setTitle($category->name);
        }

        //items
        $items = $this->ecommerce->getItems([
            'parent' => $categorysList,
            'start' => $pages->params['start'],
            'count' => $pages->params['limit'],
            'search' => trim($search),
            'sort' => $sort,
            'filters' => !empty($_GET['filters']) ? $_GET['filters'] : []
        ]);

        //params 
        if (empty(App::$cur->ecommerce->config['filtersInLast'])) {
            $options = \Ecommerce\Item\Option::getList(['where' => ['item_option_searchable', 1], 'order' => ['weight', 'asc']]);
        } else {
            $params = $this->ecommerce->getItemsParams([
                'parent' => $categorysList,
                'search' => trim($search),
                'filters' => !empty($_GET['filters']) ? $_GET['filters'] : []
            ]);
            $ids = [];
            foreach ($params as $param) {
                $ids[] = $param->item_option_id;
            }
            if ($ids) {
                $options = \Ecommerce\Item\Option::getList(['where' => ['id', $ids, 'IN'], 'order' => ['weight', 'asc']]);
            } else {
                $options = [];
            }
        }


        //child categorys
        if ($category) {
            $categorys = $category->catalogs;
        } else {
            $categorys = \Ecommerce\Category::getList(['where' => ['parent_id', 0]]);
        }

        //view content
        $this->view->page([
            'page' => $category ? $category->resolveTemplate() : (!empty(App::$cur->ecommerce->config['defaultCategoryTemplate']) ? App::$cur->ecommerce->config['defaultCategoryTemplate'] : 'current'),
            'content' => $category ? $category->resolveViewer() : (!empty(App::$cur->ecommerce->config['defaultCategoryView']) ? App::$cur->ecommerce->config['defaultCategoryView'] : 'itemList'),
            'data' => compact('active', 'category', 'sort', 'search', 'pages', 'items', 'categorys', 'bread', 'options')]);
    }

    public function favoritesAction() {
        $count = $this->module->getFavoriteCount();
        //items pages
        $pages = new \Ui\Pages($_GET, [
            'count' => $count,
            'limit' => !empty($this->Ecommerce->config['default_limit']) ? $this->Ecommerce->config['default_limit'] : 18,
        ]);
        if (Users\User::$cur->id) {
            $favs = \Ecommerce\Favorite::getList(['where' => ['user_id', Users\User::$cur->id], 'key' => 'item_id', 'start' => $pages->params['start'], 'limit' => $pages->params['limit']]);
            $ids = array_keys($favs);
        } else {
            $favs = !empty($_COOKIE['ecommerce_favitems']) ? json_decode($_COOKIE['ecommerce_favitems'], true) : [];
            $ids = array_slice($favs, $pages->params['start'], $pages->params['limit']);
        }
        if ($ids) {
            //items
            $items = \Ecommerce\Item::getList(['where' => ['id', $ids, 'IN']]);
        }

        $bread = [];
        $bread[] = ['text' => 'Каталог', 'href' => '/ecommerce/itemList/'];
        $bread[] = ['text' => 'Избранное'];
        $this->view->setTitle('Избранное');
        $this->view->page(['data' => compact('pages', 'items', 'bread')]);
    }

    public function viewAction($id = '', $quick = 0) {
        $item = \Ecommerce\Item::get((int)$id);
        if (!$item) {
            Tools::redirect('/ecommerce/', 'Такой товар не найден');
        }
        $active = $item->category_id;
        $catalog = $item->category;
        $bread = [];
        $bread[] = ['text' => 'Каталог', 'href' => '/ecommerce'];

        $catalogIds = array_values(array_filter(explode('/', $item->tree_path)));
        foreach ($catalogIds as $id) {
            $cat = Ecommerce\Category::get($id);
            if ($cat) {
                $bread[] = ['text' => $cat->name, 'href' => '/ecommerce/itemList/' . $cat->id];
            }
        }
        $bread[] = ['text' => $item->name()];
        $this->view->setTitle($item->name());
        $options = [
            'data' => compact('item', 'active', 'catalog', 'bread', 'quick'),
            'content' => $item->view ? $item->view : 'view',
        ];
        if (isset($_GET['quickview'])) {
            $options['page'] = 'blank';
        }

        $this->view->addMetaTag(['property' => 'og:title', 'content' => $item->name]);
        $this->view->addMetaTag(['property' => 'og:description', 'content' => strip_tags($item->description)]);
        if ($item->image) {
            $this->view->addMetaTag(['property' => 'og:image', 'content' => 'http://' . INJI_DOMAIN_NAME . $item->image->path]);
        }
        $this->view->addMetaTag(['property' => 'og:url', 'content' => 'http://' . INJI_DOMAIN_NAME . '/view/' . $item->id]);

        $viewHistory = !empty($_COOKIE['ecommerce_item_view_history']) ? json_decode($_COOKIE['ecommerce_item_view_history'], true) : [];
        array_unshift($viewHistory, $item->id);
        $viewHistory = array_unique($viewHistory);
        array_splice($viewHistory, 10);
        setcookie("ecommerce_item_view_history", json_encode($viewHistory), time() + 360000, "/");

        if ($quick) {
            $this->view->content($options);
        } else {
            $this->view->page($options);
        }
    }

    public function toggleFavAction($itemId) {
        $result = new Server\Result();
        $item = \Ecommerce\Item::get((int)$itemId);
        if (!$item) {
            $result->success = false;
            $result->content = 'Товар не найден';
            return $result->send();
        }
        if (Users\User::$cur->id) {
            $fav = \Ecommerce\Favorite::get([['user_id', Users\User::$cur->id], ['item_id', $item->id]]);
            if ($fav) {
                $fav->delete();
                $result->content = ['count' => $this->module->getFavoriteCount(), 'newText' => 'В&nbsp;избранное'];
                $result->successMsg = 'Товар успешно убран из избранного';
                return $result->send();
            } else {
                $fav = new \Ecommerce\Favorite([
                    'user_id' => Users\User::$cur->id,
                    'item_id' => $item->id
                ]);
                $fav->save();
                $result->content = ['count' => $this->module->getFavoriteCount(), 'newText' => 'Из&nbsp;избранного'];
                $result->successMsg = 'Товар успешно добавлен в избранное';
                return $result->send();
            }
        } else {
            $favs = !empty($_COOKIE['ecommerce_favitems']) ? json_decode($_COOKIE['ecommerce_favitems'], true) : [];
            if (in_array($item->id, $favs)) {
                unset($favs[array_search($item->id, $favs)]);
                setcookie("ecommerce_favitems", json_encode($favs), time() + 360000, "/");
                $result->content = ['count' => $this->module->getFavoriteCount(), 'newText' => 'В&nbsp;избранное'];
                $result->successMsg = 'Товар успешно убран из избранного';
                return $result->send();
            } else {
                $favs[] = $item->id;
                setcookie("ecommerce_favitems", json_encode($favs), time() + 360000, "/");
                $result->content = ['count' => $this->module->getFavoriteCount(), 'newText' => 'Из&nbsp;избранного'];
                $result->successMsg = 'Товар успешно добавлен в избранное';
                return $result->send();
            }
        }
    }
}