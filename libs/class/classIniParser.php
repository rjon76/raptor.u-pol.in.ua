<?php
/* requrements
  class VException
*/
class IniParser {

    private static $_instance;
    private $_parsed;
    private $_settings;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
	if (self::$_instance === NULL) {
	    self::$_instance = new self;
	}
	return self::$_instance;
    }

    public static function unsetInstance() {
	self::getInstance()->_parsed = NULL;
	self::getInstance()->_settings = NULL;
	self::$_instance = NULL;
    }

    public function setIni($file, $absolute=FALSE) {
	self::getInstance()->_parsed = FALSE;
	self::getInstance()->_settings = array();
	$path = $file;
	if(!$absolute && defined(LOCAL_PATH)) {
	    $path = LOCAL_PATH.ltrim($file,'/');
	}

	if(file_exists($path)) {
	    self::getInstance()->_settings = parse_ini_file($path, TRUE);
	    if(is_array(self::getInstance()->_settings) && sizeof(self::getInstance()->_settings)) {
		self::getInstance()->_parsed = TRUE;
		//return TRUE;
	    }
	    else {
		throw new VException('Error in parsing ini file.');
	    }
	}
	else {
	    throw new VException('Ini file not found.');
	}
	//return FALSE;
    }

    public function getAll() {
	if(self::getInstance()->_parsed) {
	    return self::getInstance()->_settings;
	}
	else {
	    return FALSE;
	}
    }

    public function getSection($sectionName) {
	if(self::getInstance()->_parsed && isset(self::getInstance()->_settings[$sectionName])) {
	    return self::getInstance()->_settings[$sectionName];
	}
	else {
	    return FALSE;
	}
    }

    public function getSettring($sectionName, $settingName) {
	if(self::getInstance()->_parsed
		&& isset(self::getInstance()->_settings[$sectionName],
			 self::getInstance()->_settings[$sectionName][$settingName])) {
	    return self::getInstance()->_settings[$sectionName][$settingName];
	}
	else {
	    return FALSE;
	}
    }
}
?>