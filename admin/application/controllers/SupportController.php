<?php
include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/products.php');

class SupportController extends MainApplicationController {

    //private $products = NULL;
    private $isAjax;

    public function init() {
        parent::init();

		$this->isAjax = FALSE;
		$this->tplVars['page_css'][] = 'products.css';
		$this->tplVars['header']['actions']['names'] = array(
				array('name' => 'index', 'menu_name' => 'Support'),
				array('name' => 'add', 'menu_name' => 'Support Add'),
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

//-----------------//

    public function indexAction()
	{
		include_once('support.php');
		array_push($this->viewIncludes, 'support/List.tpl');
		$support = new Support();
		$this->tplVars['support'] = $support->selectAll();
		unset($support);
    }

    public function editAction()
	{
		
		if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id'))
		{
    		include_once('support.php');
			$id = $this->_getParam('id');
		    $this->tplVars['lvals']['postRes'] = 2;
			$support = new Support();
			$this->tplVars['support'] = $support->getById($id); 

			if ($this->_request->getPost('ispost'))
			 {
				$this->tplVars['platforms'] = $this->_request->getPost();
				if (($this->tplVars['lvals']['postRes'] = $support->update($id, $this->_request->getPost())) > 0)
				{
					$this->_redirect('support/');
				}
				$select_prods = $this->_request->getPost('products_support');
				foreach ($select_prods as $val) {
					$select[$val] = $val;
				}
		    } else {
				$select_prods = $support->selectSuportProd($id);
				foreach ($select_prods as $val) {
					$select[$val['ps_product_id']] = $val;
				}
			}
			$products = new AdminProducts();
			$prods = $products->getProducts();
			foreach ($prods['prods'] as $cat=>$prod)
			{
				foreach ( $prod as $key=>$val) {
					$prods['prods'][$cat][$key]['select'] = ($select[$key] ? 1 : 0);
				}
			}
			$this->tplVars['products'] = $prods;
			
	    	array_push($this->tplVars['page_css'], 'product_edit.css');
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
			$this->tplVars['lvals']['isNewRecord'] = false;
			$this->tplVars['header']['actions']['names'][] = array('name' => 'supportedit', 'menu_name' => 'Support Edit');							
			array_push($this->viewIncludes, 'support/Edit.tpl');
			unset($os);

		}
		else
			$this->_redirect('support/');
    }

    public function addAction()
	{
		if($this->tplVars['lvals']['canEdit'])
		{
			include_once('support.php');
		    if ($this->_request->getPost('ispost'))
			{
			    $support = new Support();
				$this->tplVars['support'] = $this->_request->getPost();
				if (($this->tplVars['lvals']['postRes'] = $support->add($this->_request->getPost())) > 0)
				{
					$this->_redirect('support/');
				}
				$select_prods = $this->_request->getPost('products_support');
				foreach ($select_prods as $val) {
					$select[$val] = $val;
				}
		    } 
			$products = new AdminProducts();
			$prods = $products->getProducts();
			foreach ($prods['prods'] as $cat=>$prod)
			{
				foreach ( $prod as $key=>$val) {
					$prods['prods'][$cat][$key]['select'] = ($select[$key] ? 1 : 0);
				}
			}
			$this->tplVars['products'] = $prods;
			
		    array_push($this->tplVars['page_css'], 'product_edit.css');
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
			$this->tplVars['lvals']['isNewRecord'] = true;
		    array_push($this->viewIncludes, 'support/Edit.tpl');
		    $this->tplVars['lvals']['postRes'] = 2;
			unset($support);
		}
		else
			$this->_redirect('support/');
    }
	
	public function deleteAction() {
        if($this->_hasParam('id') && $this->tplVars['lvals']['canDelete'])
		 {
    		include_once('support.php');
			$support = new Support();
            $support->delete($this->_getParam('id'));
			unset($support);
        }
           $this->_redirect('support/');		
    }
    

}

?>