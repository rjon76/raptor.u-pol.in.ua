<?php
class Hasher {
    private $_constData = NULL;
    private $dbName;
    private $localPath;


    public function __construct($localPath) {
	$this->dbName = '';
	$this->localPath = $localPath;
	if(VBox::isExist('ConstData')) {
	    $this->_constData = VBox::get('ConstData');
	} else {
	    VBox::set('ConstData', new ConstData());
	    $this->_constData = VBox::get('ConstData');
        }
	$this->_constData->setConst('request','');
    }

    public function __destruct() {
	$this->_constData = NULL;
	$this->dbName = NULL;
	$this->localPath = NULL;
    }

    public function setDBName($dbName) {
	if(!empty($dbName)) {
	    $this->dbName = $dbName.'.';
	}
    }
/*
    public function setConstData(ConstData $consts) {
	$this->_constData = $consts;
	$this->_constData->setConst('request','');
    }
*/
// !!!!!!!!!!!!!!!!!!!!!!!
    public function rebuildHash($pageId) {
	if(is_object($this->_constData)) {
	    ob_start();
	    $page = new PageReCacher($pageId, $this->dbName);
	    $pageHandler = new RealPageHandler($this->localPath, $this->dbName);
            $pageHandler->printPage();
	    $content = ob_get_contents();
	    ob_clean();
	    if(!empty($content)) {
		$hash = md5($content);
		if($hash != $page->checksum) {
		    $page->refreshLastmodify($hash);
		}
	    }
	    unset($page, $pageHandler);
	    return TRUE;
	}
    }
}

?>