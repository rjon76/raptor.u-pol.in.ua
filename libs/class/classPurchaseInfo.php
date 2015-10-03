<?php

	class PurchaseInfo
	{
		protected $dbName;
		protected $langId;
		protected $language;
		protected $productId;
		protected $currencyData 	= array();
		protected $productArray 	= array();
		protected $currencies 		= array('AUD','CHF','EUR','GBP','CAD','CNY','NOK','SEK','PLN','JPY','RUB');
		
		const element5Pass 			= 'uTMuJFcIrKr1Fv8';

		
		public function __construct()
		{
			if(VBox::isExist('ConstData'))
			{
				$this->dbName = VBox::get('ConstData')->getConst('langsDb').'.';
			}
		}

		
		public function __destruct()
		{
		}

		
		public function init($productId)
		{
			if(!$this->dbName)
			{
				return FALSE;
			}

			if(VBox::isExist('Page'))
			{
				$this->langId 		= VBox::get('Page')->languageId;
				$this->language 	= VBox::get('Page')->language;
			}
			else
			{
				return FALSE;
			}
		
			$this->productId = intval($productId);
		
			if(1 > $this->productId)
			{
				return FALSE;
			}
		
			$this->currencyData = $this->getCurrencyCodes();

			return TRUE;
		}

		
		public function setProductId($productId)
		{
			$this->productId = intval($productId);
		}
		
		public function setCurrencyCodes( )
		{
			$this->currencyData = $this->getCurrencyCodes();
		}
	
		public function processLicenseData($cur_code='USD')
		{
			$cCode = $this->getCurIdonCode($cur_code);

			$q = '	SELECT 
							l_id,
							l_parentid,
							l_price,
							l_type,
							l_usernumber,
							l_min_usernumber,
							l_users_in_license,
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
//			var_dump($q);
			if(DB::executeQuery($q,'lic'))
			{
				$rows = DB::fetchResults('lic');
				$sRows = sizeof($rows);
				for($i = 0; $i < $sRows; $i++)
				{
					$lid 		= $rows[$i]['l_id'];
					$parentid 	= $rows[$i]['l_parentid'];

					$this->productArray['categories'][$rows[$i]['l_type']][$lid] = $lid;

					$this->productArray['lic'][$lid]['id'] 				= $lid;
					$this->productArray['lic'][$lid]['name'] 			= $rows[$i]['l_name'];
					$this->productArray['lic'][$lid]['parentid'] 		= $parentid;
					$this->productArray['lic'][$lid]['price'] 			= $rows[$i]['l_price'];
					$this->productArray['lic'][$lid]['cur_code'] 		= $cur_code;				
					$this->productArray['lic'][$lid]['price_id'] 		= $rows[$i]['oi_price_id'];
					$this->productArray['lic'][$lid]['wiki_link'] 		= $rows[$i]['l_wiki_link'];
					$this->productArray['lic'][$lid]['default'] 		= $rows[$i]['l_default'];

					$this->productArray['lic'][$lid]['htmlprice'] 		= str_replace('.', '.<sup>', $rows[$i]['l_price']).'</sup>';

					$this->productArray['lic'][$lid]['type'] 			= $rows[$i]['l_type'];
					$this->productArray['lic'][$lid]['usernumber'] 		= $rows[$i]['l_usernumber'];
					$this->productArray['lic'][$lid]['min_usernumber'] 	= $rows[$i]['l_min_usernumber'];
					$this->productArray['lic'][$lid]['users_in_license']= $rows[$i]['l_users_in_license'];
					$this->productArray['operator_id'] 					= $rows[$i]['po_operator_id'];
					$this->productArray['lic'][$lid]['save'] 			= 0;

					if(0 < $rows[$i]['l_parentid'])
					{
						$parentPrice = $this->productArray['lic'][$parentid]['price'];
					
						if(0 < $parentPrice)
						{
							$this->productArray['lic'][$lid]['save'] = 100 - round($rows[$i]['l_price'] * 100 / $parentPrice);
						}
					}

					$cCode = $this->currencyData[$rows[$i]['pr_curid']]['code'];

					if(0 < $rows[$i]['l_price'])
					{
						if($parentid > 0)
						{ 
//							$this->productArray['prices'][$parentid]['packs'][$lid]['USD'] 				= $rows[$i]['l_price'];
							$this->productArray['prices'][$parentid]['packs'][$lid][$cur_code] 			= $rows[$i]['l_price'];							
							$this->productArray['prices'][$parentid]['packs'][$lid][$cCode] 			= $rows[$i]['pr_price'];
							$this->productArray['prices'][$parentid]['packs'][$lid]['min_usernumber'] 	= $rows[$i]['l_min_usernumber'];
							$this->productArray['prices'][$parentid]['packs'][$lid]['usernumber'] 		= $rows[$i]['l_usernumber'];
							$this->productArray['prices'][$parentid]['packs'][$lid]['users_in_license'] = $rows[$i]['l_users_in_license'];
						}
						else
						{
//							$this->productArray['prices'][$lid]['USD']['price'] 	= $rows[$i]['l_price'];
							$this->productArray['prices'][$lid][$cur_code]['price'] 	= $rows[$i]['l_price'];							
							$this->productArray['prices'][$lid][$cCode]['price'] 	= $rows[$i]['pr_price'];
						}
					}
				}
			}

			$q = '	SELECT 
							oi_price_id,
							oi_default,
							oi_curid,
							oi_operator_id,

							l_id

					FROM '.$this->dbName.'pa_operators_id
                  
					LEFT JOIN '.$this->dbName.'pa_licenses ON oi_lid = l_id
					
					WHERE l_pid = '.$this->productId.' AND oi_blocked = "N"';

					/*LEFT JOIN '.$this->dbName.'pa_products_operators ON oi_operator_id = po_operator_id*/
//var_dump($q);
			if(DB::executeQuery($q,'lic_contracts'))
			{
				$rows = DB::fetchResults('lic_contracts');

				foreach($rows AS $row)
				{
					$cCode = $this->currencyData[$row['oi_curid']]['code'];

//				if($row['oi_default'] == 'Y')
//				if($cCode == 'USD')			
				if($cCode == $cur_code)
					{
						$this->productArray['prices'][$row['l_id']][$row['oi_operator_id']]['contractIds']['default'] = $row['oi_price_id'];
					//	$this->productArray['defaultOperatorId'] = $row['oi_operator_id'];
					}
					$this->productArray['prices'][$row['l_id']][$row['oi_operator_id']]['contractIds'][$cCode] = $row['oi_price_id'];
				}
			}

			$q = '	SELECT 
							oc_operator_id,
							c_code
              
					FROM '.$this->dbName.'pa_operators_currencies
				
					LEFT JOIN '.$this->dbName.'pa_currencies ON c_id = oc_currency_id';

			$operators2Currencies = array();
			
			$this->productArray['currencies2Operators'] = array();
        
			if(DB::executeQuery($q, 'opcur'))
			{
				$rows = DB::fetchResults('opcur');

				foreach($rows AS $row)
				{
					$operators2Currencies[$row['oc_operator_id']][] 			= $row['c_code'];
					$this->productArray['currencies2Operators'][$row['c_code']] = $row['oc_operator_id'];
				}
			}
			
			$q = '	SELECT *
              
					FROM '.$this->dbName.'pa_currencies
				';

			$this->productArray['currenciesRate'] = array();
        
			if(DB::executeQuery($q, 'curate'))
			{
				$rows = DB::fetchResults('curate');

				foreach($rows AS $row)
				{
					$this->productArray['currenciesRate'][$row['c_code']] = $row['c_ratio'];
				}
			}
			

			$q = '	SELECT 	os_operator_id,
							os_site_id
				  
					FROM '.$this->dbName.'pa_operators_sites';

			$this->productArray['operators2Sites'] = array();
			
			if(DB::executeQuery($q, 'opsite'))
			{
				$rows = DB::fetchResults('opsite');

				foreach($rows AS $row)
				{
					$this->productArray['operators2Sites'][$row['os_site_id']] = $row['os_operator_id'];
				}
			}

			if($this->productArray['operator_id'] == 0  && isset($this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')]))
			{
				$this->productArray['operator_id'] = $this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')];
			}

			$q = '	SELECT 	
							bn_id,
							bn_pid,
							bn_bundle_lid,
							bn_price,

							p_title,
							p_nick,
							p_wiki_link,
							p_page_link,

							l_price,

							oi_price_id,

							bnp_curid,
							bnp_price
		
					FROM '.$this->dbName.'pa_bundles
					
					LEFT JOIN '.$this->dbName.'pa_licenses ON l_id = bn_bundle_lid
					LEFT JOIN '.$this->dbName.'products ON p_id = l_pid
					LEFT JOIN '.$this->dbName.'pa_operators_id ON oi_lid = l_id AND oi_curid = 12
					LEFT JOIN '.$this->dbName.'pa_bundle_prices ON bnp_bnid = bn_id

					WHERE bn_pid = '.$this->productId;

			if(DB::executeQuery($q, 'bundles'))
			{
				$rows = DB::fetchResults('bundles');

				for($i = 0; $i < sizeof($rows); $i++)
				{
					$this->productArray['bundles'][$rows[$i]['bn_id']]['price'] 			= $rows[$i]['bn_price'];
					$this->productArray['bundles'][$rows[$i]['bn_id']]['price_id'] 			= $rows[$i]['oi_price_id'];
					$this->productArray['bundles'][$rows[$i]['bn_id']]['html_price'] 		= str_replace('.', '.<sup>', $rows[$i]['bn_price']).'</sup>';;
					$this->productArray['bundles'][$rows[$i]['bn_id']]['true_price'] 		= $rows[$i]['l_price'];
					$this->productArray['bundles'][$rows[$i]['bn_id']]['cur_code'] 		= $cur_code;
					$this->productArray['bundles'][$rows[$i]['bn_id']]['html_true_price'] 	= str_replace('.', '.<sup>', $rows[$i]['l_price']).'</sup>';
					$this->productArray['bundles'][$rows[$i]['bn_id']]['product_title'] 	= $rows[$i]['p_title'];
					$this->productArray['bundles'][$rows[$i]['bn_id']]['product_nick'] 		= $rows[$i]['p_nick'];
					$this->productArray['bundles'][$rows[$i]['bn_id']]['product_wiki_link'] = $rows[$i]['p_wiki_link'];
					$this->productArray['bundles'][$rows[$i]['bn_id']]['product_page_link'] = $rows[$i]['p_page_link'];

					$cCode = $this->currencyData[$rows[$i]['bnp_curid']]['code'];

//					$this->productArray['bundle_prices'][$rows[$i]['bn_id']]['USD']['price'] 	= $rows[$i]['bn_price'];
					$this->productArray['bundle_prices'][$rows[$i]['bn_id']][$cur_code]['price'] 	= $rows[$i]['bn_price'];
					$this->productArray['bundle_prices'][$rows[$i]['bn_id']][$cCode]['price'] 	= $rows[$i]['bnp_price'];

					$this->productArray['bundles'][$rows[$i]['bn_id']]['save'] = 100 - round($rows[$i]['bn_price'] * 100 / $rows[$i]['l_price']);
				}
			}

			$q = '	SELECT
							oi_price_id,
							oi_default,
							oi_curid,
							oi_operator_id,

							bn_id

					FROM '.$this->dbName.'pa_bundles
					
					LEFT JOIN '.$this->dbName.'pa_operators_id ON oi_lid = bn_bundle_lid
					
					WHERE bn_pid = '.$this->productId;

			if(DB::executeQuery($q,'bun_contracts'))
			{
				$rows = DB::fetchResults('bun_contracts');

				foreach($rows AS $row)
				{
					$cCode = $this->currencyData[$row['oi_curid']]['code'];
//					if($cCode == 'USD')
//					if($row['oi_default'] == 'Y')
					if($cCode == $cur_code)
					{
						$this->productArray['bundle_prices'][$row['bn_id']][$row['oi_operator_id']]['contractIds']['default'] = $row['oi_price_id'];
					}
					$this->productArray['bundle_prices'][$row['bn_id']][$row['oi_operator_id']]['contractIds'][$cCode] = $row['oi_price_id'];
				}
			}

			$q = 'SELECT l_id,
						 l_price,

						 pr_curid,
						 pr_price,

						 bn_id

				FROM '.$this->dbName.'pa_licenses
				LEFT JOIN '.$this->dbName.'pa_bundles ON bn_bundle_lid = l_id
				LEFT JOIN '.$this->dbName.'pa_prices ON pr_lid = l_id

			 WHERE bn_pid = '.$this->productId;

			if(DB::executeQuery($q, 'bundlesPrices')) {
				$rows = DB::fetchResults('bundlesPrices');

				foreach($rows AS $row) {

					$cCode = $this->currencyData[$row['pr_curid']]['code'];

//					$this->productArray['bundle_product_prices'][$row['bn_id']]['USD'] = $row['l_price'];
					$this->productArray['bundle_product_prices'][$row['bn_id']][$cur_code] = $row['l_price'];
					$this->productArray['bundle_product_prices'][$row['bn_id']][$cCode] = $row['pr_price'];
				}
			}

			$q = '	SELECT 
							af_id,
							af_text,
							af_default_price,
							af_price_percent,

							ac_contract_id,
							ac_operator_id,
							ac_cur_id,

							afp_curid,
							afp_price,

							ll_text

					FROM '.$this->dbName.'pa_adfeatures
					
					LEFT JOIN '.$this->dbName.'pa_adfeatures_prices ON afp_afid = af_id
					LEFT JOIN '.$this->dbName.'pa_adfeatures_contracts ON ac_adfeature_id = af_id

					WHERE afp_pid = '.$this->productId.'
					
					ORDER BY af_id';

			if(DB::executeQuery($q, 'offers'))
			{
				$rows = DB::fetchResults('offers'); 

				for($i = 0; $i < sizeof($rows); $i++)
				{
					$this->productArray['offers'][$rows[$i]['af_id']]['price'] 	= $rows[$i]['af_default_price'];
					$this->productArray['offers'][$rows[$i]['af_id']]['text'] 	= $rows[$i]['ll_text'];

					$cCode 	= $this->currencyData[$rows[$i]['afp_curid']]['code'];

					$this->productArray['offer_prices'][$rows[$i]['af_id']]['USD'] 				= $rows[$i]['af_default_price'];
					$this->productArray['offer_prices'][$rows[$i]['af_id']][$cCode] 			= $rows[$i]['afp_price'];
					
					$this->productArray['offer_prices'][$rows[$i]['af_id']]['price_percent'] 	= $rows[$i]['af_price_percent'];
					$this->productArray	['offer_prices']
										[$rows[$i]['af_id']]
										[$rows[$i]['ac_operator_id']]
										[$this->currencyData[$rows[$i]['ac_cur_id']]['code']] 	= $rows[$i]['ac_contract_id'];
				}
			}

			$this->productArray['os'] 			= $this->checkOS($_SERVER['HTTP_USER_AGENT']);
			$this->productArray['dontshownote'] = (isset($_COOKIE['dontshownote']) ? 1 : 0);


			if(	$this->productArray['operator_id'] == 2 
				|| count($operators2Currencies) 
				|| (isset($this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')]) 
				&& $this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')] == 2)) 
			{
//				$this->prepareForElement5($operators2Currencies);
			}

			return $this->productArray;
		}

		protected function prepareForElement5($operators2Currencies)
		{
			//echo md5('300312225#250.0RUB,N#'.Purchase::element5Pass).'<br/>';
			foreach($this->productArray['prices'] AS $licenseId => $license)
			{
				if(isset($license[2]['contractIds']))
				{
					foreach($license[2]['contractIds'] AS $currencyId => $contractId)
					{
						foreach($this->productArray['prices'][$licenseId] AS $currencyCode => $price)
						{
							if ( ( ( 	isset($operators2Currencies[2]) 
										&& in_array($currencyCode, $operators2Currencies[2])) 
										|| $this->productArray['operator_id'] == 2 
										|| (isset($this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')]) 
										&& $this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')] == 2)) 
										&& (	$currencyCode != 'contractIds' &&
												$currencyCode != 'element5passwords' &&
												$currencyCode != 'packs'))
							{
								$this->productArray['prices']
												   [$licenseId]
												   ['element5passwords']
												   [$contractId]
												   [$currencyCode] = md5($contractId.'#'.$price['price'].$currencyCode.',N#'.Purchase::element5Pass);
												   
												   //echo ($contractId.'#'.$price['price'].$currencyCode.',N#'.Purchase::element5Pass)."<br>";
							}
						}

						if(isset($this->productArray['prices'][$licenseId]['packs']))
						{
							foreach($this->productArray['prices'][$licenseId]['packs'] AS $packId => $pack)
							{
								foreach($pack AS $currencyCode => $price)
								{
									if ( ( ( 	isset($operators2Currencies[2]) 
												&& in_array($currencyCode, $operators2Currencies[2])) 
												|| $this->productArray['operator_id'] == 2 
												|| (isset($this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')]) 
												&& $this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')] == 2)) 
												&& ($currencyCode != 'min_usernumber' &&
													$currencyCode != 'usernumber' &&
													$currencyCode != 'element5passwords'))
									{
										$this->productArray['prices']
														   [$licenseId]
														   ['packs']
														   [$packId]
														   ['element5passwords']
														   [$contractId]
														   [$currencyCode] = md5($contractId.'#'.$price.$currencyCode.',N#'.Purchase::element5Pass);
														   
														   //echo ($contractId.'#'.$price.$currencyCode.',N#'.Purchase::element5Pass)."<br>";
									}
								}
							}
						}
					}
				}
			}

			if(isset($this->productArray['bundle_prices']))
			{
				foreach($this->productArray['bundle_prices'] AS $bundleId => $bundle)
				{
					if(isset($bundle[2]['contractIds']))
					{
						foreach($bundle[2]['contractIds'] AS $currencyId => $contractId)
						{
							foreach($this->productArray['bundle_prices'][$bundleId] AS $currencyCode => $price)
							{
								if ( ( ( 	isset($operators2Currencies[2]) 
											&& in_array($currencyCode, $operators2Currencies[2])) 
											|| $this->productArray['operator_id'] == 2 
											|| (isset($this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')]) 
											&& $this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')] == 2)) 
											&& ($currencyCode != 'contractIds' &&
												$currencyCode != 'element5passwords'))
								{
									$this->productArray	['bundle_prices']
														[$bundleId]
														['element5passwords']
														[$contractId]
														[$currencyCode] = md5($contractId.'#'.$price['price'].$currencyCode.',N#'.Purchase::element5Pass);
														
													//	echo ($contractId.'#'.$price['price'].$currencyCode.',N#'.Purchase::element5Pass)."<br>";
								}
							}
						}
					}
				}
			}

			if(isset($this->productArray['offer_prices']))
			{
				foreach($this->productArray['offer_prices'] AS $offerId => $offer)
				{
					if(empty($offer['price_percent']))
					{
						foreach($offer AS $currencyCode => $price)
						{
							if ( ( ( 	isset($operators2Currencies[2]) 
										&& in_array($currencyCode, $operators2Currencies[2])) 
										|| $this->productArray['operator_id'] == 2 
										|| (isset($this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')]) 
										&& $this->productArray['operators2Sites'][VBox::get('ConstData')->getConst('siteId')] == 2)) 
										&& ($currencyCode != 'contract_id' &&
											$currencyCode != 'element5passwords' &&
											$currencyCode != 'price_percent'))
							{
								$offerContractId = $offer[2][$currencyCode];

								if($offerContractId != NULL)
								{
									$contractHash = md5($offerContractId.'#'.$price.$currencyCode.',N#'.Purchase::element5Pass);
								}
								else
								{
									$contractHash = md5($offer[2]['USD'].'#'.$price.$currencyCode.',N#'.Purchase::element5Pass);
								}
								
								$this->productArray	['offer_prices']
													[$offerId]
													['element5passwords']
													[$currencyCode] = $contractHash;
							}
						}
					}
				}
			}
		}
		
		
		public function detectCurrency($code)
		{
			return 'USD';
		}

		
		private function generateLead()
		{
			return substr(md5(uniqid(mt_rand(), FALSE)), 2, 15);
		}

		
		private function getCurrencyCodes()
		{
			$res = array();
			
			$q = '	SELECT 
							c_id, 
							c_code, 
							c_ratio
					
					FROM '.$this->dbName.'pa_currencies';
	
			if(DB::executeQuery($q, 'currency'))
			{
				$rows 	= DB::fetchResults('currency');
				$tsize 	= sizeof($rows);
				
				for($i = 0; $i < $tsize; $i++)
				{
					$res[$rows[$i]['c_id']] = array('code' => $rows[$i]['c_code'], 'ratio' => $rows[$i]['c_ratio']);
				}
			}
			return $res;
		}
		private function getCurIdonCode($code)
		{
			foreach ($this->currencyData as $key=>$val)
			{
				if ($val['code']==$code)
				 return $key;
			}
			return false;	
		}
		
		public function checkOS($useragent)
		{
			$os = 'Unknown';

			if(strpos($useragent, "Win") !== false)
			{
				$os = 'win';
			}
			elseif(strpos($useragent, "Macintosh")!== false || strpos($useragent, "PowerPC"))
			{
				$os = 'mac';
			}
			return $os;
		}

		
		public function getOperators()
		{
			$q = '	SELECT 
							op_id,
							op_name
					
					FROM '.$this->dbName.'pa_operators';

			if(DB::executeQuery($q, 'operators'))
			{
				return DB::fetchResults('operators');
			}
			return FALSE;
		}

		
		public function saveLeadInfo($name, $email, $info)
		{
			$q = '	INSERT INTO '.$this->dbName.'pa_lead
					SET 
						ld_name = ?,
						ld_mail = ?,
						ld_time = NOW(),
						ld_info = ?';

			DB::executeAlter(	$q, 
								array	(
											$name,
											$email,
											serialize($info)
										)
							);
		}
		
		
		public function getProductPurchaseInfo($contractId, $contractQuant) 
		{
			$data = array();
			$data['contract_id'] 	= $contractId;
			$data['quatity'] 		= $contractQuant;
			$data['curency'] 		= 'USD';
			
			$q = '	SELECT oi_lid
					FROM '.$this->dbName.'pa_operators_id
    				WHERE oi_price_id  =  "'.$contractId.'"';

			if(DB::executeQuery($q,'lic'))
			{
				$row = DB::fetchResults('lic');
			}

			$licId = $row[0]['oi_lid'];	
			
			unset($row);
			
			$q = '	SELECT *
					FROM '.$this->dbName.'pa_licenses
    				WHERE 	l_id  		= '.$licId.'
    				OR 		l_parentid 	= '.$licId.'
    				';

			if(DB::executeQuery($q,'lic2'))
			{
				$row = DB::fetchResults('lic2');
			}
			
			if($contractQuant != 0)
			{
				foreach ($row as $key => $lic)
				{
					$minUsers = $lic['l_min_usernumber'];
					$maxUsers = $lic['l_usernumber'];
					
					if($contractQuant == $maxUsers && $maxUsers != 0)
					{
					echo '1';
						$pricePerUnit = $lic['l_price'];
					}
					elseif ( $contractQuant >= $minUsers && $contractQuant <= $maxUsers )
					{
					echo '2';
						$pricePerUnit = $lic['l_price'];
					}
					elseif ( $contractQuant >= $minUsers && $maxUsers == 0)
					{
						$pricePerUnit = $lic['l_price'];
					}
				}
				
				$data['price_per_unit']	= $pricePerUnit;
				$data['totalPrice']		= $pricePerUnit * $contractQuant;
				$data['hash']			= md5($contractId.'#'.$data['totalPrice'].$data['curency'].',N#'.Purchase::element5Pass);
				$data['elementLink']	= 'PRODUCT['.$contractId.']='.$contractQuant.'&PRODUCTPRICE['.$contractId.']='.$data['totalPrice'].$data['curency'].',N;'.$data['hash'].'&js=-1';				  
				
			}

			/*
			
			echo '<pre>';
			print_r($data);
			echo '</pre>';
					
			*/
			
		
		}
		   /* function to validate coupon code, that is entered in purchase area
			return discount percent or 0 for unvalid coupon
		    */
		public function Validatecoupon($params)
		{
			$percent = 0;
			$result	= array('percent'=>0, 'licenses'=>array());
			if(!empty($params['licenses'])){
				$lics = explode(',',trim($params['licenses']));
				$operator = isset($params['operator']) ? trim($params['operator']): 0;
	    		$q = '	SELECT cup_percent, cup_validlic, cup_unvalidlic, cup_quantity, cup_opid
		    			FROM '.$this->dbName.'pa_coupons
						WHERE cup_code 	= ?
			    		AND cup_blocked = "N"
			    		AND cup_date 	>= NOW()
						AND ((cup_opid = ? and ? <> "0") or ( ? = "0"))
						LIMIT 1';
	    
				if(DB::executeQuery($q,'coupon',array($params['coupon'], $operator, $operator, $operator))){
					$row 			= DB::fetchRow('coupon');
					$validLic 		= unserialize($row['cup_validlic']);
					$unvalidLic 	= unserialize($row['cup_unvalidlic']);
					$resinvalid		= array();
					$resvalid		= array();				

					if(sizeof($unvalidLic)){
						$resinvalid = array_intersect($lics, $unvalidLic);
					};
					if(sizeof($validLic)){
						$resvalid = array_intersect($lics, $validLic);
					}
					$resvalid = array_diff($resvalid, $resinvalid);
					$tmp = array();
				
					foreach($resvalid as $item){
						$tmp[$item] = $row['cup_percent'];
					}
					$result = array('percent'=>$row['cup_percent'], 'licenses'=>$tmp);
				}
			}
			return $result;
		}

	}
	
	
?>