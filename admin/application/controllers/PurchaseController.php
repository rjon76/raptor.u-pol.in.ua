<?php

include_once('models/products.php'); 
include_once('models/purchase.php');
//include_once('models/plimusLinksBuilder.php');

class PurchaseController extends MainApplicationController
{
    private $isAjax;
    private $purchaseHandler;

    public function init()
    {
        parent::init();
        $this->isAjax 			= FALSE;
		$this->purchaseHandler 	= new Purchase();

        $this->tplVars['header']['actions']['names'] = array(
	        	array('name' => 'products', 	'menu_name' => 'Products'),
    	        array('name' => 'coupons', 		'menu_name' => 'Coupons'),
        	    array('name' => 'additional', 	'menu_name' => 'Additional offers'),
            	array('name' => 'operators', 	'menu_name' => 'Operators'),
	    		array('name' => 'currencies', 	'menu_name' => 'Currnecies'),
	    		array('name' => 'builder', 		'menu_name' => 'Purchase Links Builder'),
	    		array('name' => 'downloadlinks', 		'menu_name' => 'Download Links Builder')				
        );
    }

    public function __destruct()
    {
		if(!$this->isAjax)
		{
           $this->display();
        }
		$this->isAjax = NULL;
        parent::__destruct();
    }

    public function indexAction()
    {
        $this->_redirect('/purchase/products/');
    }

    public function productsAction()
    {
        $products 						= new AdminProducts();
		$this->tplVars['productsList'] 	= $products->getProducts();

        array_push($this->tplVars['page_css'], 'pages.css');
        array_push($this->viewIncludes, 'purchase/productsList.tpl');
    }

