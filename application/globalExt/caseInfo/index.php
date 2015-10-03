<?php

include_once(ENGINE_PATH.'class/classCase.php');

class caseInfo {

    private $case;

    public function __construct() {
        $this->case = new CCase();
    }

    public function __destruct() {
        $this->case = NULL;
    }

    public function getCases() {
		var_dump($this->case->getCases());
        return $this->case->getCases();
    }
}

?>