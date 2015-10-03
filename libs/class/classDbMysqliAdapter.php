<?php
/* requrements
  class Error
  class VException
  interface DbAdapter
*/
include_once(ENGINE_PATH.'interface/interfaceDbAdapter.php');

class DbMysqliAdapter implements DbAdapter {
    private $_connection;
    private $_settings;
	public $table_name;
    public function __construct() { }

    public function __destruct() {
	$this->_settings = NULL;
	if(is_object($this->_connection)) {
	    $this->closeConnection();
	}
    }

    public function initAdapter($settings)
    {
		if(!is_array($settings)) {
	    	throw new VException('Wrong config parameters for DbMysqliAdapter class.');
		}
		
		$this->_settings['host'] 		= empty($settings['host']) ? 'localhost' : $settings['host'];
		$this->_settings['dbname'] 		= empty($settings['dbname']) ? '' : $settings['dbname'];
		$this->_settings['username'] 	= empty($settings['username']) ? '' : $settings['username'];
		$this->_settings['password'] 	= empty($settings['password']) ? '' : $settings['password'];
		if(!empty($settings['charset']))
		{
	    	$this->_settings['charset'] = $settings['charset'];
		}
		$this->_connect();
    }

    public function closeConnection() {
		if($this->_connection)
		{
	    	$this->_connection->close();
	    	$this->_connection = NULL;
		}
    }

    public function query($sql, $params)
    {
		$this->_connect();
		
		if(is_array($params))
		{
	    	$sql = $this->_parseQuery($sql,$params);
		}
		$result = array();
		
		if($res = $this->_connection->query($sql))
		{
	    	while ($row = $res->fetch_assoc())
	    	{
				array_push($result,$row);
	    	}
	    	$res->close();
		}
		else
		{
	    	Error::logError('Query Failed', 'Q: '.$sql."\n".$this->_connection->error);
		}

	//	$this->closeConnection(); //garbagecat76 06.01.2010
		return $result;
    }

    public function execute($sql, $params) {
	$this->_connect();
	if(is_array($params)) {
	    $sql = $this->_parseQuery($sql,$params);
	}
	$result = FALSE;
	if($this->_connection->query($sql)) {
	    $result = TRUE;
	    //$res->close();
	}
	elseif($this->_connection->error) {
	    Error::logError('Query Failed', 'Q: '.$sql."\n".$this->_connection->error);
	}
	//$this->closeConnection(); //garbagecat76 06.01.2010
	return $result;
    }

    public function lastInsertId()
    {
		$this->_connect();
		$insert_id = $this->_connection->insert_id;
	//	$this->closeConnection(); //garbagecat76 06.01.2010
		return $insert_id;
    }

    private function _connect() {
	if ($this->_connection) {
            return;
        }

        if (!extension_loaded('mysqli')) {
	    Error::logError('Extention is missing.','Mysqli extention is not loaded.');
            throw new VException('The Mysqli extension is required for this adapter but the extension is not loaded');
        }

	$port = !empty($this->_settings['port']) ? (integer)$this->_settings['port'] : NULL;

        // Suppress connection warnings here.
        // Throw an exception instead.
        $this->_connection = new mysqli(
            $this->_settings['host'],
            $this->_settings['username'],
            $this->_settings['password'],
            $this->_settings['dbname'],
            $port
        );
        if ($this->_connection === FALSE || mysqli_connect_errno()) {
	    Error::logError('Fail to connect to database.',mysqli_connect_error());
            throw new VException(mysqli_connect_error());
        }
	if(!empty($this->_settings['charset'])) {
	    $this->_connection->query('SET NAMES '.$this->_settings['charset']);
	}
    }

    private function _parseQuery($sql, $params) {
	$sqlParts = explode('?',$sql);
	$tsize = sizeof($sqlParts);
	$sql = '';
	for($i=0; $i<$tsize; $i++) {
	    $sql.= $sqlParts[$i];
	    if(isset($params[$i])) {
		$sql.= $this->_quote($params[$i]);
	    }
	}
	return $sql;
    }

    public function _quote($value) {
	if (is_int($value) || is_float($value)) {
            return $value;
        }
        return '\''.$this->_connection->real_escape_string($value).'\'';
    }
	/*---26.10.2009 garbagecat76--*/
	public function _get_where($where_arr = array())
	{
		$where_str		= '';
		if(!is_array($where_arr))
			return $where_arr;
		foreach ($where_arr as $key => $value)
		{
			if ($value !== '*')
			{
				if (empty($where_str))
					$where_str		.= "WHERE ";
				else
					$where_str		.= " AND ";
					$where_str		.=($value=='null')? "($key is null)" : "($key = '$value')";
			}
		}
		
		return $where_str;
	}
	