    public function pricesAction()
    {
        if($this->_hasParam('id'))
        {
            $productId = $this->_getParam('id');

            if ($this->_request->isPost())
            {
                if($this->_request->getPost('addLicense'))
                {
                    $this->purchaseHandler->addLicense($this->_request->getPost('name'),
                                                       $this->_request->getPost('parent'),
                                                       $productId,
                                                       $this->_request->getPost('price'),
                                                       $this->_request->getPost('type'),
                                                       $this->_request->getPost('usernumber'),
                                                       $this->_request->getPost('min_usernumber'),
                                                       $this->_request->getPost('users_in_license'),
                                                       $this->_request->getPost('default'),
                                                       $this->_request->getPost('wiki_link'));
                }

                if($this->_request->getPost('updatePrices'))
                {
                    $licenses = $this->purchaseHandler->getLicensesList($productId);

                    foreach($licenses AS $license)
                    {

                        $this->purchaseHandler->updateLicense(
                            $license['l_id'], //$licenseId,
							NULL, //$name
                            NULL, //$parentId 
                            $productId, //$productId 
                            $this->_request->getPost('usd_price_'.$license['l_id']), //$price 
                            NULL, //$type
                            NULL, //$usernumber
                            NULL, //$minUsernumber
                            NULL, //$usersInLicense						
                            $this->_request->getPost('order_'.$license['l_id']), //$order
                           ($this->_request->getPost('default') == $license['l_id'] ? TRUE : FALSE), //$defaul
                           ($this->_request->getPost('blocked_'.$license['l_id']) ? TRUE : FALSE) //$blocked
                        );
                        if($this->_request->getPost('usd_price_'.$license['l_id']) != $license['l_price'])
                        {
                            $this->purchaseHandler->recalculatePrices($productId, $license['l_id']);
                        }
                    }
                }

                if($this->_request->getPost('addOperator'))
                {
                    $this->purchaseHandler->addOperator2License2CurrencyId(
                        $this->_request->getPost('license'),
                        $this->_request->getPost('currency'),
                        $this->_request->getPost('priceId'),
                       ($this->_request->getPost('default') ? TRUE : FALSE),
                        $this->_request->getPost('operatorId')
                    );
                }

                if($this->_request->getPost('updateOperators'))
                {
                    $op2lic2curs = $this->purchaseHandler->getOperator2License2CurrencyIds($productId);

                    foreach($op2lic2curs AS $row)
                    {
                        $this->purchaseHandler->updateOperator2License2CurrencyId(
                            $row['oi_id'],
                           ($this->_request->getPost('default') == $row['oi_id'] ? TRUE : FALSE),
                           ($this->_request->getPost('blocked_'.$row['oi_id']) ? TRUE : FALSE),
                            $this->_request->getPost('priceid_'.$row['oi_id'])
                        );
                    }
                }

                if($this->_request->getPost('updateOperator'))
                {
                    $this->purchaseHandler->updateProductOperator(
                        $productId,
                        $this->_request->getPost('operator_id')
                    );
                }
            }

            $products = new AdminProducts();

            $olcIds = $this->purchaseHandler->getOperator2License2CurrencyIds($productId);

            foreach($olcIds AS $olcId)
            {
                $this->tplVars['purchase']['operator2license'][$olcId['oi_operator_id']][] = $olcId;
            }

            $this->tplVars['purchase']['pricesList'] 	= $this->purchaseHandler->getCurrencies2PricesList($productId);
            $this->tplVars['purchase']['licensesList'] 	= $this->purchaseHandler->getLicensesList($productId);

            $operatorsList = $this->purchaseHandler->getOperatorsList();

            foreach($operatorsList AS $operator)
            {
                $this->tplVars['purchase']['operatorsList'][$operator['op_id']] = $operator;
            }

            $this->tplVars['purchase']['productOperatorId'] = $this->purchaseHandler->getProductOperator($productId);
            $this->tplVars['purchase']['currenciesList'] = $this->purchaseHandler->getCurrenciesRatios(true);
            $this->tplVars['purchase']['productTitle'] = $products->getProduct($productId);

            $this->tplVars['header']['actions']['names'][] = array('name' => 'prices', 'menu_name' => 'Edit prices', 'params' => array('id' => $productId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'bundles', 'menu_name' => 'Edit bundles', 'params' => array('id' => $productId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'additional2product', 'menu_name' => 'Edit product additional offers prices', 'params' => array('id' => $productId));

            array_push($this->tplVars['page_css'], 'pages.css');
            array_push($this->tplVars['page_js'], 'purchase.js');
            array_push($this->viewIncludes, 'purchase/pricesEdit.tpl');
            array_push($this->viewIncludes, 'purchase/pricesAdd.tpl');
            array_push($this->viewIncludes, 'purchase/operators2productEdit.tpl');
        }
    }

    public function couponsAction() {
    	$byProduct = -1;
        $byLicense = -1;

        if ($this->_request->isPost()) {
            if($this->_request->getPost('addCoupon')) {
                $this->purchaseHandler->addCoupon(
                    $this->_request->getPost('name'),
                    $this->_request->getPost('code'),
                    $this->_request->getPost('percent'),
                    $this->_request->getPost('date'),
                    $this->_request->getPost('operator'),
                    $this->_request->getPost('valid_licenses'),
                    $this->_request->getPost('unvalid_licenses')
                );
            }

            if($this->_request->getPost('sortCoupons')) {
            	$by = $this->_request->getPost('by');

	            if($by == 'by_license' ){
	          	$byLicense = $this->_request->getPost('by_license');
	                $this->tplVars['purchase']['couponLicenseSelected'] = $byLicense;
		    }

		    if($by == 'by_product' ){
            		$byProduct = $this->_request->getPost('by_product');
            		$this->tplVars['purchase']['couponProductSelected'] = $byProduct;
		    }

		$coupns	= $this->purchaseHandler->getCoupons($byProduct,$byLicense);
            }
        }

		$coupns = $this->purchaseHandler->getCoupons($byProduct,$byLicense);

        $products = new AdminProducts();

        $this->tplVars['purchase']['operatorsList'] = $this->purchaseHandler->getOperatorsList();
        $this->tplVars['purchase']['licensesList'] 	= $this->purchaseHandler->getLicenses2ProductsList();
        $this->tplVars['purchase']['couponsList'] 	= $coupns;
        $this->tplVars['purchase']['productsList'] 	= $products->getProducts();
               
        array_push($this->tplVars['page_js'],  'jquery-1.8.3.js');
        array_push($this->tplVars['page_js'],  'purchase.js');
        array_push($this->tplVars['page_js'],  'jquery.datepicker.js');
        array_push($this->tplVars['page_css'], 'jquery-ui-themeroller.css');
        array_push($this->tplVars['page_css'], 'pages.css');

        array_push($this->viewIncludes, 'purchase/couponsList.tpl');
        array_push($this->viewIncludes, 'purchase/couponAdd.tpl');
    }

    public function editcouponAction() {
        if($this->_hasParam('id')) {
            $couponId = $this->_getParam('id');

            if ($this->_request->isPost()) {
                if($this->_request->getPost('editCoupon')) {
                    $this->purchaseHandler->editCoupon(
                        $couponId,
                        $this->_request->getPost('name'),
                        $this->_request->getPost('code'),
                        $this->_request->getPost('percent'),
                        $this->_request->getPost('date'),
                        $this->_request->getPost('operator'),
                        $this->_request->getPost('valid_licenses'),
                        $this->_request->getPost('unvalid_licenses'),
                       ($this->_request->getPost('blocked') ? TRUE : FALSE)
                    );

                }

                if($this->_request->getPost('editQuantities')) {
                    $coupon = $this->purchaseHandler->getCoupon($couponId);
                    $quantities = array();

                    foreach($coupon['cup_validlic'] AS $licenseId => $quantity) {
                        $quantities[$licenseId]['min'] = $this->_request->getPost('minqnt_'.$licenseId);
                        $quantities[$licenseId]['max'] = $this->_request->getPost('maxqnt_'.$licenseId);
                    }

                    $this->purchaseHandler->editCouponLicensesQuntities($couponId, $quantities);
                }
            }
            array_push($this->tplVars['page_js'], 'jquery-1.8.3.js');
            array_push($this->tplVars['page_js'], 'jquery.datepicker.js');
            array_push($this->tplVars['page_css'], 'jquery-ui-themeroller.css');

            $products = new AdminProducts();

            $this->tplVars['purchase']['productsList'] = $products->getProducts();
            $this->tplVars['purchase']['operatorsList'] = $this->purchaseHandler->getOperatorsList();
            $this->tplVars['purchase']['licensesList'] = $this->purchaseHandler->getLicenses2ProductsList();
            $this->tplVars['purchase']['val'] = $this->purchaseHandler->getCoupon($couponId);
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editcoupon', 'menu_name' => 'Edit coupon', 'params' => array('id' => $couponId));

            array_push($this->viewIncludes, 'purchase/couponEdit.tpl');
            array_push($this->viewIncludes, 'purchase/couponLicensesQuantities.tpl');
        }
    }

    public function delcouponAction() {
        if($this->_hasParam('id')) {
            $couponId = $this->_getParam('id');
			$this->purchaseHandler->delCoupon( $couponId );
        }
        $this->_redirect('/purchase/coupons/');
    }

    public function licensesAction() {

    }

    public function licenseAction()
    {
        if($this->_hasParam('id'))
        {
            $licenseId = $this->_getParam('id');

            if($this->_request->isPost())
            {
                $this->purchaseHandler->updateLicense(
                    $licenseId,
                    $this->_request->getPost('name'),
                    $this->_request->getPost('parent'),
                    NULL,
                    NULL,
                    $this->_request->getPost('type'),
                    $this->_request->getPost('usernumber'),
                    $this->_request->getPost('min_usernumber'),
                    $this->_request->getPost('users_in_license'),
                    NULL,
                    NULL,
                    NULL,
                    $this->_request->getPost('wiki_link')
                );
            }

            $licenseData 	= $this->purchaseHandler->getLicense($licenseId);
            $licensesList 	= $this->purchaseHandler->getLicensesList($licenseData['data']['l_pid']);
            
            $this->tplVars['license']['data'] 			= $licenseData['data'];
            $this->tplVars['license']['licensesList'] 	= $licensesList;
            $this->tplVars['license']['laguages'] 		= $languages;

            $products = new AdminProducts();
            $this->tplVars['license']['productTitle'] = $products->getProduct($licenseData['data']['l_pid']);

            $this->tplVars['page_js'][] = 'tiny_mce/tiny_mce.js';
            $this->tplVars['header']['actions']['names'][] = array('name' => 'prices', 'menu_name' => 'Edit prices', 'params' => array('id' => $licenseData['data']['l_pid']));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'bundles', 'menu_name' => 'Edit bundles', 'params' => array('id' => $licenseData['data']['l_pid']));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'additional2product', 'menu_name' => 'Edit product additional offers prices', 'params' => array('id' => $licenseData['data']['l_pid']));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'license', 'menu_name' => 'Edit license', 'params' => array('id' => $licenseId));

            array_push($this->viewIncludes, 'purchase/licenseEdit.tpl');
        }
    }

    public function currenciesAction() {

	if($this->_request->isPost()) {
	    $newRatios = array(
		'eur' => $this->_request->getPost('eur'),
		'gbp' => $this->_request->getPost('gbp'),
		'jpy' => $this->_request->getPost('jpy'),
		'aud' => $this->_request->getPost('aud'),
		'cad' => $this->_request->getPost('cad'),
		'cny' => $this->_request->getPost('cny'),
		'nok' => $this->_request->getPost('nok'),
		'sek' => $this->_request->getPost('sek'),
		'pln' => $this->_request->getPost('pln'),
		'rub' => $this->_request->getPost('rub'),
		'chf' => $this->_request->getPost('chf')
	    );

	    $recalcCodes = $this->_request->getPost('recalc');

	    $this->purchaseHandler->updateCurrenciesRatios($newRatios);

            if(!empty($recalcCodes)) {
		$this->purchaseHandler->recalculatePrices(NULL, NULL, $recalcCodes);
	    }

	}

	$curRatios = $this->purchaseHandler->getCurrenciesRatios();

	foreach($curRatios AS $curRatio) {
	    $this->tplVars['purchase']['val'][strtolower($curRatio['c_code'])] = $curRatio['c_ratio'];
	}

	array_push($this->viewIncludes, 'purchase/currenciesEdit.tpl');
    }

    public function operatorsAction() {

        $languages = $this->purchaseHandler->getLanguagesList();

        if($this->_request->isPost()) {
            if($this->_request->getPost('addOperator')) {

                $operatorLangIds = array();

                foreach($languages AS $lang) {
                    if(strlen($this->_request->getPost('lang_'.$lang['l_code'].'_id'))) {
                        $operatorLangIds[$lang['l_code']] = $this->_request->getPost('lang_'.$lang['l_code'].'_id');
                    }
                }

                $this->purchaseHandler->addOperator(
                    $this->_request->getPost('name'),
                    $this->_request->getPost('link'),
                   ($this->_request->getPost('default') ? TRUE : FALSE),
                    $operatorLangIds
                );
            }
        }


        $this->tplVars['operators']['operatorsList'] = $this->purchaseHandler->getOperatorsList();
        $this->tplVars['operators']['languages'] = $languages;


        array_push($this->viewIncludes, 'purchase/operatorsList.tpl');
        array_push($this->viewIncludes, 'purchase/operatorsAdd.tpl');
    }

    public function operatorAction()
    {
        if($this->_hasParam('id'))
        {
            $operatorId = $this->_getParam('id');
            $languages 	= $this->purchaseHandler->getLanguagesList();

            if($this->_request->isPost())
            {
                $operatorLangIds = array();

                foreach($languages AS $lang)
                {
                    if(strlen($this->_request->getPost('lang_'.$lang['l_code'].'_id')))
                    {
                        $operatorLangIds[$lang['l_code']] = $this->_request->getPost('lang_'.$lang['l_code'].'_id');
                    }
                }

                $this->purchaseHandler->updateOperator($operatorId,
                                                       $this->_request->getPost('name'),
                                                       $this->_request->getPost('link'),
                                                       ($this->_request->getPost('default') ? TRUE : FALSE),
                                                       $operatorLangIds,
                                                       ($this->_request->getPost('blocked') ? TRUE : FALSE));
            }

            $operatorData = $this->purchaseHandler->getOperator($operatorId);

            if(!empty($operatorData['op_langs'])) {
                $operatorData['op_langs'] = unserialize($operatorData['op_langs']);
            }

            $this->tplVars['operator']['val'] = $operatorData;
            $this->tplVars['operator']['languages'] = $languages;

        }
        $this->tplVars['header']['actions']['names'][] = array('name' => 'operator', 'menu_name' => 'Edit operator', 'params' => array('id' => $operatorId));

        array_push($this->viewIncludes, 'purchase/operatorsEdit.tpl');
    }

    public function deloliAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');

            $oliData = $this->purchaseHandler->getOperator2License2CurrencyId($id);
            $this->purchaseHandler->deleteOperator2License2CurrencyId($id);
            $this->_redirect('/purchase/prices/id/'.$oliData['l_pid'].'/');
        }
    }

    public function dellicenseAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $license = $this->purchaseHandler->getLicense($id);
            $this->purchaseHandler->deleteLicense($id);
            $this->_redirect('/purchase/prices/id/'.$license['data']['l_pid'].'/');
        }
    }

    public function deloperatorAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $this->purchaseHandler->deleteOperator($id);
            $this->_redirect('/purchase/operators/');
        }
    }

    public function bundlesAction() {

        if($this->_hasParam('id')) {
            $productId = $this->_getParam('id');

            $products = new AdminProducts();

            if ($this->_request->isPost()) {
                if($this->_request->getPost('addBundle')) {
                    $this->purchaseHandler->addBundle(
                        $productId,
                        $this->_request->getPost('license'),
                        $this->_request->getPost('price')
                    );
                }

                if($this->_request->getPost('updateBundles')) {
                    $bundles = $this->purchaseHandler->getBundles($productId);

                    foreach($bundles AS $bundle) {
                        if($bundle['bn_price'] != $this->_request->getPost('usd_price_'.$bundle['bn_id'])) {
                            $this->purchaseHandler->recalculateBundlePrices($bundle['bn_id'], $this->_request->getPost('usd_price_'.$bundle['bn_id']));
                        }
                    }
                }
            }

            $this->tplVars['purchase']['bundlesList'] = $this->purchaseHandler->getBundles($productId);
            $this->tplVars['purchase']['pricesList'] = $this->purchaseHandler->getBundles2PricesList($productId);
            $this->tplVars['purchase']['productsList'] = $products->getProducts();
            $this->tplVars['purchase']['licensesList'] = $this->purchaseHandler->getLicenses2ProductsList();
            $this->tplVars['purchase']['productTitle'] = $products->getProduct($productId);

            $this->tplVars['header']['actions']['names'][] = array('name' => 'prices', 'menu_name' => 'Edit prices', 'params' => array('id' => $productId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'bundles', 'menu_name' => 'Edit bundles', 'params' => array('id' => $productId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'additional2product', 'menu_name' => 'Edit product additional offers prices', 'params' => array('id' => $productId));

            array_push($this->tplVars['page_js'], 'purchase.js');

            array_push($this->viewIncludes, 'purchase/bundlesList.tpl');
            array_push($this->viewIncludes, 'purchase/bundlesAdd.tpl');
        }
    }

    public function delbundleAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $bundle = $this->purchaseHandler->getBundle($id);
            $this->purchaseHandler->deleteBundle($id);
            $this->_redirect('/purchase/bundles/id/'.$bundle['bn_pid'].'/');
        }
    }

    public function additionalAction() {

        if($this->_request->isPost()) {
            if($this->_request->getPost('addFeature'))
            {
                $this->purchaseHandler->addAdditionalFeature(
                    $this->_request->getPost('text'),
                    $this->_request->getPost('default_price'),
                    $this->_request->getPost('contract_id'),
                    $this->_request->getPost('price_percent'));
           }
        }

        $this->tplVars['purchase']['featuresList'] = $this->purchaseHandler->getAdditionalFeatures();
        $this->tplVars['purchase']['featuresContractIds'] = $this->purchaseHandler->getAdditionalFeaturesContractIds();

        array_push($this->viewIncludes, 'purchase/additionalList.tpl');
        array_push($this->viewIncludes, 'purchase/additionalAdd.tpl');
    }

    public function editadditionalAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');

            if($this->_request->isPost()) {
                if($this->_request->getPost('updateFeature')) {
                    $feature = $this->purchaseHandler->getAdditionalFeature($id);
                    $defaultPrice = $this->_request->getPost('default_price');

                    $this->purchaseHandler->updateAdditionalFeature(
                        $id,
                        $this->_request->getPost('text'),
                        $defaultPrice,
                        $this->_request->getPost('contract_id'),
                        $this->_request->getPost('price_percent'));

                    if($feature['af_default_price'] != $defaultPrice) {
                        $this->purchaseHandler->recalculateAdditionalFeaturePrices($id, $defaultPrice);
                    }
                }
            }

            $this->tplVars['header']['actions']['names'][] = array('name' => 'editadditional', 'menu_name' => 'Edit additional offer', 'params' => array('id' => $id));
            $this->tplVars['purchase']['val'] = $this->purchaseHandler->getAdditionalFeature($id);
            array_push($this->viewIncludes, 'purchase/additionalEdit.tpl');
        }
    }

    public function deladditionalAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $this->purchaseHandler->deleteAdditionalFeature($id);
            $this->_redirect('/purchase/additional/');
        }

    }

    public function additional2productAction() {
        if($this->_hasParam('id')) {
            $productId = $this->_getParam('id');

            if($this->_request->isPost()) {
                if($this->_request->getPost('addOffer')) {
                    $this->purchaseHandler->addAdditionalFeature2Product(
                        $this->_request->getPost('feature'),
                        $productId);
                }
            }

            $products = new AdminProducts();

            $this->tplVars['purchase']['prices'] = $this->purchaseHandler->getAdditionalFeatures2ProductPrices($productId);
            $this->tplVars['purchase']['features'] = $this->purchaseHandler->getAdditionalFeatures($productId);
            $this->tplVars['header']['actions']['names'][] = array('name' => 'prices', 'menu_name' => 'Edit prices', 'params' => array('id' => $productId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'bundles', 'menu_name' => 'Edit bundles', 'params' => array('id' => $productId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'additional2product', 'menu_name' => 'Edit product additional offers prices', 'params' => array('id' => $productId));

            $this->tplVars['purchase']['productTitle'] = $products->getProduct($productId);

            array_push($this->tplVars['page_css'], 'pages.css');
            array_push($this->tplVars['page_js'], 'purchase.js');

            array_push($this->viewIncludes, 'purchase/additional2productList.tpl');
            array_push($this->viewIncludes, 'purchase/additional2productAdd.tpl');
        }
    }

    public function deladditional2productAction() {
        if($this->_hasParam('id') && $this->_hasParam('pid')) {

            $productId = $this->_getParam('pid');
            $offerId = $this->_getParam('id');

            $this->purchaseHandler->delAdditionalFeature2Product($offerId, $productId);

            $this->_redirect('/purchase/additional2product/id/'.$productId.'/');
        }
    }

    public function ajaxeditpriceAction() {
        $this->isAjax = TRUE;

        if($this->_hasParam('lid') &&
           $this->_hasParam('cid') &&
           $this->_request->isPost()) {

            $licenseId = $this->_getParam('lid');
            $currencyId = $this->_getParam('cid');
            $value = $this->_request->getPost('value');

            $this->purchaseHandler->editPrice(
                $licenseId,
                $currencyId,
                $value
            );

            echo $value;
        }
    }

    public function ajaxeditbundlepriceAction() {
        $this->isAjax = TRUE;

        if($this->_hasParam('id') &&
           $this->_request->isPost()) {

            $bnpId = $this->_getParam('id');
            $value = $this->_request->getPost('value');

            $this->purchaseHandler->editBundlePrice($bnpId, $value);

            echo $value;
        }
    }

    public function ajaxeditofferpriceAction() {
        $this->isAjax = TRUE;

        if($this->_hasParam('id') &&
           $this->_request->isPost()) {

            $afpId = $this->_getParam('id');
            $value = $this->_request->getPost('value');

            $this->purchaseHandler->editOfferPrice($afpId, $value);

            echo $value;
        }
    }

    public function ajaxblockcouponAction() {
    	$this->isAjax = TRUE;
    	$cup_id = $this->_getParam('coupid');
    	$act 	= $this->_getParam('act');
    	$this->purchaseHandler->blockUnblockCoupon($cup_id,$act);
    }

    public function builderAction() {
		include_once('models/plimusLinksBuilder.php');
		$linksBuilder = new PurchaseLinksBuilder();
		$products = new AdminProducts();

		if($this->_request->isPost()) {

            if(!$this->_request->getPost('address') ||
                $linksBuilder->checkAddress($this->_request->getPost('address'))) {
                $this->tplVars['builder']['errors']['address'] = true;
            }

            if(!isset($this->tplVars['builder']['errors'])) {

                $licenses = array();

                //foreach($this->_request->getPost('licenses') AS $licenseId) {
                    //$licenses[$licenseId] = $this->_request->getPost('license_qnt_'.$licenseId);
                    $licenses = $this->_request->getPost('license_qnt');
                //}

                $linksBuilder->addLink($this->_request->getPost('address'),
                                       $licenses,
                                       $this->_request->getPost('currency'),
                                       $this->_request->getPost('coupon'),
                                       $this->_request->getPost('backup_cd'),
                                       $this->_request->getPost('lifetime_upgrades'),
                                       $this->_request->getPost('priority_email_support'),
                                       $this->_request->getPost('premium_tech_support'),
                                       $this->_request->getPost('premium_tech_support_price'),
                                       $this->_request->getPost('language'),
                                       $this->_request->getPost('theme'));
            } else {
                $this->tplVars['builder']['val']['address'] = $this->_request->getPost('address');
                $this->tplVars['builder']['val']['currency'] = $this->_request->getPost('currency');
                $this->tplVars['builder']['val']['coupon'] = $this->_request->getPost('coupon');
                $this->tplVars['builder']['val']['backup_cd'] = $this->_request->getPost('backup_cd');
                $this->tplVars['builder']['val']['lifetime_upgrades'] = $this->_request->getPost('lifetime_upgrades');
                $this->tplVars['builder']['val']['priority_email_support'] = $this->_request->getPost('priority_email_support');
                $this->tplVars['builder']['val']['premium_tech_support'] = $this->_request->getPost('premium_tech_support');
                $this->tplVars['builder']['val']['language'] = $this->_request->getPost('language');
                $this->tplVars['builder']['val']['theme'] = $this->_request->getPost('theme');
            }
	}



	$this->tplVars['builder']['couponsList'] = $this->purchaseHandler->getCoupons(-1, -1); // ��� ���������
	$this->tplVars['builder']['currenciesList'] = $this->purchaseHandler->getCurrenciesRatios(true);
	$this->tplVars['builder']['licensesList'] = $this->purchaseHandler->getLicenses2ProductsList();
	$this->tplVars['builder']['productsList'] = $products->getProducts();
        $this->tplVars['builder']['linksList'] = $linksBuilder->getLinks();

	array_push($this->viewIncludes, 'purchase/builder.tpl');
	array_push($this->viewIncludes, 'purchase/builderLinksList.tpl');
    }

    public function deletelinkAction() {
        if($this->_hasParam('id')) {
			include_once('models/plimusLinksBuilder.php');
            $id = $this->_getParam('id');
            $linksBuilder = new PurchaseLinksBuilder();
            $linksBuilder->deleteLink($id);
            $this->_redirect('/purchase/builder/');
        }
    }

	/*----------------------------*/
	public function downloadlinksAction(){
		include_once('models/downloadLinks.php');	
		$model = new Downloadlinks();	
        if ($this->_request->isPost()) {
	        $model->setAttributes($this->_request->getPost());
			$model->validate();
			if (!$model->hasErrors()){
				if ($dl_link_expired = $model->generateDownloadLink()){
					 $model->setAttribute('dl_link_expired', $dl_link_expired);
				}
			}
			
        }

   //     array_push($this->tplVars['page_js'],  'downloadlinks.js');
        array_push($this->tplVars['page_js'],  'jquery.datepicker.js');
        array_push($this->tplVars['page_css'], 'jquery-ui-themeroller.css');
        array_push($this->tplVars['page_css'], 'pages.css');
        $this->tplVars['model'] = $model;
        array_push($this->viewIncludes, 'purchase/downloadlinks/_form.tpl');


    }

}

?>