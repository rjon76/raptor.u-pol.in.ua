<?php

class VSession {
    private $sessionName = 'sEd8yF6JKvds68I312afvaDEf3f4CZLZRkjmrb82';
    private $isValid;
    private $figerprint;
    private $fingerHead = 'vT7fOB8wdgb2ceJ92we02Ubb6t4fED4w56tF';

    public function __construct($sessionName = '', $fingerHead = '') {
	ini_set('session.use_only_cookies','1');
	if(!empty($sessionName)) {
	    $this->sessionName = $sessionName;
	}
	if(!empty($fingerHead)) {
	    $this->fingerHead = $fingerHead;
	}
	$this->isValid = FALSE;
	if(isset($_COOKIE[$this->sessionName])) {
	    session_name($this->sessionName);
	    session_start();
	    $this->figerprint = md5($this->fingerHead.$_SERVER['HTTP_USER_AGENT'].session_id());
	    if(!empty($_SESSION['fingerprint']) && $_SESSION['fingerprint'] == $this->figerprint) {
		$this->isValid = TRUE;
	    }
	    else {
		unset($_SESSION['fingerprint']);
		session_unset();
		session_destroy();
	    }
	}
    }

    public function __destruct() {
	ini_restore('session.use_only_cookies');
	$this->sessionName = NULL;
	$this->isValid = NULL;
	$this->figerprint = NULL;
	$this->fingerHead = NULL;
    }

    public function isValid() {
	return $this->isValid;
    }

    public function setFingerprint() {
	session_name($this->sessionName);
	session_start();
	$_SESSION['fingerprint'] = md5($this->fingerHead.$_SERVER['HTTP_USER_AGENT'].session_id());
    }
}

?>