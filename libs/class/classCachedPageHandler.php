<?php
class CachedPageHandler {

    private $prepared;
    private $page;
	private $cache_uri_address;

    public function __construct() {
	$this->page = VBox::get('Page');
	$this->prepared = FALSE;
	$uri_address = $this->page->address['uri_address'];
	if(!substr_count($uri_address,'.html')) 
			{
			 $dirname=LOCAL_PATH.'cache/cache'.$uri_address.((substr($uri_address,-1) != '/') ? '/':'');
			 $filename='index';
			 $extension='.html';			
			}
			else
			{
				$dirname=LOCAL_PATH.'cache/cache'.((substr($uri_address,0,1) != '/') ? '/':'').$uri_address;
				$filename='';
			 	$extension='';			
				
			}
	$this->cache_uri_address = $dirname.$filename.$extension;			
//	if(!file_exists(LOCAL_PATH.'cache/'.$this->page->getPageId())) {
	if(file_exists($this->cache_uri_address))
		{
			$this->prepared = true;
		}
	else
		{
		    Error::logError('CachedPageHandler - No cache','The cache file "'.$this->cache_uri_address.'" is missing.');
		}
    }

    public function __destruct() {
	$this->page = NULL;
	$this->prepared = NULL;
	$this->cache_uri_address = NULL;
    }

    public function printPage() 
	{
		if($this->prepared)
		{
		/*	$uri_address = $this->page->address['uri_address'];
			if(!substr_count($uri_address,'.html')) 
				{
				 $dirname=LOCAL_PATH.'cache/cache'.$uri_address.((substr($uri_address,-1) != '/') ? '/':'');
				 $filename='index';
				 $extension='.html';			
				}
				else
				{
					$dirname=LOCAL_PATH.'cache/cache'.$dirname.((substr($dirname,-1) != '/') ? '/':'');
					$filename.='.';
				}
			$cache_uri_address = $dirname.$filename.$extension;			
			*/
//	    $content = file_get_contents(LOCAL_PATH.'cache/'.$this->page->getPageId(), FALSE);
		    $content = file_get_contents($this->cache_uri_address, FALSE);
			if($content === FALSE)
			{
				Error::logError('CachedPageHandler - Bad permission','There is no permition to read cache content from file "'.$this->cache_uri_address.'".');
				return FALSE;
		    }

            foreach($this->page->getHeaders() AS $header)
			{
                header($header);
            }
		    print $content;
		    return TRUE;
		}
		return FALSE;
    }
}
?>