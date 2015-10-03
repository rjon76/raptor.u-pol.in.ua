<?php
include_once(ENGINE_PATH.'interface/interfaceLocalExt.php');

class redirect implements LocalExtInterface
{
 
// v:win|v:www.eltima.com/products/youtube-downloader/
    private $os;
	private $url = NULL;
	
    public function __construct($args)
    {
		if(!empty($args[0])) {
		    $this->os = $args[0];
		}

		if(!empty($args[1])) {
		    $this->url = $args[1];
			if (!preg_match('#^http(s)?://#', $this->url)) {
	    		$this->url = 'http://' . $this->url;
			}
		}

	}

	public function __destruct(){
		  $this->os = NULL;
	}

	public function parseSettings(){
		return TRUE;
	}


	private function validatePost()
	{
			return TRUE;
	}
		
	public function getResult()
	{
		if ($this->get_os() && $this->url ){
			header( "Location: ".$this->url );
		}
	}

	private function get_os()  
	{  
    	$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$user_os = NULL;
		$os = array (  
	        'win' => 'Win',  
    	    'unix'=>'OpenBSD',  
	        'sun'=>'SunOS',  
	        'linux'=>'(Linux)|(X11)',  
    	    'mac'=>'(Mac_PowerPC)|(Macintosh)',  
        	'qnx'=>'QNX',  
	        'beos'=>'BeOS',  
    	    'os/2'=>'OS/2'
	    );  
   
    	foreach($os as $key=>$value)  
	    {  
        	if (preg_match('#'.$value.'#i', $user_agent))  
				$user_os = $key;  
	    }

    	return (strtolower($this->os) == $user_os);  
	}
}


?>
