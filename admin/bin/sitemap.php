<?php
define('ENGINE_PATH','/usr/home/www/venginse/');
define('LOCAL_PATH','/usr/home/www/venginse_admin/');

include_once(ENGINE_PATH.'class/classError.php');
include_once(ENGINE_PATH.'class/classDB.php');
include_once(ENGINE_PATH.'class/classConstData.php');
include_once(ENGINE_PATH.'class/classSitemapGenerator.php');

Error::initialize();
Error::setMailing(TRUE);
Error::$pathToFile = LOCAL_PATH.'logs/sitemap.log';
Error::setFileLogging(TRUE);
DB::initialize('mysql', 'localhost', NULL, 'venginse_admin', 'venginse', 'YqPffTfB');
DB::executeQuery('SET NAMES utf8', 'cp_utf8');

$q = 'SELECT s_hostname, s_dbname, s_path, s_gziped
	FROM sites
      WHERE s_indexed = 1';
DB::executeQuery($q, 'sites');
$rows = DB::fetchResults('sites');
if(!empty($rows)) {
    $tsize = sizeof($rows);
    $Sitemap = new SitemapGenerator('venginse_all');
    for($i=0; $i<$tsize; $i++) {
	$q = 'SELECT c_value
		FROM '.$rows[$i]['s_dbname'].'.const
	      WHERE c_name = "siteClosed" LIMIT 1';
	DB::executeQuery($q, 'close');
	$row = DB::fetchOne('close');
	if($row == '0') {
	    $Sitemap->setParams($rows[$i]);
	    if($Sitemap->buildSitemap()) {
		$Sitemap->writeSitemap();
		$Sitemap->submitSitemap();
	    }
	    sleep(100);
	}
    }
    unset($Sitemap);
}

Error::deInitialize();
DB::deInitialize();

?>
