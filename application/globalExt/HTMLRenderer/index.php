<?php
include_once(ENGINE_PATH.'class/classMenu.php');
//include_once(ENGINE_PATH.'class/classPage.php');
class HTMLRenderer {
	private $formData; 
	private $menu;
    public function __construct() {
        
    }
    public function __destruct() {
		 $this->menu = NULL;
		 $this->formData = NULL;
    }
	
    public function getTopMenuHTML($args) {
	   $this->menu = new Menu();	
	   $level = isset($args[1]) ? $args[1] : 1;
      return  $this->menu->getMenu($args[0],$level);
    }
	
	public function getPageId() {
		$result = 0;
		if (VBox::isExist('Page')) {
			$result = VBox::get('Page')->getPageId();
		}
        return $result;
    }
	
	public function mygetPageContent($args)
	{
		$reg_exp  = '#<body>(.*?)<\/body>#si';

		if (VBox::isExist('Page')) {
				$old_page = @VBox::get('Page');
		}
		$pageId = (int)$args[0];
//		var_dump($pageId);
		$page = new Page($pageId);
//		var_dump($page->checkHidden());
		if ($page && !$page->checkHidden())
		{
			$page->dropAllHeaders();
			ob_start();
		    $agregator = new Agregator($pageId);
            $agregator->process(true);
		    $content = ob_get_contents();
		    ob_clean();	
			if(!is_null($old_page))
			{
				VBox::set('Page', $old_page);
			}
			if (!isset($args[1]) && preg_match($reg_exp, $content, $matches))
				return $matches[1];
	
			return $content;
		}
	}
	public function getRss($args)
	{

		include_once(ENGINE_PATH.'class/classRss.php');
		$rss = new Trss;
		$count=(isset($args[1])) ? $args[1] : 1;
		//$rss->url = 'http://'.$args[0];
		$rss->url = 'http://'.str_replace('http://','',$args[0]);	
		$rss->reg_exp  ='#<item>.*?<title>(.*?)<\/title>.*?'; 
		$rss->reg_exp .='<link>(.*?)<\/link>.*?';
		$rss->reg_exp .='<pubDate>(.*?)<\/pubDate>.*?';
		$rss->reg_exp .='<description>(.*?)<\/description>.*?';
		$rss->reg_exp .='<\/item>#si';
		//var_dump($rss->reg_exp);

		$rss_data = $rss->parse_curl(array('count'=>'','title'=>'','link'=>'','pubDate'=>'','description'=>'')) ; 
		$count=min($count, count($rss_data['title']));
		$rss_result  =  $rss->output_rss_array(array('title'=>$rss_data['title'],'link'=>$rss_data['link'],'pubDate'=>$rss_data['pubDate'],'description'=>$rss_data['description']),$count); 
	//	var_dump($args, $rss_data);
		return $rss_result; 
	}
/*------------------------------*/
private function get_curl( $url,  $javascript_loop = 0, $timeout = 5 )
{
  
    $cookie = tempnam ("/tmp", "CURLCOOKIE");
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.17) Gecko/".date('Ymd')." Firefox/19.0.17" );

    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_ENCODING, "" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); 
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 ); 
    curl_setopt( $ch, CURLOPT_VERBOSE, 1 ); 
    curl_setopt( $ch, CURLOPT_HEADER, 0 ); 
	curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_REFERER, 'http://download.cnet.com');
	
	$content = curl_exec( $ch );
    $response = curl_getinfo( $ch );
	$curl_error = curl_error($ch);
    curl_close ( $ch );
	if ($debug){
		echo "<pre>";

		var_dump( $response);	
		var_dump( $curl_error );								
		echo "</pre>";		
	}

    if (in_array($response['http_code'], array(301, 302, 200)))
    {
	    ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.17) Gecko/".date('Ymd')." Firefox/19.0.17");
		return $content;
    }
	else
		return false;
} 

/*------------------------------*/
	private function get_fsockopen($url)
	{
		$page = '';	 
		$link =  parse_url($url);

		$request = "GET ".$link['path'].'?'.$link['query']." HTTP/1.0\r\n"; 
		$request .= "Host: ".$link['host']."\r\n";
		$request .= "Cache-Control: no-store, no-cache, must-revalidate\r\n";
		$request .= "Pragma: no-cache\r\n";
		$request .= "Cookie: income=1\r\n";
		$request .= "Referer: google.com/\r\n";
		$request .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)\r\n\r\n";

		$fp = fsockopen($link['host'], 80, $errno, $errstr, 30);

		if($fp){
	
			fputs ($fp,$request);
	
			while (!feof($fp)) {
				$page.= fgets($fp,1024);
			}
			fclose ($fp);
			return $page;
		}
		
		return false;
	}

/*------------------------------*/
	private function pregtrim($str) {
		   return preg_replace("/[^\x20-\xFF]/","",@strval($str));
	}