	public function _get_limit($num_page = '*', $where_arr = array())
	{
		$limit_str		= '';
		if ($num_page !== '*')
		{
			$limit = VBox::get('ConstData')->getConst('in_page');
			$settings = IniParser::getInstance()->getSection('count');
			$limit = $settings['in_page'];

			$where_str	= $this->_get_where($where_arr);
			//$where_str		= $this->_get_where($where_arr);
			$count			= $this->get_count_data($where_str);
			$from			= $num_page * $limit;
			$limit_str		= "LIMIT $from, $limit";
		}
		return $limit_str;
	}
	
	protected function _get_limit_str($num_page = '*', $where_str = '')
	{
		$limit_str		= '';
		
		if ($num_page !== '*')
		{
			$limit = VBox::get('ConstData')->getConst('in_page');
			$count			= $this->get_count_data($where_str);
			$from			= $num_page * $limit;
			$limit_str		= "LIMIT $from, $limit";
		}
		
		return $limit_str;
	} 
	
	public function _get_order($order_arr = array())
	{
		$order_str		= '';
		if (!is_array($order_arr))
		 $order_str = $order_arr;
		else 
		foreach ($order_arr as $key => $value)
		{
			if (empty($order_str))
				$order_str		= "ORDER BY $key $value";
			else
				$order_str		.= ", $key $value";
		}
		return $order_str;
	}
	
	protected function _get_fields($fields_arr = '*')
	{
		if(!is_array($fields_arr))
			return $fields_arr;
		elseif(count($fields_arr)==0)
		 return '*';
		else 
		return implode(",", $fields_arr);
	}
	
	function exist_field($field_name='')
	{
		$this->_connect();
		$exist_field	= false;
		$res = array();
		$sql	= "SELECT *	FROM " . $this->table_name . " limit 1";
		if($result = $this->_connection->query($sql))
		{
			$finfo = $result->fetch_fields();
			$result->close();
			$this->closeConnection(); //garbagecat76 06.01.2010
			foreach ($finfo as $val)
			$res[$val->name] = $val->type;
			if ($field_name!=='')
				if (isset($res[$field_name])) return true;
				else
				return false;
			return 	$res;
		}
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $exist_field;
	}
	public function get_count_data($where_str = '')
	{
		$this->_connect();
		$count 			= 0;
		$sql			= 'SELECT COUNT(*) AS count_records FROM ' . $this->table_name . ' ' . $where_str;
	//	var_dump($sql);
		if ($result	= $this->_connection->query($sql))
			{
				$row = $result->fetch_assoc();
				$count	= (int)($row['count_records']);
				$result->close();
			}
		elseif(mysqli_errno($this->_connection)) 
	    	Error::logError('Query Failed', 'Q: '.$sql."\n".mysqli_error($this->_connection));
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $count;
	}
	
