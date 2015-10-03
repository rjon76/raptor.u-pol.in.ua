<?php
/* requrements
  class Error
  class VException
  interface DbAdapter
*/
include_once(ENGINE_PATH.'interface/interfaceDbAdapter.php');

class DbMysqlAdapter implements DbAdapter {
    private $_connection;
    private $_settings;

    public function __construct() { }

    public function __destruct() {
	$this->_settings = NULL;
	if(is_resource($this->_connection)) {
	    $this->closeConnection();
	}
    }

    public function initAdapter($settings) {
	if(!is_array($settings)) {
	    throw new VException('Wrong config parameters for DbMysqlAdapter class.');
	}
	$this->_settings['host'] = empty($settings['host']) ? 'localhost' : $settings['host'];
	$this->_settings['dbname'] = empty($settings['dbname']) ? '' : $settings['dbname'];
	$this->_settings['username'] = empty($settings['username']) ? '' : $settings['username'];
	$this->_settings['password'] = empty($settings['password']) ? '' : $settings['password'];
	if(!empty($settings['charset'])) {
	    $this->_settings['charset'] = $settings['charset'];
	}
	$this->_connect();
    }

    public function closeConnection() {
	mysql_close($this->_connection);
        $this->_connection = NULL;
    }

    public function query($sql, $params) {
    	
	$this->_connect();
	if(is_array($params)) {
	    $sql = $this->_parseQuery($sql,$params);
	}
	$result = array();
	if($res = mysql_query($sql, $this->_connection)) {
	    if(mysql_num_rows($res) > 0) {
		while ($row = mysql_fetch_assoc($res)) {
		    array_push($result,$row);
		}
	    }
	    mysql_free_result($res);
	}
	else {
	    Error::logError('Query Failed', 'Q: '.$sql."\n".mysql_error($this->_connection));
	}
	return $result;
    }

    public function execute($sql, $params) {
	$this->_connect();
	if(is_array($params)) {
	    $sql = $this->_parseQuery($sql,$params);
	}
	$result = FALSE;
	if($res = mysql_query($sql, $this->_connection)) {
	    $result = TRUE;
	    mysql_free_result($res);
	}
	elseif(mysql_errno($this->_connection)) {
	    Error::logError('Query Failed', 'Q: '.$sql."\n".mysql_error($this->_connection));
	}
	return $result;
    }

    public function lastInsertId() {
	$this->_connect();
	return $mysql_insert_id($this->_connection);
    }

    private function _connect() {
	if (is_resource($this->_connection)) {
            return;
        }

	if(!empty($this->_settings['port'])) {
	    $this->_settings['host'].= ':'.$this->_settings['port'];
	}

	$this->_connection = mysql_connect($this->_settings['host'],
					   $this->_settings['username'],
					   $this->_settings['password']);

	if ($this->_connection === FALSE || mysql_errno()) {
	    Error::logError('Fail to connect to database.',mysql_error());
            throw new VException(mysql_error());
        }

	if(!mysql_select_db($this->_settings['dbname'], $this->_connection)) {
	    Error::logError('Fail to select database "'.$this->_settings['dbname'].'". ',mysql_error($this->_connection));
            throw new VException(mysql_error($this->_connection));
	}

	if(!empty($this->_settings['charset'])) {
	    mysql_query('SET NAMES '.$this->_settings['charset'], $this->_connection);
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

    private function _quote($value) {
	if (is_int($value) || is_float($value)) {
            return $value;
        }
        return '\''.mysql_real_escape_string($value, $this->_connection).'\'';
    }
}
?>