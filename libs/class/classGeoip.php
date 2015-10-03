<?php
/* ---------------------------------*/
/*  Get user location for IP        */
/*----------------------------------*/
class Geo {
	private $available_locales = array();
	private $IP;
	
    public function __construct() {
		$this->IP = $_SERVER["REMOTE_ADDR"];
		$en = array('AS','AU','BS','BH','BB','BW','IO','CM','CA','KY','DM','GB','US','UM','FJ','HK','IE','IN','JM','KE','LR','MT','NA','NZ','NG','PK','PH','PR','RW','WS','SC','SL','SG','SB','ZA','TZ','UG','ZM','ZW');
	$de = array('AT','BE','DE','LI','LU','CH');
	$fr = array('FR','MG','MC','SN');
//	$es = array('BO','CL','CO','CR','CU','DO','EC','SV','GT','HN','MX','NI','PA','PY','PE','ES','UY','UE');
//	$it = array('IT');
//	$ru = array('AM','AZ','BY','KZ','KG','MD','RU','TJ','TM','UA','UZ');
	$nl = array('NL','BE','LU');
	$jp = array('JP');
	$ch = array('CN');
	$in = array('IN');			
	$this->available_locales = array('EN'=>$en,'DE'=>$de,'FR'=>$fr,'ES'=>$es,'IT'=>$it,'RU'=>$ru,'NL'=>$nl);
	
    }
    public function __destruct() {
		 $this->IP = NULL;
		 $this->available_locales = NULL;
    }
	
    public function getCode()
	{
		$q="SELECT COUNTRY_CODE2, COUNTRY_NAME FROM ".VBox::get('ConstData')->getConst('langsDb').".countries WHERE IP_FROM<=inet_aton('".$this->IP."') AND IP_TO>=inet_aton('".$this->IP."')";
		DB::executeQuery($q, 'geo');
	    $rows = DB::fetchRow('geo');
		if (count($rows)==0)
		 return 'en';
		$user_country = $rows['COUNTRY_CODE2'];
		$user_lng = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
		$needed_locale = 'en';
		foreach ($this->available_locales as $lang => $toplng)
		{
			if (in_array($user_country, $toplng)) 
			{
			    $needed_locale = $lang;				
				break;
			}
			else
			{
			    if (in_array($user_lng, $toplng)) $needed_locale = $lang;
			}
		}
	return strtolower($needed_locale);
    }

	private function get_curl( $url,  $javascript_loop = 0, $timeout = 5 ){
		$cookie = tempnam ("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		$content = curl_exec( $ch );
		
		$response = curl_getinfo( $ch );
		curl_close ( $ch );
		
		if (in_array($response['http_code'], array(301, 302, 200)))
		{
			ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
			return $content;
		}
		else
			return false;
	} 
	
	public function getCode2(){
		$url = "http://freegeoip.net/json/".$this->IP;
		$needed_locale = 'en';
		if ($content = $this->get_curl($url)){
			$result = json_decode($content, true);
			$user_country = $result['country_code'];
			$user_lng = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));

			foreach ($this->available_locales as $lang => $toplng){
				if (in_array($user_country, $toplng)){
				    $needed_locale = $lang;				
					break;
				}elseif(in_array($user_lng, $toplng)){
					 $needed_locale = $lang;
					 break;
				}
			}
		}
		return strtolower($needed_locale);
	}
}

?>