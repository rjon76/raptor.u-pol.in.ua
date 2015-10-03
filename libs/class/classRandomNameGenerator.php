<?php

class randomNameGenerator
{
	private $secret_key;

	public function __construct($secret_key='pa55w0rd'){
		$this->secret_key = $secret_key;
	}

	public function encrypt($str)
	{
		return rawurlencode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->secret_key, $str, MCRYPT_MODE_ECB)); 
	}
	
	public function decrypt($str)
	{
		return mcrypt_decrypt(MCRYPT_BLOWFISH, $this->secret_key, rawurldecode($str), MCRYPT_MODE_ECB); 
	}

/*----------------------------
		get original file_name content for random file name
/-----------------------------------------*/
	public function get_file($file_name, $dir='')
	{
		$original = $dir.$file_name;		
		
		if ($original && file_exists($original)){
			header("Content-Disposition: attachment; filename=" . urlencode($file_name));
			header("Content-Length: ".filesize($original));
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");
			readfile($original);
		}
		else{
			header("HTTP/1.x 404 Not Found");
		}
	}

/*-------------------------------
Function support download manager
*/

public function download_file($file_name, $dir='')
{
	date_default_timezone_set('UTC');
	$original = $dir.$file_name;		


	if (file_exists($original))
	{
		$fsize = filesize($original); 
		$ftime = date('D, d M Y H:i:s T', filemtime($original)); 

		$fd = @fopen($original, 'rb'); 

		if (isset($_SERVER['HTTP_RANGE']))
		{ 
			$range = $_SERVER['HTTP_RANGE']; 
			$range = str_replace('bytes=', '', $range);
			list($range, $end) = explode('-', $range);

			if (!empty($range)){
				fseek($fd, $range);
			}
		}
		else{ 
			$range = 0;
		}

		if ($range){
			header($_SERVER['SERVER_PROTOCOL'].' 206 Partial Content'); 
		}
		else{
			header($_SERVER['SERVER_PROTOCOL'].' 200 OK'); 
		}

		header("Content-Disposition: attachment; filename=" . urlencode($file_name));
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");
		header('Last-Modified: '.$ftime);
		header('Accept-Ranges: bytes');
		header('Content-Length: '.($fsize - $range));
		if ($range)
		{
			header("Content-Range: bytes $range-".($fsize - 1).'/'.$fsize);
		}

		fpassthru($fd); 
		fclose($fd);
		exit;
	}
	else{
		return false;
	}
}
}
?>