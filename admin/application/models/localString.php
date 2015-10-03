<?php
class LocalString {
	private $dbAdapter;
	private $alldbAdapter;
	private $langs;
	private $langsNames;
	private $recCount;
    private $recPerPage;
    private $siteId;

    public function __construct($siteId) {
		$this->siteId 		= $siteId;
		$this->dbAdapter 	= Zend_Registry::get('dbAdapter');

		$dbAdapter 			= Zend_Registry::get('dbAdapter');
		$config 			= Zend_Registry::get('config');

        	$params 			= $config->db->config->toArray();
//        	$params['dbname'] 	= 'venginse_all';
			$params['dbname'] = $config->db->config->dballname;
	        $this->alldbAdapter = Zend_Db::factory($config->db->adapter, $params);
	        $select = $dbAdapter->select();
	        $select->from('sites', array('s_dbname', 's_path'));
	        $select->where('s_id = ?', $siteId);

	        $config = $dbAdapter->fetchRow($select->__toString());

	        $this->siteDbName 	= $config['s_dbname'];
	        $this->sitePath 	= $config['s_path'];

	        include_once($this->sitePath.'application/includes.inc.php');

	        IniParser::getInstance()->setIni($this->sitePath.'application/config.ini', TRUE);

	        $config 			= Zend_Registry::get('config');	
	        $params 			= $config->db->config->toArray();
	        $params['dbname'] 	= $this->siteDbName;

	        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);

	        Zend_Registry::set('siteDbAdapter', $this->siteDbAdapter);

