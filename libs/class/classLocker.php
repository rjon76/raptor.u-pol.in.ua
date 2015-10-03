<?php
class Locker {
    private $cachePath;
    private $isValidFS;
    private $lockFile;
    private $isLocked;

    public function __construct($cachePath) {
	$this->isLocked = FALSE;
	$this->isValidFS = FALSE;
	$this->cachePath = '';
	if(file_exists($cachePath)) {
	    $this->cachePath = $cachePath;
	    $this->isValidFS = TRUE;
	}
    }

    public function __destruct() {
	$cachePath = NULL;
	$isValidFS = NULL;
	$lockFile = NULL;
	$isLocked = NULL;
    }

    public function prepareLockFile($lockFile) {
	if($this->isValidFS) {
	    $this->lockFile = $lockFile;
	    $this->isLocked = TRUE;
	    if(!file_exists($this->cachePath.$this->lockFile)) {
		if($fp = fopen($this->cachePath.$this->lockFile,'w')) {
		    fclose($fp);
		    chmod($this->cachePath.$this->lockFile,0664);
		    $this->isLocked = FALSE;
		}
		else {
		    $this->isValidFS = FALSE;
		}
	    }
	    elseif(!is_writable($this->cachePath.$this->lockFile)) {
		$this->isValidFS = FALSE;
	    }
	    else {
		if(filesize($this->cachePath.$this->lockFile) < 1) {
		    $this->isLocked = FALSE;
		}
	    }
	}
    }

    public function isLocked() {
	return ($this->isLocked || !$this->isValidFS);
    }

    public function fileLock() {
	if($this->isValidFS && !$this->isLocked) {
	    if(file_put_contents($this->cachePath.$this->lockFile,date('m-d-Y H:i:s')) !== FALSE) {
		$this->isLocked = TRUE;
		return TRUE;
	    }
	}
	return FALSE;
    }

    public function fileUnLock() {
	if($this->isValidFS && $this->isLocked) {
	    if(file_put_contents($this->cachePath.$this->lockFile,'') !== FALSE) {
		$this->isLocked = FALSE;
		return TRUE;
	    }
	}
	return FALSE;
    }

}

?>