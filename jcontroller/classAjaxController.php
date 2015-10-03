<?php

class AjaxController
{
    private $isValid = FALSE;

    public function __construct()
	{
		$path 	= dirname(__FILE__);
		$length = intval(strpos($path, 'jcontroller'));
		if (0 == $length)
		{
			return;
		}
		$path = rtrim(substr($path,0,$length-1),DIRECTORY_SEPARATOR).
	    DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'includes.inc.php';
		
		if (!file_exists($path))
		{
			return;
		}
		
		include_once($path);
		VBox::set('ConstData', new ConstData());
		$this->isValid = TRUE;
    }

    public function __destruct()
	{
		$this->isValid = NULL;
		include_once(LOCAL_PATH.'application/final.inc.php');
    }

    public function getResult()
	{
        if ($this->isValid && (!empty($_POST['ajaxmethod']) || !empty($_GET['ajaxmethod']))) {

	    $method = 'call'.ucfirst(strtolower((!empty($_POST['ajaxmethod']) ? $_POST['ajaxmethod'] : $_GET['ajaxmethod'])));

	    if (method_exists($this, $method))
		{
			$res = $this->$method();

			if(isset($res))
			{
				return json_encode($res);
			}
	    }
	}

	return '';
}
public function callcheckLocale()
{
	$result = 0;
	if (isset($_POST['pg_id']))
		$pg_id = $_POST['pg_id'];
	else
	 return $result;
	include_once(ENGINE_PATH.'class/classGeoip.php');
	include_once(ENGINE_PATH.'class/classPage.php');

	
	$page = new Page($pg_id);
	$geo = new Geo();
	$currenLocale = $geo->getCode();
	$pageLang = $page->language;
//	var_dump($currenLocale, $pageLang);
	if ($currenLocale <> $pageLang)
	{
		$geotext = 	$page->getLocalizedString('geotext', $currenLocale);
		$relpages = $page->getRelativePageAddresses();
//var_dump($relpages);
		if (isset($relpages[$currenLocale]))
				$result = "<a href='".$relpages[$currenLocale]."'>".$geotext."</a>";
	}
	echo $result;
}

public function callsendResellerForm(){
	include_once(ENGINE_PATH.'class/classForm.php');

	$form = new Form();

	if ($form->ParseSettings('resellerForm.ini'))
	{
	    $formData = $form->BuildFormFields($_POST);

	    if (!empty($formData['error']))
		{
			unset($form);
			return $formData;
	    }
		else
		{
			$msg =  'Contact request from '.$_SERVER['HTTP_HOST']."\n".
					'Details'."\n\n".
			$form->__toString();

			$form->SendMail('Reseller form submit ', $msg, $formData['fields']['name'], $formData['fields']['email']);
	    }
	}
	return $formData;
}




    /* function to validate coupon code, that is entered in purchase area
	return discount percent or 0 for unvalid coupon
    */
public function callValidatecoupon()
{
	$percent = 0;
	
	if(!empty($_POST['licenses']))
	{
	    $lics = explode(',',trim($_POST['licenses']));
	    $dbName = VBox::get('ConstData')->getConst('langsDb').'.';
	    $q = 'SELECT cup_percent, cup_validlic, cup_unvalidlic, cup_quantity
		    FROM '.$dbName.'pa_coupons
			WHERE cup_code = ?
		    AND cup_blocked = "N"
		    AND cup_date >= NOW()
			LIMIT 1';
	    
		if(DB::executeQuery($q,'coupon',array($_POST['coupon'])))
		{
			$row 			= DB::fetchRow('coupon');
			$validLic 		= unserialize($row['cup_validlic']);
			$unvalidLic 	= unserialize($row['cup_unvalidlic']);

                //if($_POST[''] >= $row['cup_quantity'])
			if(sizeof($unvalidLic))
			{
				$res = array_intersect($lics, $unvalidLic);
				
				if(0 == sizeof($res))
				{
					$percent = $row['cup_percent'];
				}
			}
			else
			{
				$res = array_intersect($lics, $validLic);
				
				if(sizeof($res))
				{
					$percent = $row['cup_percent'];
				}
			}
		}
	}
	return $percent;
}

