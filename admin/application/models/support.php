<?php
class Support {

    private $allDbAdapter;
	private $tablename = 'support_managers';
    public function __construct($siteId) {
		$this->dbAdapter = Zend_Registry::get('dbAdapter');
		$config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->allDbAdapter->query('SET NAMES utf8');
    }

    /*
     Деструктор
    */
    public function __destruct()
	 {
    }

  	public function selectAll()
	{
		$select = $this->allDbAdapter->select();
		$select->from($this->tablename);
		$select->order(array('sm_id'));
		$plat = $this->allDbAdapter->fetchAll($select->__toString());
		return $plat;
    }

    public function getById($id)
	 {
		$id = intval($id);
		$prod = array();
		if($id > 0)
		{
			$select = $this->allDbAdapter->select();
			$select->from($this->tablename);
		    $select->where('sm_id = ?', $id);
	    	$prod = $this->allDbAdapter->fetchRow($select->__toString());
		}
		return $prod;
    }
	
  	public function selectSuportProd($id)
	{
		$id = intval($id);
		$select = $this->allDbAdapter->select();
		$select->from('products_support');
		$select->where('ps_support_manager_id = ?', $id);
		//$select->order(array('sm_id'));
		$plat = $this->allDbAdapter->fetchAll($select->__toString());
		return $plat;
    }	

    public function update($id, $data)
	{
		$id = intval($id);
		$set = array('sm_login' => $data['sm_login'],
					 'sm_nik' => $data['sm_nik'],
					 'sm_chat_id' => $data['sm_chat_id']
					 );
		$result = $this->allDbAdapter->update($this->tablename, $set , $this->allDbAdapter->quoteInto('sm_id = ?', $id));
		
		$select = array();
		foreach ($this->selectSuportProd($id) as $value) {
			$select[] = $value['ps_product_id'];
		}
		$addprodid = array_diff( $data['products_support'], $select );
		$delprodid = array_diff( $select, $data['products_support'] );
		
		$where = array();
		$where[] = "ps_support_manager_id = '$id'";
		$where[] = "ps_product_id IN ('".implode("','",$delprodid)."')";
		$this->allDbAdapter->delete('products_support', $where );
		var_dump($addprodid);
		foreach ($addprodid as $val) {
			$this->allDbAdapter->insert('products_support', array( 'ps_product_id'=>$val, 'ps_support_manager_id'=>$id ) );
		}
		
		return true;
    }

    public function add($data)
	{
		$set = array('sm_login' => $data['sm_login'],
					 'sm_nik' => $data['sm_nik'],
					 'sm_chat_id' => $data['sm_chat_id']
					 );
		$this->allDbAdapter->insert($this->tablename,$set);
		$sm_id = (int)$this->allDbAdapter->lastInsertId();
		foreach ($data['products_support'] as $val) {
			$this->allDbAdapter->insert('products_support', array( 'ps_product_id'=>$val, 'ps_support_manager_id'=>$sm_id ) );
		}
		 return $sm_id ;
    }

    public function delete($id) {
		$id= intval($id);	
        $this->allDbAdapter->delete($this->tablename, $this->allDbAdapter->quoteInto('sm_id = ?', $id));
		$this->allDbAdapter->delete('products_support', $this->allDbAdapter->quoteInto('ps_support_manager_id = ?', $id));
	}

}

?>