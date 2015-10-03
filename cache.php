<?php
set_time_limit(360);
if ($_SERVER['HTTP_HOST'] == NULL)
{
	ini_set('magic_quotes_runtime', 'Off');
	ini_set('display_errors', 'off');

	include_once('application/includes.inc.php');
	VBox::set('ConstData', new ConstData());
	$isCacheable = VBox::get('ConstData')->getConst('isCacheable');	// get isCacheable
	if ( (bool)$isCacheable) //If site isCacheable
	{
		$localPath = LOCAL_PATH; //local path to site
		$cachePath = IniParser::getSettring('cache', 'cache_path');	//path to cache dir
		$cachecount = IniParser::getSettring('cache', 'cachecount'); //count copy of cache			
		include_once(ENGINE_PATH.'class/classReCacher.php');
		$cacher = new ReCacher($localPath, $cachePath, $cachecount);
		$cacher->setLogMode(2);
		$cacher->_rebuildAllCache();
		unset($cacher);
	}
    include_once(LOCAL_PATH.'application/final.inc.php');	
}

?>