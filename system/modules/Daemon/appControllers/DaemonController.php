<?php

class DaemonController extends Controller {
    function startAction() {
        ignore_user_abort(true);
        echo 'start try';
        flush();
        $this->module->start(true);
    }

    function checkAction() {
        ignore_user_abort(true);
        echo 'check try';
        flush();
        $this->module->check();
    }
}