<?php

namespace Inji;

class I18n extends Module {
    public $name = 'I18n';
    public $curLang = '';

    function lang() {
        return $this->curLang ? $this->curLang : $this->config['defaultLang'];
    }
}