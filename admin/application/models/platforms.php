<?php
class Platforms {

    private $allDbAdapter;
	private $tablename = 'platforms';
		
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
		$select->order(array('platform_id'));
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
		    $select->where('platform_id = ?', $id);
	    	$prod = $this->allDbAdapter->fetchRow($select->__toString());
		}
		return $prod;
    }

    public function update($id, $data)
	{
		$id = intval($id);
		$set = array('platform_name' => $data['platform_name'],
					 'platform_acronim' => $data['platform_acronim'],
					 'platform_nick' => $data['platform_nick'],
					 'platform_order' => $data['platform_order'],
					 'platform_soft_nick' => $data['platform_soft_nick'],
					 );
		$result = $this->allDbAdapter->update($this->tablename,$set,$this->allDbAdapter->quoteInto('platform_id = ?', $id));
		var_dump($result);			 
		return (int)$result;
    }

    public function add($data)
	{
		if (!isset($data['platform_order']) || $data['platform_order']=="")
			$data['platform_order'] = $this->getmax()+10;

		$set = array('platform_name' => $data['platform_name'],
					 'platform_acronim' => $data['platform_acronim'],
					 'platform_nick' => $data['platform_nick'],
					 'platform_order' => $data['platform_order'],
					 'platform_soft_nick' => $data['platform_soft_nick'],
					 );
		$this->allDbAdapter->insert($this->tablename,$set);
		return (int)$this->allDbAdapter->lastInsertId();
    }

    public function delete($id) {
		$id= intval($id);	
        $this->allDbAdapter->delete($this->tablename, $this->allDbAdapter->quoteInto('platform_id = ?', $id));
	}
	public function getmax($field='platform_order')
	 {
			$select = $this->allDbAdapter->select();
			$select->from($this->tablename, array('maxval'=>'max('.$field.')'));
	    	$res = $this->allDbAdapter->fetchRow($select->__toString());
			return $res['maxval'];
    }

}


?>