<?php
ini_set('magic_quotes_runtime', 'off');
//	error_reporting(E_ERROR|E_STRICT);
	error_reporting(E_ERROR);
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 'off');

//	$request = $_SERVER['HTTP_HOST'].(isset($_GET['request']) ? '/'.$_GET['request']:'');
	$request = isset($_GET['request']) ? $_GET['request'] : '';

//	var_dump($request);
	if (!substr_count($_SERVER['HTTP_HOST'],'www2.') && count($_POST)==0)
	{
			$uri_address = $_SERVER['DOCUMENT_ROOT'].'/cache/cache/'.$request;
			if(!substr_count($uri_address,'.html'))
			{ 
 	 		 $cache_uri_address = $uri_address.((substr($uri_address,-1) != '/') ? '/':'').'index.html';
			}
			else
			{
 	 		 $cache_uri_address = $uri_address;
			}
		if ($content = file_get_contents($cache_uri_address, FALSE))
		 {
			 echo $content;
			 exit();
		 }
	}

	include_once('application/includes.inc.php');
    include_once(ENGINE_PATH.'class/classURIHandler.php');

    $constData = new ConstData();
    
	VBox::set('ConstData', $constData);
	
	//var_dump($request); 
	if($request != null)
	{
		// different checks for bad urs. unfortunately, there are so many of them .pics
		$badFiles = array('.jpg','.gif','.ico','.png','.bmp','.php','.php3','.asp','.aspx');
		if(in_array(strrchr($request, '.'), $badFiles))
		{
			header('HTTP/1.1 404 Not Found');
			//header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.$matched[1].'.html');
			//	include_once(LOCAL_PATH.$constData->getConst('404page'));
			$uri_address = $_SERVER['DOCUMENT_ROOT'].'/cache/cache/404.html';
			echo file_get_contents($uri_address, FALSE);
			include_once(LOCAL_PATH.'application/final.inc.php');

			exit();
		}
		
		// .htm pages are not acceptable
		if(preg_match("/(.*)\.htm$/", $request, $matched))
		{
			if(isset($matched[1]))
			{
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: http://'.$_SERVER['HTTP_HOST'].'/'.$matched[1].'.html');
				include_once(LOCAL_PATH.'application/final.inc.php');
				exit();
			}
		}
	}

    if($constData->getConst('siteClosed') && $constData->getConst('realDomain') != $_SERVER['HTTP_HOST'])
	{
        header('HTTP/1.1 302 Found');
        header('Location: '.$constData->getConst('maintainPage'));
        include_once(LOCAL_PATH.'application/final.inc.php');
        exit();
    }

	// создаем Агрегатор

    $agregator = new Agregator();

    // запускаем

    $process = $agregator->process();

    // если pageId не найден, и не найдено совпадений в greenList-ах - редирект на 404
	if($process === FALSE)
	{
		Error::mail404($request);
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://'.$_SERVER['HTTP_HOST'].$constData->getConst('404page'));
		//$uri_address = $_SERVER['DOCUMENT_ROOT'].'/cache/cache/404.html';
		//echo file_get_contents($uri_address, FALSE);
	//	include_once(LOCAL_PATH.'application/final.inc.php');
	//	exit();
/*		Error::mail404($request);
		header('HTTP/1.1 404 Not Found');
		include_once(LOCAL_PATH.$constData->getConst('404page'));
		*/
    }
    include_once(LOCAL_PATH.'application/final.inc.php');
?>