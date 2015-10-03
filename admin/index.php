<?php

	date_default_timezone_set('UTC');
	error_reporting(E_ERROR);
	//ini_set('error_reporting', E_ERROR);
	ini_set('display_errors', 1);

    $dirs = explode(DIRECTORY_SEPARATOR,dirname(__FILE__));
	define('ADMIN_DIR','/'.end($dirs));
	
	define('ROOT_DIR',str_replace("\\","/",dirname(__FILE__)));	
	define('BASE_URL',  'http://'.str_replace("\\","/",$_SERVER['HTTP_HOST']).ADMIN_DIR);
	define('ENGINE_PATH',str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']).'/libs/');
    
    set_include_path('.'.PATH_SEPARATOR.ENGINE_PATH
                        .PATH_SEPARATOR.'./application/models/'
                        .PATH_SEPARATOR.'./application/'
                        .PATH_SEPARATOR.get_include_path());

    // load the script files we need
    include_once("Zend/Loader.php");
    include_once("controllers/MainAppController.php");
    include_once('Smarty/Smarty.class.php');

    // load the classes we need
    Zend_Loader::loadClass('Zend_Controller_Front');
    Zend_Loader::loadClass('Zend_Config_Ini');
    Zend_Loader::loadClass('Zend_Registry');
    Zend_Loader::loadClass('Zend_Db_Table');
    Zend_Loader::loadClass('Zend_Auth');
    Zend_Loader::loadClass('Zend_Db');
                                 
    // load configuration
    $config 		= new Zend_Config_Ini('./application/config.ini', 'general');
    $registry 		= Zend_Registry::getInstance();
    $registry->set('config', $config);
	
    // setup database
    $dbAdapter 		= Zend_Db::factory($config->db->adapter, $config->db->config->toArray());
    Zend_Db_Table::setDefaultAdapter($dbAdapter);
    $registry->set('dbAdapter', $dbAdapter);
    $dbAdapter->query('SET NAMES utf8');

    // setup controller
    $frontController = Zend_Controller_Front::getInstance();
    $frontController->throwExceptions(true);
    $frontController->setParam('noViewRenderer', true);
    $frontController->setControllerDirectory('./application/controllers');

    // run!
    $frontController->dispatch();
?>