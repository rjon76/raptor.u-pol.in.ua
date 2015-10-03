<?php

include_once(ENGINE_PATH.'interface/interfaceLocalExt.php');
include_once(ENGINE_PATH.'class/classPurchase.php');
include_once(ENGINE_PATH.'class/classProducts.php');

class PurchaseInfo {
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


    public function __construct() {
        $this->purchase = new Purchase();
    }

    public function __destruct() {
        $this->purchase = NULL;
    }

    public function getOperators() {
        return $this->purchase->getOperators();
    }

	public function getPurchaseInfo($args)
    {
		if(!empty($args[0]))
		{
		    $this->productId = intval($args[0]);
		}
		if(1 > $this->productId)
		{
		    return;
		}

		if($this->purchase->init($this->productId))
		 {
	    	if(isset($_POST['process']))
			{
				$this->postValid = $this->validatePost();
		    }
		    else
			{
				$this->licenseData = $this->purchase->processLicenseData();
				if(isset($_COOKIE['currency']))
				{
					 $this->licenseData['geo_currency'] = $_COOKIE['currency'];
				}
				elseif(isset($args[1]))
				{
					 $this->licenseData['geo_currency'] = $args[1];
				}
				else
				{
					$this->licenseData['geo_currency'] = 'USD';
				}
				//	$this->licenseData['geo_currency'] = $this->purchase->detectCurrency($code);
				$products = new Products();
			//	$this->licenseData['prices'] = $this->licenseData['prices'];	
			//    $this->licenseData['prices'] = json_encode($this->licenseData['prices']);
			//	$this->licenseData['licenses'] = $this->licenseData['lic'];
	        //    $this->licenseData['licenses'] = json_encode($this->licenseData['lic']);
    	        $this->licenseData['currencies2Operators'] = json_encode($this->licenseData['currencies2Operators']);
        	    $this->licenseData['defaultOperatorId'] = $this->licenseData['operator_id'];

			  //  $this->licenseData['bundle_prices'] = json_encode(isset($this->licenseData['bundle_prices']) ? $this->licenseData['bundle_prices'] : NULL);
	    		//$this->licenseData['bundle_product_prices'] = json_encode(isset($this->licenseData['bundle_product_prices']) ? $this->licenseData['bundle_product_prices'] : NULL);

			   // $this->licenseData['offer_prices'] = json_encode($this->licenseData['offer_prices']);
//	    		$this->licenseData['geo_currency'] = json_encode($this->licenseData['geo_currency']);
	    		$this->licenseData['geo_currency'] = $this->licenseData['geo_currency'];				
			   // $this->licenseData['products'] = $products->getProductsShort(TRUE);

  /*      		$versions = $products->getProductMajorVersions($this->productId);
	            if(count($versions) > 1) {
    	            $this->licenseData['product_versions'] = $versions;
        	    }*/

            	$this->licenseData['product'] = $products->getProductById($this->productId);
			    return $this->licenseData;
			}
		}
  }

	private function validatePost()
	{
		return TRUE;
    }
  	public function getPurchaseLink($args)
	{
        $language = 'en';
        $currency = 'USD';	
        $coupon = '';
        $formatUrl = "&amp;recommendation=prioritysupport&amp;currency=%1\$s&amp;language=%2\$s&amp;cart=%3\$s&amp;dp_%3\$d=__PRICE:%4\$0.2f:%1\$s;N__CHECKSUM:%5\$s&amp;coupon=%6\$s";
        	
  		$this->purchase = new Purchase();
		if(!empty($args[0])) {
		    $this->productId = intval($args[0]);
		}

		if(!empty($args[1])) {
		    $licenseId = intval($args[1]);
		}

		if(!empty($args[2])) {
		    $currency = $args[2];
		}

		if(!empty($args[3])) {
		    $language = $args[3];
		}
		
		if(!empty($args[4])) {
		    $coupon = $args[4];
		}	
       
	
		if($this->purchase->init($this->productId)) {
	    	if(isset($_POST['process'])) {
				$this->postValid = $this->validatePost();
		    }
			else {
				$this->licenseData = $this->purchase->processLicenseData();
				$operator_id = $this->licenseData['operator_id'];
				foreach ($this->licenseData['prices'] as $key => $item){
					if (isset($item[$operator_id])){
						$contractIds = $item[$operator_id]['contractIds'];
						if (array_search($licenseId, $contractIds)){
							$lic_id = $key;
						}
					}
				}
				if (isset($lic_id) && $lic_id > 0){
					$price = $this->licenseData['prices'][$lic_id][$currency]['price'];	
					$CHECKSUM = $this->licenseData['prices'][$lic_id]['cbpasswords'][$licenseId][$currency];				
				}
			}
	    }

		$params  = sprintf($formatUrl, $currency, $language, $licenseId, $price, $CHECKSUM, $coupon);
		$cbUrl = $this->purchase->makeCBSecureLink($params,'mac2012',false);	
        
        return $cbUrl;
		
	  }
	  
    public function getLicensesInfo($args)
    {
        if(count($args) == 0){
            return false;
        }
	    $id = (int)$args[0];
        $result  = array();
		if($this->purchase->init($id)){
			$this->licenseData = $this->purchase->processLicenseData();
			$products = new Products();
			$result['licenses'] 	= $this->licenseData['lic'];
			$result['offers'] 	= $this->licenseData['offers'];
			$result['product']	= $products->getProductById($id);
		}
		return $result;
  }
}
?>