//
// проверяет URL и возвращает:
//  *  +1, если URL пуст                    
//        if (checkurl($url)==1) echo "пусто"
//  *  -1, если URL не пуст, но с ошибками
//        if (checkurl($url)==-1) echo "ошибка"
//  *  строку (новый URL), если URL найден и отпарсен
//        if (checkurl($url)==0) echo "все ок"
//        либо if (strlen(checkurl($url))&gt;1) echo "все ок"
//
//  Если протокола не было в URL, он будет добавлен ("http://")
//
	private function checkurl($url) {
	   // режем левые символы и крайние пробелы
	   $url=trim($this->pregtrim($url));
	   // если пусто - выход
	   if (strlen($url)==0) return 1;
	   //проверяем УРЛ на правильность
	   if (!preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}".
	   "(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
	   "org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
	   "!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&amp;".
	   "?+=\~/-]*)?(?:#[^ '\"&amp;&lt;&gt;]*)?$~i",$url,$ok))
	   return -1; // если не правильно - выход
	   // если нет протокала - добавить
	   if (!strstr($url,"://")) $url="http://".$url;
	   // заменить протокол на нижний регистр: hTtP -&gt; http
	   $url=preg_replace("~^[a-z]+~ie","strtolower('\\0')",$url);
	   return $url;
	}
	
	public function getDownloadLink($args)
	{

		$link = html_entity_decode('http://'.str_replace('http://','',trim($args[0])));	
//		echo $link;
//		$link = 'http://download.cnet.com/Disk-Drill/3000-2094_4-75307728.html?part=dl-10116181&subj=dl&tag=button';
		$link_pattern ='/http:\/\/[^\'"]+\/[^\'"]+\/([^\'"]+).html[^\'"]+&subj=([^\'"]+)& ?.*/'; 		
	//	$download_pattern = '/<a[^<>]*href=[\'"]([^\'"]+oId=3000-2094_4-75307728[^\'"]+)[\'"][^<>]*class=\"dl\"> ?.*/'; //good		
	//	$download_pattern = '/<a[^<>]*href=[\'"]([^\'"]+oId=%s[^\'"]+)[\'"][^<>]*class=\"%s\"> ?.*/'; //good		
		$download_pattern = '/href=[\'"]([^\'"]+oId=%s[^\'"]+)[\'"] ?.*/'; //good				
		if (preg_match ( $link_pattern, $link, $link_matches ))
		{
			$download_pattern = sprintf($download_pattern, $link_matches[1], $link_matches[2]); //good				

			//$content = file_get_contents ($link);
			$content = $this->get_curl($link);
			
			if (preg_match ( $download_pattern, $content, $matches ))
			{
				
				if ($this->checkurl($matches[1])==0)
				 {
						return $matches[1];	
				 }
			}
			return ; 
		}
	}
/*----------------------------*/
function getDownloadLinkNew($args)
	{
		$link = html_entity_decode('http://'.str_replace('http://','',trim($args[0])));	
		$link_pattern ='/http:\/\/[^\'"]+\/[^\'"]+\/([^\'"]+).html[^\'"]+&subj=([^\'"]+)& ?.*/'; 		
		$download_pattern = '/<a[^<>]*href=[\'"]([^\'"]+oId=%s[^\'"]+)[\'"][^<>]*class=\"%s\"> ?.*/'; //good	
		$download_pattern2 = '/<[#META#i][^<>]*[#Refresh#i][^<>]*[#CONTENT#i]=[\'"]0;[^\'"]*URL=(.*)[\'"]\/> ?.*/'; 

		if (preg_match ( $link_pattern, $link, $link_matches )){
			$download_pattern = sprintf($download_pattern, $link_matches[1], $link_matches[2]); //good				

			//$content = file_get_contents ($link);
			$content = $this->get_curl($link);
			
			if (preg_match ( $download_pattern, $content, $matches )){
				
				if ($this->checkurl($matches[1])==0){
					$content2 = $this->get_curl($matches[1]);
						if (preg_match ( $download_pattern2, $content2, $matches2 )){
							if ($this->checkurl($matches2[1])==0){
								return $matches2[1];	
							 }
						}
				 }
			}
			return ; 
		}
	}
	/*---------------------------*/	
	function getDownloadLink3($args)
	{
		$url = $this->get_curl('http://www.admlink.com/cnet.php?url='.urlencode($args[0]));
		if ((int)$this->checkurl($url)==0){
			return	$url;
		}
	}
/*---------------------------*/	
	public function getDownloadLink4($args)
	{
		$result = array('download'=>'','redirect'=>'');
		$link = html_entity_decode('http://'.str_replace('http://','',trim($args)));	
		$download_pattern1 = '/<a[^<>]*id=[\'"]dllink1[\'"][^<>]*href=[\'"]javascript:downloadNow\(\'(.*)\',\'(.*)\'\)[^<>]*[\'"][^<>]*>?.*/i'; //good	
		$download_pattern2 = '/<a[^<>]*href=[\'"]javascript:downloadNow\(\'(.*)\',\'(.*)\'\)[^<>]*[\'"][^<>]*id=[\'"]dllink1[\'"][^<>]*>?.*/i'; //good	
		
		$content = $this->get_fsockopen($link);
		
		if (preg_match( $download_pattern1, $content, $link_matches )){
			$result = array('download'=>$link_matches[1],'redirect'=>$link_matches[2] );
		}elseif(preg_match( $download_pattern2, $content, $link_matches )){
			$result = array('download'=>$link_matches[1],'redirect'=>$link_matches[2] );
		}
	
		return  json_encode($result);
				
	}
