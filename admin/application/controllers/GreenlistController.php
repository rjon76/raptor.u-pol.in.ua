<?php

include_once('models/greenList.php');

class GreenlistController extends MainApplicationController {

    private $greenList;

    public function init() {
        parent::init();

	$this->tplVars['page_css'][] = 'livevalidation.css';
	$this->tplVars['page_css'][] = 'greenlist.css';
	$this->tplVars['page_js'][] = 'livevalidation.js';
	$this->tplVars['glVals']['canEdit'] = $this->user->checkWritePerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
        $this->tplVars['glVals']['canDelete'] = $this->user->checkDelPerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'listsimple', 'menu_name' => 'Green list'),
            array('name' => 'addsimple', 'menu_name' => 'Add to Green list'),
            array('name' => 'listext', 'menu_name' => 'Extended Green list'),
	    array('name' => 'addext', 'menu_name' => 'Add Extended Green list')
        );
	$this->greenList = new GreenList($this->getSiteId());
    }

    public function __destruct() {
	$this->display();
	$this->greenList = NULL;
        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/greenlist/listsimple/');
    }

    public function listsimpleAction() {
	array_push($this->viewIncludes, 'greenlist/simplelistList.tpl');
	$this->tplVars['glist'] = $this->greenList->getSimpleGreenList();
    }

    public function addsimpleAction() {
	if($this->tplVars['glVals']['canEdit']) {
	    array_push($this->viewIncludes, 'greenlist/simplelistAdd.tpl');
	    $this->tplVars['lvals']['postRes'] = 2;
	    $this->tplVars['lform']['header']['values'] = array_keys($this->greenList->getShortHeaders());
	    $this->tplVars['lform']['header']['select'] = '1';
	    $this->tplVars['lform']['header']['output'] = array_values($this->greenList->getShortHeaders());
	    $this->tplVars['lvals']['is404'] = $this->greenList->get404();
	    $this->tplVars['lvals']['addr404'] = $this->greenList->get404Address();
	    $this->tplVars['lform']['destination'] = $this->greenList->getBaseAddress();
	    if ($this->_request->getPost('lform')) {
		$this->tplVars['lform']['header']['select'] = $this->_request->getPost('header');
		$this->tplVars['lform']['address'] = ($this->_request->getPost('address') ? $this->_request->getPost('address') : '');
		$this->tplVars['lform']['destination'] = ($this->_request->getPost('destination') ? $this->_request->getPost('destination') : '');
		if(empty($this->tplVars['lform']['address']) ||
		    ($this->tplVars['lform']['header']['select'] != $this->tplVars['lvals']['is404']
		    && empty($this->tplVars['lform']['destination']))
		) {
		    $this->tplVars['lvals']['postRes'] = 0;
		}
		if(substr($this->tplVars['lform']['address'],0,1) != '/') {
		    $this->tplVars['lform']['address'] = '/'.$this->tplVars['lform']['address'];
		}
		if($this->tplVars['lvals']['postRes'] == 2) {
		    $this->tplVars['lvals']['postRes'] = $this->greenList->addSimpleListRow($this->tplVars['lform']);
		}
	    }
	}
	else {
	    $this->_redirect('/greenlist/listsimple/');
	}
    }

    public function editsimpleAction() {
	if($this->tplVars['glVals']['canEdit'] && $this->_hasParam('id')) {
	    array_push($this->viewIncludes, 'greenlist/simplelistEdit.tpl');
	    $this->tplVars['lform'] = $this->greenList->getSimpleListRow($this->_getParam('id'));
	    $this->tplVars['lvals']['postRes'] = 2;
	    $this->tplVars['lform']['header']['values'] = array_keys($this->greenList->getShortHeaders());
	    $this->tplVars['lform']['header']['output'] = array_values($this->greenList->getShortHeaders());
	    $this->tplVars['lvals']['is404'] = $this->greenList->get404();
	    $this->tplVars['lvals']['addr404'] = $this->greenList->get404Address();
	    if ($this->_request->getPost('lform')) {
                $this->tplVars['lform']['address'] = $this->_request->getPost('address');
                $this->tplVars['lform']['destination'] = $this->_request->getPost('destination');
		$this->tplVars['lform']['header']['select'] = $this->_request->getPost('header');
		if(empty($this->tplVars['lform']['header']['select']) ||
		   ($this->tplVars['lform']['header']['select'] != $this->tplVars['lvals']['is404']
		    && empty($this->tplVars['lform']['destination'])) || empty($this->tplVars['lform']['address'])) {
		    $this->tplVars['lvals']['postRes'] = 0;
		}
                if($this->tplVars['lvals']['postRes'] == 2) {
                    $this->tplVars['lvals']['postRes'] = $this->greenList->setSimpleListRow($this->_getParam('id'), $this->tplVars['lform']);
                }
            }
	}
	else {
	    $this->_redirect('/greenlist/listsimple/');
	}
    }

    public function deletesimpleAction() {
	if($this->tplVars['glVals']['canDelete'] && $this->_hasParam('id')) {
	    $this->greenList->dropSimpleListRow($this->_getParam('id'));
	}
        $this->_redirect('/greenlist/listsimple/');
    }

    public function listextAction() {
	array_push($this->viewIncludes, 'greenlist/extlistList.tpl');
	$this->tplVars['glist'] = $this->greenList->getExtGreenList();
    }

    public function addextAction() {
	if($this->tplVars['glVals']['canEdit']) {
	    array_push($this->viewIncludes, 'greenlist/extlistAdd.tpl');
	    $this->tplVars['lvals']['postRes'] = 2;
	    $this->tplVars['lform']['header']['values'] = array_keys($this->greenList->getShortHeaders());
	    $this->tplVars['lform']['header']['select'] = array('1');
	    $this->tplVars['lform']['header']['output'] = array_values($this->greenList->getShortHeaders());
	    $this->tplVars['lvals']['is404'] = $this->greenList->get404();
	    $this->tplVars['lvals']['addr404'] = $this->greenList->get404Address();
	    $this->tplVars['lform']['destination'] = $this->greenList->getBaseAddress();
	    $this->tplVars['lform']['regular'] = '0';
	    if ($this->_request->getPost('lform')) {
		$this->tplVars['lform']['header']['select'] = $this->_request->getPost('header');
		$this->tplVars['lform']['expression'] = ($this->_request->getPost('expression') ? $this->_request->getPost('expression') : '');
		$this->tplVars['lform']['destination'] = ($this->_request->getPost('destination') ? $this->_request->getPost('destination') : '');
		$this->tplVars['lform']['order'] = ($this->_request->getPost('order') ? intval($this->_request->getPost('order')) : '0');
		$this->tplVars['lform']['regular'] = ($this->_request->getPost('regular') ? '1' : '0');
		if(empty($this->tplVars['lform']['expression']) ||
		    ($this->tplVars['lform']['header']['select'] != $this->tplVars['lvals']['is404']
		    && empty($this->tplVars['lform']['destination']))
		) {
		    $this->tplVars['lvals']['postRes'] = 0;
		}
		if($this->tplVars['lvals']['postRes'] == 2) {
		    $this->tplVars['lvals']['postRes'] = $this->greenList->addExtListRow($this->tplVars['lform']);
		}
	    }
	}
	else {
	    $this->_redirect('/greenlist/listext/');
	}
    }

    public function editextAction() {
	if($this->tplVars['glVals']['canEdit'] && $this->_hasParam('id')) {
	    array_push($this->viewIncludes, 'greenlist/extlistEdit.tpl');
	    $this->tplVars['lform'] = $this->greenList->getExtListRow($this->_getParam('id'));
	    $this->tplVars['lvals']['postRes'] = 2;
	    $this->tplVars['lform']['header']['values'] = array_keys($this->greenList->getShortHeaders());
	    $this->tplVars['lform']['header']['output'] = array_values($this->greenList->getShortHeaders());
	    $this->tplVars['lvals']['is404'] = $this->greenList->get404();
	    $this->tplVars['lvals']['addr404'] = $this->greenList->get404Address();
	    if ($this->_request->getPost('lform')) {
                $this->tplVars['lform']['expression'] = $this->_request->getPost('expression');
                $this->tplVars['lform']['destination'] = $this->_request->getPost('destination');
		$this->tplVars['lform']['header']['select'] = $this->_request->getPost('header');
		$this->tplVars['lform']['order'] = ($this->_request->getPost('order') ? intval($this->_request->getPost('order')) : '0');
		$this->tplVars['lform']['regular'] = ($this->_request->getPost('regular') ? '1' : '0');
                if(empty($this->tplVars['lform']['header']['select']) ||
		   ($this->tplVars['lform']['header']['select'] != $this->tplVars['lvals']['is404']
		    && empty($this->tplVars['lform']['destination'])) || empty($this->tplVars['lform']['expression'])) {
		    $this->tplVars['lvals']['postRes'] = 0;
		}
		if($this->tplVars['lvals']['postRes'] == 2) {
                    $this->tplVars['lvals']['postRes'] = $this->greenList->setExtListRow($this->_getParam('id'), $this->tplVars['lform']);
                }
            }
	}
	else {
	    $this->_redirect('/greenlist/listext/');
	}
    }

    public function deletextAction() {
	if($this->tplVars['glVals']['canDelete'] && $this->_hasParam('id')) {
	    $this->greenList->dropExtListRow($this->_getParam('id'));
	}
        $this->_redirect('/greenlist/listext/');
    }
}

?>