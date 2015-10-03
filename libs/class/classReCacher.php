<?php

class ReCacher {
    private $_constData;
    private $dbName;
    private $cachePath;
    private $localPath;
    private $isValidFS;
    private $logMode;
    private $cacheLog;
	private $cachecount;
	private $loginPage;

    public function __construct($localPath, $cacheDir, $cachecount=5) {
	$this->dbName = '';
	$this->isValidFS = FALSE;
	$this->localPath = '';
	$this->cachePath = '';
	$this->cacheLog['error'] = array();
	$this->cacheLog['event'] = array();
	$this->setLogMode(1);
	$this->cachecount = $cachecount;
//	date_default_timezone_set("UTC");
	if(VBox::isExist('ConstData')) {
	    $this->_constData = VBox::get('ConstData');
	} else {
	    $this->_constData = new ConstData();
	    VBox::set('ConstData', $this->_constData);
        }
	$this->loginPage = VBox::get('ConstData')->getConst('loginPage');	
//	$this->cachecount = $this->_constData->getConst('cachecount');
	if(!file_exists($localPath.$cacheDir.'/'))
		mkdir($localPath.$cacheDir.'/', 0775, TRUE);
	if(file_exists($localPath.$cacheDir.'/')) {
	    $this->localPath = $localPath;
	    $this->cachePath = $localPath.$cacheDir.'/';
	    $this->isValidFS = TRUE;
	}
    }

    public function __destruct() {
	$this->flushLog();
	$this->_constData = NULL;
	$this->dbName = NULL;
	$this->cachePath = NULL;
	$this->localPath = NULL;
	$this->isValidFS = NULL;
	$this->logMode = NULL;
	$this->cacheLog = NULL;
    }

    public function setDBName($dbName) {
	if(!empty($dbName)) {
	    $this->dbName = $dbName.'.';
	}
    }

    /*
     0 - not mailing at all
     1 - only errors mailed
     2 - full report
    */
    public function setLogMode($mode) {
		if(in_array($mode,array(0,1,2))) {
	    	$this->logMode = $mode;
		}
    }

// !!!!!!!!!!!!!!!!!!!!!!!
    public function rebuildCache($pageId) {

	if(is_object($this->_constData) && $this->isValidFS) {

	    ob_start();
	    $agregator = new Agregator($pageId);
            $agregator->process();
	    $content = ob_get_contents();
	    ob_clean();
	    file_put_contents($pageId.'txt', $content);

   //     if (VBox::isExist('Page'))
//			$page = VBox::get('Page');
//		else
//			{
				$page = new PageReCacher($pageId);
//				VBox::set('Page', $page);
//			}
	    if(!empty($content)) {
			if(!file_exists($this->cachePath.$pageId)) {
			    if(file_put_contents($this->cachePath.$pageId, $content) === FALSE) {
					$this->cacheLog['error'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): unable to create file at path "'.$this->cachePath.$pageId.'"';
		    	}
		    	else {
					chmod($this->cachePath.$pageId, 0664);
					$hash = md5($content);
					$page->refreshLastmodify($hash);
					$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): succesfuly modified at '.date('m-d-Y H:i:s');
		    	}
			}
			elseif(is_writable($this->cachePath.$pageId)) {
		    	$hash = md5($content);
		    	if($hash != md5_file($this->cachePath.$pageId)) {
					file_put_contents($this->cachePath.$pageId,$content);
					$page->refreshLastmodify($hash);
					$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): succesfuly modified at '.date('m-d-Y H:i:s');
		    	}
		    	else {
					$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): living untouched at '.date('m-d-Y H:i:s');
		    	}
			}
			else {
		    	$this->cacheLog['error'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): unable to write to file at path "'.$this->cachePath.$pageId.'"';
			}
	    }
	    else {
			$this->cacheLog['error'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): reseive empty content while rebuilding page.';
	    }
	    unset($page, $pageHandler);
	    return !(bool)sizeof($this->cacheLog['error']);
	}
	return FALSE;
    }
