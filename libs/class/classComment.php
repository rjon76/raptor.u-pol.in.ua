<?php

class Comment {
    
	private $pageId;
    private $pageUrl=null;
    private $productId;
    private $siteDb;
    
    public function __construct() 
    {
    	if (VBox::isExist('Page')) {
    		$page = VBox::get('Page');
            //$this->siteDb = VBox::get('Page')->siteDb;
            $this->pageId = $page->getPageId();
            $this->pageUrl = $page->getPageUrl();
    	}
    	else {
    		$this->pageId = 0;
    	}  	   
    }
    
    public function __destruct() {
		 $this->pageId = null;
         $this->productId = null;
    }

    public function getComments($args=null) 
    {
        $db = VBox::get('ConstData')->getConst('langsDb');

        if (isset($args[0]) && strpos($args[0],',') !== false){
            $where = " AND c.comment_product_id IN({$args[0]}) ";      
        }
        elseif(isset($args[0]) &&  $args[0] > 0){
            $where = " AND c.comment_product_id = '{$args[0]}' ";
              
        }
        elseif (isset($args[1]) && strpos($args[1],',') !== false){
            $where = " AND c.comment_id IN({$args[1]}) ";      
        }
        elseif(isset($args[1]) &&  $args[1] > 0){
            $where = " AND c.comment_id = '{$args[1]}' ";
              
        }
        else{
            $where='';
        }
        
        $q = "SELECT * FROM $db.comments AS c WHERE c.comment_hidden = '0'$where ORDER BY comment_order";


        DB::executeQuery($q, 'comments');
        $rows = DB::fetchResults('comments');

        $comments = $pages = array();
        
        foreach($rows as $row) {
            
            $pages = explode(',',$row['comment_pages']);
            
            if (in_array($this->pageUrl, $pages) && isset($this->pageUrl))
            {
                $comments[] = $row;
            }
        }

		return  $comments;
    }
}
?>