    /*
      function to validate contact form "Contact sales representative"
      and send response to mail
    */
public function callSendcontactform()
{
	include_once(ENGINE_PATH.'class/classForm.php');

	$form = new Form();

	if ($form->ParseSettings('purchaseAreaContact.ini'))
	{
	    $formData = $form->BuildFormFields($_POST);

	    if (!empty($formData['error']))
		{
			unset($form);
			return $formData['error'];
	    }
		else
		{
			include_once(ENGINE_PATH.'class/classProducts.php');
			$productObj = new Products();
			$product 	= $productObj->getProductById($formData['fields']['cont_product']);

			if (empty($product))
			{
				return array('cont_product' => '1');
			}

			$msg =  'Contact request from '.$_SERVER['HTTP_HOST']."\n".
					'Details'."\n\n".
					'Product: '.$product['p_title']."\n".
			$form->__toString();

			$form->SendMail('New contact request on '.$product['p_title'], $msg, $formData['fields']['cont_name'], $formData['fields']['cont_email']);
	    }
	}
	return array();
}

    /*
      function to validate contact form "Place Purchase Order"
      and send response to mail
    */
    public function callSendplaceorder() {
	include_once(ENGINE_PATH.'class/classForm.php');

	$form = new Form();

	if ($form->ParseSettings('placeOrderForm.ini')) {

	    $formData = $form->BuildFormFields($_POST);

	    if (!empty($formData['error'])) {
		unset($form);
		return $formData['error'];
	    } else {
		include_once(ENGINE_PATH.'class/classProducts.php');

		$productObj = new Products();
		$product = $productObj->getProductById($formData['fields']['place_product']);

		if (empty($product)) {
		    return array('place_product' => '1');
		}

		$msg = 'Contact request from '.$_SERVER['HTTP_HOST']."\n".
		       'Details'."\n\n".
		       'Product: '.$product['p_title']."\n".
		       $form->__toString();

		$form->SendMail('New contact request', $msg);
	    }
	}
	return array();
    }

    /*
      function to validate contact froms from "Contacts Us Page" Process
    */
    public function callProcesscontactforms() {

        /* Include classes we need */
        include_once(LOCAL_PATH.'application/localExt/contactForm/index.php');
        /*                         */

        $contactForm = new contactForm();
        return $contactForm->getResult();
    }

    public function callProcessuninstallform() {
        include_once(LOCAL_PATH.'application/localExt/uninstallForm/index.php');

        $uninstallForm = new UninstallForm();
        return $uninstallForm->getResult();
    }

    /*
     Get page content
    */
    public function callGetpagecontent() {
        //echo $_GET['url'] . '?ajax=true';
        $pageContent = file_get_contents($_GET['url'] . '?ajax=true');

        if(strstr($_GET['url'], 'user-guides')) {
            echo substr($pageContent, 0, 1200).'...';
        } else {
            echo $pageContent;
        }

    }

    public function callProcessorder() {
        include_once(ENGINE_PATH.'class/classPurchase.php');

        $purchae = new Purchase();
        $purchae->init($_POST['productId']);
        $purchae->saveLeadInfo($_POST['userName'],
                               $_POST['userEmail'],
                               $_POST);
    }

    public function callGetmd5() {
        include_once(ENGINE_PATH.'class/classPurchase.php');

        if(!empty($_POST['offers'])) {
            $hashes = array();
            foreach($_POST['offers'] AS $contractId => $price) {
                $hashes[$contractId] = md5($contractId.'#'.$price.',N#'.Purchase::element5Pass);
            }

            echo json_encode($hashes);
        }
    }

	public function callGetCBMd5()
	{
		include_once(ENGINE_PATH.'class/classPurchase.php');

		if(!empty($_POST['offers']))
		{
			$hashes = array();
            foreach($_POST['offers'] AS $contractId => $price)
            {
                $hashes[$contractId] = md5('__PRICE:'.$price.';N#'.Purchase::CBPass);
            }

            echo json_encode($hashes);
        }
    }
    
 	public function callMakeCBLink()
    {	
		$not_to_convert = array('ajaxmethod', 'template', 'recommendation','productid','addoffers','additionalParams');
		
		include_once(ENGINE_PATH.'class/classPurchase.php');
    	
//    	$design = (isset($_POST['template']) ? $_POST['template'] : 'mac2012').'&amp;x-trackingb=1';  
    	$design = isset($_POST['template']) ? $_POST['template'] : 'mac2012';  //new		

        if (isset($_POST['recommendation']))
        {
            $recommendation = $_POST['recommendation'];
        }
        else
        {
             $recommendation = 'defbackupcd,productsfor'.$_POST['productid']; 
        }	
        
		if(isset($_POST['addoffers']))
        {
			$recommendation .= ','.$_POST['addoffers'];
        }		

		//$recommendation = isset($_POST['recommendation']) ? $_POST['recommendation'] : 'backupcd,productsfor'.$_POST['productid']; 		
		
		foreach($not_to_convert as $key){
			unset($_POST[$key]);			
		}
	
		//$purchaseStr = '&amp;recommendation='.$recommendation .'&amp;'.http_build_query($_POST, '', '&amp;');
		$purchaseStr = '&amp;x-trackingb=1&amp;recommendation='.$recommendation .'&amp;'.http_build_query($_POST, '', '&amp;');

		$link = Purchase::makeCBSecureLink( $purchaseStr, $design );		

		echo $link;
	}
	   
