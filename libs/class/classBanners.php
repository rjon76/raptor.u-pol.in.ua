<?php
/**
 * class include banners & overlays for set pages
 * @author italiano
 * @version 25.11.2014
 */
class Banners {
    
	private $PageId;
    
    public function __construct() 
    {
        
    	if (VBox::isExist('Page')) {
    		$page = VBox::get('Page');
            $this->PageId = $page->getPageId();
    	}
    	else {
    		$this->PageId = 0;
    	}
    }
    public function __destruct() {
		 $this->PageId = null;
    }
	
    /**
     * function for get array banners & overlays
     * @version 25.11.2014
     * @author italiano
     * @param $alias = empty or overlay, $id = id item 
     * @return array
     */
    public function getBanners($args=array('alias'=>null,'id'=>null)) 
    {
        
        $limit=10;

        if (isset($args['alias']))
        {  
           $where = " AND b.banner_alias ='{$args['alias']}'";
        }
    	else
    	{
    		$where = '';
    	}

        if (isset($args['id']))
        {
 
           $where .= " AND bi.bi_id = '{$args['id']}'";
        }
    	else
    	{

    		$where .= '';
    	}
        
        
        
        $q = "SELECT bi.* FROM banners as b, banners_item as bi WHERE b.banner_id = bi.bi_banner_id $where AND bi.bi_hidden = '0' ORDER BY bi.bi_order LIMIT 0,$limit";
        
        
                
        DB::executeQuery($q, 'banners');
        $rows = DB::fetchResults('banners');
        
        $result = array();
        $tsize = count($rows);
        $assign = null;

        $cookieBanners = isset($_COOKIE['banners']) ? $_COOKIE['banners'] : false;
        $cookieOverlays = isset($_COOKIE['overlay']) ? $_COOKIE['overlay'] : false;


        for($i=0; $i<$tsize; $i++)
        {
            $page_for_view = explode(',',$rows[$i]['bi_pages']);
            $assign = $rows[$i]['bi_assign'];           
            
            //if not isset banner ID in cookie['banners']
            if(!isset($cookieBanners[$rows[$i]['bi_id']]))
            {
                switch($assign){
                    
                    //for all pages
                    case "0":
                        $result[] = array(
                                    'link' => $rows[$i]['bi_link'],	
                                    'type' => $rows[$i]['bi_type'],	
                                    'id' => $rows[$i]['bi_id'],						  
                        );
                        break;
                    //for select pages
                    case "1":
                        if (in_array($this->PageId, $page_for_view))
                        {
                            $result[] = array(
                                        'link' => $rows[$i]['bi_link'],	
                                        'type' => $rows[$i]['bi_type'],	
                                        'id' => $rows[$i]['bi_id'],						  
                            );
                        }
                        break; 
                    //for no select pages    
                    case "2":
                        if (!in_array($this->PageId, $page_for_view))
                        {
                            $result[] = array(
                                        'link' => $rows[$i]['bi_link'],	
                                        'type' => $rows[$i]['bi_type'],	
                                        'id' => $rows[$i]['bi_id'],						  
                            );
                        }
                        break;
                    
                    
                }
            }
            
        }
        
		return $result;
    }
}
?>