<?php

include_once('models/controllersHandler.php');

class ControllersController extends MainApplicationController {

    #PIVATE VARIABLES

    #PUBLIC VARIABLES

    public function init() {
        parent::init();

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Controllers list')
        );

        $this->controllers = new ControllersHandler();
    }

    public function __destruct() {
        $this->display();
    }

    public function indexAction() {
        $this->_redirect('/controllers/list/');
    }

    public function listAction() {
        if($this->_request->isPost()) {

            if($this->_request->getPost('contadd')) {
                $this->addController();
            }
        }

        $contList = $this->controllers->getControllersList();

        $this->tplVars['conts']['contsList'] = $contList['all'];
        array_push($this->viewIncludes, 'controllers/controllersList.tpl');
        array_push($this->viewIncludes, 'controllers/controllersAdd.tpl');
    }

    public function deleteAction() {
        if($this->_hasParam('id')) {
            $this->controllers->deleteController($this->_getParam('id'));
        }

        $this->_redirect('/controllers/list/');
    }

    public function editAction() {
        if($this->_hasParam('id')) {
			
			$contId = $this->_getParam('id');
            
			if ($this->_request->isPost()) {
			 	if($this->_request->getPost('controller')) {
			
					Zend_Loader::loadClass('Zend_Filter_StripTags');
			        $filter = new Zend_Filter_StripTags();
					$attributes = $this->_request->getPost('controller');
					$name = $filter->filter($attributes[cont_name]);
			        $menuName = $filter->filter($attributes['cont_menu_name']);
        			$is_dependent = ($attributes['is_site_dependent'] ? true : false);
		    	    if(!strlen($name)) {
        			    $this->tplVars['conts']['err']['contName'] = true;
			        }
	
        			if(!strlen($menuName)) {
			            $this->tplVars['conts']['err']['contMenuName'] = true;
		    	    }
					
					$tmp = $this->controllers->getControllerIdByName($name);
					if( $tmp && $this->controllers->getControllerIdByName($name) !== $contId) {
		            	$this->tplVars['conts']['err']['contNameExist'] = true;
			        }

    	    		if(!isset($this->tplVars['conts']['err'])) {
						 $this->controllers->changeController($contId, $name, $menuName, $is_dependent);
		    	    }
				
				}
               	if($this->_request->getPost('reladd')) {
                    	$this->addRealtion();
				}
            }

           
            $contData = $this->controllers->getControllerData($contId);
            $contRels = $this->controllers->getController2SiteRelationList($contId);
            $sites = $this->getSitesList();

            foreach($contRels AS $relation) {
                if(isset($sites['names'][$relation['sc_site_id']])) {
                    unset($sites['names'][$relation['sc_site_id']]);
                }
            }

            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit Controller');
            $this->tplVars['conts']['val']['contName'] = $contData['c_name'];
            $this->tplVars['conts']['val']['contMenuName'] = $contData['c_menu_name'];
            $this->tplVars['conts']['val']['siteDependent'] = $contData['c_is_site_dependent'];
            $this->tplVars['conts']['relsList'] = $contRels;
            $this->tplVars['conts']['sitesList'] = $sites['names'];

            array_push($this->viewIncludes, 'controllers/controllersEdit.tpl');
            array_push($this->viewIncludes, 'controllers/controllersRelations.tpl');
        }
    }

    public function delrelAction() {
        if($this->_hasParam('cont') && $this->_hasParam('site')) {
            $contId = $this->_getParam('cont');
            $siteId = $this->_getParam('site');

            $this->controllers->deleteController2SiteRelation($contId, $siteId);

            $this->_redirect('/controllers/edit/id/'.$contId.'/');
        }
        $this->_redirect('/controllers/list/');
    }

    private function addController() {
        Zend_Loader::loadClass('Zend_Filter_StripTags');
        $filter = new Zend_Filter_StripTags();

        $name = $filter->filter($this->_request->getPost('cont_name'));
        $menuName = $filter->filter($this->_request->getPost('cont_menu_name'));
        $is_dependent = $this->_request->getPost('is_site_dependent');

        if(!strlen($name)) {
            $this->tplVars['conts']['err']['contName'] = true;
        }

        if(!strlen($menuName)) {
            $this->tplVars['conts']['err']['contMenuName'] = true;
        }

        if(strlen($name) && $this->controllers->getControllerIdByName($name)) {
            $this->tplVars['conts']['err']['contNameExist'] = true;
        }

        if(!isset($this->tplVars['conts']['err'])) {
            $this->controllers->addController($name, $menuName, ($is_dependent ? TRUE : FALSE));
        }
    }

    private function addRealtion() {
        $siteId = $this->_request->getPost('site');
        $this->controllers->addController2SiteRelation($this->_getParam('id'), $siteId);
    }
}

?>