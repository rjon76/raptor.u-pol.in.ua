<?php
/* requrements
  class VException
  class IniParser
  class Error
  class DbMysqlAdapter
  class DbMysqliAdapter
*/
class DB {
    private static $_instance;
    private $_adapter;
    private static $DB_TYPES = array('mysql', 'mysqli');
    private $type;
    private $queries;
	public $table_name	= '';
    private function __construct($settings)
    {
		
		$this->queries = array();
		if(!is_array($settings) || sizeof($settings)<1)
		{
	    	Error::logError('Error in DB class','Can not found appropriate section for the database params in ini file.');
	    	throw new VException('Wrong config parameters for DB class.');
		}

		$this->type = 'mysql';
		if (!empty($settings['type']))
		{
    		$settings['type'] = strtolower($settings['type']);
    		if(in_array($settings['type'], self::$DB_TYPES))
    		{
				$this->type = $settings['type'];
    		}
		}

		$class = 'Db'.ucfirst($this->type).'Adapter';

		if(file_exists(ENGINE_PATH.'class/class'.$class.'.php'))
		{
	    	include_once(ENGINE_PATH.'class/class'.$class.'.php');
	    	$this->_adapter = new $class;
	    	$this->_adapter->initAdapter($settings);
			$this->_adapter->table_name = $this->table_name;
		}
		else
		{
	    	Error::logError('Error in DB class','Can not found appropriate file class'.$class.'.php for including.');
	    	throw new VException('Can not include class '.ENGINE_PATH.'class/class'.$class.'.php');
		}
    }

    private function __clone() { }

    public static function getInstance()
    {
		if (self::$_instance === NULL)
		{
	    	self::$_instance = new self(IniParser::getInstance()->getSection('db'));
		}
		return self::$_instance;
    }

    public static function unsetInstance()
    {
		self::getInstance()->type = NULL;
		self::getInstance()->_adapter->closeConnection();
		self::getInstance()->_adapter = NULL;
		self::$_instance = NULL;
    }

    // Execute SQL Queries that returns results - like SELECT
    // Returns TRUE or FALSE as the result of the query
    public static function executeQuery($sql, $name, $params = FALSE)
    {
		
		$name = trim($name); $sql = trim($sql);
        
		if (strlen($name) && strlen($sql))
		{
		    $resResult = FALSE;

	    	if (!isset(self::getInstance()->queries[$name]))
	    	{
				$resResult = self::getInstance()->_adapter->query($sql, $params);
            }
            else
            {
				Error::logError('Execute Query Error', 'The unfetched query with the name "'.$name.'" already exist.');
	    	}
	    	
	    	if($resResult)
	    	{
				self::getInstance()->queries[$name] = $resResult;
				return TRUE;
	    	}
	    	return FALSE;
        }
        Error::logError('Execute Query or Name Missing', 'The  query or name parameter was empty, please provide a name for the query.');
        return FALSE;
    }

    // Execute SQL Queries that do not return results - like INSERT
    // Returns TRUE or FALSE as the result of the query
    public static function executeAlter($sql, $params = FALSE)
    {
		$sql = trim($sql);
        if (strlen($sql))
        {
	    	return self::getInstance()->_adapter->execute($sql, $params);
		}
		Error::logError('Execute Altered Query Missing', 'The  query parameter was empty, please provide a valid altered query.');
        return FALSE;
    }

    // Fetch Results
    // Returns an array of the query results
    public static function fetchResults($name) {
        $results = array();
	$name = trim($name);
        if(strlen($name)) {
	    if (isset(self::getInstance()->queries[$name])) {
		$results = self::getInstance()->queries[$name];
		unset(self::getInstance()->queries[$name]);
	    }
        }
        return $results;
    }

    // Fething only one column from one row
    // Return: string
    public static function fetchOne($name) {
	$results = '';
	$name = trim($name);
        if(strlen($name)) {
	    if (isset(self::getInstance()->queries[$name][0])) {
		foreach(self::getInstance()->queries[$name][0] as $key => $val) {
		    $results = $val;
		    break;
		}
		unset(self::getInstance()->queries[$name]);
	    }
        }
        return $results;
    }

    // Fetching only one row
    // Return: array
    public static function fetchRow($name) {
	$results = array();
	$name = trim($name);
        if(strlen($name)) {
	    if (isset(self::getInstance()->queries[$name][0])) {
		$results = self::getInstance()->queries[$name][0];
		unset(self::getInstance()->queries[$name]);
	    }
        }
        return $results;
    }
    
    public static function getLastInsertId(){
    	return self::getInstance()->_adapter->lastInsertId();
    }
   // Execute SQL Queries that returns results - like SELECT
    // Returns TRUE or FALSE as the result of the query
    public static function _get_few_data($name, $fields_arr = '*', $where_arr = array(), $num_page = '*', $order_arr = array('id' => 'ASC'))
    {
		$name = trim($name);
         $resResult = array();
		if (strlen($name))
		{
	    	if (!isset(self::getInstance()->queries[$name]))
	    	{
				$resResult = self::getInstance()->_adapter->get_few_data($fields_arr, $where_arr, $num_page, $order_arr);
            }
            else
            {
				Error::logError('Execute Query Error', 'The unfetched query with the name "'.$name.'" already exist.');
	    	}
	    	
	    	if($resResult)
				self::getInstance()->queries[$name] = $resResult;
       }
       else
		   Error::logError('Execute Query or Name Missing', 'The  query or name parameter was empty, please provide a name for the query.');
	    	return $resResult;
    }
	public static function _get_page_count_date($where_str = '')
	{
		return self::getInstance()->_adapter->get_page_count_date($where_str);
	}
	public static function _get_where($where_arr=array())
	{
		return self::getInstance()->_adapter->_get_where($where_arr);
	}
	public static function _get_limit($num_page = '*', $where_arr = array())
	{
		return self::getInstance()->_adapter->_get_limit($num_page, $where_arr);
	}
	public static function _get_order($order_arr = array())
	{
		return self::getInstance()->_adapter->_get_order($order_arr);
	}
	public static function _set_table_name($table_name)
	{
		self::getInstance()->_adapter->_set_table_name($table_name);
	}
	public static function _get_empty_date()
	{
		return self::getInstance()->_adapter->get_empty_date();
	}
	public static function _get_one_data($where_arr = array(), $order_arr = array())
	{
		return self::getInstance()->_adapter->get_one_data($where_arr, $order_arr);
	}
	public static function _add_data()
	{
		return self::getInstance()->_adapter->add_data();
	}
	public static function _quote($value)
	{
		return self::getInstance()->_adapter->_quote($value);
	}	
}
?>