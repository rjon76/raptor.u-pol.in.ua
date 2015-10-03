<?php

include_once(ENGINE_PATH.'class/classProducts.php');

class ProductInfo {

    private $products;

    public function __construct() {
        $this->products = new Products();
    }

    public function __destruct() {
        $this->products = NULL;
    }

    public function getProductFeatures($args) {
        return $this->products->getProductFeatures($args[0]);
    }

    public function getProductDemoLimits($args) {
        return $this->products->getProductDemoLimits($args[0]);
    }

    public function getProductBuildInfo($args) {
        return $this->products->getProductBuildInfo($args[0]);
    }

    public function getProductLanguages($args) {
        return $this->products->getProductLanguagesIds($args[0]);
    }

    public function getProductsList($args) {
        return $this->products->getProductsShort(TRUE, FALSE, $args[0]);
    }

    public function getProductsPlatforms($args) {
        return $this->products->getProductsPlatforms($args[0], false);
    }

    public function getProductsVersions($args) {
        $versions = $this->products->getProductMajorVersions($args[0]);

        if(count($versions) > 1) {
            return $versions;
        } else {
            return false;
        }
    }

    public function getProductVersion($args) {
        $product = $this->products->getProductById($args[0]);

        return $product['p_version'];
    }

	public function getProductSupport($args) {
		
		if (!isset($args[0])){
			return false;		
		}

		$login = $nik =  $args[0];
		$chat_id = time() + rand(0, 1000);	
			
		if (is_numeric($args[0]) > 0){
			if(isset($args[1])){
				$login = $args[1];	
				$nik = isset($args[2]) ? $args[2] : $args[1];	
			}else{
				$id = $args[0];
				if ($result = $this->products->getProductSupport($id )){
					$login = $result['sm_login'];	
					$nik = isset($args[2]) ? $args[2] : $result['sm_nik'];	
				}
			}
		}

        return array('login'=>$login,'nik'=>$nik, 'chat_id'=>$chat_id);
    }
	// convert object in to array
	private function objectsIntoArray( $arrObjData, $arrSkipIndices = array() )
	{
	    $arrData = array();
	   
	    // if input is object, convert into array
	    if( is_object( $arrObjData ) )
	    {
	        $arrObjData = get_object_vars($arrObjData);
	    }
	   
	    if ( is_array( $arrObjData ) )
	    {
	        foreach ( $arrObjData as $index => $value )
	        {
	            if ( is_object($value) || is_array( $value ) )
	            {
	                $value = $this->objectsIntoArray( $value, $arrSkipIndices ); // recursive call
	            }
	            
	            if ( in_array( $index, $arrSkipIndices ) )
	            {
	                continue;
	            }
	            
	            $arrData[$index] = $value;
	        }
	    }
	    return $arrData;
	}
	
	public function getCNETProductRating($args){
		$result = array(); 
		$partKey = "4ff4vm279t2dww5ff2a6kaf4";
		$partTag = "4ff4vm279t2dww5ff2a6kaf4";
		$productId = $args[0];
		$xmlStr ="http://developer.api.cnet.com/rest/v1.0/softwareProduct?iod=userRatings&partKey=%1\$s&partTag=%1\$s&productSetId=%2\$s";
		$xmlStr = sprintf($xmlStr, $partKey, $productId);
		$xmlObj = simplexml_load_file($xmlStr);
		if ($xmlObj != false ){
			$res	= $this->objectsIntoArray($xmlObj->SoftwareProduct[0]->UserRatingProduct);
			if ($res['TotalVotes']==0){
				$res['TotalVotes'] = 10;
			};
			
			if (is_array($res['Rating'])){
				$res['Rating'] = 4.8;
			}
			$result = array('Rating'=>$res['Rating'], 'TotalVotes'=>$res['TotalVotes']);
		}
		return $result;
	}
	private function oas($a)
	{
		echo '<pre>';
		print_r($a);
		echo '</pre>';
	}

	private function oa($a)
	{
		echo '<pre>';
		var_dump($a);
		echo '</pre>';
	}
/*----------------------*/
	public function getProductInfo($args) {
		include_once(ENGINE_PATH.'class/classPurchase.php');
		$result = array(); 
		$product_id = $args[0];
		$build_info = $this->products->getProductBuildInfo($product_id);
		$platforms = $this->products->getProductsPlatforms($product_id);
		$platforms_tmp = array();
		foreach ($platforms as $os){
			array_push($platforms_tmp, $os['os_value']);				
		}
		
		$lang = $this->products->getProductLanguagesIds($product_id);
		$result['name'] = $build_info['p_title'];
		$result['nick'] = $build_info['p_nick'];
		$result['download'] = $build_info['p_download'];		
		$result['softwareVersion'] = $build_info['p_version'];	
		$result['datePublished'] = $build_info['ctime'];			
//		$result['author'] = array('name'=>'Eltima Software', 'url'=>'www.eltima.com');		
		$result['fileSize'] = $build_info['size'];				
		$result['operatingSystems'] = implode(',', $platforms_tmp);
		$result['inLanguage'] = implode(',', $lang);
		$purchase = new Purchase();
		if($purchase->init($product_id )) {
			$licenseData = $purchase->processLicenseData();
			$result['licenses'] =  $licenseData['lic'];
		}
		$result['Rating'] = mt_rand(450, 500) / 100;
		$result['TotalVotes'] = mt_rand(10, 100);
		return $result;
	}
    
        /**
     * get product name
     * 
     */
    public function getName($args) {
        
        if (isset($args[0]) && $args[0] != 0)
        {
            $product = $this->products->getProductById($args[0]);
            if ($product){
                return $product['p_title'];
            }
        }
        
        return '';
    }
    
    public function getDownloads($args) {
        
        if (!isset($args[1])){
            $args[1] = 'format';
        }
        
        if (isset($args[0]) && $args[0] != 0)
        {
            $product = $this->products->getProductById($args[0]);
            $downloads = $product['p_downloads'];
            
            if ($downloads >0)
            {
                switch($args[1]){
                    
                    case "format":
                        return number_format($downloads,0,'',' ');
                        break; 
                    
                    case "formatUp":
                        return number_format($downloads,0,'',' ').'+';
                        break; 
                            
                    default:
                        return $downloads;
                        break; 
        
                } 
            }
        }
        
        return '';
    }
}

?>