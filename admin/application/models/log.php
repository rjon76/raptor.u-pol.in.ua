<?php
include_once('Zend/Paginator.php');
include_once('Zend/Paginator/Adapter/DbSelect.php');

class Log {

    #PIVATE VARIABLES
    private $siteDbAdapter;
	private $tablename = 'admin_log';
	private $countPerPage = 25;
	private $pageRange = 5;
    #PUBLIC VARIABLES

    /*
     Конструктор
    */
    public function __construct($siteId) {
        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname'));
        $select->where('s_id = ?', $siteId);
        $this->siteDbName = $dbAdapter->fetchOne($select->__toString());

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $params['dbname'] = $this->siteDbName;

        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);
        $this->siteDbAdapter->query('SET NAMES utf8');

		$this->siteDbAdapter->query('CREATE TABLE IF NOT EXISTS `admin_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_date` int(11) DEFAULT NULL,
  `log_user` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_ip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_controller` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_action` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_request` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `log_message` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;');

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
    }
	
  	public function selectAll( $pageNumber = 1 , $filter = array() )
	{
		$db = $this->siteDbAdapter->select()->from($this->tablename);
		
		foreach($filter as $key => $val) {
			if ($val) {
				if ( is_int($val) )
					$db->where( $key.' = ?', $val );
				else
					$db->where( $key.' LIKE ?', '%'.$val.'%' );
			}
		}
		$db->order('log_date DESC');
		
		$adapter = new Zend_Paginator_Adapter_DbSelect( $db );
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($pageNumber);
		$paginator->setItemCountPerPage($this->countPerPage);
		$paginator->setPageRange($this->pageRange);
		
		return $paginator;
/*
		$select = $this->siteDbAdapter->select();
		$select->from($this->tablename);
		$select->order(array('log_id'));
		$log = $this->siteDbAdapter->fetchAll($select->__toString());
		return $log;
*/
    }

}

?>