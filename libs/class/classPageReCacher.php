<?php

class PageReCacher extends Page {
/*
    public function __construct($pg_id, $db='') {
	parent::__construct($pg_id, $db);
    }

    public function __destruct() {
	parent::__destruct();
    }
*/
	
    public function refreshLastmodify($hash) {
		$cached = ($hash=='') ? 0 : 1;
	$q = 'UPDATE '.$this->siteDb.'pages SET
		pg_lastmodify = "'.date('Y-m-d H:i:s').'",
		pg_checksum = "'.$hash.'",
		pg_cached = "'.$cached.'"
	      WHERE pg_id = '.$this->id.'
	      LIMIT 1';
	DB::executeAlter($q,'lastmod');
    }

    // method parses uri part of address into the array
    protected function parseAddress_bkp($uri) {
        $addr = array();
        $addr['domain_address'] = $addr['base_address'] = 'http://'.VBox::get('ConstData')->getConst('cachedDomain');
        $addr['lang_prefix'] = $this->alLangs[$this->language]['prefix'];
        if(strlen($addr['lang_prefix'])) {
            $addr['base_address'] .= '/'.$addr['lang_prefix'];
        }
        if(substr($uri,0,1) != '/') {
            $uri = '/'.$uri;
        }
        $addr['uri_address'] = $uri;
        $addr['full_address'] = $addr['base_address'].$addr['uri_address'];
        $addr['params'] = array();
        return $addr;
    }
	 protected function parseAddress($uri) {
        $addr = array();
//        $addr['domain_address'] = $addr['base_address'] = 'http://'.$_SERVER['HTTP_HOST'];
        $addr['domain_address'] = $addr['base_address'] = 'http://'.VBox::get('ConstData')->getConst('cachedDomain');
        $addr['lang_prefix'] = $addr['prefix'] = $this->alLangs[$this->language]['prefix'];
        if(strlen($addr['lang_prefix'])) {
            $addr['base_address'] .= '/'.$addr['lang_prefix'];
            $addr['prefix'] = '/'.$addr['prefix'];
        }
        if(substr($uri,0,1) != '/') {
            $uri = '/'.$uri;
        }
        $addr['uri_address'] = $uri;
        $addr['full_address'] = $addr['base_address'].$addr['uri_address'];
        $addr['params'] = array();
        if(!substr_count($uri,'.html')) {
            if(strlen(trim($addr['uri_address'],'/')) != strlen(trim(VBox::get('ConstData')->getConst('request'),'/'))) {
                $params = substr(trim(VBox::get('ConstData')->getConst('request'),'/'),strlen(trim($addr['uri_address'],'/')));
                $addr['params'] = explode('/',$params);
            }
        }
        return $addr;
    }
}

?>