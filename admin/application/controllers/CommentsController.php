<?php
include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/comments.php');

class CommentsController extends MainApplicationController {

    //private $products = NULL;
    private $isAjax;
	private $pages;
    
    public function init() {
        parent::init();

	$this->isAjax = FALSE;
	$this->tplVars['page_css'][] = 'comments.css';
	$this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'List Comments'),
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

    public function indexAction() {
        $this->_redirect('/comments/list/');
    }

    public function listAction() 
    {
        $this->tplVars['page_js'][] = 'comments.js';
        $this->tplVars['header']['actions']['names'][] = array('name' => 'add', 'menu_name' => 'Add Comment');	   
    	array_push($this->viewIncludes, 'comments/commentsList.tpl');
    	$comments = new CommentsModel();

/*        
        if($this->_hasParam('order')){
            $order = array($this->_getParam('order'));
        }
*/        
        $this->tplVars['comments'] = $comments->getComments();
        
        $hostname = $this->getNCSiteHostname();
        
        foreach($this->tplVars['comments'] as $key=>$comment)
        {
            foreach($comment['comments'] as $key2=>$comment2)
            {
                $pages = array(); $string = '';
                
                if(strlen($comment2['comment_pages'])>1)
                {
                    $pages = explode(',',$comment2['comment_pages']);
         
                    foreach($pages as $page){
                        
                        $string .= "<a href=\"http://{$hostname}{$page}\" target=\"_blank\">{$page}</a>, ";
                    }
                    if (strlen($string)){
                        $string = substr($string,0,-2);    
                    }
    
                    $this->tplVars['comments'][$key]['comments'][$key2]['comment_pages'] = $string;
                }
            }

        }
        
    	unset($comments);
    }

    public function editAction() {
	if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id')) 
    {
        array_push($this->tplVars['page_js'], 'jquery-1.8.3.js');
        array_push($this->tplVars['page_js'], 'select2.min.js');
            
        array_push($this->tplVars['page_css'], 'comments.css');
        array_push($this->tplVars['page_css'], 'select2.css');
	    array_push($this->tplVars['header']['actions']['names'], array('name' => 'edit', 'menu_name' => 'Edit Comment'));	   
        array_push($this->tplVars['header']['actions']['names'], array('name' => 'add', 'menu_name' => 'Add Comment'));	
        
		$this->tplVars['lvals']['isNewRecord'] = false;			
	    array_push($this->viewIncludes, 'comments/commentsEdit.tpl');
	    $id = $this->_getParam('id');
	    $this->tplVars['lvals']['postRes'] = 2;
	    
        $comments = new CommentsModel();
        $this->tplVars['products'] = $comments->getProducts(true);
        $this->tplVars['pages'] = $comments->getPages(true, $this->getSiteId());
        
	    if ($this->_request->getPost('ispost')) {
			$this->to_log();
			$this->tplVars['comment'] = $this->_request->getPost();
			$this->tplVars['pages']['select'] = $this->_request->getPost('comment_pages');
			$this->tplVars['products']['select'] = $this->_request->getPost('comment_product_id');
			$this->tplVars['lvals']['postRes'] = $comments->setCommentById($id, $this->tplVars['comment']);
            
            if((bool)$this->_request->getPost('toitem')){
                $this->_redirect('/comments/list/#comment'.$id.'');
            }
            
            
	    } else {
			$this->tplVars['comment'] = $comments->getCommentById($id);
			$this->tplVars['products']['select'] = array($this->tplVars['comment']['comment_product_id']);
			$this->tplVars['pages']['select'] = explode(',',$this->tplVars['comment']['comment_pages']);
	    }
		}
    }
	
    public function addAction() {
		if($this->tplVars['lvals']['canEdit'])
		 {
		  
            array_push($this->tplVars['page_js'], 'jquery-1.8.3.js');
            array_push($this->tplVars['page_js'], 'select2.min.js');
            
		    array_push($this->tplVars['page_css'], 'comments.css');
            array_push($this->tplVars['page_css'], 'select2.css');
		    array_push($this->tplVars['header']['actions']['names'], array('name' => 'add', 'menu_name' => 'Add Comment'));	   

	    	array_push($this->viewIncludes, 'comments/commentsEdit.tpl');
		    $this->tplVars['lvals']['postRes'] = 2;
            
            $comments = new CommentsModel();
            $this->tplVars['products'] = $comments->getProducts(true);
            $this->tplVars['pages'] = $comments->getPages(true, $this->getSiteId());
        
                if ($this->_hasParam('pid')){
                    $this->tplVars['products']['select'] = array($this->_getParam('pid'));    
                }
        
			$this->tplVars['lvals']['isNewRecord'] = true;	
            
		    if ($this->_request->getPost('ispost')) {
				$this->tplVars['comments'] = $this->_request->getPost();
				$this->tplVars['pages']['select'] = $this->_request->getPost('comment_pages');
				$this->tplVars['products']['select'] = $this->_request->getPost('comment_product_id');
				$id = $this->tplVars['lvals']['postRes'] = $comments->addComment($this->tplVars['comments']);
				$this->to_log($id);
				
                if ($id > 0){
                    if((bool)$this->_request->getPost('toitem')){
                        $this->_redirect('/comments/list/#comment'.$id.'');
                    }else{
                        $this->_redirect('/comments/edit/id/'.$id.'/');     
                    }  
				}  
		    } 
		 }
    }
	
    public function deleteAction() {
        if($this->_hasParam('id') && $this->tplVars['lvals']['canDelete'])
		 {
		    $comments = new CommentsModel();
            $comments->deleteComment($this->_getParam('id'));
        }
           $this->_redirect('/comments/list/');		
    }

    public function commentupAction() {
    	$this->isAjax = TRUE;
        
    	if($this->_hasParam('id') && $this->_hasParam('cid')) 
        {
            Zend_Loader::loadClass('Zend_Json');
            
            $id = $this->_getParam('id');
            $cid = $this->_getParam('cid');
            $comments = new CommentsModel();
    	    echo Zend_Json::encode($comments->upComment($cid,$id));
         }
    }

    public function commentdownAction() {
    	$this->isAjax = TRUE;
        
    	if($this->_hasParam('id') && $this->_hasParam('cid')) 
        {
            Zend_Loader::loadClass('Zend_Json');
    
            $id = $this->_getParam('id');
            $cid = $this->_getParam('cid');
            $comments = new CommentsModel();
    	    echo Zend_Json::encode($comments->downComment($cid,$id));
        }
    }
    
    public function hiddenAction() {
        $this->isAjax = true;

        if($this->_hasParam('id')) {
            
            Zend_Loader::loadClass('Zend_Json');

            $id = $this->_getParam('id');
            
		    $comments = new CommentsModel();
            $result = $comments->hiddenComment($id);
            
            echo Zend_Json::encode($result);

        }
    }
}

?>