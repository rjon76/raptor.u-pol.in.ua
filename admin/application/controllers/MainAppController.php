<?php

include_once('models/user.php');
include_once('models/log.php');
include_once('models/controllersHandler.php');
/**
 *  Load the classes we need:
 */
Zend_Loader::loadClass('Zend_Controller_Action');
   
abstract class MainApplicationController extends Zend_Controller_Action {

    #PIVATE VARIABLES
    private $auth;
    private $siteId;
    private $dbAdapter;

    #PUBLIC VARIABLES
    public $tplVars;

    #PROTECTED VARIABLES
    protected $viewIncludes; // Smarty template names to include
    protected $user;
    protected $curControllerActions;
    protected $controllers;

	/*
		логирование событий
	*/
    public function to_log( $message = NULL )
	{
		parent::getRequest();
		$request 	= $this->getRequest();
        $controller = $request->getControllerName();
        $action 	= $request->getActionName();
		
			$data = array(
				'log_user' => $this->user->getLogin(),
				'log_ip' => $_SERVER['REMOTE_ADDR'],
				'log_controller' => $controller,
				'log_action' => $action,
				'log_request' => $_SERVER['REQUEST_URI'],
				'log_message' => $message,
			);
			
			$log = new Log($this->siteId);
			$log->addLog( $data );
	}

    /*
     "Коструктор"
    */
    public function init()
	{
        parent::getRequest();

        $this->view 				= new Smarty();
        $this->view->template_dir 	= ROOT_DIR.'/application/views/';
		$this->view->compile_dir 	= ROOT_DIR.'/application/views_c/';
		$this->view->assign('BASE_URL', BASE_URL);
		$this->view->assign('ROOT_DIR', ROOT_DIR);
		$this->view->assign('ADMIN_DIR', ADMIN_DIR);
		

		$this->view->caching 		= false;

        $this->viewIncludes 		= array();
		$this->dbAdapter 			= Zend_Registry::get('dbAdapter');

        $this->auth 				= Zend_Auth::getInstance();
		//---------- from predispatch
		$request 	= $this->getRequest();
        $controller = $request->getControllerName();
        $action 	= $request->getActionName();

        if (!$this->auth->hasIdentity() && $controller != 'auth')
		{
			$this->_redirect('/auth/');
        }
		else if($this->auth->hasIdentity() && $controller == 'auth' && $action != 'logout')
		{
			$this->_redirect('/');
        }
		// ---------------

		if($this->auth->hasIdentity())
		{
            $this->user = new User($this->auth->getStorage()->read()->u_id);
        }
		else
		{ 
            return;
        }

        $this->controllers = new ControllersHandler();

        if(!$this->user->checkReadPerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName())))
		{
            $this->_redirect('/pages/');
        }

    /*    if(!($this->siteId = $this->getCookie('cur_site_id')))
		{
            $this->siteId = 2;
        }
*/
  
      $sitesList = $this->getSitesList();
        $this->tplVars['header'] = array(
            'username'     => $this->user->getLogin(),
            'sites'        => $sitesList,
            'curSite'      => $sitesList['names'][$this->siteId],
            'curHost'      => $this->getNCSiteHostname(),
            'controllers'  => $this->getControllersList(),
            'actions'      => array('selected' => $this->getRequest()->getActionName()),
            'curController'=> $this->getRequest()->getControllerName(),
            'adminHost'    => $this->getXadmin());
		$this->tplVars['page_css'] = array();
		$this->tplVars['page_js'] = array();
		

		$config = new Zend_Config_Ini( ROOT_DIR.'/application/config.ini', 'general' );
		$filter = $config->log->toArray();
		foreach ( $filter as $k => $f) {
			$filter[$k] =  explode(",",$f);
		}
		if ( isset( $filter[$controller] ) &&  in_array( $action, $filter[$controller] ) ) {
			$this->to_log( );	
		}		
	
    }

    public function __destruct() {
        $this->auth                 = NULL;
        $this->viewIncludes         = NULL;
        $this->user                 = NULL;
        $this->curControllerActions = NULL;
        $this->siteId               = NULL;
        $this->tplVars              = NULL;
    }

    /*
     Получить список кантроллеров привязаных к текущему сайту
    */
    private function getControllersList() {
        $tmp = $this->controllers->getControllersList($this->siteId);
        $out['names'] = $tmp['site_related'];

        foreach($out['names'] AS $contId => $controller) {
            if(!$this->user->checkReadPerm($contId)) {
                unset($out['names'][$contId]);
            }
        }

        $out['selected'] = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
        return $out;
    }

    /*
     Получить список сайтов
    */
    public function getSitesList() {

		$select = $this->dbAdapter->select();
        $select->from('sites', array('s_id','s_hostname'));
		$select->order('sites.s_hostname');
        $sitesRes = $this->dbAdapter->fetchPairs($select->__toString());
        
        if(!($this->siteId = $this->getCookie('cur_site_id')))
		{
            $this->siteId = min(array_keys($sitesRes));
        }
        
		if (!array_key_exists($this->siteId,$sitesRes))
		{
            $this->siteId = min(array_keys($sitesRes));
        }
		        
		if (!file_exists($this->getSiteDir().'index.php'))
		{
            $this->siteId = min(array_keys($sitesRes));
        }

        $sites['names'] = $sitesRes;
        $sites['selected'] = $this->siteId;

        return $sites;
    }

    /*
     Получить hostname не кешируемой версии сайта
    */
    public function getNCSiteHostname() {
        $select = $this->dbAdapter->select();
        $select->from('sites', array('s_nc_hostname'));
        $select->where('s_id = ?', $this->siteId);

        return $this->dbAdapter->fetchOne($select->__toString());
    }

    /*
     Вовод страници на экран
    */
    public function display() {
        if(!empty($this->tplVars)) {
            foreach($this->tplVars AS $key => $value) {
                $this->view->assign($key, $value);
            }
        }

        $this->view->assign('files_to_include', $this->viewIncludes);
        $this->view->display('index.tpl');
    }


    public function preDispatch() {
/*
        $request = $this->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!$this->auth->hasIdentity() && $controller != 'auth') {
            $this->_redirect('/auth/');
        } else if($this->auth->hasIdentity() && $controller == 'auth' && $action != 'logout') {
            $this->_redirect('/');
        }
							       */
    }

    /*
     Получить куку
    */
    public function getCookie($cookie_name) {
	   return (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : false);
    }
    
    public function setCookie($cookie_name,$value, $days=365) {
	    setcookie($cookie_name, $value, time()+60*60*24*$days, '/', $_SERVER['HTTP_HOST'], false);
    }   

    /*
     Получть ID текущего сайта
    */
    public function getSiteId() {
        return $this->siteId;
    }

    public function getSiteDir() {
        $select = $this->dbAdapter->select();
        $select->from('sites', array('s_path'));
        $select->where('s_id = ?', $this->siteId);
        return $this->dbAdapter->fetchOne($select->__toString());
    }
	
	
	public function getIsAjaxRequest()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}

    public function getSiteDbName() {
        $select = $this->dbAdapter->select();
        $select->from('sites', array('s_dbname'));
        $select->where('s_id = ?', $this->siteId);
        return $this->dbAdapter->fetchOne($select->__toString());
    }	
    
    public function getXadmin(){
        return BASE_URL;
    }
	
	public function setFlash($key, $message){
		$this->tplVars['page']['flash'][$key] = $message;	
	}
}

?>