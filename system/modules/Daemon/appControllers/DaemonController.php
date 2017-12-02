<?php

class DaemonController extends Controller {
    function startAction() {
        $this->module->start();
    }

    function checkAction() {
        $this->module->check();
    }
}