    /*public function callMakeCBLink()
    {
    	include_once(ENGINE_PATH.'class/classPurchase.php');
    	
    	$purchaseStr = '';
    	
		foreach($_POST AS $key => $val)
		{
			if( $key != 'ajaxmethod' )
			{
				$purchaseStr .='&amp;'.$key.'='.$val;
			}
		}
		
		$design = 'design052011a&amp;x-trackinga=1';
		
		if( isset( $_POST['design'] ) )
		{
			if( $_POST['design'] == 'white' )
			{
				$design = 'design052011b&amp;x-trackinga=2';
			}
			if( $_POST['design'] == 'black' )
			{
				$design = 'design052011a&amp;x-trackinga=1';
			}
		}
		
		$link = Purchase::makeCBSecureLink( $purchaseStr, $design );		
		echo $link;
    }
    */
    public function getCBDesignId()
    {
    	$file = LOCAL_PATH.'jcontroller/cbCounter';
    	
    	if( is_file( $file ) == false )
    	{
    		$fp = fopen( $file, 'w+');
			fwrite($fp, '1');
			fclose($fp);
			return 1;
    	}
    	else 
    	{
    		$fp = fopen( $file,'r' );
    		$val = fgets($fp);
    		fclose($fp);

    		$fp = fopen( $file, 'w+');
			fwrite($fp, $val+1);
			fclose($fp);
    		
			return $val;
    	}
    }
    
    public function callsubscribeupdates() {
        include_once(LOCAL_PATH.'application/localExt/softwareUpdates/index.php');

        $softwareUpdates = new softwareUpdates();

        if(isset($_POST['subscribe'])) {

            if($softwareUpdates->subscribe()) {
                echo 1;
            } else {
                echo 0;
            }
        }

        if(isset($_POST['unsubscribe'])) {

            if($softwareUpdates->unsubscribe()) {
                echo 1;
            } else {
                echo 0;
            }
        }

        if(isset($_POST['forgot'])) {

            if($softwareUpdates->recallPassword()) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
/*----------------------------*/	
	public function callgetDownloadLink()
	{
		include_once(LOCAL_PATH.'application/globalExt/HTMLRenderer/index.php');
		$HTMLRenderer = new HTMLRenderer;
		echo $HTMLRenderer->getDownloadLink4($_POST['link']);
	}
public function callcheckPlatform()
{
	include_once(ENGINE_PATH.'class/classProducts.php');
     include_once(ENGINE_PATH.'class/classPurchase.php');
		$products = new Products();
		$platforms = $products->getProductsPlatforms($_POST['productId']);
        $purchase = new Purchase();
        $purchase->init($_POST['productId']);
		$os=$purchase->checkOS($_SERVER['HTTP_USER_AGENT']);
		$countplatform = count($platforms);
		for ($i=0; $i<$countplatform;$i++)
		{
			if (stristr($platforms[$i]['os_value'], $os))
				return 1;
		}
	return 0;
}

    public function callUpdatedownloads() {

		include_once(ENGINE_PATH.'class/classProducts.php');

		$product = new Products();
        
        $productId = $_POST['productId'];        
        $file = end(explode('/',$_POST['file']));
                
        $format = $_POST['format'];
        
        //callback product info
        $callback = (bool)isset($_POST['callback']) ? $_POST['callback'] : 0;
        
        if (isset($_POST['format'])){
            $format = $_POST['format'];
        }else{
            $format = "formatUp";
        }
        
        if (empty($productId))
        {
            if (!empty($file)){
                $result = $product->getProductByFile($file);
                if ($result){
                   $productId = $result['p_id']; 
                }
            }
        }
        
        if ($productId>0){

    		//$result = $product->updateProductById($productId,array('p_downloads'=>'ifnull(p_downloads,0)+1'),$callback);
            $result = $product->updateProductById($productId,array('p_downloads'=>'p_downloads+1'),$callback);
            if ($result && $callback) {
                
                switch($format){
                    
                    case "format":
                        $downloads = number_format($result['p_downloads'],0,'',' ');
                        break; 
                    
                    case "formatUp":
                        $downloads =  number_format($result['p_downloads'],0,'',' ').'+';
                        break; 
                            
                    default:
                        $downloads =  $result['p_downloads'];
                        break; 
        
                } 

                return array(/*'product'=>$result['p_id'],*/'name'=>$result['p_title'], 'downloads'=>$downloads); 
                
    		}            
        }
	return '';
    
    }

}

?>