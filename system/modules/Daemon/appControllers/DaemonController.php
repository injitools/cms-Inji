<?php

class DaemonController extends Controller {
    function startAction() {
        ignore_user_abort(true);
        $this->module->start(true);
    }

    function checkAction() {
        $this->module->check();
    }
}