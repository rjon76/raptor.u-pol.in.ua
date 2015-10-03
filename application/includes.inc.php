<?php
date_default_timezone_set('UTC');
ini_set('display_errors', 1);

if(!defined('LOCAL_PATH')) {
    //define('LOCAL_PATH', 'D:/people/jon/widestep3/');
    define('LOCAL_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
}
if(!defined('ENGINE_PATH')) {
    //define('ENGINE_PATH', LOCAL_PATH.'libs/');
    define('ENGINE_PATH', LOCAL_PATH.'libs/');
}
if(!defined('LIB_PATH')) {
    //define('LIB_PATH', LOCAL_PATH.'libs/');
    define('LIB_PATH', LOCAL_PATH.'libs/');
}

include_once(ENGINE_PATH.'class/classError.php');
include_once(ENGINE_PATH.'class/classVException.php');
include_once(ENGINE_PATH.'class/classIniParser.php');
include_once(ENGINE_PATH.'class/classDB.php');
include_once(ENGINE_PATH.'class/classConstData.php');
include_once(ENGINE_PATH.'class/classVBox.php');
include_once(ENGINE_PATH.'class/classAgregator.php');

IniParser::getInstance()->setIni(LOCAL_PATH.'application/config.ini', TRUE);

?>