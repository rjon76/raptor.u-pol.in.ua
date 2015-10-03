<?php

/**
 *  Console component for tor
 *
 * @author garbagecat76 <garbagecat76@gmail.com>
 * @link http://www.Garbagecat.com/
 * @copyright Copyright &copy; 2000-2013 Garbagecat Software
 * @version 1.0.0.1 
 * @date 09-04-2014
 */ 
class Tor
{

	/**
	 * IP tor server
	 * Defaults to '127.0.0.1'.
	 */ 
	public $torIp = '127.0.0.1';
	 
	/**
	 * PORT tor server
	 * Defaults to '9050'.
	 */ 
	public $torPort = '9050';

	/**
	 * PORT tor server
	 * Defaults to '9050'.
	 */ 
	public $torControlPort = '9051';

	/**
	 * PORT tor server
	 * Defaults to '9050'.
	 */ 
	public $torTimeout = '30';
	
	/**
	 * PORT tor server
	 * Defaults to '9050'.
	 */ 
	public $torAuthcode = '';

	/**
	 * Proxy tipe tor server
	 * Defaults to 'socks5'.
	 */ 
	public $torProxyType = 'socks5';

	
	/**
	 *Swich new IP for torr
	 */ 
	public function swich_ip($debug = false)
    {
        $fp = fsockopen($this->torIp, $this->torControlPort, $errno, $errst, $this->torTimeout);
        if (!$fp){
	        if ($debug){
				echo 'fsockopen connect error'."\r\n";
				echo 'fsockopen errno'.$errno."\r\n";
				echo 'fsockopen errst'.$errst."\r\n";
			}
			return false;
		}
 		
		fputs($fp, 'AUTHENTICATE "'.$this->torAuthcode.'"'."\r\n");
        $response = fread($fp, 1024);
         list($code, $text) = explode(' ', $response, 2);
        if ($code != '250'){
	        if ($debug){
				echo 'authentication failed '.$text."\r\n";
			}

            return false;
		}

        fputs($fp, "signal NEWNYM\r\n");
        $response = fread($fp, 1024);
        list($code, $text) = explode(' ', $response, 2);
        if ($code != '250'){
	        if ($debug){
				echo 'signal failed '.$code."\r\n";
			}
            return false;
		}
        fclose($fp);
       // echo "@@4";
        return true;
    }

	public function get_ip()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.telize.com/ip");
		curl_setopt($ch, CURLOPT_PROXY, $this->torIp.':'.$this->torPort);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		if(strlen($error) > 0) {
			echo 'Http code'.$info['http_code']."\r\n";
			echo 'Errno'.$errno."\r\n";
			echo 'Error'.$error."\r\n";
			curl_close($ch);
			return false;	
		}
		curl_close($ch);
		return $result;	
	}
}

?>
