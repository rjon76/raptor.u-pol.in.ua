<?php
include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/log.php');

class LogController extends MainApplicationController {

    //private $products = NULL;
    private $isAjax;

    public function init() {
        parent::init();
		$this->isAjax = FALSE;
/*
		$this->tplVars['page_css'][] = 'products.css';
		$this->tplVars['header']['actions']['names'] = array(
				array('name' => 'index', 'menu_name' => 'Support'),
				array('name' => 'add', 'menu_name' => 'Support Add'),
			);
		$controllId = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
        $this->tplVars['lvals']['canEdit'] = $this->user->checkWritePerm($controllId);
        $this->tplVars['lvals']['canDelete'] = $this->user->checkDelPerm($controllId);
		*/
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
		$log = new Log( $this->getSiteId() );
		
		$filter = array(
			'log_id' =>  $this->getRequest()->getParam('log_id'),
			'log_user' =>  $this->getRequest()->getParam('log_user'),
			'log_ip' =>  $this->getRequest()->getParam('log_ip'),
			'log_controller' =>  $this->getRequest()->getParam('log_controller'),
			'log_action' =>  $this->getRequest()->getParam('log_action'),
			'log_request' =>  $this->getRequest()->getParam('log_request'),
			'log_message' =>  $this->getRequest()->getParam('log_message'),
			/*'log_date' =>  $this->getRequest()->getParam('log_date'),*/
		);
		$url = parse_url($_SERVER['REQUEST_URI'] );

		$pageNumber = $this->getRequest()->getParam('page', 1);
		$paginator = $log->selectAll( $pageNumber , $filter );
		
		$this->tplVars['log'] = $paginator;
		$this->tplVars['pages'] = $paginator->getPages();
		$this->tplVars['filter'] = $filter;
		$this->tplVars['query'] = @$url['query'];
		
		array_push($this->viewIncludes, 'log/List.tpl');		
		unset($log);
    }

}

?>