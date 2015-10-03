<?php

class VAuth {

    private $_storage = NULL;
    private $_consts = NULL;
    //private $prepData;
    private $isValid;

    public function __construct($sessionName = '', $fingerHead = '') {
	$this->_consts = VBox::get('ConstData');
	$this->_storage = new VSession($sessionName, $fingerHead);
	$this->isValid = $this->_storage->isValid();
	//$this->prepData = FALSE;
    }

    public function __destruct() {
	$_storage = NULL;
	$_consts = NULL;
	$isValid = NULL;
	//$this->prepData = NULL;
    }
/*
    public function prepareAuthData() {
	$this->loginField = (!empty($_POST[$loginField]) ? $loginField : '');
	$this->passwdField = (!empty($_POST[$passwdField]) ? $passwdField : '');
	if(!empty($this->loginField) && !empty($this->passwdField)) {
	    $this->prepData = TRUE;
	}
    }
*/
    public function authenticate($loginField, $passwdField) {
        if(!empty($_POST[$loginField]) && !empty($_POST[$passwdField])) {
	    $q = 'SELECT u_passwd
		    FROM '.$this->_consts->getConst('adminDb').'.users
		  WHERE u_login = ?
		  LIMIT 1';
	    DB::executeQuery($q, 'user_data', array($_POST[$loginField]));
	    $row = DB::fetchOne('user_data');
	    if(!empty($row)) {
		if(md5($_POST[$passwdField]) == $row) {
		    $this->_storage->setFingerprint();
		    $this->isValid = TRUE;
		}
	    }
	}
    }

    public function isValid() {
	return $this->isValid;
    }

}
