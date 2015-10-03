<?php
include_once('models/products.php');
include_once('models/pages.php');
class CommentsModel {

    private $allDbAdapter;
    private $siteDbAdapter;
	
    public function __construct() {
		$this->dbAdapter = Zend_Registry::get('dbAdapter');

		$config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
		$params['dbname'] = $config->db->config->dballname;
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->allDbAdapter->query('SET NAMES utf8');

    }

    public function __destruct() {

    }
    /**
     * @var $order array
     * @return array
    */
    public function getComments($order=null) {

    	$select = $this->allDbAdapter->select();
    	$select->from('comments');
        //$select->where('comment_hidden = ?', '0');
        if(isset($order)){
            $select->order($order);      
        }
        else{
    	   $select->order(array('comment_product_id','comment_order'));
        }

    	$comments = $this->allDbAdapter->fetchAll($select->__toString());

        $products = new AdminProducts();
        $array = array();
        foreach($comments as $comment)
        {
            $product = $products->getProductById($comment['comment_product_id']);
            if (count($product)){
                if (isset($array[$product['p_id']])){
                    $array[$product['p_id']]['comments'][] = $comment;    
                }
                else{
                    $array[$product['p_id']]['name'] = $product['p_title'];
                    $array[$product['p_id']]['id'] = $product['p_id'];
                    $array[$product['p_id']]['comments'][] = $comment;
                }
            }
            else{
                if (isset($array['others'])){
                    $array['others']['comments'][] = $comment;    
                }
                else{
                    $array['others']['name'] = 'Comments are not tied to products';
                    $array['others']['class'] = 'hidden';
                    $array['others']['comments'][] = $comment;
                }
            }
        }
    	return $array;
    }
    
    public function getCommentById($id) {
	   
       $comment = array();
       
        if($id > 0) {
    	    $select = $this->allDbAdapter->select();
    	    $select->from('comments');
    	    $select->where('comment_id = ?', $id);
    	    $comment = $this->allDbAdapter->fetchRow($select->__toString());
    	}
        
    	return $comment;
    }

