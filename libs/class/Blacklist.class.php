<?php
//	Blacklists
class Blacklist
{
	private function _ip() {
		  if(isset($HTTP_SERVER_VARS)) {
			if(isset($HTTP_SERVER_VARS[HTTP_X_FORWARDED_FOR])) {
			$realip = $HTTP_SERVER_VARS[HTTP_X_FORWARDED_FOR];
			}elseif(isset($HTTP_SERVER_VARS[HTTP_CLIENT_IP])) {
			  $realip = $HTTP_SERVER_VARS[HTTP_CLIENT_IP];
			}else{
			  $realip = $HTTP_SERVER_VARS[REMOTE_ADDR];
			}
		  }else{
		  if(getenv( HTTP_X_FORWARDED_FOR ) ) {
			$realip = getenv( HTTP_X_FORWARDED_FOR );
		  }elseif ( getenv( HTTP_CLIENT_IP ) ) {
			$realip = getenv( HTTP_CLIENT_IP );
		  }else {
			$realip = getenv( REMOTE_ADDR );
		  }
		}
		return $realip;
	}
	
	public function init($analyzeBoots = false) {
	
		if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'blacklists.txt')) {
		
			$userIp = $this->_ip();//$_SERVER['REMOTE_ADDR'];
			//var_dump($userIp);
			$blacklists = file_get_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'blacklists.txt');
			$ips = explode ("\n",$blacklists);
			//var_dump($ips);
			foreach($ips as $filter) {
				if( $filter===$userIp || (($pos=strpos($filter,'*'))!==false && !strncmp($userIp,$filter,$pos))) {
					header("HTTP/1.1 403 Forbidden");
					exit();
				}
			}
		}
        
        if ($analyzeBoots)
        {  
           	include_once('AnalyzeBoots.class.php');
        	$AnalyzeBoots = new AnalyzeBoots($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'boots.db');
            $AnalyzeBoots->init();
        }
	
	}
	
}
	
?>