	        $this->siteDbAdapter->query('SET NAMES utf8');
 		$this->alldbAdapter->query('SET NAMES utf8');
 		$this->loadLangs();
		$this->recCount 	= $this->countStrings();
		$this->recPerPage 	= 20;
    }

    public function __destruct(){
		$this->dbAdapter 		= NULL;
		$this->alldbAdapter 	= NULL;
		$this->siteDbAdapter 	= NULL;
		$this->langs 			= NULL;
		$this->langsNames 		= NULL;
		$this->recCount 		= NULL;
		$this->recPerPage 		= NULL;
		$this->siteId 			= NULL;
    }

    public function getLangs(){
		return $this->langs;
    }

    public function getStrings($lang, $page = 1)
    {
		$result = array();

		if(in_array($lang,array_keys($this->langsNames)))
		{
			$page 	= intval($page);
	    	$start 	= $this->recPerPage*($page - 1);
	    	$count 	= $this->recPerPage;

	    	if($page == $this->getPagesCount())
	    	{
				$count = $this->recCount - $start;
	    	}
	    	elseif( $page > $this->getPagesCount() || $page < 1 )
	    	{
				$start = 0;
	    	}

            // edit italiano, 24.07.2015
            // search languages in site base and join necessary lang-table
		    $select = $this->siteDbAdapter->select();
		    $select->from(
		    	'languages',
		    	array(
		    		'code' => 'l_code'
		    	)
		    );
	    	$select->order(array('l_id'));
    	    $langs = $this->siteDbAdapter->fetchAll($select->__toString()); 
            //$langs = $this->getLangs(); // from main base, exp: vinginse_all

    		$select = $this->siteDbAdapter->select();
    		$select->from('lstrings', array('id' => 'ls_id', 'nick' => 'ls_nick'));
    		$select->joinLeft('en', 'ls_id = en.ll_nick_id', array('text_en' => 'IFNULL(en.ll_text, "")'));
            
            foreach($langs as $items){
                $item = $items['code'];
                if($item != 'en'){
                    $select->joinLeft("$item", "ls_id = $item.ll_nick_id", array("text_$item" => "IFNULL($item.ll_text, '')","isT_$item" => "$item.is_translate"));
                }    
            }
            
/*
    		$select->joinLeft('fr', 'ls_id = fr.ll_nick_id', array('text_fr' => 'IFNULL(fr.ll_text, "")','isT_fr' => 'fr.is_translate'));
    		$select->joinLeft('de', 'ls_id = de.ll_nick_id', array('text_de' => 'IFNULL(de.ll_text, "")','isT_de' => 'de.is_translate'));

    		$select->joinLeft('jp', 'ls_id = jp.ll_nick_id', array('text_jp' => 'IFNULL(jp.ll_text, "")','isT_jp' => 'jp.is_translate'));
    		$select->joinLeft('es', 'ls_id = es.ll_nick_id', array('text_es' => 'IFNULL(es.ll_text, "")','isT_es' => 'es.is_translate'));
    		$select->joinLeft('it', 'ls_id = it.ll_nick_id', array('text_it' => 'IFNULL(it.ll_text, "")','isT_it' => 'it.is_translate'));
			$select->joinLeft('ru', 'ls_id = ru.ll_nick_id', array('text_ru' => 'IFNULL(ru.ll_text, "")','isT_ru' => 'ru.is_translate'));
            $select->joinLeft('cn', 'ls_id = cn.ll_nick_id', array('text_cn' => 'IFNULL(cn.ll_text, "")','isT_cn' => 'cn.is_translate'));
*/
    	//	$select->where('ls_site = ?', $this->siteId);
    		$select->order(array('nick'));
    		
    		//$select->limit($count, $start);
    		
    		$result = $this->siteDbAdapter->fetchAll($select->__toString());
		}
		return $result;
    }


    public function getString($lang, $id) {
		$result = array();
		if(in_array($lang,array_keys($this->langsNames))) {
	    	$id = intval($id);
		    $select = $this->siteDbAdapter->select();
		    $select->from('lstrings', array('id' => 'ls_id', 'nick' => 'ls_nick'));
	    	if($lang != 'en') {
				$select->joinLeft('en', 'ls_id = en.ll_nick_id', array('en_text' => 'en.ll_text'));
				$select->joinLeft($lang, 'ls_id = '.$lang.'.ll_nick_id', array('text' => 'IFNULL('.$lang.'.ll_text, "")','isT' => ''.$lang.'.is_translate'));
	    	}else {
				$select->joinLeft('en', 'ls_id = en.ll_nick_id', array('en_text' => 'en.ll_text','text' => 'en.ll_text'));
	    	}
	    	$select->where('ls_id = ?', $id);
	//    	$select->where('ls_site = ?', $this->siteId);
	    	$result = $this->siteDbAdapter->fetchAll($select->__toString());
	    	$result = $result[0];
		}
		return $result;
    }

    public function setString($lang, $id, $params, $admin) {
		$result = FALSE;

		if(in_array($lang,array_keys($this->langsNames))) {
	    	$id 	= intval($id);
	    	$res 	= $this->getString($lang, $id);

	    	$select = $this->siteDbAdapter->select();
	    	$select->from($lang, array('ll_text'));
	    	$select->where('ll_nick_id = ?', $id);
	    	$res 	= $this->siteDbAdapter->fetchAll($select->__toString());

	    	if(!empty($res)) {
				$result = TRUE;
			    $set 	= array('ll_text' => $params['text'],'is_translate' => $params['isTrans']);
		    	$where 	= $this->siteDbAdapter->quoteInto('ll_nick_id = ?', $id);
		    	$result = (bool)$this->siteDbAdapter->update($lang, $set, $where);
		    }else{
				$set 	= array('ll_nick_id' => $id, 'll_text' => $params['text'],'is_translate' => $params['isTrans']);
				$result = (bool)$this->siteDbAdapter->insert($lang, $set);
	    	}
		    if($admin && $result) {
				$set 	= array('ls_nick' => $params['nick'],'is_translate' => $params['isTrans']);
				$where 	= $this->siteDbAdapter->quoteInto('ls_id = ?', $id);
				$this->siteDbAdapter->update('lstrings', $set, $where);
		    }
		}
		return $result;
    }

    public function dropString($id) {
		$id = intval($id);
		foreach($this->langsNames as $key => $val) {
	    	$where = $this->siteDbAdapter->quoteInto('ll_nick_id = ?', $id);
	    	$this->siteDbAdapter->delete($key, $where);
		}
		$table = 'lstrings';
		$where = $this->siteDbAdapter->quoteInto('ls_id = ?', $id);
		$this->siteDbAdapter->delete($table, $where);
    }

    public function dropStrings($strIds) {
        foreach($strIds AS $id) {
            $this->dropString($id);
        }
		return json_encode($strIds);
    }


    public function addString($params) {
		$result = FALSE;
		$set = array('ls_nick' => $params['nick'], 'ls_site' => $this->siteId);
		//$set = array('ls_nick' => $params['nick']);		
		if($this->siteDbAdapter->insert('lstrings', $set)) {
	    	$id = $this->siteDbAdapter->lastInsertId();
	    	$set = array('ll_nick_id' => $id);
	    	foreach($params as $key => $val) {
				if(in_array($key,array_keys($this->langsNames))) {
		    		$result = (bool)$this->siteDbAdapter->insert($key, array_merge($set, array('ll_text' => $val)));
				}
	    	}	
		}
		return $result;
    }

    public function checkNick($nick) {
		$select = $this->siteDbAdapter->select();
		$select->from('lstrings', array('cnt' => 'COUNT(ls_nick)'));
		$select->where('ls_nick = ?', $nick);
//		$select->where('ls_site = ?', $this->siteId);
		$res = $this->siteDbAdapter->fetchAll($select->__toString());
		if($res[0]['cnt'] > 0) {
	    	return TRUE;
		}
		return FALSE;
    }

    public function searchString($param, $text) {
		$result = array();
		$text 	= preg_replace('/[^a-z, A-Z, 0-9,_,-]/', '', $text);
//		var_dump($text);
		if(!empty($param) && !empty($text)) {
		    $select = $this->siteDbAdapter->select();
		    $select->from('lstrings', array('id' => 'ls_id', 'nick' => 'ls_nick'));
	    	foreach($this->langsNames AS $key => $val) {
				$select->joinLeft($key, 'ls_id = '.$key.'.ll_nick_id', array($key => 'IFNULL('.$key.'.ll_text, "")'));
	    	}
	    //	$select->where('ls_site = ?', $this->siteId);
	    	if($param == 'nick') {
				$select->where('ls_nick LIKE ?', '%'.$text.'%');
	    	}elseif(in_array($param,array_keys($this->langsNames))){
				$select->where($param.'.ll_text LIKE ?', '%'.$text.'%');
	    	}else{
				return $results;
	    	}
	    	$result = $this->siteDbAdapter->fetchAll($select->__toString());
		}
		return $result;
    }

    public function getPagesCount() {
		return ceil($this->recCount/$this->recPerPage);
    }

	public function countStrings() {
		$select = $this->siteDbAdapter->select();
		$select->from('lstrings', array('row_count' => 'COUNT(ls_id)'));
		//$select->where('ls_site = ?', $this->siteId);
		$result = $this->siteDbAdapter->fetchAll($select->__toString());
		return $result[0]['row_count'];
    }

    
    
    private function loadLangs() {
		$select = $this->alldbAdapter->select();
//		$select = $this->siteDbAdapter->select();
        $select->from('languages', array('l_id', 'l_name', 'l_code', 'l_addrcode', 'l_blocked'));
        $result = $this->alldbAdapter->fetchAll($select->__toString());
//        $result = $this->siteDbAdapter->fetchAll($select->__toString());
		$this->langs = array();
		$this->langsNames = array();
        foreach($result AS $row) {
            $this->langs[$row['l_id']] = array('name' => $row['l_name'],
					 'code' => $row['l_code'],
					 'prefix' => $row['l_addrcode'],
					 'blocked' => (bool)$row['l_blocked']);
	    $this->langsNames[$row['l_code']] = $row['l_id'];
        }
    }


    public function openEditWindow($data,$lang) {
    	$data['en_text'] 	= htmlentities($data['en_text']);
    	$data['lang'] 		= $lang;
    	echo json_encode($data);
    }

    public function openEditNickWindow($data){
    	echo json_encode($data);
    }

    public function setNick($id,$nick){
		$set 	= array('ls_nick' => $nick);
		$where 	= $this->alldbAdapter->quoteInto('ls_id = ?', $id);
		$result = (bool)$this->alldbAdapter->update('lstrings', $set, $where);
		return $result;
    }


}

?>