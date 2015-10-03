<?php
/* requrements
  class DB
  class VBox
*/
class Page {

    # PRIVATE VАRIABLES
	private $scripts = array();

    # PUBLIC VARIABLES
    public $title;
    public $menuTitle;
    public $address;
    public $language;
    public $languageId;
    public $parentPageId;
    // for google sitemap generator
    public $isIndexed;
    public $priority;
    public $lastmodify;
    public $checksum;
    public $options; /* added 18.11.2014, italiano */
    public $screen; /* added 08.01.2015, italiano */

    # PROTECTED VARIABLES
    protected $id;
    protected $relativePages;
    protected $siteDb;
    protected $alLangs;
    protected $headers;
    protected $metas;
    protected $css;
    protected $js;
    protected $isCacheable;
    protected $isReCaching;
    protected $isHidden;
    protected $extensions;
    protected $lStrings;
    

	#const
	/**
		 * The script is rendered in the head section right before the title element.
		 */
		const POS_HEAD='header';
		/**
		 * The script is rendered at the beginning of the body section.
		 */
		const POS_BEGIN='begin';
		/**
		 * The script is rendered at the end of the body section.
		 */
		const POS_END='end';
		/**
		 * The script is rendered inside window onload function.
		 */
		const POS_LOAD='load';
		/**
		 * The body script is rendered inside a jQuery ready function.
		 */
		const POS_READY='ready';
	
	
    // constructor
    public function __construct($pg_id, $db='') {

        $this->id            = intval($pg_id);
        $this->siteDb        = (!empty($db) ? $db.'.' : '');
        $this->address       = array();
        $this->address['domain_address'] = '';
        $this->address['base_address'] = '';
        $this->address['lang_prefix'] = '';
        $this->address['prefix'] = '';
        $this->address['uri_address'] = '';
        $this->address['full_address'] = '';
        $this->address['params'] = array();
        $this->address['domain_address'] = '';

        $q = 'SELECT *,UNIX_TIMESTAMP(pg_lastmodify) AS pg_lastmod
              FROM '.$this->siteDb.'pages
              WHERE pg_id = ? LIMIT 1';
        DB::executeQuery($q, 'page_data', array($this->id));
        $page_data = DB::fetchRow('page_data');

        if(!empty($page_data)) {
            $this->parentPageId  = $page_data['pg_parent'];
            $this->title         = $page_data['pg_title'];
            $this->menuTitle     = $page_data['pg_menu_title'];
            $this->relativePages = strlen($page_data['pg_relative']) ? unserialize($page_data['pg_relative']) : array();
            $this->css           = strlen($page_data['pg_css']) ? unserialize($page_data['pg_css']) : array();
            $this->js            = strlen($page_data['pg_jscript']) ? unserialize($page_data['pg_jscript']) : array();
            $this->headers       = strlen($page_data['pg_headers']) ? unserialize($page_data['pg_headers']) : array();
            $this->extensions    = strlen($page_data['pg_extensions']) ? unserialize($page_data['pg_extensions']) : array();
            $this->isIndexed     = (bool)$page_data['pg_indexed'];
            $this->priority      = $page_data['pg_priority'];
            $this->lastmodify    = $page_data['pg_lastmod'];
            $this->checksum      = $page_data['pg_checksum'];
            $this->isCacheable   = (bool)$page_data['pg_cacheable'];
            $this->isReCaching   = (bool)$page_data['pg_cached'];
            $this->isHidden      = (bool)$page_data['pg_hidden'];
            $this->languageId    = $page_data['pg_lang'];
            $this->initLangs($page_data['pg_lang']);
            $this->address       = $this->parseAddress($page_data['pg_address']);
            $this->metas         = $this->parseMetas();
            $this->options       = strlen($page_data['pg_options']) ? unserialize($page_data['pg_options']) : array(); /* added 18.11.2014, italiano */
            $this->screen        = $page_data['pg_screen']; /* added 08.01.2015, italiano */
            $this->loadLocalStrings();
        } else {
            Error::logError('class Page conctructor', 'Site '.VBox::get('ConstData')->getConst('cachedDomain').': Page with id "'.$pg_id.'" does not exist. Request URL '.$_SERVER['REQUEST_URI']);
            Error::mailUrgent('class Page.constructor', 'Site '.VBox::get('ConstData')->getConst('cachedDomain').': Trying to create class with bad page id. Id is "'.$pg_id.'". Request URL '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        }
    }

    // destuctor
    public function __destruct() {
        $this->id                     = NULL;
        $this->parentPageId           = NULL;
        $this->address                = NULL;
        $this->title                  = NULL;
        $this->menuTitle              = NULL;
        $this->options                = NULL;/* added 18.11.2014, italiano */
        $this->screen                 = NULL;/* added 08.01.2015, italiano */
        $this->language               = NULL;
        $this->languageId             = NULL;
        $this->relativePages          = NULL;
        $this->css                    = NULL;
        $this->js                     = NULL;
        $this->headers                = NULL;
        $this->extentions             = NULL;
        $this->isIndexed              = NULL;
        $this->priority               = NULL;
        $this->lastmodify             = NULL;
        $this->isCacheable            = NULL;
        $this->isReCaching            = NULL;
        $this->isHidden               = NULL;
        $this->metas                  = NULL;
        $this->alLangs                = NULL;
        $this->siteDb                 = NULL;
    }

    public function getPageId() {
        return $this->id;
    }
    
    public function getPageUrl() {
        return $this->address['uri_address'];
    }

    // method returns the list of identical pages on other languages
    // if some language is blocked - it page would not appear in result array
    public function getRelativePageIds() {
        $tarr = array();
        foreach($this->relativePages as $key => $val) {
            /*if($this->alLangs[$key]['blocked']) {
                continue;
            }*/
            $tarr[$key] = $val;
        }

        return $tarr;
    }
/*
		garbagecat76
		29/12/2010
*/
    public function getRelativePageAddresses() {
        $addresses = array();
		$relativePages = implode(',',array_values($this->getRelativePageIds()));
		if ($relativePages){
			$q = "SELECT pg_address, pg_lang
				  FROM ".$this->siteDb."pages
				  WHERE pg_id in (".$relativePages.") AND pg_hidden = 0";
			DB::executeQuery($q, 'relpageaddr');			
        	$rows = DB::fetchResults('relpageaddr');
        	foreach($rows AS $row){
	           $addresses[$row['pg_lang']] = $row['pg_address'];
        	}
		}
        $addresses[$this->languageId] = $this->address['uri_address'];
        ksort($addresses);
        return $addresses;
    }

    public function getRelativePageAddresses_bkp() {

        $addresses = array();
		if ($this->relativePages)
		{
			foreach($this->relativePages as  $key => $val) {
				$q = 'SELECT pg_address, pg_lang
					  FROM '.$this->siteDb.'pages
					  WHERE pg_id = ? AND pg_hidden = 0';
				DB::executeQuery($q, 'relpage_addr', array($val));
				$res = DB::fetchRow('relpage_addr');
				if(!empty($res)) {
					$addresses[$res['pg_lang']] = $res['pg_address'];
				}
			}
		}
        $addresses[$this->languageId] = $this->address['uri_address'];
        ksort($addresses);
        return $addresses;
    }

    // method for adding style sheet to the list of css' for this page
    // in: $cssName - name of css file with relative path (if needed)
    public function addCss($cssName) {

        $cssName = trim($cssName);
        if(strlen($cssName) && !in_array($cssName,$this->css)) {
            array_push($this->css, $cssName);
            return TRUE;
        }
        return FALSE;
    }

    // method returns the list of css' for this page
    public function getCssList() {
        return $this->css;
    }

    // method for adding JavaScript to the list of js' for this page
    // in: $jsName - name of JavaScript file with relative path (if needed)
    public function addJS($jsName) {
        $jsName = trim($jsName);
        if(strlen($jsName) && !in_array($jsName,$this->js)) {
            array_push($this->js, $jsName);
            return TRUE;
        }
        return FALSE;
    }

    // method returns the list of js' for this page
    public function getJSList() {
        return $this->js;
    }

    // method for adding of header to the list of headers for this page
    // in: $headerBody - header to be added
    public function addHeader($headerBody) {
        $headerBody = trim($headerBody);
        if(strlen($headerBody)) {
            // checking for "Location" header - it must be the last one
            $hasLocation = FALSE;
            if(substr_count($this->headers[$tsize],'Location:')) {
                $hasLocation = TRUE;
            }
            if(substr_count($headerBody,'Location:')) {
                if($hasLocation) {
                    Error::logError('Page.addHeader error','Error while adding header - "Location" header already exist', __FILE__, __LINE__);
                    return FALSE;
                }
                array_push($this->headers,$headerBody);
                return TRUE;
            }
            // checking for "HTTP" header (t.e. "HTTP/1.0 404 Not Found") - it must be the first one
            $hasHTTP = FALSE;
            if(substr_count($this->headers[$tsize],'HTTP')) {
                $hasHTTP = TRUE;
            }
            if(substr_count($headerBody,'HTTP')) {
                if($hasHTTP) {
                    Error::logError('Page.addHeader error','Error while adding header - "HTTP" header already exist', __FILE__, __LINE__);
                    return FALSE;
                }
                $this->headers = array_merge((array)$headerBody,$this->headers);
                return TRUE;
            }
            // cheking for identical headers
            if(array_search($headerBody, $this->headers[$i]) !== FALSE) {
                Error::logError('Page.addHeader error','Error while adding header - header "'.$headerBody.'" already exist', __FILE__, __LINE__);
                return FALSE;
            }
            $tsize = sizeof($this->headers);
            if($hasLocation) {
                $this->headers = array_merge(array_slice($this->headers,0,$tsize-1),(array)$headerBody,(array)$this->headers[$tsize]);
            }
            else {
                array_push($this->headers,$headerBody);
            }
            return TRUE;
        }
    }

    // method returns the list of headers for this page
    public function getHeaders() {
        return $this->headers;
    }

    // method drops header from the list
    // in: $pos - position of header in the list
    public function dropHeader($pos) {
        if($pos == 0) {
            $this->headers = array_slice($this->headers,1);
            return TRUE;
        }
        $tsize = sizeof($this->headers);
        if(($pos > 0) && ($pos < $tsize)) {
            $tarray = array();
            for($i=0; $i<$tsize; $i++) {
                if($i == $pos) {
                    continue;
                }
                $tarray[] = $this->headers[$i];
            }
            $this->headers = $tarray;
            return TRUE;
        }
        return FALSE;
    }

    // method drops all headers of the page
    public function dropAllHeaders() {
        $this->headers = array();
        return TRUE;
    }

    // method for adding and droping cookies
    // in: $varName - name of the cookie; $varVal - value of the cookie
    // optional in: $expire - expiration date (unix timestamp); $path - path on the server in which the cookie will be available on
    // optional in: $domain - domain that the cookie is available; $secure - only HTTPS
    public function addCookie($varName, $varVal, $expire=0, $path='', $domain='', $secure=FALSE) {
        if(empty($varName)) {
            return FALSE;
        }
        $expire = intval($expire);
        $hcookie = 'Set-Cookie: '.$varName.'='.$varVal;
        $hcookie .= '; EXPIRES='.gmdate("D, d M Y H:i:s",$expire).' GMT';
        if(!empty($domain)) {
            $hcookie .= '; DOMAIN='.$domain;
        }
        if(!empty($path)) {
            $hcookie .= '; PATH='.$path;
        }
        if($secure) {
            $hcookie .= '; SECURE';
        }
        if(substr($hcookie,strlen($hcookie)-1,1) != ';') {
            $hcookie .= ';';
        }
        $this->addHeader($hcookie);
        return TRUE;
    }

    // method returns array of metas
    public function getMetas() {
        return $this->metas;
    }

    // method returns array of extensions
    public function getExtensions() {
        return $this->extensions;
    }

    // method checks if the page is cached or not
    public function checkCacheable() {
        return $this->isCacheable;
    }

    // method checks if the page is instantly recached or not
    public function checkReCaching() {
        return $this->isReCaching;
    }

    // method checks if the page is hidden or not
    public function checkHidden() {
        return $this->isHidden;
    }

    public function getLocalizedString($textNick) {
        if(!empty($textNick)) {
            $q = 'SELECT ll_text
                  FROM lstrings
                  LEFT JOIN '.$this->language.' ON ll_nick_id = ls_id
                  WHERE ls_nick LIKE ?';
//                  WHERE ls_nick = ? AND ls_site = ?';				  

//            DB::executeQuery($q, 'getLocStr', array($textNick, VBox::get('ConstData')->getConst('siteId')));
            DB::executeQuery($q, 'getLocStr', array($textNick));			
            $res = DB::fetchOne('getLocStr');
            
            if ($res)
            {
                return $res;
            }
            else{
                return false;
            }

        }
        return false;
    }


    // method parses uri part of address into the array
    protected function parseAddress($uri) {

        $addr = array();
        $addr['domain_address'] = $addr['base_address'] = 'http://'.$_SERVER['HTTP_HOST'];
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

    // method parses all available languages
    // if current language is disabled, then this page will be hidden
    // in: $langId - language Id of current page
    protected function initLangs($langId) {

        $q = 'SELECT l_id, l_code, l_addrcode, l_blocked
              FROM '.VBox::get('ConstData')->getConst('langsDb').'.languages';
        DB::executeQuery($q, 'langs');
        $rows = DB::fetchResults('langs');
        if(!empty($rows)) {
            for($i=0; $i<sizeof($rows); $i++) {
                $this->alLangs[$rows[$i]['l_code']] = array('id' => $rows[$i]['l_id'], 'prefix' => $rows[$i]['l_addrcode'], 'blocked' => (bool)$rows[$i]['l_blocked']);
                if($langId == $rows[$i]['l_id']) {
                    $this->language = $rows[$i]['l_code'];
                    if($rows[$i]['l_blocked'] == '1') {
                        $this->isHidden = TRUE;
                    }
                }
            }
        }

    }

    // method returns the array of metas for this page
    protected function parseMetas() {

        $q = 'SELECT mt_name, mt_content, mt_lang
                FROM '.$this->siteDb.'metas
              WHERE mt_page_id = ?
                OR mt_page_id = 0';
        DB::executeQuery($q, 'meta_data', array($this->id));
        $rows = DB::fetchResults('meta_data');
        $res = array();
        $tsize = sizeof($rows);
        if($tsize) {
            for($i=0; $i<$tsize; $i++) {
                $res[$i] = array('name' => $rows[$i]['mt_name'], 'description' => $rows[$i]['mt_content'], 'lang' => $rows[$i]['mt_lang']);
            }
        }
        return $res;

    }

    //
    private function loadLocalStrings() {
    	if(VBox::get('ConstData')->getConst('siteId') == 1115){
    		$q = "
				SELECT ln.ls_id,ln.ls_nick,ls.ls_text FROM `local_nicks` AS ln
				LEFT JOIN `local_strings` AS `ls` ON ln.ls_id = ls.ls_id
				WHERE ls.ls_lang = ? ";
        	DB::executeQuery($q, 'lstrings', array($this->languageId));
        	$rows = DB::fetchResults('lstrings');

        	$this->lStrings = array();
        	foreach($rows AS $row) {
	            $this->lStrings[$row['ls_nick']] = $row['ls_text'];
        	}
    	}else{
/*	        $q = 'SELECT ls_nick,
   	                  ll_text
   	           FROM '.VBox::get('ConstData')->getConst('langsDb').'.lstrings AS ls
   	           LEFT JOIN '.VBox::get('ConstData')->getConst('langsDb').'.'.$this->language.' AS enls ON enls.ll_nick_id = ls.ls_id
   	           WHERE ls_site = ?';*/
				$q = 'SELECT ls_nick,
   	                  ll_text
   	           FROM lstrings AS ls
   	           LEFT JOIN '.$this->language.' AS enls ON enls.ll_nick_id = ls.ls_id';
//   	           WHERE ls_site = ?';

//   	     	DB::executeQuery($q, 'lstrings', array(VBox::get('ConstData')->getConst('siteId')));
   	     	DB::executeQuery($q, 'lstrings');
   	     	$rows = DB::fetchResults('lstrings');

        	$this->lStrings = array();
        	foreach($rows AS $row) {
            	$this->lStrings[$row['ls_nick']] = $row['ll_text'];
        	}
    	}
	}
/*
    public function getLocalStrings() {
        return $this->lStrings;
    }
*/
    public function getLocalStrings($nick=null) {
        
        if (isset($nick))
        {
            return isset($this->lStrings[$nick]) ? $this->lStrings[$nick] : '';
        }
        
        return $this->lStrings;
    }
    public function getSiteDbName() {
        return $this->siteDb;
    }
	
	
	
	/**
		 * Registers a piece of javascript code.
		 * @param string $id ID that uniquely identifies this piece of JavaScript code
		 * @param string $script the javascript code
		 * @param integer $position the position of the JavaScript code. Valid values include the following:
		 * <ul>
		 * <li>self::POS_HEAD : the script is inserted in the head section right before the title element.</li>
		 * <li>self::POS_BEGIN : the script is inserted at the beginning of the body section.</li>
		 * <li>self::POS_END : the script is inserted at the end of the body section.</li>
		 * <li>self::POS_LOAD : the script is inserted in the window.onload() function.</li>
		 * <li>self::POS_READY : the script is inserted in the jQuery's ready function.</li>
		 * </ul>
		 * @param array $htmlOptions additional HTML attributes
		 * Note: HTML attributes are not allowed for script positions "CClientScript::POS_LOAD" and "CClientScript::POS_READY".
		 * @return CClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
		 */
	
	public function addScripts($params){
		
		$script = isset($params['script']) ? $params['script'] : '';
		$position = isset($params['position']) ? $params['position'] : self::POS_HEAD;
		
		$this->scripts[$position][] = $script;

		
	}

	public function getScripts($params){
		
		
		$html = $output = '';
		
		$position = isset($params['position']) ? $params['position'] : self::POS_HEAD;

		if(isset($this->scripts[$position]))
            $html.=$this->renderScriptBatch($this->scripts[$position]);


		if($html!==''){
			$count=0;
			$output=preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is','<###head###>$1',$output,1,$count);
			if($count)
				$output=str_replace('<###head###>',$html,$output);
			else
				$output=$html.$output;
		}

		return $output;
	}

	protected function renderScriptBatch(array $scripts){
		$html = '';
		$scriptBatches = array();
		foreach($scripts as $scriptValue){
			if(is_array($scriptValue))
			{
				$scriptContent = $scriptValue['content'];
				unset($scriptValue['content']);
				$scriptHtmlOptions = $scriptValue;
			}
			else
			{
				$scriptContent = $scriptValue;
				$scriptHtmlOptions = array();
			}
			$key=serialize(ksort($scriptHtmlOptions));
			$scriptBatches[$key]['htmlOptions']=$scriptHtmlOptions;
			$scriptBatches[$key]['scripts'][]=$scriptContent;
		}
		foreach($scriptBatches as $scriptBatch)
			if(!empty($scriptBatch['scripts']))
				$html.=implode("\n",$scriptBatch['scripts'])."\n";
		return $html;
	}
    
    /**
     * Function create url with language prefix, exp: /de/url.html
     * 
     */
    public function createUrl($args=array('url'=>null)) 
    {
        $url = trim($args['url']);
        
        if (isset($url) && !empty($url) )
        {
            if ($this->language != 'en' && strpos($url,'http') === false){
                $url = "{$this->address['prefix']}{$url}";  
            }
        }
        
        return $url;
	}
	
}

?>
