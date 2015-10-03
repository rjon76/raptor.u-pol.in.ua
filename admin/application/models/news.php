<?php

class AdminNews {

    private $allDbAdapter;
    private $siteDbAdapter;

    public function __construct() {
	$this->dbAdapter = Zend_Registry::get('dbAdapter');
	$config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->allDbAdapter->query('SET NAMES utf8');
        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);
        Zend_Registry::set('siteDbAdapter', $this->siteDbAdapter);
        $this->siteDbAdapter->query('SET NAMES utf8');
		
    }

    public function __destruct() {

    }

    public function getNews($lang) {
	$news = array();

	if(strlen($lang)==2) {
	    $select = $this->allDbAdapter->select();
	    $select->from('news_'.$lang, array('id' => 'news_id','text' => 'news_text','title' => 'news_title','order' => 'news_order'));
	    $select->order(array('news_order'));
	    $news = $this->allDbAdapter->fetchAll($select->__toString());
	}
	return $news;
    }

    public function saveNews($data, $lang) {
	$fid = $data['nid'];
	$ftext = $data['ntext'];
	$forder = $data['norder'];
	$ftitle = $data['ntitle'];
	$tsize = sizeof($nid);
	if(strlen($lang)==2 && sizeof($ftext)==$tsize) {
	    for($i=0; $i<$tsize; $i++) {
		$set = array('news_text' => $ftext[$i], 'news_order' => $forder[$i], 'news_title' => $ftitle[$i]);
		$this->allDbAdapter->update('news_'.$lang, $set, $this->allDbAdapter->quoteInto('news_id = ?', $fid[$i]));
	    }
	}
    }

    public function dropNews($fId, $lang) {
	$fId = intval($fId);
	//$langs = $this->getLangs();
	if(strlen($lang)==2 && $fId>0) {
	    $this->allDbAdapter->delete('news_'.$lang, $this->allDbAdapter->quoteInto('news_id = ?', $fId));
	}
    }

    public function addNews($lang, $data) {
	if(!empty($data['aftext']) && strlen($lang)==2) {
	    $set = array(
			 'news_text' => $data['aftext'],
			 'news_title' => $data['aftitle'],
			 'news_order' => intval($data['aforder']));
	    $this->allDbAdapter->insert('news_'.$lang, $set);
	}
    }
}
?>