    public function getProducts($smarty = FALSE) {
	
    $select = $this->allDbAdapter->select();
	$select->from('products', array('p_title','p_id','p_featured','p_blocked'));
	$select->order(array('p_order'));
	$prods = $this->allDbAdapter->fetchAll($select->__toString());
    
	if($smarty) {
	    $tcats['values'] = array();
	    $tcats['names'] = array();
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		array_push($tcats['values'],$prods[$i]['p_id']);
		array_push($tcats['names'],$prods[$i]['p_title']);
	    }
        
	    $prods = $tcats;
	}
	return $prods;
    
    }
    
    public function getPages($smarty = FALSE,$siteId) {
        
    $pages = new Pages($siteId);    
    $prods = $pages->getPagesList(NULL, array('pg_lang','pg_address'));
    
	if($smarty) {
	    $tcats['values'] = array();
	    $tcats['names'] = array();
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		array_push($tcats['values'],$prods[$i]['pg_address']);
		array_push($tcats['names'],$prods[$i]['pg_address']);
	    }
        
	    $prods = $tcats;
	}
    
	return $prods;
    
    }

    public function upComment($cid=0, $id=0) {
	$succed = FALSE;
	if($id>0 && $cid>0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('comments', array('comment_id','comment_order'));
	    $select->where('comment_product_id = ?', $cid);
	    $select->order(array('comment_order'));
	    $prods = $this->allDbAdapter->fetchAll($select->__toString());
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		if($prods[$i]['comment_id'] == $id) {
		    if($i == 0) {
			break;
		    }
		    $low = $prods[$i]['comment_order'];
		    $high = $prods[$i-1]['comment_order'];
		    $lpId = $prods[$i-1]['comment_id'];
		    $this->allDbAdapter->update('comments', array('comment_order' => $high), $this->allDbAdapter->quoteInto('comment_id = ?', $id));
		    $this->allDbAdapter->update('comments', array('comment_order' => $low), $this->allDbAdapter->quoteInto('comment_id = ?', $lpId));
		    $succed = TRUE;
		    break;
		}
	    }
	}
	return $succed;
    }

    public function downComment($cid=0, $id=0) {
   	$succed = FALSE;
	if($id>0 && $cid>0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('comments', array('comment_id','comment_order'));
	    $select->where('comment_product_id = ?', $cid);
	    $select->order(array('comment_order'));
	    $prods = $this->allDbAdapter->fetchAll($select->__toString());
	    $tsize = sizeof($prods);
        
	    for($i=0; $i<$tsize; $i++) {
		if($prods[$i]['comment_id'] == $id) {
		    if($i == $tsize-1) {
			break;
		    }
            
		    $high = $prods[$i]['comment_order'];
		    $low = $prods[$i+1]['comment_order'];
		    $lpId = $prods[$i+1]['comment_id'];
		    $this->allDbAdapter->update('comments', array('comment_order' => $low), $this->allDbAdapter->quoteInto('comment_id = ?', $id));
		    $this->allDbAdapter->update('comments', array('comment_order' => $high), $this->allDbAdapter->quoteInto('comment_id = ?', $lpId));
		    $succed = TRUE;
		    break;
		}
	    }
	}
	return $succed;
    }

    public function setCommentById($id, $data) {
	
    $id = intval($id);
    
		if (!isset($data['comment_order']) || $data['comment_order'] == "")
			$data['comment_order'] = $this->getmax($data['comment_product_id'])+1;

	$set = array(
            'comment_hidden' => isset($data['comment_hidden']) ? $data['comment_hidden'] : 0 , 
            'comment_pages' => implode(',',$data['comment_pages']),
            'comment_product_id' => $data['comment_product_id'],
            'comment_author' => $data['comment_author'], 
            'comment_text' => $data['comment_text'],
            'comment_avatar' => $data['comment_avatar'],
            'comment_order' => $data['comment_order']);
            
	return (string)$select = $this->allDbAdapter->update('comments',$set,$this->allDbAdapter->quoteInto('comment_id = ?', $id));
    }

    public function addComment($data) {

	if (!isset($data['comment_order']) || $data['comment_order']=="")
			$data['comment_order'] = $this->getmax($data['comment_product_id'])+1;
            
	$set = array(
            'comment_hidden' => isset($data['comment_hidden']) ? $data['comment_hidden'] : 0 , 
            'comment_pages' => implode(',',$data['comment_pages']),
            'comment_product_id' => $data['comment_product_id'],
            'comment_author' => $data['comment_author'], 
            'comment_text' => $data['comment_text'],
            'comment_avatar' => $data['comment_avatar'],
            'comment_order' => $data['comment_order']);
            
		$this->allDbAdapter->insert('comments',$set);
        
		return (int)$this->allDbAdapter->lastInsertId();
    }

    public function deleteComment($id) {
		$id = intval($id);
        $this->allDbAdapter->delete('comments', $this->allDbAdapter->quoteInto('comment_id = ?', $id));
   }
   
   public function getmax($cid=null,$field='comment_order')
	 {
			$select = $this->allDbAdapter->select();
			$select->from('comments', array('maxval'=>'max('.$field.')'));
            
            if (isset($cid)){
                $select->where('comment_product_id = ?', $cid);   
            }
            
	    	$res = $this->allDbAdapter->fetchRow($select->__toString());
			return (int)$res['maxval'];
    }
    
    public function hiddenComment($id=null) 
    {
                        
            if (isset($id))
            {
                $select = $this->allDbAdapter->select();
                $select->from('comments', array('comment_hidden'));
                $select->where('comment_id = ?', $id);
                $select->limit(1);
                $result = $this->allDbAdapter->fetchRow($select->__toString());
                
                if (count($result)>0)
                {
                    if (empty($result['comment_hidden']))
                    {
                        $result['comment_hidden'] = 0;
                    }
                    
                    $type = ($result['comment_hidden'] == 1 ? 0 : 1);
                    
                    $set = array(
                        'comment_hidden' => $type,
                    );

                    $this->allDbAdapter->update('comments', $set, $this->allDbAdapter->quoteInto('comment_id = ?', $id));

                }
                
            }
            
            return (bool)$type;
    }


}
?>