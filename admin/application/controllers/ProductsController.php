<?php
include_once('models/controllersHandler.php');
include_once('models/user.php');
include_once('models/products.php');

class ProductsController extends MainApplicationController {

    //private $products = NULL;
    private $isAjax;

    public function init() {
        parent::init();

	$this->isAjax = FALSE;
	$this->tplVars['page_css'][] = 'products.css';
	$this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'List Products'),
        );
	$controllId = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
        $this->tplVars['lvals']['canEdit'] = $this->user->checkWritePerm($controllId);
        $this->tplVars['lvals']['canDelete'] = $this->user->checkDelPerm($controllId);
    }

    public function __destruct() {
	if(!$this->isAjax) {
           $this->display();
        }
	$this->isAjax = NULL;
        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/products/list/');
    }

    public function listAction() {
    $this->tplVars['page_js'][] = 'products.js';
   $this->tplVars['header']['actions']['names'][] = array('name' => 'add', 'menu_name' => 'Add Product');
   $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');	
   $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');	   
	array_push($this->viewIncludes, 'products/productsList.tpl');
	$products = new AdminProducts();
	$this->tplVars['products'] = $products->getProducts();
	unset($products);
    }

    public function editAction() {
	if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id')) {
	    array_push($this->tplVars['page_css'], 'livevalidation.css');
	    array_push($this->tplVars['page_js'], 'livevalidation.js');
	    array_push($this->tplVars['page_css'], 'product_edit.css');
	    array_push($this->tplVars['header']['actions']['names'], array('name' => 'edit', 'menu_name' => 'Edit Product'));
	   $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');	
	   $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');	   
	   $this->tplVars['header']['actions']['names'][] = array('name' => 'changelog', 'menu_name' => 'Edit change log', 'params'=>array('id'=>$this->_getParam('id')));	
       
		$this->tplVars['lvals']['isNewRecord'] = false;			
	    array_push($this->viewIncludes, 'products/productsEdit.tpl');
	    $id = $this->_getParam('id');
	    $this->tplVars['lvals']['postRes'] = 2;
	    $products = new AdminProducts();
	    $this->tplVars['cats'] = $products->getCategories(TRUE);
	    $this->tplVars['plat'] = $products->getPlatforms(TRUE);
	    $this->tplVars['langs'] = $products->getProductLangs(TRUE);
	    $this->tplVars['os'] = $products->getProductOS(TRUE);
	    if ($this->_request->getPost('ispost')) {
			$this->to_log();
			$this->tplVars['product'] = $this->_request->getPost();
			$this->tplVars['cats']['select'] = $this->_request->getPost('p_cat');
			$this->tplVars['plat']['select'] = $this->_request->getPost('p_platform');
			$this->tplVars['langs']['select'] = $this->_request->getPost('p_languages');
			$this->tplVars['os']['select'] = $this->_request->getPost('p_os');
			$this->tplVars['lvals']['postRes'] = $products->setProductById($id, $this->tplVars['product']);
	    } else {
			$this->tplVars['product'] = $products->getProductById($id);
			$this->tplVars['cats']['select'] = array($this->tplVars['product']['p_cat']);
			$this->tplVars['plat']['select'] = array($this->tplVars['product']['p_platform']);
			$this->tplVars['langs']['select'] = explode('|',trim($this->tplVars['product']['p_languages'],'|'));
			$this->tplVars['os']['select'] = explode('|',trim($this->tplVars['product']['p_os'],'|'));
	    }
		}
    }
	
    public function addAction() {
		if($this->tplVars['lvals']['canEdit'])
		 {
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
		    array_push($this->tplVars['page_css'], 'product_edit.css');
		    array_push($this->tplVars['header']['actions']['names'], array('name' => 'add', 'menu_name' => 'Add Product'));
		   $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');	
		   $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');	   

	    	array_push($this->viewIncludes, 'products/productsEdit.tpl');
		    $this->tplVars['lvals']['postRes'] = 2;
		    $products = new AdminProducts();
			$this->tplVars['product'] = array();			
	    	$this->tplVars['cats'] = $products->getCategories(TRUE);
		    $this->tplVars['plat'] = $products->getPlatforms(TRUE);
		    $this->tplVars['langs'] = $products->getProductLangs(TRUE);
		    $this->tplVars['os'] = $products->getProductOS(TRUE);
			$this->tplVars['lvals']['isNewRecord'] = true;	
		    if ($this->_request->getPost('ispost')) {
				$this->tplVars['product'] = $this->_request->getPost();
				$this->tplVars['cats']['select'] = $this->_request->getPost('p_cat');
				$this->tplVars['plat']['select'] = $this->_request->getPost('p_platform');
				$this->tplVars['langs']['select'] = $this->_request->getPost('p_languages');
				$this->tplVars['os']['select'] = $this->_request->getPost('p_os');
		
				$id = $this->tplVars['lvals']['postRes'] = $products->addProduct($this->tplVars['product']);
				$this->to_log($id);
				if ($id > 0)
		            $this->_redirect('/products/edit/id/'.$id);
		    } 
		 }
    }
	
    public function deleteAction() {
        if($this->_hasParam('id') && $this->tplVars['lvals']['canDelete'])
		 {
		    $products = new AdminProducts();
            $products->deleteProduct($this->_getParam('id'));
        }
           $this->_redirect('/products/list/');		
    }

    public function productupAction() {
	$this->isAjax = TRUE;
	if($this->_hasParam('pid') && $this->_hasParam('cid')) {
            Zend_Loader::loadClass('Zend_Json');

            $pId = $this->_getParam('pid');
	    $catId = $this->_getParam('cid');
            $products = new AdminProducts();
	    echo Zend_Json::encode($products->upProduct($catId,$pId));
        }
    }

    public function productdownAction() {
	$this->isAjax = TRUE;
	if($this->_hasParam('pid') && $this->_hasParam('cid')) {
            Zend_Loader::loadClass('Zend_Json');

            $pId = $this->_getParam('pid');
	    $catId = $this->_getParam('cid');
            $products = new AdminProducts();
	    echo Zend_Json::encode($products->downProduct($catId,$pId));
        }
    }

    public function platformsAction()
	{
		include_once('platforms.php');
		$this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');	
		$this->tplVars['header']['actions']['names'][] = array('name' => 'platformadd', 'menu_name' => 'Platform add');	
	    $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');						
		array_push($this->viewIncludes, 'products/platformList.tpl');
		$platforms = new Platforms();
		$this->tplVars['platforms'] = $platforms->selectAll();
		unset($platforms);
    }


    public function platformeditAction()
	{
		
		if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id'))
		 {
    		include_once('platforms.php');
			$id = $this->_getParam('id');
		    $this->tplVars['lvals']['postRes'] = 2;
			$platforms = new Platforms();
			$this->tplVars['platforms'] = $platforms->getById($id); 

			if ($this->_request->getPost('ispost'))
			 {
				$this->to_log();
				$this->tplVars['platforms'] = $this->_request->getPost();
				if (($this->tplVars['lvals']['postRes'] = $platforms->update($id, $this->_request->getPost())) == 0)
				{
//					$this->_redirect('/products/platforms/');
				}
		    }
			
	    	array_push($this->tplVars['page_css'], 'product_edit.css');
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
			$this->tplVars['lvals']['isNewRecord'] = false;
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'platformedit', 'menu_name' => 'Platform edit');	
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');											
		    array_push($this->viewIncludes, 'products/platformEdit.tpl');
			unset($platforms);

		}
		else
			$this->_redirect('/products/platforms/');
    }

    public function platformaddAction()
	{
		if($this->tplVars['lvals']['canEdit'])
		{
			include_once('platforms.php');
		    if ($this->_request->getPost('ispost'))
			{
				$this->to_log();
			    $platforms = new Platforms();
				$this->tplVars['platforms'] = $this->_request->getPost();
				if (($this->tplVars['lvals']['postRes'] = $platforms->add($this->_request->getPost())) > 0)
				{
					$this->_redirect('/products/platforms/');
				}
	   		} 
		    array_push($this->tplVars['page_css'], 'product_edit.css');
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
			$this->tplVars['lvals']['isNewRecord'] = true;			
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'platformadd', 'menu_name' => 'Platform add');	
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');											
		    array_push($this->viewIncludes, 'products/platformEdit.tpl');
		    $this->tplVars['lvals']['postRes'] = 2;
			unset($platforms);
		}
		else
			$this->_redirect('/products/platforms/');
    }
    
	public function platformdeleteAction() {
        if($this->_hasParam('id') && $this->tplVars['lvals']['canDelete'])
		 {
    		include_once('platforms.php');
			$platforms = new Platforms();
            $platforms->delete($this->_getParam('id'));
			unset($platforms);
        }
           $this->_redirect('/products/platforms/');		
    }

    public function osAction()
	{
		include_once('os.php');
	    $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');				
	    $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');	
		$this->tplVars['header']['actions']['names'][] = array('name' => 'osadd', 'menu_name' => 'OS Add');	
		array_push($this->viewIncludes, 'products/osList.tpl');
		$os = new Os();
		$this->tplVars['os'] = $os->selectAll();
		unset($os);
    }


    public function oseditAction()
	{
		
		if($this->tplVars['lvals']['canEdit'] && $this->_hasParam('id'))
		 {
    		include_once('os.php');
			$id = $this->_getParam('id');
		    $this->tplVars['lvals']['postRes'] = 2;
			$os = new Os();
			$this->tplVars['os'] = $os->getById($id); 

			if ($this->_request->getPost('ispost'))
			 {
				$this->to_log();
				$this->tplVars['platforms'] = $this->_request->getPost();
				if (($this->tplVars['lvals']['postRes'] = $os->update($id, $this->_request->getPost())) > 0)
				{
					$this->_redirect('/products/os/');
				}
		    }
			
	    	array_push($this->tplVars['page_css'], 'product_edit.css');
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
			$this->tplVars['lvals']['isNewRecord'] = false;
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');							
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');	
			$this->tplVars['header']['actions']['names'][] = array('name' => 'osedit', 'menu_name' => 'OS Edit');							
		    array_push($this->viewIncludes, 'products/osEdit.tpl');
			unset($os);

		}
		else
			$this->_redirect('/products/os/');
    }

    public function osaddAction()
	{
		if($this->tplVars['lvals']['canEdit'])
		{
			include_once('os.php');
		    if ($this->_request->getPost('ispost'))
			{
				$this->to_log();
			    $os = new Os();
				$this->tplVars['os'] = $this->_request->getPost();
				if (($this->tplVars['lvals']['postRes'] = $os->add($this->_request->getPost())) > 0)
				{
					$this->_redirect('/products/os/');
				}
	   		} 
		    array_push($this->tplVars['page_css'], 'product_edit.css');
			array_push($this->tplVars['page_css'], 'livevalidation.css');
			array_push($this->tplVars['page_js'], 'livevalidation.js');
			$this->tplVars['lvals']['isNewRecord'] = true;			
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'platforms', 'menu_name' => 'Platform list');							
		    $this->tplVars['header']['actions']['names'][] = array('name' => 'os', 'menu_name' => 'OS list');
			$this->tplVars['header']['actions']['names'][] = array('name' => 'osadd', 'menu_name' => 'OS Add');														
		    array_push($this->viewIncludes, 'products/osEdit.tpl');
		    $this->tplVars['lvals']['postRes'] = 2;
			unset($os);
		}
		else
			$this->_redirect('/products/os/');
    }
    
	public function osdeleteAction() {
        if($this->_hasParam('id') && $this->tplVars['lvals']['canDelete'])
		 {
    		include_once('os.php');
			$os = new Os();
            $os->delete($this->_getParam('id'));
			unset($os);
        }
           $this->_redirect('/products/os/');		
    }



    public function featureditAction() {
	if($this->tplVars['lvals']['canEdit']) {
	    array_push($this->tplVars['header']['actions']['names'], array('name' => 'featuredit', 'menu_name' => 'Edit Product\'s Features'));
	    //array_push($this->tplVars['page_js'], 'features.js');
	    array_push($this->viewIncludes, 'products/featuresAdd.tpl');
	    array_push($this->viewIncludes, 'products/featuresList.tpl');
	    if($this->_hasParam('id') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('id');
		$lang = $this->_getParam('lang');
		$saved = ($this->_hasParam('saved') ? $this->_getParam('saved') : '0');
		$products = new AdminProducts();
		$this->tplVars['lvals']['language'] = $lang;
		$this->tplVars['lvals']['product'] = $pId;
		$this->tplVars['lvals']['title'] = $products->getProduct($pId);
		$this->tplVars['lvals']['saved'] = $saved;
		$this->tplVars['features'] = $products->getFeatures($lang,$pId);
	    }
	}
	else {
	    $this->_redirect('/products/list/');
	}
    }

    public function featuredropAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canDelete']) {
	    if($this->_hasParam('pid') && $this->_hasParam('fid') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('pid');
		$fId = $this->_getParam('fid');
		$lang = $this->_getParam('lang');
		$products = new AdminProducts();
		$products->dropFeatures($fId,$lang);
	    }
	}
	$this->_redirect('/products/featuredit/id/'.$pId.'/lang/'.$lang.'/');
    }

    public function featuresaveAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canEdit']) {
	    if($this->_hasParam('pid') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('pid');
		$lang = $this->_getParam('lang');
		$products = new AdminProducts();
		$products->saveFeatures($this->_request->getPost(),$lang);
	    }
	}
	$this->_redirect('/products/featuredit/id/'.$pId.'/lang/'.$lang.'/saved/1/');
    }

    public function featureaddAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canEdit']) {
	    if($this->_hasParam('pid') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('pid');
		$lang = $this->_getParam('lang');
		$products = new AdminProducts();
		$products->addFeatures($pId, $lang, $this->_request->getPost());
	    }
	}
	$this->_redirect('/products/featuredit/id/'.$pId.'/lang/'.$lang.'/saved/2/');
    }

    public function exportAction() {
	if($this->_hasParam('id')) {
	    $products = new AdminProducts();

	    $this->tplVars['header']['actions']['names'][] = array('name' => 'export', 'menu_name' => 'Export new data', 'params' => array('id' => $this->_getParam('id')));
	    array_push($this->viewIncludes, 'products/export.tpl');
            $this->tplVars['export']['message'] = '';
	    if($products->exportNewDataToOldDB($this->_getParam('id'), $this->tplVars['export']['message'])) {
		$this->tplVars['export']['message'] .= '<br/><strong>New data has been exported succesfully!</strong>';
	    } else {
		$this->tplVars['export']['message'] .= '<strong style="color:red;">New data has\'t exported!</strong>';
	    }
	}
    }
