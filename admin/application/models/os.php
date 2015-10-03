<?php
class Os {

    private $allDbAdapter;
	private $tablename = 'os';
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
		$select->order(array('o_id'));
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
		    $select->where('o_id = ?', $id);
	    	$prod = $this->allDbAdapter->fetchRow($select->__toString());
		}
		return $prod;
    }

    public function update($id, $data)
	{
		$id = intval($id);
		$set = array('o_value' => $data['o_value'],
					 'o_acronim' => $data['o_acronim'],
					 'o_order' => $data['o_order']
					 );
		return (string)$select = $this->allDbAdapter->update($this->tablename, $set , $this->allDbAdapter->quoteInto('o_id = ?', $id));
    }

    public function add($data)
	{
		if (!isset($data['o_order']) || $data['o_order']=="")
			$data['o_order'] = $this->getmax()+10;

		$set = array('o_value' => $data['o_value'],
					 'o_acronim' => $data['o_acronim'],
					 'o_order' => $data['o_order']
					 );
		$this->allDbAdapter->insert($this->tablename,$set);
		return (int)$this->allDbAdapter->lastInsertId();
    }

    public function delete($id) {
		$id= intval($id);	
        $this->allDbAdapter->delete($this->tablename, $this->allDbAdapter->quoteInto('o_id = ?', $id));
	}

    public function getmax($field='o_order')
	 {
			$select = $this->allDbAdapter->select();
			$select->from($this->tablename, array('maxval'=>'max('.$field.')'));
	    	$res = $this->allDbAdapter->fetchRow($select->__toString());
			return $res['maxval'];
    }

}


?>