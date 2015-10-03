<?php

include_once('classPrice.php');

class Purchase {

    private $dbAdapter;
    private $allDbAdapter;

    public function __construct() {

        /* DB initializations*/
        $this->dbAdapter = Zend_Registry::get('dbAdapter');

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
//        $params['dbname'] = 'venginse_all';
		$params['dbname'] = $config->db->config->dballname;
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
	$this->allDbAdapter->query('SET NAMES utf8');

    }

    public function updateCurrenciesRatios($newRatios) {
        foreach($newRatios AS $code => $value) {
            $set = array('c_ratio' => $value);
            $where = $this->allDbAdapter->quoteInto('c_code = ?', strtoupper($code));
            $this->allDbAdapter->update('pa_currencies', $set, $where);
        }
    }

    public function getCurrenciesRatios($all = false) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_currencies', array('c_id', 'c_code', 'c_ratio'));

        if(!$all) {
            $select->where('c_code <> "USD"');
        }

        $currencies = $this->allDbAdapter->fetchAll($select->__toString());

        $toReturn = array();
        foreach($currencies AS $currency) {
            $toReturn[$currency['c_id']] = $currency;
        }

        return $toReturn;
    }

    public function getCurrencies2PricesList($productId) {

	$select = $this->allDbAdapter->select();
	$select->from('pa_prices', array('pr_price', 'pr_curid'));
	$select->joinLeft('pa_currencies', 'pr_curid = c_id', array('c_code', 'c_id', 'c_ratio'));
	$select->joinLeft('pa_licenses', 'pr_lid = l_id', array('l_id', 'l_price'));
	$select->where('l_pid = ?', $productId);

	$prices = $this->allDbAdapter->fetchAll($select->__toString());

	$lic2prices = array();

	foreach($prices AS $price) {
	    $lic2prices[$price['l_id']][] = $price;
	}

	return $lic2prices;
    }

    public function getLicensesList($productId = NULL) {
	$select = $this->allDbAdapter->select();
        $select->from('pa_licenses', array('l_id',
					   'l_name',
					   'l_parentid',
					   'l_pid',
					   'l_price',
					   'l_type',
					   'l_usernumber',
					   'l_order',
					   'l_default',
					   'l_blocked'));
	if(isset($productId)) {
	    $select->where('l_pid = ?', $productId);
	}

        $select->order('l_order');

	$licenses = $this->allDbAdapter->fetchAll($select->__toString());
        $toReturn = array();
        foreach($licenses AS $license) {
            $toReturn[$license['l_id']] = $license;
        }
        return $toReturn;
    }

    public function getLicenses2ProductsList($productId = NULL) {
        $licenses = $this->getLicensesList($productId);
        $toReturn = array();
        foreach($licenses AS $license) {
            $toReturn[$license['l_pid']][] = $license;
        }
        return $toReturn;
    }

    public function getLicense($licenseId)
    {
        $select = $this->allDbAdapter->select();

        $select->from('pa_licenses', array(	'l_name',
					   						'l_parentid',
					   						'l_pid',
					   						'l_price',
					   						'l_type',
					   						'l_usernumber',
                                           	'l_min_usernumber',
                                           	'l_users_in_license',
					   						'l_order',
					   						'l_default',
					   						'l_blocked',
                                           	'l_wiki_link',
                                            'l_text'));
        
        $select->where('l_id = ?', $licenseId);
        
        $licensesData['data'] = $this->allDbAdapter->fetchRow($select->__toString());

        return $licensesData;
    }

    public function addLicense(	$name,
			       				$parentId,
			       				$productId,
			       				$price,
			       				$type,
			       				$usernumber,
                               	$minUsernumber,
                               	$usersInLicense,
			       				$default,
								$wikiLink = NULL
			       				) 
	{

		$maxOrder = $this->getMaxOrder($productId, $parentId);

		$row = array	(
            			'l_name'			=> $name,
            			'l_parentid'		=> isset($parentId) ? $parentId : 0,
            			'l_pid'				=> $productId,
            			'l_price'  			=> $price,
	    				'l_type'  			=> $type,
	    				'l_usernumber'  	=> $usernumber,
            			'l_min_usernumber'  => $minUsernumber,
            			'l_users_in_license'=> $usersInLicense,
	    				'l_default'  		=> isset($default) ? 'Y' : 'N',
	    				'l_blocked'  		=> 'N',
	    				'l_order'			=> isset($maxOrder) ? ($maxOrder + 1) : 1,
						'l_wiki_link'		=> $wikiLink
        				);

        $this->allDbAdapter->insert('pa_licenses', $row);
        
        $newLicenseId 	= $this->allDbAdapter->lastInsertId();

        $currencies 	= $this->getCurrenciesRatios();

        foreach($currencies AS $cur)
        {
            $row = array	(
                			'pr_lid' 	=> $newLicenseId,
                			'pr_curid' 	=> $cur['c_id'],
                			'pr_price' 	=> Price::calcPrice($price, $cur['c_ratio'], $cur['c_code'])
            				);

            $this->allDbAdapter->insert('pa_prices', $row);
        }
    }

    public function updateLicense(	$licenseId,
                                  	$name 			= NULL,
                                  	$parentId 		= NULL,
                                  	$productId 		= NULL,
                                  	$price 			= NULL,
                                  	$type 			= NULL,
                                  	$usernumber 	= NULL,
                                  	$minUsernumber 	= NULL,
                                  	$usersInLicense = NULL,
                                  	$order 			= NULL,
                                  	$default 		= NULL,
                                  	$blocked 		= NULL,
                                  	$wikiLink 		= NULL,
                                    $text 		    = NULL
                                  	)
	{

        if(isset($productId))   	{ $set['l_pid']     		= $productId; }
        if(isset($price))       	{ $set['l_price']   		= $price; }
        if(isset($order))       	{ $set['l_order']   		= $order; }
        if(isset($default))     	{ $set['l_default'] 		= $default ? 'Y' : 'N'; }
        if(isset($blocked))     	{ $set['l_blocked'] 		= $blocked ? 'Y' : 'N'; }
        if(isset($name))        	{ $set['l_name']    		= $name; }
        if(isset($parentId))    	{ $set['l_parentid'] 		= $parentId; }
        if(isset($type))        	{ $set['l_type']    		= $type; }
        if(isset($usernumber))  	{ $set['l_usernumber'] 		= $usernumber; }
        if(isset($minUsernumber))  	{ $set['l_min_usernumber'] 	= $minUsernumber; }
        if(isset($usersInLicense))  { $set['l_users_in_license']= $usersInLicense; }
        if(isset($wikiLink))    	{ $set['l_wiki_link'] 		= $wikiLink; }
        if(isset($text))    	    { $set['l_text'] 		    = $text; }

        $where = $this->allDbAdapter->quoteInto('l_id = ?', $licenseId);
        $this->allDbAdapter->update('pa_licenses', $set, $where);
    }

    public function getMaxOrder($productId, $parentId = NULL) {
	$select = $this->allDbAdapter->select();
	$select->from('pa_licenses', array('l_order' => 'MAX(l_order)'));
	$select->where('l_pid = ?', $productId);

	if(isset($parentId)) {
	    $select->where('l_parentid = ?', $parentId);
	}

	return $this->allDbAdapter->fetchOne($select->__toString());
    }

    public static function calcPrice($usdPrice, $ratio, $curCode) {
	$strict_eq = array('CAD');
	$eq = array('AUD','CHF','EUR','GBP');
	$large = array('JPY','RUB');

	if(in_array($curCode, $strict_eq)) {
	    return $usdPrice;
	}
	$price = floatval($usdPrice) * floatval($ratio);

        if(0 == substr_count($price, '.')) {
            $price .= '.00';
        }

        list($integer, $fractional) = explode('.', $price);

	$fractional = (in_array($curCode,$large) ? '00' : '95');
	$integer = intval($integer);
	//for large currencies, i.e. japan, we increasing or reducing to 50 or 100
	if(in_array($curCode,$large)) {
	    $modulus = $integer % 100;
	    //$integer+= ((50 < $modulus) ? (50 -  $modulus) : (100 - $modulus));
	    if(0 < $modulus) {
		$modulus = ((50 < $modulus) ? ($modulus - 25) : ($modulus - 75));
		$integer+= (-$modulus) + 25;
	    }
	}
	else {
	    $modulus = $integer % 10;
	    //if value is 100 we'll make it 99
	    if(0 == $modulus) {
		$integer--;
	    }
	    //if currency is not in strict price list - the last digit should be 9
	    if(!in_array($curCode,$eq)) {
		$integer+= (9 - $modulus);
	    }
	}
	return $integer.'.'.$fractional;
    }

    public function editPrice($licenseId,
                              $currencyId,
                              $newPrice) {
        $set = array(
            'pr_price' => $newPrice
        );

        $where = 'pr_lid = '.$licenseId.' AND pr_curid = '.$currencyId;
        $this->allDbAdapter->update('pa_prices', $set, $where);
    }

    public function recalculatePrices($productId = NULL, $licenseId = NULL, $currencyCodes = NULL) {

	$cursRatios = $this->getCurrenciesRatios();

	if(isset($productId)) {
	    $lic2prices = $this->getCurrencies2PricesList($productId);

	    foreach($lic2prices AS $licId => $prices) {
		if((isset($licenseId) && $licenseId == $licId) || isset($licenseId)) {

                    foreach($prices AS $price) {
                        if((isset($currencyCodes) && in_array($price['c_code'], $currencyCodes)) || !isset($currencyCodes)) {

                            $set = array(
                                'pr_price' => Price::calcPrice($price['l_price'], $price['c_ratio'], $price['c_code'])
                            );

                            $where = 'pr_lid = '.$licId.' AND pr_curid = '.$price['c_id'];

                            $this->allDbAdapter->update('pa_prices', $set, $where);
                        }
                    }
		}
	    }
	} else if(isset($currencyCodes)) {

            foreach($currencyCodes AS $code) {
                $select = $this->allDbAdapter->select();
                $select->from('pa_prices', array('pr_lid', 'pr_curid'));
                $select->joinLeft('pa_currencies', 'c_id = pr_curid');
                $select->joinLeft('pa_licenses', 'pr_lid = l_id', 'l_price');
                $select->where('c_code = ?', strtoupper($code));
                $prices = $this->allDbAdapter->fetchAll($select->__toString());

                foreach($prices AS $price) {

                    $set = array(
                        'pr_price' => Price::calcPrice($price['l_price'], $price['c_ratio'], strtoupper($code))
                    );

                    $where = 'pr_lid = '.$price['pr_lid'].' AND pr_curid = '.$price['pr_curid'];

                    $this->allDbAdapter->update('pa_prices', $set, $where);
                }
            }
        }
    }

    public function getLanguagesList() {
        $select = $this->allDbAdapter->select();
	$select->from('languages', array('l_id', 'l_code'));
	$select->order('l_order');

        $langs = $this->allDbAdapter->fetchAll($select->__toString());
        $languages = array();
        foreach($langs AS $lang) {
            $languages[$lang['l_code']] = $lang;
        }

        return $languages;
    }
