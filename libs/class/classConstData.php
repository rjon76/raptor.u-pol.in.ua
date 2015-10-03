<?php
/* requrements
  class DB
*/
class ConstData {

    private $siteDb;
    protected $internalData;  // Array of constants

    public function __construct($db='') {

        $this->siteDb = (!empty($db) ? $db.'.' : '');
        $this->internalData = array();
        $this->loadConst();
    }

    public function __destruct() {
        $this->siteDb = NULL;
        $this->internalData = NULL;
    }

    // Set new value of a constant
    public function setConst($name, $value) {
        if(!isset($this->internalData[$name])) {
            $this->internalData[$name] = $value;
            return TRUE;
        }
        return FALSE;
    }

    // Get the value of a constant
    public function getConst($name) {
        if(strlen($name)) {
            if(isset($this->internalData[$name])) {
                return $this->internalData[$name];
            }
        }
        return NULL;
    }

    // Load all constants to the array
    public function loadConst() {
        $q = 'SELECT c_name,
                     c_value
              FROM '.$this->siteDb.'const';

        DB::executeQuery($q, 'const_data');
        $results = DB::fetchResults('const_data');
        if(!empty($results)) {
            foreach($results as $row) {
                $this->internalData[$row['c_name']] = $row['c_value'];
            }
        }
    }
/*
    public function POST($key) {
        if(isset($_POST[$key])) {
            return $_POST[$key];
        }
        return FALSE;
    }
*/
    public function GET($key) {
        if(isset($_GET[$key])) {
            return rawurldecode($_GET[$key]);
        }
        return FALSE;
    }
}

?>