/*---------------------------*/
    public function getCases($args = array()) {
		include_once(ENGINE_PATH.'class/classCase.php');
		$case = new CCase();
        return $case->getCases($args[0]);
    }
	
	public function getCasesToPage() {
		include_once(ENGINE_PATH.'class/classCase.php');
		$case = new CCase();
		 return $case->getCasesToPage();

    }
/*-----------------*/
	function xmas2011_bundle_link($args)
	{
		include_once(ENGINE_PATH.'class/classPurchase.php');
		$purchase = new Purchase();
		$page = VBox::get('Page');
        $pageLanguage = $page->language;		
		$url = "&amp;currency=usd&amp;language=".$pageLanguage."&amp;enablecoupon=false&amp;x-tracking=xmas2011_bundle&amp;cart=";	
		$formatPrice = "&amp;minquantity_%1\$d=1&amp;maxquantity_%1\$d=1&amp;dp_%1\$d=__PRICE:%2\$0.2f:%3\$s;N__CHECKSUM:%4\$s";						
		$count = sizeof($args);
		for($i=0;$i<$count;$i=$i+3){
			$url.= $args[$i].',';			
			$price.=sprintf($formatPrice, $args[$i], $args[$i+1], $args[$i+2], md5('__PRICE:'.$args[$i+1].';N#'.$purchase ->CBPass));			
		}
		$url = substr($url,0,-1).$price;

		$cbUrl = $purchase->makeCBSecureLink($url,'design052011a&amp;x-trackinga=1',false);
		
		return $cbUrl;
	}
/*-----------------*/	 
    public function getBanners($args)
    {
        //$args[0] - alias banner, $args[1] - id banner
        include_once(ENGINE_PATH.'class/classBanners.php');   
        $banners = new Banners();
        
        if (is_array($args))
        {
            $banneritems  = $banners->getBanners(array('alias'=>$args[0],'id'=>$args[1]));  
        }
        else
        {
            $banneritems  = $banners->getBanners();  
        }

        return $banneritems;
    }
	
	 /**
     * @data $args[0],$args[1] - url, $args[2] - percent redirect
     * exp.: array('google.com','yandex.ru',60)
     */
    public function getRedirect($args=null)
    {    
        session_start();
        $varCookie = md5('url');

        if (isset($_COOKIE[$varCookie]) || isset($_SESSION[$varCookie]))
        {
		    header("HTTP/1.1 301 Moved Permanently");
            header('Location: http://'.(isset($_COOKIE[$varCookie]) ? $_COOKIE[$varCookie] : $_SESSION[$varCookie]));
            exit();
        }       
        
        if (count($args) == 3 && !isset($_COOKIE[$varCookie]) && !isset($_SESSION[$varCookie]) )
        {
            $percent=(int)$args[2];
            
            $length = intval(strpos(dirname(__FILE__), 'HTMLRenderer'));
            
            if (0 == $length)
            {
			 return;
            }
          
            $path = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'tmp'; 
                        
            if(!file_exists($path))
            {
                if (!mkdir($path, 0775)) {
                    die("error to create dir $path");
                }
            }
            
            $cachefile = $path.DIRECTORY_SEPARATOR.'redirect.dat';

                    $fp = fopen($cachefile, "a+") or die("error to open file $cachefile for read");
                    
                    $rowCount = explode('|',fgets($fp,256));
                    fclose($fp);
                    
                    if (count($rowCount) != 2)
                    {
                        $rowCount = array();
                        for($i=1; $i<3; $i++)
                        {
                            $rowCount[] = $i;
                        }
                    }

                    if ($rowCount[1]>100 || $rowCount[0]>100)
                    {
                        $rowCount[0] = 1;
                        $rowCount[1] = 2;
                    }

                    $account = (int)((($rowCount[1] / ($rowCount[0] + $rowCount[1])) * 100) > $percent ) ? 0 : 1;
                    $rowCount[$account] = $rowCount[$account]+1;
                    
                    $fp = fopen($cachefile, "w+") or die("error to open file $cachefile for write"); 
                    fwrite($fp, "{$rowCount[0]}|{$rowCount[1]}");
                    fclose($fp);
                    
                    setcookie($varCookie, $args[$account]);
                    $_SESSION[$varCookie] = $args[$account];
                    
					header("HTTP/1.1 301 Moved Permanently");
                    header('Location: http://'.(isset($_COOKIE[$varCookie]) ? $_COOKIE[$varCookie] : $_SESSION[$varCookie]));
                    exit();

        }
    }
}

?>