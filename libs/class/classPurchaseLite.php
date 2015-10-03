<?php
/* requrements
  class DB
  class VBox
*/

include_once(ENGINE_PATH.'class/classPurchase.php');

class PurchaseLite extends Purchase {

    public function __construct() {
        parent::__construct();
    }
    
    public function __destruct() {
        parent::__destruct();
    }
    
    public function processLicenseData() {

        $q = 'SELECT l_id,
                     l_parentid,
                     l_price,
                     l_type,
                     l_usernumber,
                     l_min_usernumber,
                     l_name,
                     l_wiki_link,
                     l_default,

		     pr_curid,
                     pr_price,

                     oi_price_id,

		     po_operator_id

		FROM '.$this->dbName.'pa_licenses
		LEFT JOIN '.$this->dbName.'pa_prices ON l_id = pr_lid
		LEFT JOIN '.$this->dbName.'pa_products_operators ON po_product_id = l_pid
                LEFT JOIN '.$this->dbName.'pa_operators_id ON oi_lid = l_id AND oi_curid = 12
                WHERE l_pid = '.$this->productId.' AND l_blocked = "N"
                ORDER BY l_order';

	if(DB::executeQuery($q,'lic')) {

            $rows = DB::fetchResults('lic');

	    for($i = 0; $i < sizeof($rows); $i++) {

                $lid = $rows[$i]['l_id'];
                $parentid = $rows[$i]['l_parentid'];

                $this->productArray['categories'][$rows[$i]['l_type']][$lid] = $lid;

                $this->productArray['lic'][$lid]['id'] = $lid;
                $this->productArray['lic'][$lid]['name'] = $rows[$i]['l_name'];
                $this->productArray['lic'][$lid]['parentid'] = $parentid;
                $this->productArray['lic'][$lid]['price'] = $rows[$i]['l_price'];
                $this->productArray['lic'][$lid]['price_id'] = $rows[$i]['oi_price_id'];
                $this->productArray['lic'][$lid]['wiki_link'] = $rows[$i]['l_wiki_link'];
                $this->productArray['lic'][$lid]['default'] = $rows[$i]['l_default'];

                $this->productArray['lic'][$lid]['htmlprice'] = str_replace('.', '.<sup>', $rows[$i]['l_price']).'</sup>';

                $this->productArray['lic'][$lid]['type'] = $rows[$i]['l_type'];
                $this->productArray['lic'][$lid]['usernumber'] = $rows[$i]['l_usernumber'];
                $this->productArray['lic'][$lid]['min_usernumber'] = $rows[$i]['l_min_usernumber'];
                $this->productArray['operator_id'] = $rows[$i]['po_operator_id'];
                $this->productArray['lic'][$lid]['save'] = 0;

                if(0 < $rows[$i]['l_parentid']) {
                    $parentPrice = $this->productArray['lic'][$parentid]['price'];
                    if(0 < $parentPrice) {
                        $this->productArray['lic'][$lid]['save'] = 100 - round($rows[$i]['l_price'] * 100 / $parentPrice);
                    }
                }

                $cCode = $this->currencyData[$rows[$i]['pr_curid']]['code'];

                if(0 < $rows[$i]['l_price']) {
                    
	            $this->productArray['prices'][$lid]['USD']['price'] = $rows[$i]['l_price'];
                    $this->productArray['prices'][$lid][$cCode]['price'] = $rows[$i]['pr_price'];
		    
		}
	    }
	}

        $q = 'SELECT oi_price_id,
                     oi_default,
                     oi_curid,
                     oi_operator_id,

                     l_id

                  FROM '.$this->dbName.'pa_licenses
                  LEFT JOIN '.$this->dbName.'pa_operators_id ON l_id = oi_lid
                  WHERE l_pid = '.$this->productId.' AND oi_blocked = "N"';

        if(DB::executeQuery($q,'lic_contracts')) {
	    $rows = DB::fetchResults('lic_contracts');

            foreach($rows AS $row) {
		//echo $row['oi_price_id'].'<br/>';
		
                $cCode = $this->currencyData[$row['oi_curid']]['code'];
                if($cCode == 'USD') {
		    $this->productArray['lic'][$row['l_id']]['contractId'][$row['oi_operator_id']] = $row['oi_price_id'];
                    $this->productArray['prices'][$row['l_id']][$row['oi_operator_id']]['contractIds']['default'] = $row['oi_price_id'];
                }
                $this->productArray['prices'][$row['l_id']][$row['oi_operator_id']]['contractIds'][$cCode] = $row['oi_price_id'];
            }
	}

        return $this->productArray;
    }

}

?>