<?php
include_once('data.php');

class DataController extends MainApplicationController {
    
    private $data = array();
    private $isAjax;
    
    public function init() {
        parent::init();
        
        $this->isAjax = $this->getIsAjaxRequest();

        $this->data = new DataModel($this->getSiteId());
        
    	$this->isAjax = FALSE;
    	//$this->tplVars['page_css'][] = 'comments.css';
    	$this->tplVars['header']['actions']['names'] = array(
                array('name' => 'list', 'menu_name' => 'List Data'),
                array('name' => 'add', 'menu_name' => 'New Data'),
            );
            
            
        $this->tplVars['page_css'][] 	= 'dataTables/css/demo_table.css';
        $this->tplVars['page_css'][] 	= 'datalist.css';
        $this->tplVars['page_js'][] 	= 'datalist.js';
        $this->tplVars['page_js'][] 	= 'tiny_mce/tiny_mce.js';
        $this->tplVars['page_js'][] 	= 'dataTables/js/jquery.dataTables.js';    
            
            
    	$controllId = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
            $this->tplVars['canEdit'] = $this->user->checkWritePerm($controllId);
            $this->tplVars['canDelete'] = $this->user->checkDelPerm($controllId);
    }

    public function __destruct() {
        if(!$this->isAjax) {
           $this->display();
        }

        $this->isAjax = NULL;
        parent::__destruct();
    }
    
    public function indexAction(){
        $this->_redirect('/data/list/');
        
    }
    
    public function listAction(){
        $this->tplVars['langs'] = $this->data->langs;
        $this->tplVars['content']['data'] = $this->data->getData();
        array_push($this->viewIncludes, 'data/dataList.tpl');
    }
    
    public function editAction() {
        if($this->_hasParam('id')) {
            
            $id = $this->_getParam('id');

            $this->tplVars['isNewRecord'] = false;	
            
            if($this->_request->isPost()) {


                    $row = array(
                            'd_type'=>$this->_request->getPost('d_type'),
                            'd_comment'=>$this->_request->getPost('d_comment'),
                            'd_nick'=>$this->_request->getPost('d_nick'),
                    );
                    
                    foreach($_POST['text'] as $key=>$lang)
                    {
                        $row["d{$key}_value"] = $_POST['text'][$key];
                    }

                    $this->data->edit($id,$row);

                    $this->to_log('updateData '.$id);
 
                      
                   if($this->_hasParam('list')){
                        $this->_redirect('/data/list/');    
                   }
                        
                    $this->_redirect('/data/edit/id/'.$id);
                    
            }
            $this->tplVars['langs'] = $this->data->langs;   
            $this->tplVars['content'] = $this->data->getDataToEdit($id);
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit Data', 'params' => array('id' => $id));

            array_push($this->viewIncludes, 'data/dataEdit.tpl');

        } else {
            $this->_redirect('/data/index/');
        }
    }
    
    public function addAction() {
            
            $this->tplVars['isNewRecord'] = true;	
            
            if($this->_request->isPost()) {
                    
                    $row = array(
                            'd_type'=>$this->_request->getPost('d_type'),
                            'd_comment'=>$this->_request->getPost('d_comment'),
                            'd_nick'=>$this->_request->getPost('d_nick'),
                    );
                    
                    foreach($_POST['text'] as $key=>$lang)
                    {
                        $row["d{$key}_value"] = $_POST['text'][$key];
                    }
                    
                    $pk = $this->data->add($row);
                                        
                    $this->to_log('addData '.$pk);
                    
                    if($this->_hasParam('list')){
                        $this->_redirect('/data/list/');    
                    }
                    
                    $this->_redirect('/data/edit/id/'.$pk);

            }
            $this->tplVars['langs'] = $this->data->langs;
            array_push($this->viewIncludes, 'data/dataEdit.tpl');

    }
    
    public function deleteAction() {
        $this->isAjax = true;
        if($this->_hasParam('id')) {
            $this->data->delete($this->_getParam('id'));
        } 
        
  		if (!$this->isAjax){
            $this->_redirect('/data/list/');
		}
    }
    
    public function deletesAction() {
    
        if ($this->_request->isPost()) {
            if($this->_hasParam('ids')) {
             echo $this->data->deletes($this->_getParam('ids'));
            } 
        }
        
  		if (!$this->isAjax){
            $this->_redirect('/data/list/');
		}
    }
    
    public function getstrAction(){
    	$this->isAjax = true;
    	$lang 	= $this->_getParam('lang');
    	$id 	= $this->_getParam('id');
    	$str 	= $this->data->getDataToEdit($id);
    	$this->data->openEditWindow($str,$lang);
    }
    
    public function saveAction(){
    	$this->isAjax = true;
    	$lang 			= $this->_getParam('lang');
    	$id 			= $this->_getParam('id');
    	$data['isTrans'] 		= $this->_getParam('isTrans');
    	$data['text'] 			= $this->_getParam('txted');
    	if($data['isTrans'] == ''){
    		$data['isTrans'] = 0;
    	}else{
    		$data['isTrans'] = 1;
    	}
    	$res = $this->data->setdata($id,$lang,$data,$this->tplVars['canEdit']);
    	echo $res;
    }

}

?>