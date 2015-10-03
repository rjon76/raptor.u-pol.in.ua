<?php
include_once('blacklist.php');

class BlacklistController extends MainApplicationController {

    #PIVATE VARIABLES
    private $isAjax;
    private $model;	


    #PUBLIC VARIABLES

    public function init() {
        parent::init();
        $this->isAjax = $this->getIsAjaxRequest();
		$this->tplVars['page_js'][] = 'blacklist.js';
		$this->tplVars['content']['canEdit'] = $this->user->checkWritePerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));
        $this->tplVars['content']['canDelete'] = $this->user->checkDelPerm($this->controllers->getControllerIdByName($this->getRequest()->getControllerName()));

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Black keys list'),
            array('name' => 'add', 'menu_name' => 'Add new'),
			array('name' => 'addMany', 'menu_name' => 'Add many'),
			//array('name' => 'search', 'menu_name' => 'Search'),
			
        );
		$this->model = new Blacklist($this->getSiteId());
    }

    public function __destruct() {
        if(!$this->isAjax) {
           $this->display();
        }

        $this->isAjax = NULL;
        $this->model = NULL;
        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/'.$this->tplVars['header']['curController'].'/list/');
    }

    public function listAction() {

		$where = $this->_request->getPost('bl_name') ? 'bl_name LIKE "%'.$this->_request->getPost('bl_name').'%"' : NULL;
				
		$countAll = $this->model->_getCount($where);
		
		$pagesCount = $this->model->_getPagesCount($countAll , $this->model->recPerPage);

		$page = ($this->_hasParam('page') ? intval($this->_getParam('page')) : 1);
		
		$from = $page - $this->model->pagerLinks +1 ;
		
		if ($from < 1){
			$from = 1;
		}
		
		$to = $from + $this->model->pagerLinks ;
		
		if ($to > $pagesCount){
			$to = $pagesCount+1 ;
		}
		
		$start = $this->model->recPerPage*($page - 1);
		
	    $count = $this->model->recPerPage;

	    if($page == $pagesCount) {
				$count = $countAll - $start;
	    	}elseif($page > $pagesCount || $page < 1) {
				$start = 0;
	    	}
		
		if($from > 1 && $pagesCount > 0){
			$this->tplVars['content']['prev'] = $from - 1 ;
		}
		if( $to <= $pagesCount){
			$this->tplVars['content']['next'] = $to ;
		}
	//	oa(array('page'=>$page, 'pagesCount'=>$pagesCount, 'countAll'=>$countAll,'recPerPage'=> $this->model->recPerPage ));
		$this->tplVars['content']['from'] = $from;
		$this->tplVars['content']['to'] = $to;
		$this->tplVars['content']['count_pages'] = $to - $from ;
        $this->tplVars['content']['data'] = $this->model->_fetchAll(array('where'=> $where, 'order' => 'bl_id', 'count' => $count , 'offset' => $start));
		
		//var_dump($this->tplVars['content']['data']);
		
        array_push($this->viewIncludes, $this->tplVars['header']['curController'].'/list.tpl');
    }

    public function editAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $model = $this->model->findByPk($id);

            if($this->_request->isPost()) {
				
                if($this->_request->getPost('update')) {
					$model->setAttributes($this->_request->getPost());
					$bl_hidden = $this->_request->getPost('bl_hidden') ? 1 : 0;
					$model->setAttribute('bl_hidden', $bl_hidden);
					$model->validate();

					if (!$model->hasErrors()){
						if ($model->_update())
							$this->_redirect('/'.$this->tplVars['header']['curController'].'/list/');
					}
    	           
                }
			}
			

            $this->tplVars['content']['val'] = $model->getAttributes();
            $this->tplVars['model'] = $model;			
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit', 'params' => array('id' => $id));

            array_push($this->viewIncludes, $this->tplVars['header']['curController'].'/edit.tpl');


        } else {
            $this->_redirect('/'.$this->tplVars['header']['curController'].'/list/');
        }
    }

    public function addAction() {

        if($this->_request->isPost()) {
			
			$this->model->setAttributes($this->_request->getPost());
			$bl_hidden = $this->_request->getPost('bl_hidden') ? 1 : 0;
			$this->model->setAttribute('bl_hidden', $bl_hidden);	
			
			$this->model->validate();

			if (!$this->model->hasErrors()){
				$id = $this->model->_insert();
	            if ($id > 0){
		            $this->_redirect('/'.$this->tplVars['header']['curController'].'/list/');
				}
			}
        }

		$this->tplVars['model'] = $this->model;
		$this->tplVars['content']['val'] = $this->model->getAttributes();
        array_push($this->viewIncludes, $this->tplVars['header']['curController'].'/add.tpl');
    }
	
	 public function addManyAction() {

        if($this->_request->isPost()) {
			$nl_char="\n";
			$bl_name = $this->_request->getPost('bl_name');
			$arr=explode($nl_char, $bl_name);
			$arr2 = array_unique($arr); 
			$error = false;
			foreach($arr as $row){
				$this->model->setAttributes(array('bl_name'=>$row, 'bl_hidden'=>0 ));
				$this->model->validate();
				if ($this->model->hasErrors()){
					$error = true;
				}
			}
			if (count($arr2) !== count($arr)){
				$this->model->addError('bl_name','Dublicated key values');
				$error = true;
			}
			if ($error){
				$this->tplVars['model'] = $this->model;
				$this->tplVars['content']['val'] = array('bl_name'=>$bl_name);
        		array_push($this->viewIncludes, $this->tplVars['header']['curController'].'/addMany.tpl');
				return;
			}
			foreach($arr as $row){
				$this->model->setAttributes(array('bl_name'=>$row, 'bl_hidden'=>0 ));
				$id = $this->model->_insert();
			}
			 $this->_redirect('/'.$this->tplVars['header']['curController'].'/list/');
		}
		$this->tplVars['model'] = $this->model;
        array_push($this->viewIncludes, $this->tplVars['header']['curController'].'/addMany.tpl');
    }
	
    public function deleteAction() {
        if($this->tplVars['content']['canDelete'] && $this->_hasParam('id')) {
      		$this->model->deleteByPk($this->_getParam('id'));
		}
		if (!$this->isAjax){
	   	    	$page = ($this->_hasParam('page') ? 'page/'.$this->_getParam('page').'/' : '');
        		$this->_redirect($this->tplVars['header']['curController'].'/list/'.$page);
		}
	}
	
	public function deleteselAction() {
        if ($this->tplVars['content']['canDelete'] && $this->_request->isPost()) {
            $Ids = $this->_request->getPost('chx');
           echo $this->model->deleteAllByPk($Ids);
        }
		if (!$this->isAjax){
    	    $page = ($this->_hasParam('page') ? 'page/'.$this->_getParam('page').'/' : '');
      		$this->_redirect($this->tplVars['header']['curController'].'/list/'.$page);
		}
		
    }
}

?>