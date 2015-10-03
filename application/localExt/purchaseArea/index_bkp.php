<?php

include_once(ENGINE_PATH.'interface/interfaceLocalExt.php');
include_once(ENGINE_PATH.'class/classPurchase.php');
include_once(ENGINE_PATH.'class/classProducts.php');

class PurchaseArea implements LocalExtInterface {
    private $productId = 0;
    private $purchase;
    private $licenseData = array('prices' => array(),
				 'lic' => array(),
				 'licenses' => array(),
				 'bundles' => array(),
				 'bundle_prices' => array(),
				 'bundle_product_prices' => array(),
				 'offers' => array(),
				 'offer_prices' => array(),
                                 'products' => array()
				 );
    private $postValid = FALSE;
    private $couponValid = 0;

    public function __construct($productId) {

	if(!empty($productId[0])) {
	    $this->productId = intval($productId[0]);
	}
	if(1 > $this->productId) {
	    return;
	}
	$this->purchase = new Purchase();
	if($this->purchase->init($this->productId)) {
	    if(isset($_POST['process'])) {
		$this->postValid = $this->validatePost();
	    }
	    else {
		$this->licenseData = $this->purchase->processLicenseData();
		if(!isset($_COOKIE['currency'])) {
		    $this->licenseData['geo_currency'] = 'USD';

                    /*include(LIB_PATH.'geo_ip/geoip.inc');

                    $gi = geoip_open(LIB_PATH.'geo_ip/GeoIP.dat', GEOIP_STANDARD);
                    $code = geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']);
        	    geoip_close($gi);

		    if (function_exists('geoip_country_code_by_name')) {
			$code = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
		    }
		    else {
			//Error::logError('Error calling geoip.','Can not detect geoip functions.');
		    }*/

		    if(!empty($code)) {
			$this->licenseData['geo_currency'] = $this->purchase->detectCurrency($code);
		    }
		}
	    }
	}
    }

    public function __destruct() {
        $this->productId = NULL;
    }

    public function parseSettings() {
    }

    public function getResult() {
        // Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        // HTTP/1.0
        header("Pragma: no-cache");

	if(isset($_POST['process'])) {
	}
	else {
	    $products = new Products();
	    $this->licenseData['prices'] = json_encode($this->licenseData['prices']);
            $this->licenseData['licenses'] = json_encode($this->licenseData['lic']);

	    $this->licenseData['bundle_prices'] = json_encode(isset($this->licenseData['bundle_prices']) ? $this->licenseData['bundle_prices'] : NULL);
	    $this->licenseData['bundle_product_prices'] = json_encode(isset($this->licenseData['bundle_product_prices']) ? $this->licenseData['bundle_product_prices'] : NULL);

	    $this->licenseData['offer_prices'] = json_encode($this->licenseData['offer_prices']);
	    $this->licenseData['geo_currency'] = json_encode($this->licenseData['geo_currency']);
	    $this->licenseData['products'] = $products->getProductsShort(TRUE, FALSE, 3);
            $this->licenseData['product_versions'] = $products->getProductMajorVersions($this->productId);
            $this->licenseData['product'] = $products->getProductById($this->productId);
	    return $this->licenseData;
	}
    }

    private function validatePost() {
	return TRUE;
    }
}

?>