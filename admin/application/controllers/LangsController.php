<?php
include_once('models/languages.php');

class LangsController extends MainApplicationController {

    #PRIVATE VARIABLES
    private $langs;
	private $existLangs;
    private $siteDbAdapter;

    public function init() {
        parent::init();

	$this->tplVars['glVals']['canEdit'] = $this->user->checkWritePerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
    $this->tplVars['glVals']['canDelete'] = $this->user->checkDelPerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
    $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Languages'),
            array('name' => 'add', 'menu_name' => 'Add language')
        );
	$this->existLangs = new Languages($this->getSiteId());
    }

    public function __destruct() {
        $this->display();
        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/languages/list/');
    }

    public function listAction() {

        $this->tplVars['langs']['langs'] = $this->existLangs;
		array_push($this->tplVars['page_css'], 'pages.css');
        array_push($this->viewIncludes, 'languages/list.tpl');
    }

    public function addAction() {

        if($this->_request->isPost()) {
            $name = $this->_request->getPost('name');
            $code = $this->_request->getPost('code');
            $addrcode = $this->_request->getPost('addrcode');
            $order = $this->_request->getPost('order');
	        $blocked = isset($this->_request->getPost('blocked')) ? 1 : 0;
			
            $row = array(
                'c_name' => $name,
                'c_code' => $val,
                'c_addrcode' => $addrcode,
                'c_order' => $order,
                'c_blocked' => $blocked		
            );

            $this->siteDbAdapter->insert('languages', $row);
            $this->_redirect('/languages/list/');
        }

        $existLangs = array();
        foreach($this->existLangs AS $langs) {
            $existLangs[] = $langs['l_code'];
        }

        $this->tplVars['langs']['langs'] = array_diff($this->existLangs, $existLangs);
        array_push($this->viewIncludes, 'languages/add.tpl');
    }

    public function editAction() {
        if($this->_hasParam('id')) {
            $constId = $this->_getParam('id');
        	
			if($this->_request->isPost())
			{
            	$name = $this->_request->getPost('name');
	            $code = $this->_request->getPost('code');
	            $addrcode = $this->_request->getPost('addrcode');
	            $order = $this->_request->getPost('order');
		        $blocked = isset($this->_request->getPost('blocked')) ? 1 : 0;
		
	            $row = array(
                'c_name' => $name,
                'c_code' => $val,
                'c_addrcode' => $addrcode,
                'c_order' => $order,
                'c_blocked' => $blocked		
           		 );

                $this->siteDbAdapter->update('languages', $row, $this->siteDbAdapter->quoteInto('l_id = ?', $constId));
                $this->_redirect('/languages/list/');
            }

            $this->tplVars['langs']['val']['name'] = $this->existConstants[$constId]['c_name'];
            $this->tplVars['langs']['val']['code'] = $this->existConstants[$constId]['c_code'];
            $this->tplVars['langs']['val']['addrcode'] = $this->existConstants[$constId]['c_addrcode'];
            $this->tplVars['langs']['val']['order'] = $this->existConstants[$constId]['c_order'];
            $this->tplVars['langs']['val']['blocked'] = $this->existConstants[$constId]['c_blocked'];

			array_push($this->tplVars['header']['actions']['names'], array('name' => 'edit', 'menu_name' => 'Edit language'));
            array_push($this->viewIncludes, 'languages/edit.tpl');
        }
    }

    public function deleteAction() {
        if($this->_hasParam('id')) {
            $constId = $this->_getParam('id');
            $this->siteDbAdapter->delete('languages', $this->siteDbAdapter->quoteInto('l_id = ?', $constId));
            $this->_redirect('/languages/list/');
        }
    }
}

?>