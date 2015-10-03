<?php
include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/news.php');

class NewsController extends MainApplicationController {

    //private $products = NULL;
    private $isAjax;

    public function init() {
        parent::init();

	$this->isAjax = FALSE;
	$this->tplVars['page_css'][] = 'news.css';
	$this->tplVars['header']['actions']['names'] = array(
            array('name' => 'edit/lang/en', 'menu_name' => 'News EN'),
			array('name' => 'edit/lang/de', 'menu_name' => 'News DE'),
            array('name' => 'edit/lang/fr', 'menu_name' => 'News FR'),
			array('name' => 'edit/lang/es', 'menu_name' => 'News ES'),
            array('name' => 'edit/lang/it', 'menu_name' => 'News IT'),
			array('name' => 'edit/lang/nl', 'menu_name' => 'News NL'),
            array('name' => 'edit/lang/ru', 'menu_name' => 'News RU'),
        );
	$controllId = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
        $this->tplVars['lvals']['canEdit'] = $this->user->checkWritePerm($controllId);
        $this->tplVars['lvals']['canDelete'] = $this->user->checkDelPerm($controllId);
    }

    public function __destruct() {
	if(!$this->isAjax) {
           $this->display();
        }
	$this->isAjax = NULL;
        parent::__destruct();
    }
   public function indexAction() {
	    $lang = 'en';
		if($this->_hasParam('lang'))
				$lang = $this->_getParam('lang');
        $this->_redirect('/news/edit/lang/'.$lang.'/');
    }
   public function editAction() {
	if($this->tplVars['lvals']['canEdit']) {
	 //   array_push($this->tplVars['header']['actions']['names'], array('name' => 'edit', 'menu_name' => 'Edit News'));
	    //array_push($this->tplVars['page_js'], 'features.js');
	    array_push($this->viewIncludes, 'news/Add.tpl');
	    array_push($this->viewIncludes, 'news/List.tpl');
	    if( $this->_hasParam('lang')) {
		$lang = $this->_getParam('lang');
		$saved = ($this->_hasParam('saved') ? $this->_getParam('saved') : '0');
		$news = new AdminNews();
		$this->tplVars['lvals']['language'] = $lang;
	//	$this->tplVars['lvals']['product'] = $pId;
	//	$this->tplVars['lvals']['title'] = $products->getProduct($pId);
		$this->tplVars['lvals']['saved'] = $saved;
		$this->tplVars['news'] = $news->getNews($lang);
	    }
	}
	else {
//	    $this->_redirect('/news/list/');
	}
    }

    public function dropAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canDelete']) {
	    if($this->_hasParam('fid') && $this->_hasParam('lang')) {
		$fId = $this->_getParam('fid');
		$lang = $this->_getParam('lang');
		$news = new AdminNews();
		$news->dropNews($fId,$lang);
	    }
	}
	$this->_redirect('/news/edit/lang/'.$lang.'/');
    }

    public function saveAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canEdit']) {
	    if($this->_hasParam('lang')) {
		$lang = $this->_getParam('lang');
		$news = new AdminNews();
		$news->saveNews($this->_request->getPost(),$lang);
	    }
	}
	$this->_redirect('/news/edit/lang/'.$lang.'/saved/1/');
    }

    public function addAction() {
	 $lang = 'en';
	if($this->tplVars['lvals']['canEdit']) {
	    if($this->_hasParam('lang')) {
				$lang = $this->_getParam('lang');
			$news = new AdminNews();
		$news->addNews($lang, $this->_request->getPost());
	    }
	}
	$this->_redirect('/news/edit/lang/'.$lang.'/saved/2/');
    }
}

?>