// !!!!!!!!!!!!!!!!!!!!!!!
    public function rebuildCachePageAdress($pageId) {
	if ($this->loginPage==$pageId) return true;
	if(is_object($this->_constData) && $this->isValidFS) {
		$page = new PageReCacher($pageId);
		if (!$page->checkHidden())
		{
			ob_start();
		    $agregator = new Agregator($pageId);
            $agregator->process();
		    $content = ob_get_contents();
		    ob_clean();
/*	        if (VBox::isExist('Page'))
				$page = VBox::get('Page');
			else
			{
				$page = new PageReCacher($pageId);
	//			VBox::set('Page', $page);
			}
	*/		
			if ($page->checkCacheable())
			{
				if(!empty($content))
				{
					$uri_address = $page->address['uri_address'];
					$path_parts = pathinfo($uri_address);
					$dirname = (isset($path_parts['dirname'])) ? $path_parts['dirname'] : '';
					$filename = (isset($path_parts['filename'])) ? $path_parts['filename'] : '';
					$extension = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';
					if(!substr_count($uri_address,'.html')) 
					{
						$dirname=$this->cachePath.'cache'.((substr($uri_address,0,1) != '/') ? '/':'').$uri_address.((substr($uri_address,-1) != '/') ? '/':'');
						$filename='index';
						$extension='.html';			
					}
					else
					{
						$dirname=$this->cachePath.'cache'.((substr($dirname,0,1) != '/') ? '/':'').$dirname.((substr($dirname,-1) != '/') ? '/':'');
						$filename.='.';
					}
				if (!file_exists($dirname))
				{	
					mkdir($dirname, 0775, true);
				}
				$cache_uri_address = $dirname.$filename.$extension;
			    if(file_put_contents($cache_uri_address, $content) === FALSE) {
					$this->cacheLog['error'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): unable to create file at path "'.$cache_uri_address.'"';
		    	}
		    	else
				{
					chmod($cache_uri_address, 0664);
					$hash = md5($content);
					if($hash != $page->checksum)
					{
						$page->refreshLastmodify($hash);
						$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): succesfuly modified at '.date('d/m/Y H:i:s');
					}
		    	}
	    }
	    else {
			$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): reseive empty content while rebuilding page.';
		    }
		}
		else
			$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): is not cacheble.';
		}
		else
			$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): is hiden.';

		unset($page, $pageHandler);
//		return $this->flushLog();
	    return !(bool)sizeof($this->cacheLog['error']);
	}
	return FALSE;
    }
// !!!!!!!!!!!!!!!!!!!!!!!
    public function clearCachePage($pageId) {
	if(is_object($this->_constData) && $this->isValidFS)
	{
//	        if (VBox::isExist('Page'))
//				$page = VBox::get('Page');
//			else
//			{
				$page = new PageReCacher($pageId);
//				VBox::set('Page', $page);
//			}
			$uri_address = $page->address['uri_address'];
			$path_parts = pathinfo($uri_address);
			$dirname = (isset($path_parts['dirname'])) ? $path_parts['dirname'] : '';
			$filename = (isset($path_parts['filename'])) ? $path_parts['filename'] : '';
			$extension = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';
			if(!substr_count($uri_address,'.html')) 
			{
			 $dirname=$this->cachePath.'cache'.((substr($uri_address,0,1) != '/') ? '/':'').$uri_address.((substr($uri_address,-1) != '/') ? '/':'');
			 $filename='index';
			 $extension='.html';			
			}
			else
			{
				$dirname=$this->cachePath.'cache'.((substr($dirname,0,1) != '/') ? '/':'').$dirname.((substr($dirname,-1) != '/') ? '/':'');
				$filename.='.';
			}
			$cache_uri_address = $dirname.$filename.$extension;
			if (file_exists($cache_uri_address))
			{	
				if (unlink($cache_uri_address)=== FALSE)
					$this->cacheLog['error'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): unable to clear file at path "'.$cache_uri_address.'"';
		    	else {
						$page->refreshLastmodify('');
						$this->cacheLog['event'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): succesfuly clear cache at '.date('d/m/Y H:i:s');
				}
			}
	   		else {
			$this->cacheLog['error'][] = 'Page "'.$page->address['uri_address'].'" ('.$pageId.'): cache file not exists "'.$cache_uri_address.'"';
	    }
	    unset($page, $pageHandler);
	    return !(bool)sizeof($this->cacheLog['error']);
	}
	return FALSE;
    }