//'USD','EUR','GBP','JPY','AUD','CAD','CNY','NOK','SEK','PLN','RUB','CHF'
    public function getOperatorsList() {
        $select = $this->allDbAdapter->select();
	$select->from('pa_operators', array('op_id',
                                            'op_name',
                                            'op_link',
                                            'op_default',
                                            'op_langs',
                                            'op_blocked'));
        $operators = $this->allDbAdapter->fetchAll($select->__toString());
        $toReturn = array();
        foreach($operators AS $operator) {
            $toReturn[$operator['op_id']] = $operator;
        }
        return $toReturn;
    }

    public function getOperator($operatorId) {
        $select = $this->allDbAdapter->select();
	$select->from('pa_operators', array('op_name',
                                            'op_link',
                                            'op_default',
                                            'op_langs',
                                            'op_blocked'));
        $select->where('op_id = ?', $operatorId);
        return $this->allDbAdapter->fetchRow($select->__toString());
    }

    public function addOperator($name,
                                $link,
                                $default,
                                $langs) {
        $row = array(
            'op_name'    => $name,
            'op_link'    => $link,
            'op_default' => ($default ? 'Y' : 'N'),
            'op_langs'   => serialize($langs),
            'op_blocked' => 'N'
        );

        $this->allDbAdapter->insert('pa_operators', $row);
    }

    public function updateOperator($id,
                                   $name,
                                   $link,
                                   $default,
                                   $langs,
                                   $blocked) {
        $set = array(
            'op_name'    => $name,
            'op_link'    => $link,
            'op_default' => ($default ? 'Y' : 'N'),
            'op_langs'   => serialize($langs),
            'op_blocked' => ($blocked ? 'Y' : 'N')
        );

        $where = $this->allDbAdapter->quoteInto('op_id = ?', $id);
        $this->allDbAdapter->update('pa_operators', $set, $where);
    }

    public function addOperator2License2CurrencyId($licenseId,
                                                   $currencyId,
                                                   $priceId,
                                                   $default,
                                                   $operatorId) {
        $row = array(
            'oi_lid' => $licenseId,
            'oi_curid' => $currencyId,
            'oi_price_id' => $priceId,
            'oi_default' => ($default ? 'Y' : 'N'),
            'oi_blocked' => 'N',
            'oi_operator_id' => $operatorId
        );

        $this->allDbAdapter->insert('pa_operators_id', $row);
    }

    public function getOperator2License2CurrencyIds($productId) {
        $select = $this->allDbAdapter->select();
	$select->from('pa_operators_id', array('oi_id',
                                               'oi_lid',
                                               'oi_curid',
                                               'oi_default',
                                               'oi_blocked',
                                               'oi_price_id',
                                               'oi_operator_id'));

        $select->joinLeft('pa_licenses', 'oi_lid = l_id');
        $select->where('l_pid = ?', $productId);
        $select->order('l_order');
        return $this->allDbAdapter->fetchAll($select->__toString());
    }

    public function getOperator2License2CurrencyId($id) {
        $select = $this->allDbAdapter->select();
	$select->from('pa_operators_id', array('oi_id',
                                               'oi_lid',
                                               'oi_curid',
                                               'oi_default',
                                               'oi_blocked',
                                               'oi_price_id',
                                               'oi_operator_id'));

        $select->joinLeft('pa_licenses', 'oi_lid = l_id', 'l_pid');
        $select->where('oi_id = ?', $id);
        return $this->allDbAdapter->fetchRow($select->__toString());
    }

    public function updateOperator2License2CurrencyId($id,
                                                      $default,
                                                      $blocked,
                                                      $priceId) {

        $set = array(
            'oi_default' => $default ? 'Y' : 'N',
            'oi_blocked' => $blocked ? 'Y' : 'N',
            'oi_price_id' => $priceId
        );

        $where = $this->allDbAdapter->quoteInto('oi_id = ?', $id);
        $this->allDbAdapter->update('pa_operators_id', $set, $where);
    }

    public function deleteOperator2License2CurrencyId($id) {
        $this->allDbAdapter->delete('pa_operators_id', $this->allDbAdapter->quoteInto('oi_id = ?', $id));
    }

    public function deleteLicense($licenseId) {
        $this->allDbAdapter->delete('pa_licenses', $this->allDbAdapter->quoteInto('l_id = ?', $licenseId));
        $this->allDbAdapter->delete('pa_prices', $this->allDbAdapter->quoteInto('pr_lid = ?', $licenseId));
    }

    public function deleteOperator($operatorId) {
        $this->allDbAdapter->delete('pa_operators', $this->allDbAdapter->quoteInto('op_id = ?', $operatorId));
        $this->allDbAdapter->delete('pa_operators_id', $this->allDbAdapter->quoteInto('oi_opid = ?', $operatorId));
    }

    public function updateProductOperator($productId,
                                 $operatorId) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_products_operators', array('po_id'));
        $select->where('po_product_id = ?', $productId);

        if($poId = $this->allDbAdapter->fetchOne($select->__toString())) {
            $where = $this->allDbAdapter->quoteInto('po_id = ?', $poId);
            $this->allDbAdapter->update('pa_products_operators', array('po_operator_id' => $operatorId), $where);
        } else {
            $row = array(
                'po_product_id' => $productId,
                'po_operator_id' => $operatorId
            );
            $this->allDbAdapter->insert('pa_products_operators', $row);
        }
    }
	
    public function getProductOperator($productId) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_products_operators', array('po_operator_id'));
        $select->where('po_product_id = ?', $productId);

        return $this->allDbAdapter->fetchOne($select->__toString());
    }


	
    public function getBundles($productId) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_bundles', array('bn_id',
					  'bn_pid',
					  'bn_bundle_lid',
					  'bn_price'));

        $select->joinLeft('pa_licenses', 'bn_bundle_lid = l_id');
        $select->joinLeft('products', 'p_id = l_pid', 'p_title');
        $select->where('bn_pid = ?', $productId);
        return $this->allDbAdapter->fetchAll($select->__toString());
    }

    public function getBundle($bundleId) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_bundles', array('bn_id',
					  'bn_pid',
					  'bn_bundle_lid',
					  'bn_price'));

        $select->joinLeft('pa_licenses', 'bn_bundle_lid = l_id');
        $select->joinLeft('products', 'p_id = l_pid', 'p_title');
        $select->where('bn_id = ?', $bundleId);
        return $this->allDbAdapter->fetchRow($select->__toString());
    }

    public function getBundles2PricesList($productId) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_bundle_prices', array('bnp_id', 'bnp_bnid', 'bnp_curid', 'bnp_price'));
        $select->joinLeft('pa_bundles', 'bnp_bnid = bn_id');
        $select->joinLeft('pa_currencies', 'bnp_curid = c_id', 'c_code');
        $select->where('bn_pid = ?', $productId);
        $select->order('c_id');

        $prices = $this->allDbAdapter->fetchAll($select->__toString());

	$bundles2prices = array();

	foreach($prices AS $price) {
	    $bundles2prices[$price['bnp_bnid']][] = $price;
	}

	return $bundles2prices;
    }

    public function addBundle($productId,
                              $bundleLicenseId,
                              $price) {
        $row = array(
            'bn_pid' => $productId,
            'bn_bundle_lid' => $bundleLicenseId,
            'bn_price' => $price
        );

        $this->allDbAdapter->insert('pa_bundles', $row);
        $newBundleId = $this->allDbAdapter->lastInsertId();

        $currencies = $this->getCurrenciesRatios();

        foreach($currencies AS $cur) {
            $row = array(
                'bnp_bnid' => $newBundleId,
                'bnp_curid' => $cur['c_id'],
                'bnp_price' => Price::calcPrice($price, $cur['c_ratio'], $cur['c_code'])
            );

            $this->allDbAdapter->insert('pa_bundle_prices', $row);
        }
    }

    public function deleteBundle($bundleId) {
        $this->allDbAdapter->delete('pa_bundles', $this->allDbAdapter->quoteInto('bn_id = ?', $bundleId));
        $this->allDbAdapter->delete('pa_bundle_prices', $this->allDbAdapter->quoteInto('bnp_bnid = ?', $bundleId));
    }

    public function editBundlePrice($bnpId,
                               $newPrice) {
        $set = array(
            'bnp_price' => $newPrice
        );

        $where = $this->allDbAdapter->quoteInto('bnp_id = ?', $bnpId);
        $this->allDbAdapter->update('pa_bundle_prices', $set, $where);
    }

    public function recalculateBundlePrices($bundleId,
                                            $price) {

        $set = array(
            'bn_price' => $price
        );

        $where = $this->allDbAdapter->quoteInto('bn_id = ?', $bundleId);
        $this->allDbAdapter->update('pa_bundles', $set, $where);

        $currencies = $this->getCurrenciesRatios();

        foreach($currencies AS $cur) {
            $set = array(
                'bnp_price' => Price::calcPrice($price, $cur['c_ratio'], $cur['c_code'])
            );

            $where = 'bnp_bnid = '.$bundleId.' AND bnp_curid = '.$cur['c_id'];
            $this->allDbAdapter->update('pa_bundle_prices', $set, $where);
        }
    }

    public function addAdditionalFeature($text,
                                         $defaultPrice,
                                         $contractId,
                                         $pricePercent) {
        if($contractId == null )
        {
            $contractId = 0;
        }
                                             
                                             
        $row = array(
            'af_text' => $text,
            'af_default_price' => $defaultPrice,
          //  'af_contract_id' => $contractId,
            'af_price_percent' => $pricePercent
        );

        $this->allDbAdapter->insert('pa_adfeatures', $row);
    }

    public function getAdditionalFeatures() {

        $select = $this->allDbAdapter->select();
        $select->from('pa_adfeatures', array('af_id', 'af_text', 'af_default_price', 'af_price_percent'));
        $features = $this->allDbAdapter->fetchAll($select->__toString());

        $features2return = array();
        foreach($features AS $feature) {
            $features2return[$feature['af_id']] = $feature;
        }
        return $features2return;
    }

    public function getAdditionalFeaturesContractIds() {
        $select = $this->allDbAdapter->select();
        $select->from('pa_adfeatures_contracts', array('ac_id', 'ac_operator_id', 'ac_contract_id', 'ac_adfeature_id'));
        $contractIds = $this->allDbAdapter->fetchAll($select->__toString());

        $ids2return = array();
        foreach($contractIds AS $id) {
            $ids2return[$id['ac_adfeature_id']][$id['ac_operator_id']] = $id;
        }
        return $ids2return;
    }

    public function getAdditionalFeature($id) {
        $select = $this->allDbAdapter->select();
        $select->from('pa_adfeatures', array('af_id', 'af_text', 'af_default_price', 'af_price_percent'));
        $select->where('af_id = ?', $id);
        return $this->allDbAdapter->fetchRow($select->__toString());
    }

    public function updateAdditionalFeature($id,
                                            $text,
                                            $defaultPrice,
                                            $contractId,
                                            $pricePercent) {
        $set = array(
            'af_text' => $text,
            'af_default_price' => $defaultPrice,
         //   'af_contract_id' => $contractId,
            'af_price_percent' => $pricePercent

        );

        $where = $this->allDbAdapter->quoteInto('af_id = ?', $id);
        $this->allDbAdapter->update('pa_adfeatures', $set, $where);


    }

    public function recalculateAdditionalFeaturePrices($afId, $defaultPrice) {

        $currencies = $this->getCurrenciesRatios();

        foreach($currencies AS $cur) {
            $set = array(
                'afp_price' => Price::calcPrice($defaultPrice, $cur['c_ratio'], $cur['c_code'])
            );

            $where = 'afp_afid = '.$afId.' AND afp_curid = '.$cur['c_id'];
            $this->allDbAdapter->update('pa_adfeatures_prices', $set, $where);
        }
    }

    public function deleteAdditionalFeature($afId) {
        $this->allDbAdapter->delete('pa_adfeatures', $this->allDbAdapter->quoteInto('af_id = ?', $afId));
        $this->allDbAdapter->delete('pa_adfeatures_prices', $this->allDbAdapter->quoteInto('afp_afid = ?', $afId));
    }

    public function getAdditionalFeatures2ProductPrices($productId) {

        $select = $this->allDbAdapter->select();
        $select->from('pa_adfeatures_prices', array('afp_id',
                                                    'afp_afid',
                                                    'afp_pid',
                                                    'afp_curid',
                                                    'afp_price'));
        $select->where('afp_pid = ?', $productId);
        $select->order('afp_curid');

        $prices = $this->allDbAdapter->fetchAll($select->__toString());
        $prices2return = array();

        foreach($prices AS $price) {
            $prices2return[$price['afp_afid']][] = $price;
        }

        return $prices2return;
    }

    public function addAdditionalFeature2Product($featureId,
                                                 $productId) {

        $feature = $this->getAdditionalFeature($featureId);

        $currencies = $this->getCurrenciesRatios();

        foreach($currencies AS $cur) {
            $row = array(
                'afp_afid' => $featureId,
                'afp_pid' => $productId,
                'afp_curid' => $cur['c_id'],
                'afp_price' => Price::calcPrice($feature['af_default_price'], $cur['c_ratio'], $cur['c_code'])
            );

            $this->allDbAdapter->insert('pa_adfeatures_prices', $row);
        }

    }

    public function delAdditionalFeature2Product($featureId,
                                                 $productId) {
        $where = 'afp_afid = '.$featureId.' AND afp_pid = '.$productId;
        $this->allDbAdapter->delete('pa_adfeatures_prices', $where);
    }

    public function editOfferPrice($afpId,
                          $newPrice) {
        $set = array(
            'afp_price' => $newPrice
        );

        $where = $this->allDbAdapter->quoteInto('afp_id = ?', $afpId);
        $this->allDbAdapter->update('pa_adfeatures_prices', $set, $where);
    }

    public function addCoupon($name,
                              $code,
                              $percent,
                              $date,
                              $operatorId,
                              $validlicIds,
                              $unvalidlicIds) {

        $row = array(
            'cup_name' 			=> trim($name),
            'cup_code' 			=> trim($code),
            'cup_opid' 			=> $operatorId,
            'cup_percent' 		=> intval($percent),
            'cup_date' 			=> $date,
            'cup_validlic' 		=> serialize($validlicIds),
            'cup_unvalidlic' 	=> serialize($unvalidlicIds),
            'cup_blocked' 		=> 'N'
        );


        $this->allDbAdapter->insert('pa_coupons', $row);
    }

    public function getCoupons($byProduct, $byLicense) {

    	if ($byProduct == -1 && $byLicense == -1) {  // WTF?! ���������! ��� �� ��� ������ ��??? ������ �������� ������ ���� NULL.
	    $select = $this->allDbAdapter->select();
            $select->from('pa_coupons', array('cup_id',
                                          'cup_name',
                                          'cup_opid',
                                          'cup_percent',
                                          'cup_date' => 'SUBSTR(cup_date, 1, 10)',
                                          'cup_validlic',
                                          'cup_unvalidlic',
                                          'cup_blocked',
                                          'cup_code'));
            $select->order('cup_name');
            $coupons = $this->allDbAdapter->fetchAll($select->__toString());
	}

    	if ($byProduct != -1) {
		$select = $this->allDbAdapter->select();
        	$select->from('pa_licenses', array('l_id'));
        	$select->where('l_pid = '.$byProduct);
        	$lIds = $this->allDbAdapter->fetchAll($select->__toString());

		$select = $this->allDbAdapter->select();
        	$select->from('pa_coupons', array(	'cup_id',
            	                              	'cup_name',
            				      				'cup_opid',
                                              	'cup_percent',
                                              	'cup_date' => 'SUBSTR(cup_date, 1, 10)',
                                              	'cup_validlic',
                                              	'cup_unvalidlic',
                                              	'cup_blocked',
                                              	'cup_code'));
        	$select->order('cup_name');
        	$coupons = $this->allDbAdapter->fetchAll($select->__toString());

        	foreach ($coupons as $key => $val){
        		$coupLic = unserialize($val['cup_validlic']);
        		foreach ($lIds as $k => $v){
        			if(in_array($v['l_id'],$coupLic)){
        				$couponsA[] = $val;
        				break;
	       			}
        		}
        	}
			$coupons = $couponsA;
	   	}

    	if ($byLicense != -1) {
			$select = $this->allDbAdapter->select();
        	$select->from('pa_coupons', array('cup_id',
            	                              'cup_name',
            				      'cup_opid',
                                              'cup_percent',
                                              'cup_date' => 'SUBSTR(cup_date, 1, 10)',
                                              'cup_validlic',
                                              'cup_unvalidlic',
                                              'cup_blocked',
                                              'cup_code'));
        	$select->order('cup_name');
        	$coupons = $this->allDbAdapter->fetchAll($select->__toString());

        	foreach ($coupons as $key => $val){
        		$coupLic = unserialize($val['cup_validlic']);
        		if(in_array($byLicense,$coupLic)){
        			$couponsA[] = $val;
           		}
	       	}
			$coupons = $couponsA;
	   	}

        for($i = 0; $i < count($coupons); $i++) {
            $coupons[$i]['cup_validlic']   = unserialize($coupons[$i]['cup_validlic']);
            $coupons[$i]['cup_unvalidlic'] = unserialize($coupons[$i]['cup_unvalidlic']);
        }

        return $coupons;
    }

    public function getCoupon($couponId)
	{
        $select = $this->allDbAdapter->select();
        $select->from('pa_coupons', array('cup_id',
                                          'cup_name',
                                          'cup_opid',
                                          'cup_percent',
                                          'cup_date' => 'SUBSTR(cup_date, 1, 10)',
                                          'cup_validlic',
                                          'cup_unvalidlic',
                                          'cup_quantity',
                                          'cup_blocked',
                                          'cup_code'));
        
		$select->where('cup_id = ?', $couponId);
        
		$coupon 		= $this->allDbAdapter->fetchRow($select->__toString());

        $cupValidlic 	= unserialize($coupon['cup_validlic']);
        $cupUnvalidlic 	= unserialize($coupon['cup_unvalidlic']);
        $cupQuantity 	= unserialize($coupon['cup_quantity']);

        $coupon['cup_validlic'] 	= array();
        $coupon['cup_unvalidlic'] 	= array();
        $coupon['cup_quantity'] 	= array();

        if(!empty($cupValidlic))
		{
            foreach($cupValidlic AS $cup)
			{
                $coupon['cup_validlic'][$cup] = $cup;
            }
        }

        if(!empty($cupUnvalidlic))
		{
            foreach($cupUnvalidlic AS $cup)
			{
                $coupon['cup_unvalidlic'][$cup] = $cup;
            }
        }

		if($cupQuantity)
		{		
			foreach($cupQuantity AS $licenseId => $quantity)
			{
				$coupon['cup_quantity'][$licenseId] = $quantity;
			}
		}	
        return $coupon;
    }

    public function editCoupon($id,
                                 $name,
                                 $code,
                                 $percent,
                                 $date,
                                 $operatorId,
                                 $validlicIds,
                                 $unvalidlicIds,
                                 $blocked) {

        $row = array(
            'cup_name' => $name,
            'cup_code' => $code,
            'cup_opid' => $operatorId,
            'cup_percent' => $percent,
            'cup_date' => $date,
            'cup_validlic' => serialize($validlicIds),
            'cup_unvalidlic' => serialize($unvalidlicIds),
            'cup_blocked' => ($blocked ? 'Y' :'N')
        );


        $where = $this->allDbAdapter->quoteInto('cup_id = ?', $id);
        $this->allDbAdapter->update('pa_coupons', $row, $where);
    }

    public function delCoupon( $couponId ) {
        $where = $this->allDbAdapter->quoteInto('cup_id = ?', $couponId);
        $this->allDbAdapter->delete('pa_coupons', $where);
    }

    public function editCouponLicensesQuntities($id, $quantity) {
        $row = array(
            'cup_quantity' => serialize($quantity)
        );

        $where = $this->allDbAdapter->quoteInto('cup_id = ?', $id);
        $this->allDbAdapter->update('pa_coupons', $row, $where);
    }

    public function blockUnblockCoupon($cup_id,$act) {

	$set = array(
	    'cup_blocked'      => $act
        );
        $this->allDbAdapter->update('pa_coupons', $set, 'cup_id = '.$cup_id);
		($act == 'Y')?( $response = 0):( $response = 1);
		echo $response;



	}
}


?>
