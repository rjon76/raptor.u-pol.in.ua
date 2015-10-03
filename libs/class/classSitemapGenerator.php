<?php
//а
class SitemapGenerator {

    private $isValid;
    private $smFile;
    private $smIndexFile;
    private $Host;
    private $dbName;
    private $localPath;
    private $isGziped;
    private $sitemap;
    private $alLangs;


    public function __construct($langDb) {
	$this->isValid = FALSE;
	$this->smFile = 'sitemap.xml';
	$this->smIndexFile = 'sitemap_index.xml';
	$this->dbName = '';
	$this->alLangs = array();
	$q = 'SELECT l_id, l_addrcode, l_blocked
                FROM '.$langDb.'.languages';
        DB::executeQuery($q, 'langs');
        $rows = DB::fetchResults('langs');
        if(!empty($rows)) {
            for($i=0; $i<sizeof($rows); $i++) {
                $this->alLangs[$rows[$i]['l_code']] = array('id' => $rows[$i]['l_id'],
		    'prefix' => (!empty($rows[$i]['l_addrcode']) ? '/'.$rows[$i]['l_addrcode'] : ''),
		    'blocked' => (bool)$rows[$i]['l_blocked']);
            }
        }
    }

    public function __destruct() {
	$this->isValid = NULL;
	$this->smFile = NULL;
	$this->smIndexFile = NULL;
	$this->Host = NULL;
    	$this->dbName = NULL;
    	$this->localPath = NULL;
	$this->isGziped = NULL;
	$this->sitemap = NULL;
	$this->alLangs = NULL;
    }

    public function setParams($params) {
	$this->isValid = TRUE;
	if(empty($params['s_hostname']) || empty($params['s_dbname']) || empty($params['s_path'])) {
	    $this->isValid = FALSE;
	}
	$this->Host = trim($params['s_hostname'],'/');
	$this->dbName = $params['s_dbname'].'.';
	$this->localPath = $params['s_path'];
	$this->isGziped = (bool)$params['s_gziped'];
	$this->sitemap = '';
	if(!file_exists($this->localPath)) {
	    $this->isValid = FALSE;
	}
    }

    public function buildSitemap() {
	if($this->isValid) {
	    $q = 'SELECT pg_address, pg_lang, pg_priority, UNIX_TIMESTAMP(pg_lastmodify) AS pg_lastmod
		    FROM '.$this->dbName.'pages
		  WHERE pg_indexed = 1 AND pg_hidden = 0';
	    DB::executeQuery($q,'pages');
	    $rows = DB::fetchResults('pages');
	    if(!empty($rows)) {
		$tsize = sizeof($rows);
		for($i=0; $i<$tsize; $i++) {
		    if(!$this->alLangs[$rows[$i]['pg_lang']]['blocked']) {
			$this->sitemap .= '
    <url>
	<loc>http://'.$this->Host.$this->alLangs[$rows[$i]['pg_lang']]['prefix'].$rows[$i]['pg_address'].'</loc>
	<lastmod>'.date('Y-m-d\Th:i:s-04:00',$rows[$i]['pg_lastmod']).'</lastmod>
	<changefreq>weekly</changefreq>
	<priority>'.(!empty($rows[$i]['pg_priority']) ? $rows[$i]['pg_priority'] : '0.3').'</priority>
    </url>';
		    }
		}
		if(!empty($this->sitemap)) {
		    $this->sitemap = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'.$this->sitemap.'
</urlset>';
		}
		return TRUE;
	    }
	    return FALSE;
	}
	return FALSE;
    }

    public function writeSitemap() {
	if($this->isValid) {
	    if(!$this->isGziped) {
		if(file_put_contents($this->localPath.$this->smFile,$this->sitemap) === FALSE) {
		    Error::mailResults('Sitemap generator error', 'Unable to write to file "'.$this->localPath.$this->smFile.'"', TRUE);
		    $this->isValid = FALSE;
		}
	    }
	    elseif($this->compressSitemap()) {
		$content = '<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84">
    <sitemap>
	<loc>'.$this->localPath.$this->smFile.'</loc>
	<lastmod>'.date('Y-m-d\Th:i:s-04:00').'</lastmod>
    </sitemap>
</sitemapindex>';
		if(file_put_contents($this->localPath.$this->smIndexFile,$content) === FALSE) {
		    Error::mailResults('Sitemap generator error', 'Unable to write to file "'.$this->localPath.$this->smIndexFile.'"', TRUE);
		    $this->isValid = FALSE;
		}
		return;
	    }
	}
	$this->isValid = FALSE;
    }

    public function submitSitemap() {
	if($this->isValid) {
	    
	    $service_port = getservbyname('www', 'tcp');
	    $address = gethostbyname('www.google.com');
	    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	    if ($socket < 0) {
		Error::mailResults('Sitemap generator error','socket_create() failed: reason: '.socket_strerror($socket), TRUE);
	    }
	    $result = socket_connect($socket, $address, $service_port);
	    if ($result < 0) {
		Error::mailResults('Sitemap generator error','socket_connect() failed: reason: '.socket_strerror($socket), TRUE);
	    }
	    $sm_url = 'http://'.$this->Host.'/'.($this->isGziped ? $this->smFile.'.gz' : $this->smFile);
	    $in = 'GET /webmasters/sitemaps/ping?sitemap='.(urlencode($sm_url))." HTTP/1.1\r\n";
	    $in .= "Host: www.google.com\r\n";
	    $in .= "Connection: Close\r\n\r\n";
	    $out = $res = '';
	    socket_write($socket, $in, strlen($in));
	    while ($out = socket_read($socket, 2048)) {
		$res .= $out;
	    }
	    socket_close($socket);
	    Error::logWarning('Sitemap submit result for site "'.$this->Host.'"',$res);
	}
	$this->isValid = FALSE;
    }

    private function compressSitemap() {
	if($this->isValid && $this->isGziped) {
	    if($gzp = gzopen($this->localPath.$this->smFile.'.gz','wb')) {
		gzwrite($gzp, $sm_map);
		gzclose($gzp);
		return TRUE;
	    }
	    Error::mailResults('Sitemap generator error', 'Unable to write to file "'.$this->localPath.$this->smFile.'.gz"', TRUE);
	    return FALSE;
	}
	return FALSE;
    }

}

?>