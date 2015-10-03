<?php

/* Serge Borbit 16.03.09*/
include_once('models/purchase.php');

class PurchaseLinksBuilder {

    private $allDbAdapter;

    public function __construct() {
	$config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();

	$this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
	$this->allDbAdapter->query('SET NAMES utf8');
    }

    public function __destruct() {
        $this->allDbAdapter = NULL;
    }

    public function getLinks() {

        $select = $this->allDbAdapter->select();
	$select->from('pa_plimus_links', array('link_id',
                                               'link_address',
                                               'link_data'));
        $select->order('link_address');
        return $this->allDbAdapter->fetchAll($select->__toString());

    }

    public function addLink($address,
                            $licenses,
                            $currencyId,
                            $couponId = NULL,
                            $backupCD = FALSE,
                            $lifetimeUpgrades = FALSE,
                            $emailSupport = FALSE,
                            $techSupport = FALSE,
                            $techSupportPrice = NULL,
                            $language = 'en',
			    $theme = '661554') {

	$purchaseHandler = new Purchase();
	$currencies = $purchaseHandler->getCurrenciesRatios(true);

        $linkStr = 'bCur='.$currencies[$currencyId]['c_code'];
	$linkStr .= '&templateId='.$theme;
        $linkStr .= '&ald=Y';

        $totalPrice = 0;
        $mainLicenseId = 0;
        $lifetimeUpgradesId = 1;

        $i = 0;

        foreach($licenses AS $license => $quantity) {

            $select = $this->allDbAdapter->select();
            $select->from('pa_licenses', array('l_price'));
	    $select->joinLeft('pa_operators_id', 'l_id = oi_lid', array('oi_price_id'));

            $select->where('l_id = ?', $license);
	    $select->where('oi_operator_id = 3');

	    if($currencyId != 12) {
		$select->joinLeft('pa_prices', 'l_id = pr_lid', array('pr_price'));
		$select->where('pr_curid = ?', $currencyId);
	    }

            $res = $this->allDbAdapter->fetchRow($select->__toString());

            $price = ($currencyId != 12 ? $res['pr_price'] : $res['l_price']);
            $totalPrice += $price * $quantity;

            if($i == 0) {

                $linkStr .= '&contractId='.$res['oi_price_id'];
                $linkStr .= '&quantity='.$quantity;
                $linkStr .= '&overridePrice='.$price;

                $mainLicenseId = $license;

            } else {

                $linkStr .= '&promoteContractId'.($i - 1).'='.$res['oi_price_id'];
                $linkStr .= '&promoteContractFlag'.($i - 1).'=Y';
                $linkStr .= '&addPromoteContract'.($i - 1).'=Y';
                $linkStr .= '&promoteQuantity'.($i - 1).'='.$quantity;
                $linkStr .= '&promoteOverridePrice'.($i - 1).'='.$price;
            }

            $i++;

        }

        $offersIds = array();

        if($lifetimeUpgrades) { array_push($offersIds, 2); }
        if($emailSupport) { array_push($offersIds, 4); }
        if($techSupport) { array_push($offersIds, 5); }

        if(!empty($offersIds)) {
            $select = $this->allDbAdapter->select();
            $select->from('pa_adfeatures', array('af_id',
                                                 'af_default_price',
                                                 'af_price_percent'));

	    $select->joinLeft('pa_adfeatures_contracts', 'ac_adfeature_id = af_id', array('ac_contract_id'));

            $select->where('af_id IN(?)', $offersIds);
	    $select->where('ac_operator_id = 3');

	    if($currencyId != 12) {
		$select->joinLeft('pa_adfeatures_prices', 'afp_afid = af_id', array('afp_price'));
		$select->joinLeft('pa_licenses', 'l_pid = afp_pid');
		$select->where('afp_curid = ?', $currencyId);
		$select->where('l_id = ?', $mainLicenseId);
	    }

            $offers = $this->allDbAdapter->fetchAll($select->__toString());

            foreach($offers AS $offer) {

                if($i == 0) {

                    $linkStr .= '&contractId='.$offer['ac_contract_id'];
                    $linkStr .= '&quantity=1';
                    $linkStr .= '&overridePrice='.$price;

                    if($offer['af_id'] == 5 && isset($techSupportPrice)) {
                        $linkStr .= '&overridePrice='.$techSupportPrice;
                    } elseif($offer['af_price_percent']) {
                        $linkStr .= '&overridePrice='.($totalPrice / 100 * $offer['af_price_percent']);
                    } else {
                        $offerPrice = ($currencyId == 12 ? $offer['af_default_price'] : $offer['afp_price']);
                        $linkStr .= '&overridePrice='.$offerPrice;
                    }

                } else {
                    $linkStr .= '&promoteContractId'.($i - 1).'='.$offer['ac_contract_id'];
                    $linkStr .= '&promoteContractFlag'.($i - 1).'=Y';
                    $linkStr .= '&addPromoteContract'.($i - 1).'=Y';
                    $linkStr .= '&promoteQuantity'.($i - 1).'=1';

                    if($offer['af_id'] == 5 && isset($techSupportPrice)) {
                        $linkStr .= '&promoteOverridePrice'.($i - 1).'='.$techSupportPrice;
                    } elseif($offer['af_price_percent']) {
                        $linkStr .= '&promoteOverridePrice'.($i - 1).'='.($totalPrice / 100 * $offer['af_price_percent']);
                    } else {
                        $offerPrice = ($currencyId == 12 ? $offer['af_default_price'] : $offer['afp_price']);
                        $linkStr .= '&promoteOverridePrice'.($i - 1).'='.$offerPrice;
                    }

                    $i++;
                }
            }
        }

        if($i > 1) {
            $linkStr .= '&numberOfPromotionContract='.($i - 1);
        }

        if($backupCD) {
            $linkStr .= '&addCD=Y';
        }

        if(isset($couponId) && strlen($couponId)) {
            $coupon = $purchaseHandler->getCoupon($couponId);
            $linkStr .= '&couponCode='.$coupon['cup_code'];
        }

	if(isset($language)) {
	    if($language == 'en') {
		$linkStr .= '&language=ENGLISH';
	    } else if($language == 'fr') {
		$linkStr .= '&language=FRENCH';
	    } else if($language == 'de') {
		$linkStr .= '&language=GERMAN';
	    }
	}

	$row = array(
	    'link_address' => $address,
	    'link_data' => $linkStr
	);


	$this->allDbAdapter->insert('pa_plimus_links', $row);
        return $this->allDbAdapter->lastInsertId();

    }

    public function checkAddress($address) {
        $select = $this->allDbAdapter->select();
	$select->from('pa_plimus_links', array('link_id'));
        $select->where('link_address = ?', $address);
        return $this->allDbAdapter->fetchOne($select->__toString());
    }

    public function deleteLink($linkId) {
        $where = $this->allDbAdapter->quoteInto('link_id = ?', $linkId);
        $this->allDbAdapter->delete('pa_plimus_links', $where);
    }

}
?>