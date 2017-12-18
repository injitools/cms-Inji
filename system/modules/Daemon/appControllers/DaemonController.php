<?php

class DaemonController extends Controller {
    function startAction() {
        ignore_user_abort(true);
        echo 'start';
        $this->module->start(true);
    }

    function checkAction() {
        echo 'check';
        $this->module->check();
    }
}