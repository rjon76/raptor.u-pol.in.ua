<?php
/* requrements
  class VException
  class Error
*/
class VBox extends ArrayObject {

    private static $box = null;

    //public function __construct() {}
    private function __clone() {}

    public static function getInstance() {
        if (self::$box === null) {
            self::init();
        }

        return self::$box;
    }

    protected static function init() {
        self::$box = new self();
    }

    public static function get($index) {
        $instance = self::getInstance();

        if (!$instance->offsetExists($index)) {
            Error::logError('No entry is registered for key '.$index);
	    throw new VException('No entry is registered for key '.$index);
        }

        return $instance->offsetGet($index);
    }

    public static function set($index, $value) {
        $instance = self::getInstance();
        $instance->offsetSet($index, $value);
    }

    public static function isExist($index) {
        if (self::$box === null) {
            return false;
        }
        return array_key_exists($index, self::$box);
    }

    public static function getAll(){
    	return self::$box;
    }

    public static function clearAll() {
	$instance = self::getInstance();
        $instance->box = null;
    }

    public static function clear($index) {
	$instance = self::getInstance();

	if($instance->offsetExists($index)) {
            $instance->offsetUnset($index);
        }
    }


}

?>