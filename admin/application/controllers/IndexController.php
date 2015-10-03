<?php

class IndexController extends MainApplicationController {

    #PIVATE VARIABLES

    #PUBLIC VARIABLES

    public function init() {
        parent::init();
    }

    public function __destruct() {
        $this->display();
    }

    public function indexAction() {
        $this->_redirect('/pages/');
    }
}

?>