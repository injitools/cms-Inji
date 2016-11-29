<?php

/**
 * Text blocks admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class TextBlocksController extends Controller {

    public function indexAction() {
        $this->view->setTitle('Текстовые блоки');
        $dataManager = new Ui\DataManager('TextBlocks\Block');
        $this->view->page(['data' => compact('dataManager')]);
    }

}