	public function get_page_count_date($where_str = '')
	{
		
		$where_str	= $this->_get_where($where_str);
		$count		= $this->get_count_data($where_str);
		//var_dump($count, IniParser::getInstance()->getSettring('count', 'in_page'));
		$page_count = ceil(($count)/(int)IniParser::getInstance()->getSettring('count', 'in_page'));

		return $page_count;
	}
	public function get_few_data($fields_arr = '*',$where_arr = array(), $num_page = '*', $order_arr = array('id' => 'ASC'))
	{
		$where_str	= $this->_get_where($where_arr);
		$limit_str	= $this->_get_limit($num_page, $where_arr);
		$fields_str	= $this->_get_fields($fields_arr);
		$order_str	= $this->_get_order($order_arr);
		
		$sql	= "SELECT $fields_str
			FROM " . $this->table_name . "
			$where_str
			$order_str
			$limit_str";
		//var_dump($sql);
		
		$result = array();
		$this->_connect();
		if($res = $this->_connection->query($sql))
		{
	    	while ($row = $res->fetch_assoc())
	    	{
				$result[$row['id']] = $row;
				//array_push($result,$row);
	    	}
	    	$res->close();
		}
		else
		{
	    	Error::logError('Query Failed', 'Q: '.$sql."\n".$this->_connection->error);
		}
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $result;
	}
	public function _set_table_name($table_name)
	{
		$this->table_name =  $table_name;	
	}
	public function get_empty_date()
	{
		$data_info	= array();
		$sql	= "SELECT *	FROM " . $this->table_name . " limit 1";
		$this->_connect();
		if($result = $this->_connection->query($sql))
		{
/*
DECIMAL           0       ENUM           247
TINY              1       SET            248
SHORT             2       TINY_BLOB      249
LONG              3       MEDIUM_BLOB    250
FLOAT             4       LONG_BLOB      251
DOUBLE            5       BLOB           252
NULL              6       VAR_STRING     253
TIMESTAMP         7       STRING         254
LONGLONG          8       GEOMETRY       255
INT24             9
DATE             10
TIME             11
DATETIME         12
YEAR             13
NEWDATE          14
	*/	
		
		$finfo = $result->fetch_fields();
			    foreach ($finfo as $val)
			{
			$field_name		= $val->name;
			$field_type		= $val->type;
			switch ($field_type)
			{
				case 0:
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 9:
					$data_info[$field_name]	= 0;
					break;
				case 249:
				case 251:
				case 252:
				case 253:
				case 254:
					$data_info[$field_name]	= '';
					break;
				case 10:
					$data_info[$field_name]	= date('Y-m-d');
					break;	
				case 11:
					$data_info[$field_name]	= date('H:i');
					break;
				case 12:
					$data_info[$field_name]	= date('Y-m-d H:i');
					break;
				case 13:
					$data_info[$field_name]	= date('y');
					break;						
				default: $data_info[$field_name]	= '';
			}
			
			switch ($field_name)
			{
				case 'is_blocked':
					$data_info[$field_name]	= 0;
					break;
				case 'image':
					$data_info['image_save'] = $data_info[$field_name];
					break;
			}
		}
		$result->close();
		}
		else
		{
	    	Error::logError('Query Failed', 'Q: '.$sql."\n".$this->_connection->error);
		}
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $data_info;
	}
	public function get_one_data($where_arr = array(), $order_arr = array())
	{
		$data_info		= array();
		$where_str	= $this->_get_where($where_arr);
		$fields_str	= $this->_get_fields();
		$order_str	= $this->_get_order($order_arr);
		
		$sql	= "SELECT $fields_str
			FROM " . $this->table_name . "
			$where_str
			$order_str
			limit 1";
		$this->_connect();		
		if($result = $this->query($sql, false))
		{
				$data_info = $result[0];
				if ($this->exist_field('date'))
				{
					$data_info['s_day']		= date('d', strtotime($data_info['date']));
					$data_info['s_month']	= date('m', strtotime($data_info['date']));
					$data_info['s_year']	= date('Y', strtotime($data_info['date']));
					$data_info['s_hour']	= date('H', strtotime($data_info['date']));
					$data_info['s_minute']	= date('i', strtotime($data_info['date']));
					$data_info['month_number'] = date('n', strtotime($data_info['date']))-1 ; 
				}
				if ($this->exist_field('image'))
				{
					$data_info['image_save']	= $data_info['image'];
					$data_info['image_type']	= strrchr($data_info['image'], ".");
					if ($data_info['image_type'] != '.swf')
					{
				//	$data_info['image_size']	= (!empty($data_info['image'])) ? $GLOBALS['images']->get_image_size($GLOBALS['options']->patch->small_img, $GLOBALS['options']->patch->norm_img, $data_info['image']) : null;
					}
					else
						$data_info['image_flash']	= substr ($data_info['image'], 0, strlen($data_info['image']) - 4);
				}
		}
		else
		{
	    	Error::logError('Query Failed', 'Q: '.$sql."\n".$this->_connection->error);
		}
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $data_info;
	}
//------------
public function check_post()
{
	return $_POST;
}

public function add_data()
	{
		$result = false;
		$data_info	= $this->check_post();
		$fields_arr = $this->exist_field();
		if (count($data_info) > 0)
		{
			$keys	= '';
			$values	= '';
			
			foreach ($data_info as $key => $value)
			{
				if (isset($fields_arr[$key]))
					{
						if (!empty($keys))
						{
							$keys	.= ',';
							$values	.= ',';
						}
						$keys	.= " $key";
						$values	.= " ".$this->_quote((is_array($value) ? implode(',',$value) : $value)).' ';
					}
			}
			
			if (isset($fields_arr['date']) && !isset($data_info['date']))
			{
				$keys	.= ", date";
				$values	.= ", NOW()";
			}
			if (isset($fields_arr['ip']) && !isset($data_info['ip']))
			{
				$keys	.= ", ip";
				$values	.= ", '".$_SERVER['REMOTE_ADDR']."'";
			}
			$sql = "INSERT INTO " . $this->table_name . "
			( $keys )
			VALUES ( $values )";
//			var_dump($sql);
			$this->_connect();	
 			if($res =  $this->_connection->query($sql, false))
			{
				$result = $this->lastInsertId();
				$res->close();
			}
			else
			{
		    	Error::logError('Query Failed', 'Q: '.$sql."\n".$this->_connection->error);
			} 
		}
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $result;
	}
//-----------------
public function escape($value, $quote = true)
	{ 
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}
		if (!is_numeric($value))
		{
			$this->_connect();
			$value =  $this->_connection->real_escape_string($value);
			if($quote)
				$value	= "'$value'";
		}
		$this->closeConnection(); //garbagecat76 06.01.2010
		return $value;
	}	
}
?>