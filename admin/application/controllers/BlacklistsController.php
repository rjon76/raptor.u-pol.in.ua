<?php
include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/blacklists.php');

class BlacklistsController extends MainApplicationController {

    //private $products = NULL;
    private $isAjax;

    public function init() {
        parent::init();
		$this->isAjax = FALSE;
		
		$this->tplVars['header']['actions']['names'] = array(
				array('name' => 'list', 'menu_name' => 'List'),
				array('name' => 'add', 'menu_name' => 'Add ip'),
				array('name' => 'generate', 'menu_name' => 'Generate file'),
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
    public function indexAction() {
        $this->_redirect('/blacklists/list/');
    }

    public function listAction()
	{
		$blacklists = new Blacklists( $this->getSiteId() );
		
		$filter = array(
			'bl_id' =>  $this->getRequest()->getParam('bl_id'),
			'bl_ip' =>  $this->getRequest()->getParam('bl_ip'),
			'bl_site_id' =>  $this->getRequest()->getParam('bl_site_id'),
		);
		$url = parse_url($_SERVER['REQUEST_URI'] );

		$pageNumber = $this->getRequest()->getParam('page', 1);
		$paginator = $blacklists->selectAll( $pageNumber , $filter );

		$sites = $this->getSitesList();

		foreach( $paginator as $key => &$val){
			$site_ids = unserialize($val['bl_site_id']);
			foreach($site_ids as $id){
				$val['bl_site'] .= $sites['names'][$id].'<br/>';
			}
			unset($val['bl_site_id']);
		}
		
		$this->tplVars['blacklists'] = $paginator;
		$this->tplVars['pages'] = $paginator->getPages();
		$this->tplVars['filter'] = $filter;
		$this->tplVars['query'] = @$url['query'];
		$this->tplVars['sitesList'] = $sites['names'];
		
		array_push($this->viewIncludes, 'blacklists/blacklistsList.tpl');		
		unset($blacklists);
    }

    public function addAction() {
		if($this->tplVars['lvals']['canEdit'])
		{
			if ($this->_request->getPost('ispost')) {
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();

				$ips = $filter->filter($this->_request->getPost('ips'));
				$site_ids =$this->_request->getPost('site_ids');

				if(!strlen($ips)) {
					$this->tplVars['conts']['err']['ips'] = true;
					$this->tplVars['val']['ips'] = $ips;
					$this->tplVars['val']['site_ids'] = $site_ids;
				} else {
					$blacklists = new Blacklists( );
					$ips = explode ("\n",$ips);
					foreach(array_keys($ips) as $key) {
						$ip = trim($ips[$key]);
						$blacklists->addIp($ip, $site_ids);
					}
					$this->_redirect('/blacklists/list/');
				}
			}
			$sites = $this->getSitesList();
			$this->tplVars['sitesList'] = $sites['names'];
			array_push($this->viewIncludes, 'blacklists/blacklistsAdd.tpl');
		}
    }
	
    public function editAction() {
		if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id'))
		{
			$blacklists = new Blacklists( );
			$blacklistsId = $this->_getParam('id');
			array_push($this->tplVars['header']['actions']['names'], array('name' => 'edit', 'menu_name' => 'Edit Ip'));
			
			if ($this->_request->getPost('ispost')) {
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$filter = new Zend_Filter_StripTags();

				$ips = $filter->filter($this->_request->getPost('ips'));
				$site_ids =$this->_request->getPost('site_ids');

				if(!strlen($ips)) {
					$this->tplVars['conts']['err']['ips'] = true;
					$this->tplVars['val']['ips'] = $ips;
					$this->tplVars['val']['site_ids'] = $site_ids;
				} else {
					$blacklists->updateIp($blacklistsId, $ips, $site_ids);
					$this->_redirect('/blacklists/list/');
				}
			} else {
				$contData = $blacklists->getData($blacklistsId);
				$this->tplVars['val']['ips'] = $contData['bl_ip'];
				$this->tplVars['val']['site_ids'] = unserialize($contData['bl_site_id']);
			}
			
			$sites = $this->getSitesList();
			$this->tplVars['sitesList'] = $sites['names'];
			array_push($this->viewIncludes, 'blacklists/blacklistsEdit.tpl');
		}
    }
	
    public function deleteAction() {
        if($this->_hasParam('id') && $this->tplVars['lvals']['canDelete'])
		 {
		    $blacklists = new Blacklists();
            $blacklists->deleteIp($this->_getParam('id'));
        }
		if($this->_request->getPost('delete') && $this->_request->getPost('del_bl_id')) {
			$blacklists = new Blacklists();
			$ids = $this->_request->getPost('del_bl_id');
			foreach($ids as $id)
				$blacklists->deleteIp( $id );
		}
		$this->_redirect('/blacklists/list/');
    }
	
    public function generateAction() {
		$data = array();
		
		if($this->_request->getPost('generate') && $this->tplVars['lvals']['canEdit']) {
			$blacklists = new Blacklists();

			$data_ips = $blacklists->getAllIps();
			foreach($data_ips as $key => $ip ) {
				$site_ids = unserialize($ip['bl_site_id']);
				$ips[$key] = $ip['bl_ip'];
				foreach($site_ids as $site_id) {
					$not_use[$site_id][$key] = true;
				}
			}
			unset($data_ips);

			
			$sites = $blacklists->getSitesData();
			foreach($sites as $site){
				$data[ $site['s_path'] ] = false;
				if ( is_dir($site['s_path']) ) {
					$site_ips = array_diff_key($ips, ($not_use[$site['s_id']]?$not_use[$site['s_id']]:array()) );
					$site_ips = array_unique($site_ips);
					$tt = file_put_contents($site['s_path'].'blacklists.txt', implode("\n",$site_ips)) ;
					if ($tt)
						$data[ $site['s_path'] ] = true;
				}
			}
		}
		$this->tplVars['sitesList'] = $data;
		array_push($this->viewIncludes, 'blacklists/blacklistsGenerate.tpl');
	}
	
}

?>