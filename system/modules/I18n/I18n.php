<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 04.11.2017
 * Time: 15:38
 */

class I18n extends Module {
    public $curLang = '';

    function lang() {
        return $this->curLang ? $this->curLang : $this->config['defaultLang'];
    }
}