<?php
include_once(ENGINE_PATH.'interface/interfaceLocalExt.php');
include_once(ENGINE_PATH.'class/classPurchase.php');
include_once(ENGINE_PATH.'class/classProducts.php');

class CBRedirect implements LocalExtInterface
{
 
// v:125|v:328|v:EUR|v:fr
    private $purchase;
	private $operator_id = 5;
    private $language = 'en';
    private $currency = 'USD';	
    private $productId = 0;		
    private $licenseId = 0; //Sync-mate Personal License (for 2 Macs)			
	private $CHECKSUM = '';
	private $price='';
    private $formatUrl = "&amp;recommendation=prioritysupport&amp;currency=%1\$s&amp;language=%2\$s&amp;cart=%3\$s&amp;dp_%3\$d=__PRICE:%4\$0.2f:%1\$s;N__CHECKSUM:%5\$s&amp;coupon=%6\$s";		
	private $coupon='';
	
    public function __construct($args)
    {
		$this->purchase = new Purchase();
		if(!empty($args[0])) {
		    $this->productId = intval($args[0]);
		}

		if(!empty($args[1])) {
		    $this->licenseId = intval($args[1]);
		}

		if(!empty($args[2])) {
		    $this->currency = $args[2];
		}

		if(!empty($args[3])) {
		    $this->language = $args[3];
		}
		
		if(!empty($args[4])) {
		    $this->coupon = $args[4];
		}		
		
		
	}

  public function __destruct(){
		  $this->purchase = NULL;
	}


    public function parseSettings(){}


	private function validatePost()
	{
			return TRUE;
	}
		
	public function getResult()
	{
	
		if($this->purchase->init($this->productId)) {
	    	if(isset($_POST['process'])) {
				$this->postValid = $this->validatePost();
		    }
			else {
				$this->licenseData = $this->purchase->processLicenseData();
				$this->operator_id = $this->licenseData['operator_id'];
				foreach ($this->licenseData['prices'] as $key => $item){
					if (isset($item[$this->operator_id])){
						$contractIds = $item[$this->operator_id]['contractIds'];
						if (array_search($this->licenseId, $contractIds)){
							$lic_id = $key;
						}
					}
				}
				if (isset($lic_id) && $lic_id > 0){
					$this->price = $this->licenseData['prices'][$lic_id][$this->currency]['price'];	
					$this->CHECKSUM = $this->licenseData['prices'][$lic_id]['cbpasswords'][$this->licenseId][$this->currency];				
				}
			}
	    }

		$params  = sprintf($this->formatUrl, $this->currency, $this->language, $this->licenseId, $this->price, $this->CHECKSUM, $this->coupon);

		//$cbUrl = $this->purchase->makeCBSecureLink($params,'design052011b',false);
		$cbUrl = $this->purchase->makeCBSecureLink($params,'mac2012',false);	
//		oa($params);
		//oa($cbUrl);
		
		header( "Location: ".$cbUrl );
		
	  }

	function oa($a)
	{
		echo '<pre>';
		var_dump($a);
		echo '</pre>';
	}

}


?>