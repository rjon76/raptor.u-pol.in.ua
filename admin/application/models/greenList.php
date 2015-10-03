<?php

include_once(ENGINE_PATH.'class/classIniParser.php');
include_once(ENGINE_PATH.'class/classDB.php');
include_once(ENGINE_PATH.'class/classConstData.php');

class GreenList {
    private $constData;
    private $siteDbAdapter;
    private $siteDbName;
    private $headerShort;
    private $headerFull;
    private $id404;

    public function __construct($siteId) {

        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname', 's_path'));
        $select->where('s_id = ?', $siteId);
        $res = $dbAdapter->fetchRow($select->__toString());
        $this->siteDbName = $res['s_dbname'];

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $params['dbname'] = $this->siteDbName;

        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);
        $this->siteDbAdapter->query('SET NAMES utf8');

        @IniParser::setIni($res['s_path'].'application/config.ini', TRUE);

	$this->constData = new ConstData($params['dbname']);

	$this->headerShort = array('1' => '301 Moved Permanently',
				   '2' => '302 Moved Temporary',
				   '3' => '404 Not Found',
				   '4' => '410 Never exist',
				   '5' => 'No headers');
	$this->headerFull = array('1' => 'HTTP/1.1 301 Moved Permanently',
				   '2' => 'HTTP/1.1 302 Found',
				   '3' => 'HTTP/1.1 404 Not Found',
				   '4' => 'HTTP/1.1 410 Gone',
				   '5' => '');
	$this->id404 = '3';
    }

    // destuctor
    public function __destruct() {
	$this->siteDbAdapter = NULL;
	$this->siteDbName = NULL;
	$this->constData = NULL;
	$this->headerShort = NULL;
	$this->headerFull = NULL;
	$this->id404 = NULL;
    }

    public function getShortHeaders() {
	return $this->headerShort;
    }

    public function get404() {
	return $this->id404;
    }

    public function getBaseAddress() {
	return 'http://'.$this->constData->getConst('cachedDomain').'/';
    }

    public function get404Address() {
	return 'http://'.$this->constData->getConst('cachedDomain').'/'.$this->constData->getConst('404page');
    }

    public function getSimpleGreenList() {
	$select = $this->siteDbAdapter->select();
	$select->from('greenlist', array('id' => 'gl_id',
				    'address' => 'gl_address',
				    'header' => 'gl_header',
				    'destination' => 'gl_destination'));
	$res = $this->siteDbAdapter->fetchAll($select->__toString());
	$result = array();
	$tsize = sizeof($res);
	for($i=0; $i<$tsize; $i++) {
	    $item = $res[$i];
	    if(!empty($res[$i]['header'])) {
		$titem = unserialize($res[$i]['header']);
		$item['header'] = array_intersect_key($this->headerShort,array_intersect($this->headerFull,$titem));
		$item['header'] = array_values($item['header']);
	    }
	    else {
		$item['header'] = array();
	    }
	    $result[] = $item;
	}
	return $result;
    }

    public function getSimpleListRow($id) {
	$id = intval($id);
	$select = $this->siteDbAdapter->select();
	$select->from('greenlist', array('id' => 'gl_id',
				    'address' => 'gl_address',
				    'header' => 'gl_header',
				    'destination' => 'gl_destination'));
	$select->where('gl_id = ?', $id);
	$res = $this->siteDbAdapter->fetchRow($select->__toString());
	$result = array();
	if(!empty($res)) {
	    $result = $res;
	    $result['header'] = array();
	    if(!empty($res['header'])) {
		$result['header']['select'] = array_keys(array_intersect($this->headerFull,unserialize($res['header'])));
	    }
	    else {
		$result['header']['select'] = array('1');
	    }
	}
	return $result;
    }

    public function setSimpleListRow($id, $params) {
	$result = FALSE;
	$headers = array();
	if(!is_array($params['header']['select'])) {
	    $params['header']['select'] = array($params['header']['select']);
	}
	foreach($params['header']['select'] as $value) {
	    $value = (string)$value;
	    if(isset($this->headerFull[$value])) {
		$headers[] = $this->headerFull[$value];
	    }
	}
	if(!empty($headers)) {
	    $id = intval($id);
	    $result = TRUE;
	    $set = array('gl_address' => $params['address'],
			 'gl_header' => serialize($headers),
			 'gl_destination' => $params['destination']);
	    $where = $this->siteDbAdapter->quoteInto('gl_id = ?', $id);
	    $this->siteDbAdapter->update('greenlist', $set, $where);
	}
	return $result;
    }

    public function addSimpleListRow($params) {
	$result = FALSE;
	$header = (string)$params['header']['select'];
	if(isset($this->headerFull[$header]) && !$this->checkAddress($params['address'])) {
	    $set = array('gl_address' => $params['address'],
			'gl_header' => serialize(array($this->headerFull[$header])));
	    if($params['header'] != $this->id404) {
		$set = array_merge($set,array('gl_destination' => $params['destination']));
	    }
	    $result = $this->siteDbAdapter->insert('greenlist', $set);
	}
	return $result;
    }

    public function dropSimpleListRow($id) {
	$id = intval($id);
	$where = $this->siteDbAdapter->quoteInto('gl_id = ?', $id);
	$this->siteDbAdapter->delete('greenlist', $where);
    }

    public function getExtGreenList() {
	$select = $this->siteDbAdapter->select();
	$select->from('greenlistext', array('id' => 'gle_id',
				    'expression' => 'gle_expression',
				    'header' => 'gle_header',
				    'destination' => 'gle_destination',
				    'regular' => 'gle_regular',
				    'order' => 'gle_order'));
	$select->order('gle_order');
	$res = $this->siteDbAdapter->fetchAll($select->__toString());
	$result = array();
	$tsize = sizeof($res);
	for($i=0; $i<$tsize; $i++) {
	    $item = $res[$i];
	    if(!empty($res[$i]['header'])) {
		$titem = unserialize($res[$i]['header']);
		$item['header'] = array_intersect_key($this->headerShort,array_intersect($this->headerFull,$titem));
		$item['header'] = array_values($item['header']);
	    }
	    else {
		$item['header'] = array();
	    }
	    $result[] = $item;
	}
	return $result;
    }

    public function addExtListRow($params) {
	$result = FALSE;
	$header = (string)$params['header']['select'];
	if(isset($this->headerFull[$header]) && !$this->checkExpression($params['expression'])) {
	    $set = array('gle_expression' => $params['expression'],
			'gle_regular' => $params['regular'],
			'gle_header' => serialize(array($this->headerFull[$header])),
			'gle_order' => $params['order']);
	    if($header != $this->id404) {
		$set = array_merge($set,array('gle_destination' => $params['destination']));
	    }
	    $result = $this->siteDbAdapter->insert('greenlistext', $set);
	}
	return $result;
    }

    public function getExtListRow($id) {
	$id = intval($id);
	$select = $this->siteDbAdapter->select();
	$select->from('greenlistext', array('id' => 'gle_id',
				    'expression' => 'gle_expression',
				    'header' => 'gle_header',
				    'destination' => 'gle_destination',
				    'regular' => 'gle_regular',
				    'order' => 'gle_order'));
	$select->where('gle_id = ?', $id);
	$res = $this->siteDbAdapter->fetchRow($select->__toString());
	$result = array();
	if(!empty($res)) {
	    $result = $res;
	    $result['header'] = array();
	    if(!empty($res['header'])) {
		$result['header']['select'] = array_keys(array_intersect($this->headerFull,unserialize($res['header'])));
	    }
	    else {
		$result['header']['select'] = array('1');
	    }
	}
	return $result;
    }

    public function setExtListRow($id, $params) {
	$result = FALSE;
	$headers = array();
	if(!is_array($params['header']['select'])) {
	    $params['header']['select'] = array($params['header']['select']);
	}
	foreach($params['header']['select'] as $value) {
	    $value = (string)$value;
	    if(isset($this->headerFull[$value])) {
		$headers[] = $this->headerFull[$value];
	    }
	}
	if(!empty($headers)) {
	    $id = intval($id);
	    $result = TRUE;
	    $set = array('gle_expression' => $params['expression'],
			 'gle_header' => serialize($headers),
			 'gle_destination' => $params['destination'],
			 'gle_regular' => $params['regular'],
			 'gle_order' => $params['order']);
	    $where = $this->siteDbAdapter->quoteInto('gle_id = ?', $id);
	    $this->siteDbAdapter->update('greenlistext', $set, $where);
	}
	return $result;
    }

    public function dropExtListRow($id) {
	$id = intval($id);
	$where = $this->siteDbAdapter->quoteInto('gle_id = ?', $id);
	$this->siteDbAdapter->delete('greenlistext', $where);
    }

    private function checkAddress($addr) {
	$select = $this->siteDbAdapter->select();
	$select->from('greenlist', array('cnt' => 'COUNT(gl_id)'));
	$select->where('gl_address = ?', $addr);
	$res = $this->siteDbAdapter->fetchAll($select->__toString());
	return (bool)$res[0]['cnt'];
    }

    private function checkExpression($addr) {
	$select = $this->siteDbAdapter->select();
	$select->from('greenlistext', array('cnt' => 'COUNT(gle_id)'));
	$select->where('gle_expression = ?', $addr);
	$res = $this->siteDbAdapter->fetchOne($select->__toString());
	return (bool)$res['cnt'];
    }
}

?>