//------------------------
    private function flushLog() {
	$mailtext = ''; $urgent = FALSE;
	if($this->logMode == 1 || $this->logMode == 2) {
	    if(sizeof($this->cacheLog['error'])) {
		$urgent = TRUE;
		$mailtext .= 'Errors section (at '.date('m-d-Y H:i:s').')'."\n\n";
		$mailtext .= implode("\n",$this->cacheLog['error']);
		$mailtext .= "\n".'-----------------------------------------'."\n";
	    }
	}
	if($this->logMode == 2) {
	    if(sizeof($this->cacheLog['event'])) {
		$mailtext .= 'Events section (at '.date('d/m/Y H:i:s').')'."\n\n";
		$mailtext .= implode("\n",$this->cacheLog['event']);
	    }
	}
	if(!empty($mailtext)) {
	    $mailtext = 'ReCacher results for site "'.$this->_constData->getConst('cachedDomain').'".'."\n\n".$mailtext;
	    Error::mailResults('ReCacher results', $mailtext, $urgent);
		echo $mailtext;
	}
	$this->cacheLog['error'] = array();
	$this->cacheLog['event'] = array();
    }
//---------------------
public function _rebuildAllCache() 
{	
	$this->renameCacheDir();
	$sql = 'select pg_id from pages where pg_cacheable=1 and pg_hidden=0';
	DB::executeQuery($sql, 'all_pages_data');
        $results = DB::fetchResults('all_pages_data');
        if(!empty($results)) {
            foreach($results as $row) {
                $this->rebuildCachePageAdress($row['pg_id']);
            }
        }
	//$this->flushLog();	
}
//----
private function renameCacheDir()
{
	$objs = glob($this->cachePath."cache*",GLOB_ONLYDIR);

	if ($objs) { 
        foreach($objs as $obj) { 
            if (is_dir($obj)) $dir_array[] = $obj; 
        } 
		array_multisort($dir_array,SORT_DESC, SORT_STRING);
        foreach($dir_array as $obj) { 
			$num = (int) substr($obj,-2);
			if ($num >= $this->cachecount)
				$this->removeDirRec($obj, true);	
			else
			{
				$newname = ($num==0) ? $obj : substr($obj,0,-2);
				$newname =$newname.sprintf("%02d", $num+1);
				//rename($obj, $newname);
				$this->copyDirRec($obj, $newname,true);
			}
		} 
	 if (!file_exists($this->cachePath."cache"))
		{
			mkdir($this->cachePath."cache", 0775, true);
		}
	/*if (file_exists($this->cachePath."cache"))
	 	$this->removeDirRec($this->cachePath."cache");
	 else
		{
			mkdir($this->cachePath."cache", 0775, true);
		}
	*/	
    }
	else
	{
		mkdir($this->cachePath."cache", 0775, true);
	}
}
//-------------------
private function removeDirRec($dir, $self=false) 
{ 
    if ($objs = glob($dir."/*")) { 
        foreach($objs as $obj) { 
            is_dir($obj) ? $this->removeDirRec($obj,$self) : unlink($obj); 
        } 
    } 
	if ($self && is_dir($dir)) {
		rmdir($dir);
	}
}
//-------------------
private function copyDirRec($from_path, $to_path, $forced = false) 
{ 
    if (!file_exists($to_path))
	 	mkdir($to_path, 0775); 
	if ($objs = glob($from_path."/*")) { 
        foreach($objs as $obj) { 
			$file = str_replace($from_path,'',$obj);
          if (is_dir($obj))
			  {
			  	$this->copyDirRec($obj, $to_path.$file);
			  }
		  if (is_file($obj)) 
		  	{
				if(is_file($to_path.$file))
    	        	$ow = filemtime($obj) - filemtime($to_path.$file);
				else $ow = 1;
	          // если надо обновлять
    	      if($ow > 0 || $forced) {
				copy($obj, $to_path.$file); 
				touch($to_path.$file, filemtime($obj));
				chmod($to_path.$file, 0664);
			  }
			}
        } 
    } 
}
}
?>