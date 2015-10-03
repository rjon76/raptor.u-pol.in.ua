<?php
include_once('Zend/Paginator.php');
include_once('Zend/Paginator/Adapter/DbSelect.php');

class Blacklists {

    #PIVATE VARIABLES
    private $allDbAdapter;
    private $siteDbAdapter;
	private $adminDbAdapter;
	private $tablename = 'blacklists';

	private $countPerPage = 20;
	private $pageRange = 5;
	#PUBLIC VARIABLES

    /*
     Конструктор
    */
    public function __construct($siteId) {
		$this->dbAdapter = Zend_Registry::get('dbAdapter');

		$config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();

		$params['dbname'] = $config->db->config->dballname;
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->allDbAdapter->query('SET NAMES utf8');
		
		$params['dbname'] = $config->db->config->dbname;
        $this->adminDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->adminDbAdapter->query('SET NAMES utf8');
    }
	
    /*
     Деструктор
    */
    public function __destruct() {
        $this->siteDbAdapter = NULL;
    }
	
    /*
     Добавить запись в лог
    */
    public function addLog($data) {
	/*
        $row = array(
			'log_date' => time(),
			'log_user' => $data['log_user'],
			'log_ip' => $data['log_ip'],
			'log_controller' => $data['log_controller'],
			'log_action' => $data['log_action'],
			'log_request' => $data['log_request'],
			'log_message' => $data['log_message'],
        );
        $this->siteDbAdapter->insert($this->tablename, $row);
        return $this->siteDbAdapter->lastInsertId();
	*/
    }
	
  	public function selectAll( $pageNumber = 1 , $filter = array() )
	{
		$db = $this->allDbAdapter->select()->from($this->tablename);

		foreach($filter as $key => $val) {
			if ($val) {
				if ($key == 'bl_site_id')
					$db->where( $key.' LIKE ?', '%"'.$val.'"%' );
				else if ( is_int($val) )
					$db->where( $key.' = ?', $val );
				else
					$db->where( $key.' LIKE ?', '%'.$val.'%' );
			}
		}
		$db->order('bl_id ASC');

		$adapter = new Zend_Paginator_Adapter_DbSelect( $db );
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($pageNumber);
		$paginator->setItemCountPerPage($this->countPerPage);
		$paginator->setPageRange($this->pageRange);

		return $paginator;
    }
	
    public function getData($id) {
		$select = $this->allDbAdapter->select();
		$select->from($this->tablename);
		$select->where('bl_id = ?', $id);
		$data = $this->allDbAdapter->fetchRow($select->__toString());
		return $data;
	}
	
    public function addIp($ip, $site_ids) {
		if ($ip) {
			$set = array(
				'bl_ip' => $ip,
				'bl_site_id' => serialize($site_ids),
			);
			$this->allDbAdapter->insert($this->tablename, $set);
		}
    }

    public function updateIp($blacklistsId, $ip, $site_ids) {
		if ($ip) {
			$set = array(
				'bl_ip' => $ip,
				'bl_site_id' => serialize($site_ids),
			);
			$this->allDbAdapter->update($this->tablename, $set, 'bl_id = ' . $blacklistsId );
		}
    }
	
    public function deleteIp($id) {
		$id = intval($id);
        $this->allDbAdapter->delete($this->tablename, 'bl_id = '.$id);
   }
   
	public function getSitesData() {
		$select = $this->adminDbAdapter->select();
		$select->from('sites');
		$data = $this->adminDbAdapter->fetchAll($select->__toString());
		return $data;
	}
   
	public function getAllIps() {
		$select = $this->allDbAdapter->select();
		$select->from($this->tablename);
		$data = $this->allDbAdapter->fetchAll($select->__toString());
		return $data;
	}
	
}

?>