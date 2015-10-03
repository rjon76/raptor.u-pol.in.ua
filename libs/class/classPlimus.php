<?php
/* requrements
  class DB
  class VBox
*/
class Plimus {

    private $centralDbName;

    public function __construct() {
        if(VBox::isExist('ConstData')) {
            $this->dbName = VBox::get('ConstData')->getConst('langsDb').'.';
        } else {
            /* Exception */
        }
    }

    public function __destruct() {}

    public function getPlimusLinkDataByAddress($address) {

        $q = 'SELECT link_data
	      FROM '.$this->dbName.'pa_plimus_links
              WHERE link_address = "'.$address.'"';

        if(DB::executeQuery($q,'link')) {
            return DB::fetchOne('link');
        }


    }
}

?>