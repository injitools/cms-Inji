<?php

class DaemonController extends Controller {
    function startAction() {
        $this->module->start(true);
    }

    function checkAction() {
        $this->module->check();
    }
}