/*
    public function featureupAction() {
	$this->isAjax = TRUE;
	if($this->_hasParam('pid') && $this->_hasParam('fid') && $this->_hasParam('lang')) {
            Zend_Loader::loadClass('Zend_Json');

	    $pId = $this->_getParam('pid');
            $fId = $this->_getParam('fid');
	    $lang = $this->_getParam('lang');
            $products = new AdminProducts();
	    echo Zend_Json::encode($products->upFeature($pId, $fId, $lang));
        }
    }

    public function featuredownAction() {
	$this->isAjax = TRUE;
	if($this->_hasParam('pid') && $this->_hasParam('fid') && $this->_hasParam('lang')) {
            Zend_Loader::loadClass('Zend_Json');

            $pId = $this->_getParam('pid');
	    $fId = $this->_getParam('fid');
	    $lang = $this->_getParam('lang');
            $products = new AdminProducts();
	    echo Zend_Json::encode($products->downFeature($pId, $fId, $lang));
        }
    }
*/

    public function demoeditAction() {
	if($this->tplVars['lvals']['canEdit']) {
	    array_push($this->tplVars['header']['actions']['names'], array('name' => 'demoedit', 'menu_name' => 'Edit Product\'s Demolimits'));
	    array_push($this->viewIncludes, 'products/demoAdd.tpl');
	    array_push($this->viewIncludes, 'products/demoList.tpl');
	    if($this->_hasParam('id') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('id');
		$lang = $this->_getParam('lang');
		$saved = ($this->_hasParam('saved') ? $this->_getParam('saved') : '0');
		$products = new AdminProducts();
		$this->tplVars['lvals']['language'] = $lang;
		$this->tplVars['lvals']['product'] = $pId;
		$this->tplVars['lvals']['title'] = $products->getProduct($pId);
		$this->tplVars['lvals']['saved'] = $saved;
		$this->tplVars['demolimits'] = $products->getDemo($lang,$pId);
	    }
	}
	else {
	    $this->_redirect('/products/list/');
	}
    }

    public function demodropAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canDelete']) {
	    if($this->_hasParam('pid') && $this->_hasParam('did') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('pid');
		$dId = $this->_getParam('did');
		$lang = $this->_getParam('lang');
		$products = new AdminProducts();
		$products->dropDemo($dId,$lang);
	    }
	}
	$this->_redirect('/products/demoedit/id/'.$pId.'/lang/'.$lang.'/');
    }

    public function demosaveAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canEdit']) {
	    if($this->_hasParam('pid') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('pid');
		$lang = $this->_getParam('lang');
		$products = new AdminProducts();
		$products->saveDemo($this->_request->getPost(),$lang);
	    }
	}
	$this->_redirect('/products/demoedit/id/'.$pId.'/lang/'.$lang.'/saved/1/');
    }

    public function demoaddAction() {
	$pId = 79; $lang = 'en';
	if($this->tplVars['lvals']['canEdit']) {
	    if($this->_hasParam('pid') && $this->_hasParam('lang')) {
		$pId = $this->_getParam('pid');
		$lang = $this->_getParam('lang');
		$products = new AdminProducts();
		$products->addDemo($pId, $lang, $this->_request->getPost());
	    }
	}
	$this->_redirect('/products/demoedit/id/'.$pId.'/lang/'.$lang.'/saved/2/');
    }


    public function changelogAction() {
    	$this->tplVars['page_js'][] = 'changelog.js';
    	$pId 		= $this->_getParam('id');
    	$products 	= new AdminProducts();
    	if($this->_hasParam('lang')){$lang = $this->_getParam('lang');}else{$lang = 1;}
    	$prChangelog 	= $products->getChangelogById($pId,$lang);
    	$productInfo 	= $products->getProductById($pId);
    	$this->tplVars['prodinfo'] = $productInfo;
    	$this->tplVars['chlogdata'] = $prChangelog;
		$this->tplVars['lvals']['lang'] = $lang;
        $this->tplVars['header']['actions']['names'][] = array('name' => 'changelog',
                                                               'menu_name' => 'Product change log',
                                                               'params' => array('id' => $this->_getParam('id')));
        $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit product', 'params'=>array('id'=>$pId));

		array_push($this->viewIncludes, 'products/changelogList.tpl');

    }

    public function statusAction() {
    	$this->isAjax 	= true;
		$products 		= new AdminProducts();
		if($this->_hasParam('lang')){$lang = $this->_getParam('lang');}else{$lang = 1;}

		echo $products->changeChangelogStatus($this->_getParam('build'),$lang);
    }

    public function changelogsendAction() {
    	$products = new AdminProducts();
    	$buildId = $this->_getParam('build');
    	$productId = $this->_getParam('id');
    	$product = $products->getProductById($productId);
    	$lang = 1;

    	$info = $products->getChangelogByBuildId($buildId, $lang);
    	
	$updateList = '';

    	foreach ($info as $key => $val) {
    	    $updateList .= '- '.$val['pc_text']."\n";
    	}

    	$subscribers = $products->getSubscribers($productId);
    	$products->getSetSendStatus($info);
    	$mess = "Good day to you!

We are glad to inform you that ".$this->company_params['name']."  has released the new version (build) of \"".$product['p_title']."\" - Version ".$product['p_version']." (Build ".$product['p_build']."), release date: ".date('F jS, Y', strtotime($product['p_date_release'])).".

Version fixes and enhancements:
".$updateList."

To learn more about the product please visit: ".$product['p_page_link']."

To download the new version (build), please visit: ".$this->company_params['download_prefix'].$product['p_download']."\n

According to our purchase policy all registered users can download free upgrades within the major version. All further upgrades can be purchased with discount.

If you want to upgrade to the most recent build within one major version, just download the latest setup file from product's download page to install the latest build. Your old registration information will be valid for changing the demo into full version within one major version.

If you are an unregistered user we welcome you to try out and purchase our solutions to discover the full potential of ".$this->company_params['name']." cutting edge software.

Glad to keep you informed,
".$this->company_params['name'];
    	foreach ($subscribers as $key => $data){
	    
	    $headers = "MIME-Version: 1.0\n";
	    $headers .= "X-Priority: 1\n";
	    $headers .= "X-Mailer: PHP mailer (v0.1)\n";
	    $headers .= "X-MSMail-Priority: High\n";
	    $headers .= "From: \"".$this->company_params['name']."\" <".$this->company_params['contact_email'].">\n";

	    mail($data['user_email'], '"'.$product['p_title'].'" - '.$this->company_params['name'].' Updates Notifications', $mess, $headers);
    	}
    	header("Location: ".ADMIN_DIR."/products/changelog/id/".$productId."/");
    }

     /*******************************************/

    public function changelognewAction()
    {
    	$products	 				= new AdminProducts();
    	$data['productId']  		= $this->_getParam('pid');
    	//$data['newChLogStrings']	= array_combine($this->_getParam('chOrder'), $this->_getParam('chItem'));
        $data['newChLogStrings']	= $this->_getParam('chItem'); //italiano, 17/09/2015
    	$data['newChLogNum']		= $this->_getParam('newChLogNum');
    	$products->addChangelog($data);
		$this->_redirect('/products/changelog/id/'.$data['productId'].'/');
    }

	/*******************************************/



    public function changelogdelAction() {
    	$products	= new AdminProducts();
    	$buildId 	= $this->_getParam('build');
    	$productId  = $this->_getParam('id');
    	$products->delChangelog($buildId);
    	$this->_redirect('/products/changelog/id/'.$productId.'/');
    }

    /*******************************************/

    public function changelogeditAction()
    {
    	$products		= new AdminProducts();
    	$buildId 		= $this->_getParam('build');
    	$productId  	= $this->_getParam('id');
   		$lang 			= $this->getParamLang();
    	$info 			= $products->getChangelogByBuildId($buildId,$lang);

    	$info 			= $this->prepareChLogInfo($info);
    	$productInfo 	= $products->getProductById($productId);
//oas($info);
		$this->tplVars['page_js'][] 			= 'changelog.js';
    	$this->tplVars['chlogdata'] 			= $info;
    	$this->tplVars['prodinfo'] 				= $productInfo;
    	$this->tplVars['otherinfo']['build'] 	= $info['pb_build'];
    	$this->tplVars['otherinfo']['buildid'] 	= $info['pb_id'];
    	$this->tplVars['lvals']['lang'] = $lang;

        $this->tplVars['header']['actions']['names'][] = array('name' => 'changelog',
                                                               'menu_name' => 'Product change log',
                                                               'params' => array('id' => $this->_getParam('id')));

        $this->tplVars['header']['actions']['names'][] = array('name' => 'changelogedit',
                                                               'menu_name' => 'Product change log edit',
                                                               'params' => array('build' => $this->_getParam('build'),
                                                                                 'id' => $this->_getParam('id')));

    	array_push($this->viewIncludes, 'products/changelogEdit.tpl');
    }


	private function prepareChLogInfo($infos)
	{
		$info = array();
		$string = '';

		foreach( $infos as $k => $val )
		{
			$info 		 = $val;
			$string 	.= $val['pc_text']."\n";
		}

		$info['pc_text'] = $string;

		return $info;
	}


    public function changelogsaveAction()
    {
    	$products			= new AdminProducts();
    	$data['productId']  = $this->_getParam('pid');
    	$string 			= $this->_getParam('chItem');
    	$orders 			= $this->_getParam('chOrder');
    	$id 				= $this->_getParam('chId');

    	if($this->_hasParam('lang'))
    	{
    		$lang = $this->_getParam('lang');
    	}
    	else
    	{
    		$lang = 1;
    	}

   		$data['ChLogStrings'][] = array(
   			'txt' 	=> $string,
   			'order' => 0,
   			'ids'	=> $id
   		);

    	$data['ChLogNum']			= $this->_getParam('ChLogNum');
    	$data['buildId']			= $this->_getParam('pbid');

    	if($this->_hasParam('chItemNew'))
    	{
    		$data['ChLogStringsNew']	= array_combine($this->_getParam('chOrderNew'), $this->_getParam('chItemNew'));
    	}

    	$products->saveChangelog($data,$lang);

    	$this->_redirect('/products/changelog/id/'.$data['productId'].'/lang/'.$lang.'/');
    }


    private function getParamLang()
    {
    	if($this->_hasParam('lang'))
    	{
    		return $this->_getParam('lang');
    	}
    	else
    	{
    		return 1;
    	}
    }
}

?>