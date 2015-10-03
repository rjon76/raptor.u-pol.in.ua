<?php

include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/localString.php');

class LocalstringController extends MainApplicationController {

	private $isAjax;
    private $localString;

    public function init()
    {
        parent::init();

        $this->isAjax = $this->getIsAjaxRequest();
	
        $this->localString = new LocalString($this->getSiteId());
        
        foreach ($this->localString->getLangs() as $id => $val)
        {
            $this->tplVars['langs'][$id] = array('code' => $val['code'], 'name' => $val['name']);
        }
        
        $this->tplVars['page_css'][] 	= 'dataTables/css/demo_table.css';
        $this->tplVars['page_css'][] 	= 'localize.css';
        $this->tplVars['page_js'][] 	= 'localize.js';
        $this->tplVars['page_js'][] 	= 'tiny_mce/tiny_mce.js';
        $this->tplVars['page_js'][] 	= 'dataTables/js/jquery.dataTables.js';
        
        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'List Localization')
        );
        
        $this->tplVars['lvals']['canEdit'] 		= $this->user->checkWritePerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
        $this->tplVars['lvals']['canDelete'] 	= $this->user->checkDelPerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
        
        if($this->user->getGroupId() == 1)
        {
            $this->tplVars['header']['actions']['names'][] = array('name' => 'add', 'menu_name' => 'Add Localization');
        }
        
        $this->tplVars['header']['actions']['names'][] = array('name' => 'search', 'menu_name' => 'Search Localization');
    }

    
    public function __destruct()
    {
    	if(!$this->isAjax)
    	{
           $this->display();
        }

		$this->isAjax = NULL;
        parent::__destruct();
    }

    
    
    public function indexAction()
    {
        $this->_redirect('/localstring/list/');
    }

    public function listAction()
    {
        $page = ($this->_hasParam('page') ? $this->_getParam('page') : 1);
        $lang = ($this->_hasParam('lang') ? $this->_getParam('lang') : 'en');
        $search = ($this->_hasParam('search') ? $this->_getParam('search') : '');

        $this->tplVars['lvals']['lang'] = $lang;
        $this->tplVars['lvals']['page'] = $page;
        $this->tplVars['lvals']['search'] = $search;
        $this->tplVars['lvals']['count_pages'] = $this->localString->getPagesCount();
        $this->tplVars['lstrings'] = $this->localString->getStrings($lang, $page);
        array_push($this->viewIncludes, 'localize/stringsList.tpl');
    }

    public function addAction() {
        if($this->user->getGroupId() == 1) {
            $this->tplVars['lvals']['postRes'] = 2;
            $this->tplVars['lform']['nick'] = ($this->_getParam('nick') ? $this->_getParam('nick') : '');
                        
            array_push($this->viewIncludes, 'localize/stringsAdd.tpl');

            if ($this->_request->getPost('lform')) {
                $this->tplVars['lvals']['errors'] = array();
                $this->tplVars['lform']['nick'] = ($this->_request->getPost('nick') ? $this->_request->getPost('nick') : '');
                if(!$this->_request->getPost('nick')) {
                    $this->tplVars['lvals']['errors']['nick'] = 'Field &quot;nick&quot; is empty.';
                    $this->tplVars['lvals']['error']['nick'] = 'Field is empty.';
                }
                elseif(!preg_match('/(^[A-Za-z0-9_]+$)/i', $this->_request->getPost('nick'))) {
                    $this->tplVars['lvals']['errors'][] = 'Nick &quot;'.$this->_request->getPost('nick').'&quot;, only &quot;A-Za-z0-9_&quot;.';
                    $this->tplVars['lvals']['error']['nick'] = 'Only &quot;A-Za-z0-9_&quot; characters.';
                }
                elseif(strlen($this->_request->getPost('nick'))>32) {
                    $this->tplVars['lvals']['errors'][] = 'Nick &quot;'.$this->_request->getPost('nick').'&quot; a maximum of 32 characters.';
                    $this->tplVars['lvals']['error']['nick'] = 'A maximum of 32 characters.';
                }
                elseif($this->localString->checkNick($this->_request->getPost('nick'))) {
                    $this->tplVars['lvals']['errors'][] = 'Nick &quot;'.$this->_request->getPost('nick').'&quot; already exist.';
                    $this->tplVars['lvals']['error']['nick'] = 'Already exist.';
                }
                
                foreach ($this->localString->getLangs() as $id => $val) {
                    if(!$this->_request->getPost($val['code'].'_text')) {
                        $this->tplVars['lvals']['errors'][] = 'Field &quot;'.$val['code'].'_text&quot; is empty.';
                        $this->tplVars['lform'][$val['code']] = '';
                        $this->tplVars['lvals']['error'][$val['code']] = 'Field is empty.';
                    }
                    else {
                        $this->tplVars['lform'][$val['code']] = $this->_request->getPost($val['code'].'_text');
                    }
                }
                if(empty($this->tplVars['lvals']['errors'])) {
                    $this->tplVars['lvals']['postRes'] = (int)$this->localString->addString($this->tplVars['lform']);
                }
            }
        }
    }

    public function editAction() {
        if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id') && $this->_hasParam('lang')) {
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit Localization');
            $this->tplVars['lvals']['lang'] = $this->_getParam('lang');
            $this->tplVars['lvals']['id'] = $this->_getParam('id');
            $this->tplVars['lvals']['postRes'] = 2;
            array_push($this->viewIncludes, 'localize/stringsEdit.tpl');
            $this->tplVars['lvals']['canEditNick'] = FALSE;
            if($this->user->getGroupId() == 1) {
                $this->tplVars['lvals']['canEditNick'] = TRUE;
            }
            $this->tplVars['lform'] = $this->localString->getString($this->_getParam('lang'), $this->_getParam('id'));

            if ($this->_request->getPost('lform')) {
                $this->tplVars['lform']['nick'] = $this->_request->getPost('nick');
                $this->tplVars['lform']['text'] = $this->_request->getPost('text');
                if(!empty($this->tplVars['lform']['nick']) && !empty($this->tplVars['lform']['text'])) {
                    $this->tplVars['lvals']['postRes'] = (int)$this->localString->setString($this->tplVars['lvals']['lang'], $this->tplVars['lvals']['id'],
                            $this->tplVars['lform'], $this->tplVars['lvals']['canEditNick']);
                }
            }
        }
        else {
            $this->_redirect('/localstring/list/');
        }
    }

    public function deleteAction() {
        if($this->tplVars['lvals']['canDelete'] && $this->_hasParam('id')) {
            $this->localString->dropString($this->_getParam('id'));
        }
		if (!$this->isAjax){
	        $lang = ($this->_hasParam('lang') ? 'lang/'.$this->_getParam('lang').'/' : '');
    	    $page = ($this->_hasParam('page') ? 'page/'.$this->_getParam('page').'/' : '');
        	$this->_redirect('/localstring/list/'.$lang.$page);
		}
    }
    public function deleteselAction() {
  //      var_dump($this->_request->isAjax());

        if ($this->_request->isPost()) {
            $strIds = $this->_request->getPost('chx');
           echo $this->localString->dropStrings($strIds);
        }
		if (!$this->isAjax){
	        $lang = ($this->_hasParam('lang') ? 'lang/'.$this->_getParam('lang').'/' : '');
    	    $page = ($this->_hasParam('page') ? 'page/'.$this->_getParam('page').'/' : '');
        	$this->_redirect('/localstring/list/'.$lang.$page);
		}
		
    }

    public function searchAction() {
        array_push($this->viewIncludes, 'localize/stringsSearch.tpl');
        $this->tplVars['lform']['search'] = '';
        if ($this->_request->getPost('lform')) {
            $this->tplVars['lform']['subject'] = ($this->_request->getPost('subject') ? $this->_request->getPost('subject') : '');
            $this->tplVars['lform']['search'] = ($this->_request->getPost('search') ? $this->_request->getPost('search') : '');
            $this->tplVars['lstrings'] = $this->localString->searchString($this->tplVars['lform']['subject'], $this->tplVars['lform']['search']);
        }
    }

    public function getstrAction(){
    	$this->isAjax = true;
    	$lang 	= $this->_getParam('lang');
    	$strId 	= $this->_getParam('lid');
    	$str 	= $this->localString->getString($lang, $strId);
    	$this->localString->openEditWindow($str,$lang);
    }

    public function saveAction(){
    	$this->isAjax = true;
    	$lang 			= $this->_getParam('lang');
    	$strId 			= $this->_getParam('lid');
    	$data['isTrans'] 		= $this->_getParam('isTrans');
    	$data['text'] 			= $this->_getParam('txted');
    	if($data['isTrans'] == ''){
    		$data['isTrans'] = 0;
    	}else{
    		$data['isTrans'] = 1;
    	}
    	$res = $this->localString->setString($lang,$strId,$data);
    	echo $res;
    }

    public function savenickAction(){
    	$this->isAjax = true;
    	$strId 			= $this->_getParam('lid');
    	$newnick 		= $this->_getParam('newnick');
    	$res = $this->localString->setNick($strId,$newnick);
    	echo $res;
    }

    public function editnickAction(){
    	$this->isAjax = true;
	   	$lang 	= $this->_getParam('lang');
    	$strId 	= $this->_getParam('lid');
    	$str 	= $this->localString->getString($lang, $strId);
    	$this->localString->openEditNickWindow($str,$lang);
    }


}

?>