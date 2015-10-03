<?php
/*----------------------------------*/
//	dbmodel class
//	version 1.0.0.0
//	start 01/12/2010
//	last update 01/12/2010
//	autor garbagecat76
/*----------------------------------*/
include_once('classValidator.php');
class dbmodel extends Zend_Db_Table{

    #PIVATE VARIABLES
    public $siteDbAdapter;
	private $rows;

    #PUBLIC VARIABLES
    public $pk;
    private $pkname;
    public $tablename;
	public $isNewRecord=true;
	public $attributes=array();
	public $table;	
	private $row = NULL;
	public $siteId;
	public $errors= array();

/*--------------*/
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
		$this->siteId = $siteId;
		parent::__construct(array(Zend_Db_Table::ADAPTER=>$this->siteDbAdapter, Zend_Db_Table::NAME=>$this->tablename));
		$info = $this->info();
//		var_dump($info);

		$this->pkname = array_shift($info[Zend_Db_Table::PRIMARY]);
		$this->createAttributesList();
    }

/*--------------*/
    public function __destruct() {
        $this->siteDbAdapter = NULL;
		$this->rows = NULL;
		$this->attributes = NULL;
		$this->$errors = NULL;
		$this->$rules = NULL;	
    }
/*--------------*/  
   public function _fetchRow($criteria=array('where'=> NULL, 'order' => NULL)){
		$row =  $this->fetchRow($criteria['where'], $criteria['order']);
       return $row->toArray();
	}
/*--------------*/
    public function _fetchAll($criteria=array('where'=> NULL, 'order' => NULL, 'count' => NULL , 'offset' => NULL)) {
		$rows =  $this->fetchAll($criteria['where'], $criteria['order'],  $criteria['count'],  $criteria['offset']);
       return $rows->toArray();
    }
/*--------------*/
    public function findByPk($id) {
		$rows =  $this->find($id);
		$this->row =  $rows->current();		
		$this->setAttributes($this->row->toArray()); 
		$this->setPrimaryKey($id);
		 return $this;
    }

/*--------------*/
    public function _insert() {
		$this->pk = parent::insert($this->attributes);
		return $this->pk;
    }

/*--------------*/
    public function _update() {
       parent::update($this->attributes, $this->siteDbAdapter->quoteInto($this->pkname.' = ?', $this->pk));		
		return $this->pk;
    }

/*--------------*/
    public function deleteAll($where = NULL) {
	      return  parent::delete($where);
    }
/*--------------*/
    public function deleteByPk($id) {
       return  parent::delete($this->siteDbAdapter->quoteInto($this->pkname.' = ?', $id));
    }
/*--------------*/

	public function getAttribute($name)
	{
		return (isset($this->attributes[$name])) ? $this->attributes[$name] : false;
	}
/*--------------*/	
	public function getAttributes($names=null)
	{
    	$values=array();
	    foreach($this->attributeNames() as $name)
    	    $values[$name]=$this->attributes[$name];

	    if(is_array($names))
    	{
        	$values2=array();
	        foreach($names as $name)
    	        $values2[$name]=isset($values[$name]) ? $values[$name] : null;
        	return $values2;
	    }
    	else
        	return $values;
	}
	
	public function getAttributes2($values = NULL)
	{
		
		return $this->attributes;	
	}
/*--------------*/
	public function setAttributes($values, $safe=true)
	{
    	if(!is_array($values))
        	return;
		if (!$safe)
			$this->attributes = array();	
	//	$this->createAttributesList();
	//	var_dump($this->attributeNames());
	    $attributenames = array_flip($this->attributeNames());
	    foreach($values as $name=>$value)
    	{
        	if(isset($attributenames[$name]))
            	$this->attributes[$name] = $value;
	    }

	}
/*--------------*/	
	public function setAttribute($name, $value)
	{
		$this->attributes[$name]=$value;
    	return true;
	}
/*---------------*/
	public function setPrimaryKey($id)	
	{
		$this->pk = $id	;
	}
/*---------------*/
	private function attributeNames()
	{
		$attributes = array();
		foreach($this->_metadata as $name=>$column)
		{
			if(!$column['PRIMARY'])
				array_push($attributes, $name);
				
		}
		return $attributes;
	}
/*---------------*/
	private function createAttributesList()
	{
		$this->attributes = array();
		foreach($this->_metadata as $name=>$column)
		{
			if(!$column['PRIMARY'])
				$this->attributes[$name]=($column['DEFAULT']!==null) ? $column['DEFAULT'] : '';
		}
	}
/*---------------*/
	public function validate()
	{
		$validator = new CValidator;
		$validator->validate($this);
		
	}
/*---------------*/
	public function clearErrors()
	{
		$this->errors = array();
	}
/*---------------*/
	public function hasErrors()
	{
		return !empty($this->errors);
	}
/*---------------*/
	public function addError($name, $message)
	{
		$this->errors[$name] = $message;
	}
	
	public function addErrors($messages)
	{
		foreach($messages as $message){
			$this->addError($message[0], $message[1]);
		}
	}
	public function getErrors()
	{
		return $this->errors;
	}
	public function getError($name)
	{
		return (isset($this->errors[$name])) ? $this->errors[$name] : false;
	}

	
	public function printErrors()
	{
		foreach($this->errors as $key=>$val){
			printf("<p>%s</p>", $val);	
		}
	}
}
?>