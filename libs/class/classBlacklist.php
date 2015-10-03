<?php
class Blacklist {
public $blackip=array('86.47.251.189','192.168.0.62');
public $blackemail=array('viagra@email.tst','sample@email.tst');

	public function checkIp($ip = NULL)
	{
		if ($ip == NULL){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (array_search(trim($ip), $this->blackip)){
			return true;
		}
		return false;
	}

	public function checkEmail($email)
	{
		if (array_search(trim($email), $this->blackemail)){	
			return true;
		}
		return false;
	}
}
?>