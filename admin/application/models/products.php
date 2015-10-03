<?php

class AdminProducts {

    private $allDbAdapter;
    private $siteDbAdapter;
	private $tablename = 'products';
	
    public function __construct() {
		$this->dbAdapter = Zend_Registry::get('dbAdapter');

		$config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
		$params['dbname'] = $config->db->config->dballname;
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->allDbAdapter->query('SET NAMES utf8');

    }

    public function __destruct() {

    }

    public function getCategories($smarty = FALSE) {
	/* SELECT cat_id, c_name
	    FROM category
		LEFT JOIN category_en ON c_id = cat_id
	   ORDER BY cat_order
	*/
	$select = $this->allDbAdapter->select();
	$select->from('category', array('id' => 'cat_id'));
	$select->joinLeft('category_en', 'c_id = cat_id', array('name' => 'c_name'));
	$select->order(array('cat_order'));
	$cats = $this->allDbAdapter->fetchAll($select->__toString());
	if($smarty) {
	    $tcats['values'] = array();
	    $tcats['names'] = array();
	    $tsize = sizeof($cats);
	    for($i=0; $i<$tsize; $i++) {
		array_push($tcats['values'],$cats[$i]['id']);
		array_push($tcats['names'],$cats[$i]['name']);
	    }
	    $cats = $tcats;
	}
	return $cats;
    }

    public function getPlatforms($smarty = FALSE) {
	$select = $this->allDbAdapter->select();
	$select->from('platforms', array('id' => 'platform_id', 'name' => 'platform_name'));
	$select->order(array('platform_order'));
	$plat = $this->allDbAdapter->fetchAll($select->__toString());
	if($smarty) {
	    $tplat['values'] = array();
	    $tplat['names'] = array();
	    $tsize = sizeof($plat);
	    for($i=0; $i<$tsize; $i++) {
		array_push($tplat['values'],$plat[$i]['id']);
		array_push($tplat['names'],$plat[$i]['name']);
	    }
	    $plat = $tplat;
	}
	return $plat;
    }
	

    public function getProductLangs($smarty = FALSE) {
	$select = $this->allDbAdapter->select();
	$select->from('product_languages', array('id' => 'lang_id', 'name' => 'lang_value'));
	$select->order(array('lang_value'));
	$langs = $this->allDbAdapter->fetchAll($select->__toString());
	if($smarty) {
	    $tlangs['values'] = array();
	    $tlangs['names'] = array();
	    $tsize = sizeof($langs);
	    for($i=0; $i<$tsize; $i++) {
		array_push($tlangs['values'],$langs[$i]['id']);
		array_push($tlangs['names'],$langs[$i]['name']);
	    }
	    $langs = $tlangs;
	}
	return $langs;
    }

    public function getProductOS($smarty = FALSE) {
	$select = $this->allDbAdapter->select();
	$select->from('os', array('id' => 'o_id', 'name' => 'o_value'));
//	$select->joinLeft('os_en', 'os_id = o_id', array('name' => 'os_value'));
	$select->order(array('o_order'));
	$os = $this->allDbAdapter->fetchAll($select->__toString());
	if($smarty) {
	    $tos['values'] = array();
	    $tos['names'] = array();
	    $tsize = sizeof($os);
	    for($i=0; $i<$tsize; $i++) {
		array_push($tos['values'],$os[$i]['id']);
		array_push($tos['names'],$os[$i]['name']);
	    }
	    $os = $tos;
	}
	return $os;
    }

    public function getCategoryProducts($catId) {
	$catId = intval($catId);
	$result['langs'] = array();
	$result['prods'] = array();
	if($catId > 0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('languages', array('l_name', 'l_code', 'l_addrcode', 'l_blocked'));
	    $result['langs'] = $this->allDbAdapter->fetchAll($select->__toString());

	    $select = $this->allDbAdapter->select();
	    //'p_platform','p_title','p_nick','p_featured','p_blocked'
	    $select->from('products', array('p_title','p_nick','p_featured','p_blocked'));
	    $select->joinLeft('platforms', 'platform_id = p_platform', array('platform' => 'platform_acronim'));
	    $select->order(array('p_title'));
	    $result['prods'] = $this->allDbAdapter->fetchAll($select->__toString());
	}
	return $result;
    }

    public function getProducts() {
	$select = $this->allDbAdapter->select();
	$select->from('languages', array('l_name', 'l_code', 'l_addrcode', 'l_blocked'));
	$select->order(array('l_order DESC'));
	$result['langs'] = $this->allDbAdapter->fetchAll($select->__toString());

	$select = $this->allDbAdapter->select();
	$select->from('products', array('p_title','p_id','p_featured','p_blocked'));
	$select->joinLeft('platforms', 'platform_id = p_platform', array('platform' => 'platform_acronim'));
	$select->joinLeft('category', 'cat_id = p_cat', array('cat_id'));
	$select->joinLeft('category_en', 'c_id = p_cat', array('name' => 'c_name'));
	$select->order(array('cat_order','p_order'));
	$prods = $this->allDbAdapter->fetchAll($select->__toString());
	$tsize = sizeof($prods);
	for($i=0; $i<$tsize; $i++) {
	    $result['prods'][$prods[$i]['name']][$prods[$i]['p_id']] = $prods[$i];
	    $result['cat'][$prods[$i]['name']] = $prods[$i]['cat_id'];
	}
	return $result;
    }

    public function getProduct($pId) {
	$pId = intval($pId);
	$prods = '';
	if($pId > 0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('products', array('p_title'));
	    $select->where('p_id = ?', $pId);
	    $prods = $this->allDbAdapter->fetchOne($select->__toString());
	}
	return $prods;
    }

    public function upProduct($catId,$pId) {
	$catId = intval($catId);
	$pId = intval($pId);
	$succed = FALSE;
	if($catId>0 && $pId>0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('products', array('p_id','p_order'));
	    $select->where('p_cat = ?', $catId);
	    $select->order(array('p_order'));
	    $prods = $this->allDbAdapter->fetchAll($select->__toString());
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		if($prods[$i]['p_id'] == $pId) {
		    if($i == 0) {
			break;
		    }
		    $low = $prods[$i]['p_order'];
		    $high = $prods[$i-1]['p_order'];
		    $lpId = $prods[$i-1]['p_id'];
		    $this->allDbAdapter->update('products', array('p_order' => $high), $this->allDbAdapter->quoteInto('p_id = ?', $pId));
		    $this->allDbAdapter->update('products', array('p_order' => $low), $this->allDbAdapter->quoteInto('p_id = ?', $lpId));
		    $succed = TRUE;
		    break;
		}
	    }
	}
	return $succed;
    }

    public function downProduct($catId,$pId) {
	$catId = intval($catId);
	$pId = intval($pId);
	$succed = FALSE;
	if($catId>0 && $pId>0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('products', array('p_id','p_order'));
	    $select->where('p_cat = ?', $catId);
	    $select->order(array('p_order'));
	    $prods = $this->allDbAdapter->fetchAll($select->__toString());
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		if($prods[$i]['p_id'] == $pId) {
		    if($i == $tsize-1) {
			break;
		    }
		    $high = $prods[$i]['p_order'];
		    $low = $prods[$i+1]['p_order'];
		    $lpId = $prods[$i+1]['p_id'];
		    $this->allDbAdapter->update('products', array('p_order' => $low), $this->allDbAdapter->quoteInto('p_id = ?', $pId));
		    $this->allDbAdapter->update('products', array('p_order' => $high), $this->allDbAdapter->quoteInto('p_id = ?', $lpId));
		    $succed = TRUE;
		    break;
		}
	    }
	}
	return $succed;
    }

    public function getFeatures($lang, $pId) {
	$pId = intval($pId);
	$feats = array();
	if($pId>0 && strlen($lang)==2) {
	    $select = $this->allDbAdapter->select();
	    $select->from('product_features_'.$lang, array('id' => 'feat_id','promo' => 'feat_promo','text' => 'feat_text','order' => 'feat_order'));
	    $select->where('feat_pid = ?', $pId);
	    $select->order(array('feat_order'));
	    $feats = $this->allDbAdapter->fetchAll($select->__toString());
	}
	return $feats;
    }

    public function saveFeatures($data, $lang) {
	$fid = $data['fid'];
	$ftext = $data['ftext'];
	$fpromo = $data['fpromo'];
	$forder = $data['forder'];
	$tsize = sizeof($fid);
	if(strlen($lang)==2 && sizeof($ftext)==$tsize) {
	    for($i=0; $i<$tsize; $i++) {
		$set = array('feat_text' => $ftext[$i], 'feat_order' => $forder[$i]);
		if(in_array($fid[$i],$fpromo)) {
		    $set = array_merge($set,array('feat_promo' => '1'));
		}
		else {
		    $set = array_merge($set,array('feat_promo' => '0'));
		}
		$this->allDbAdapter->update('product_features_'.$lang, $set, $this->allDbAdapter->quoteInto('feat_id = ?', $fid[$i]));
	    }
	}
    }
/*
    public function upFeature($pId, $fId, $lang) {
	$pId = intval($pId);
	$fId = intval($fId);
	$succed = FALSE;
	if($pId>0 && $fId>0 && strlen($lang)==2) {
	    $select = $this->allDbAdapter->select();
	    $select->from('product_features_'.$lang, array('feat_id','feat_order','f_p_id' => 'feat_pid'));
	    $select->where('feat_pid = ?', $pId);
	    $select->order(array('feat_order'));
	    $prods = $this->allDbAdapter->fetchAll($select->__toString());
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		if($prods[$i]['feat_id'] == $fId) {
		    if($i == 0) {
			break;
		    }
		    $low = $prods[$i]['feat_order'];
		    $high = $prods[$i-1]['feat_order'];
		    $lpId = $prods[$i-1]['feat_id'];
		    $this->allDbAdapter->update('product_features_'.$lang, array('feat_order' => $high), $this->allDbAdapter->quoteInto('feat_id = ?', $fId));
		    $this->allDbAdapter->update('product_features_'.$lang, array('feat_order' => $low), $this->allDbAdapter->quoteInto('feat_id = ?', $lpId));
		    $succed = TRUE;
		    break;
		}
	    }
	}
	return $succed;
    }

    public function downFeature($pId, $fId, $lang) {
	$fId = intval($fId);
	$pId = intval($pId);
	$succed = FALSE;
	if($fId>0 && $pId>0 && strlen($lang)==2) {
	    $select = $this->allDbAdapter->select();
	    $select->from('product_features_'.$lang, array('feat_id','feat_order','f_p_id' => 'feat_pid'));
	    $select->where('feat_pid = ?', $pId);
	    $select->order(array('feat_order'));
	    $prods = $this->allDbAdapter->fetchAll($select->__toString());
	    $tsize = sizeof($prods);
	    for($i=0; $i<$tsize; $i++) {
		if($prods[$i]['feat_id'] == $fId) {
		    if($i == $tsize-1) {
			break;
		    }
		    $high = $prods[$i]['feat_order'];
		    $low = $prods[$i+1]['feat_order'];
		    $lpId = $prods[$i+1]['feat_id'];
		    $this->allDbAdapter->update('product_features_'.$lang, array('feat_order' => $low), $this->allDbAdapter->quoteInto('feat_id = ?', $fId));
		    $this->allDbAdapter->update('product_features_'.$lang, array('feat_order' => $high), $this->allDbAdapter->quoteInto('feat_id = ?', $lpId));
		    $succed = TRUE;
		    break;
		}
	    }
	}
	return $succed;
    }
*/
    public function dropFeatures($fId, $lang) {
	$fId = intval($fId);
	//$langs = $this->getLangs();
	if(strlen($lang)==2 && $fId>0) {
	    $this->allDbAdapter->delete('product_features_'.$lang, $this->allDbAdapter->quoteInto('feat_id = ?', $fId));
	}
    }

    public function addFeatures($pId, $lang, $data) {
	if(!empty($data['aftext']) && strlen($lang)==2) {
	    $promo = (isset($data['afpromo']) ? '1' : '0');
	    $set = array('feat_pid' => intval($pId),
			 'feat_text' => $data['aftext'],
			 'feat_order' => intval($data['aforder']),
			 'feat_promo' => 1);
	    $this->allDbAdapter->insert('product_features_'.$lang, $set);
	}
    }

    public function getDemo($lang, $pId) {
	$pId = intval($pId);
	$demo = array();
	if($pId>0 && strlen($lang)==2) {
	    $select = $this->allDbAdapter->select();
	    $select->from('product_demolimits_'.$lang, array('id' => 'd_id','text' => 'd_text','order' => 'd_order'));
	    $select->where('d_pid = ?', $pId);
	    $select->order(array('d_order'));
	    $demo = $this->allDbAdapter->fetchAll($select->__toString());
	}
	return $demo;
    }

    public function saveDemo($data, $lang) {
	$fid = $data['did'];
	$ftext = $data['dtext'];
	$forder = $data['dorder'];
	$tsize = sizeof($fid);
	if(strlen($lang)==2 && sizeof($ftext)==$tsize && sizeof($forder)==$tsize) {
	    for($i=0; $i<$tsize; $i++) {
		$set = array('d_text' => $ftext[$i], 'd_order' => $forder[$i]);
		$this->allDbAdapter->update('product_demolimits_'.$lang, $set, $this->allDbAdapter->quoteInto('d_id = ?', $fid[$i]));
	    }
	}
    }

    public function dropDemo($dId, $lang) {
	$dId = intval($dId);
	if(strlen($lang)==2 && $dId>0) {
	    $this->allDbAdapter->delete('product_demolimits_'.$lang, $this->allDbAdapter->quoteInto('d_id = ?', $dId));
	}
    }

    public function addDemo($pId, $lang, $data) {
	if(!empty($data['adtext']) && strlen($lang)==2) {
	    $set = array('d_pid' => intval($pId),
			 'd_text' => $data['adtext'],
			 'd_order' => intval($data['adorder']));
	    $this->allDbAdapter->insert('product_demolimits_'.$lang, $set);
	}
    }

    public function getProductById($id) {
	$id = intval($id);
	$prod = array();
	if($id > 0) {
	    $select = $this->allDbAdapter->select();
	    $select->from('products',array('*', 'p_date' => 'UNIX_TIMESTAMP(p_date_release)'));
	    $select->where('p_id = ?', $id);
	    $prod = $this->allDbAdapter->fetchRow($select->__toString());
	    $prod['p_date'] = date('d.m.Y',$prod['p_date']);
	}
	return $prod;
    }

    public function setProductById($id, $data) {
	$id = intval($id);
	$date = explode('.',$data['p_date']);
		if (!isset($data['p_order']) || $data['p_order']=="")
			$data['p_order'] = $this->getmax()+10;

	$set = array('p_title' => $data['p_title'], 'p_menu_title' => $data['p_menu_title'],
		     'p_cat' => $data['p_cat'], 'p_platform' => $data['p_platform'],
		     'p_version' => $data['p_version'], 'p_build' => $data['p_build'],
		     'p_nick' => $data['p_nick'], 'p_download' => $data['p_download'], 'p_downloads' => $data['p_downloads'],
		     'p_order' => $data['p_order'],
		     'p_featured' => (isset($data['p_featured']) ? '1' : '0'),
		     'p_free' => (isset($data['p_free']) ? '1' : '0'),
		     'p_blocked' => (isset($data['p_blocked']) ? '1' : '0'),
		     'p_date_release' => $date[2].'-'.$date[1].'-'.$date[0].' 00:00:00',
		     'p_languages' => '|'.implode('|',$data['p_languages']).'|',
		     'p_os' => '|'.implode('|',$data['p_os']).'|',
                     'p_faq_link' => $data['p_faq_link'],
		     'p_wiki_link' => $data['p_wiki_link'],
                     'p_page_link' => $data['p_page_link']);
	return (string)$select = $this->allDbAdapter->update('products',$set,$this->allDbAdapter->quoteInto('p_id = ?', $id));
    }

    public function addProduct($data) {
	$date = explode('.',$data['p_date']);
	if (!isset($data['p_order']) || $data['p_order']=="")
			$data['p_order'] = $this->getmax()+10;
	$set = array('p_title' => $data['p_title'], 'p_menu_title' => $data['p_menu_title'],
		     'p_cat' => $data['p_cat'], 'p_platform' => $data['p_platform'],
		     'p_version' => $data['p_version'], 'p_build' => $data['p_build'],
		     'p_nick' => $data['p_nick'], 'p_download' => $data['p_download'],
		     'p_order' => $data['p_order'],
		     'p_featured' => (isset($data['p_featured']) ? '1' : '0'),
		     'p_free' => (isset($data['p_free']) ? '1' : '0'),
		     'p_blocked' => (isset($data['p_blocked']) ? '1' : '0'),
		     'p_date_release' => $date[2].'-'.$date[1].'-'.$date[0].' 00:00:00',
		     'p_languages' => '|'.implode('|',$data['p_languages']).'|',
		     'p_os' => '|'.implode('|',$data['p_os']).'|',
                     'p_faq_link' => $data['p_faq_link'],
		     'p_wiki_link' => $data['p_wiki_link'],
                     'p_page_link' => $data['p_page_link']);
		$this->allDbAdapter->insert('products',$set);
		return (int)$this->allDbAdapter->lastInsertId();
    }

    public function deleteProduct($id) {
		$id = intval($id);
        $this->allDbAdapter->delete('products', $this->allDbAdapter->quoteInto('p_id = ?', $id));
   }
/*
    private function getLangs() {
	$langs = array();
	$select = $this->allDbAdapter->select();
	$select->from('languages', array('l_code'));
	$rows = $this->allDbAdapter->fetchAll($select->__toString());
	for($i = 0; $i < sizeof($rows); $i++) {
	    array_push($langs,$rows[$i]['l_code']);
	}
	return $langs;
    }
*/

    public function exportNewDataToOldDB($productId, &$log) {

	$select = $this->allDbAdapter->select();
	$select->from('products', array('p_version',
					'p_build',
					'p_date_release'));
	$select->where('p_id = ?', $productId);
	$build = $this->allDbAdapter->fetchRow($select->__toString());

	$select = $this->allDbAdapter->select();
	$select->from('product_features_en', array('feat_order',
						   'feat_text',
						   'feat_promo'));
	$select->where('feat_pid = ?', $productId);
	$features = $this->allDbAdapter->fetchAll($select->__toString());

	$select = $this->allDbAdapter->select();
	$select->from('product_demolimits_en', array('d_order',
						   'd_text'));
	$select->where('d_pid = ?', $productId);
	$demolimits = $this->allDbAdapter->fetchAll($select->__toString());

	if(!empty($build)) {
	    $config = Zend_Registry::get('config');
	    $oldDbAdapter = Zend_Db::factory($config->old_db->adapter, $config->old_db->config->toArray());

	    $select = $oldDbAdapter->select();
	    $select->from('product_builds', 'pb_build');
	    $select->where('pb_pid = ?', $productId);
	    $select->where('pb_build = ?', $build['p_build']);
	    $existBuild = $oldDbAdapter->fetchOne($select->__toString());

	    if($existBuild === false) {
		$buildRow = array(
		    'pb_pid' => $productId,
		    'pb_version' => $build['p_version'],
		    'pb_build' => $build['p_build'],
		    'pb_date_release' => $build['p_date_release']
		);

		$oldDbAdapter->insert('product_builds', $buildRow);
		$oldDbAdapter->delete('product_features', $oldDbAdapter->quoteInto('feat_prod_id = ?', $productId));

                /* Log */
                $log .= '<strong>Version:</strong> '.$build['p_version'].'<br/>';
                $log .= '<strong>Build:</strong> '.$build['p_build'].'<br/>';
                $log .= '<strong>Date release:</strong> '.$build['p_date_release'].'<br/><br/>';
                $log .= '<strong>Features:</strong><br/>';
                /*******/

		foreach($features AS $feature) {
		    $featureRow = array(
			'feat_prod_id' => $productId,
			'feat_order' => $feature['feat_order'],
			'feat_text' => $feature['feat_text'],
			'feat_is_promo' => $feature['feat_promo']
		    );
		    $oldDbAdapter->insert('product_features', $featureRow);

                    /* Log */
                    $log .= $feature['feat_text'].'<br/>';
                    /*******/
		}

		$oldDbAdapter->delete('product_demolimits', $oldDbAdapter->quoteInto('p_demo_prod_id = ?', $productId));

                /* Log */
                $log .= '<br/><br/><strong>Demolimits:</strong><br/>';
                /*******/

		foreach($demolimits AS $demolimit) {
		    $demolimitRow = array(
			'p_demo_prod_id' => $productId,
			'p_demo_order' => $demolimit['d_order'],
			'p_demo_text' => $demolimit['d_text']
		    );
		    $oldDbAdapter->insert('product_demolimits', $demolimitRow);

                    /* Log */
                    $log .= $demolimit['d_text'].'<br/>';
                    /*******/
		}

	    } else {
		return false;
	    }

	} else {
	    return false;
	}

	return true;
    }

    /**************  --  Changelog function  --  *****************/

    public function getChangelogById($id,$lang){
    	$changelogs = array();
  		if($id > 0) {
			$lng = $this->getLCode($lang);
		    $select = $this->allDbAdapter->select();

		    $select->from('product_builds',array(
		    									'pb_id',
		    									'pb_pid',
		    									'pb_version',
		    									'pb_build'

		    									));

		    $select->joinLeft('product_changelog_'.$lng, 'pc_pbid = pb_id',array(
		    																'pc_id',
		    																'pc_text',
		    																'pc_blocked',
		    																'pc_issend'
		    																));
		    $select->where('pb_pid = ?', $id);
		    $select->order(array('pb_date_release DESC'));
		    $chLog = $this->allDbAdapter->fetchAll($select->__toString());
		    if(count($chLog)>0){
		    	$prData 				= array();
		    	$prData['pbId'] 		= $chLog[0]['pb_pid'];

		    	foreach ($chLog as $key => $val){
		    		$prData['builds'][$val['pb_build']]['chlog'][] = array(
		    															'text'   => $val['pc_text'],
		    															'build'	 => $val['pb_id'],
		    															'textId' => $val['pc_id']
		    															);

		    		$prData['builds'][$val['pb_build']]['pc_blocked'] = $val['pc_blocked'];
		    		$prData['builds'][$val['pb_build']]['pc_issend']  = $val['pc_issend'];
			    }
		    }
		    return $prData;
  		}else{
  			return null;
  		}
  	}

  	public function changeChangelogStatus($build,$lang){
  		$lng = $this->getLCode($lang);
  		$select = $this->allDbAdapter->select();
		$select->from('product_changelog_'.$lng,array('pc_blocked'));
		$select->where('pc_pbid = ?', $build);
	    $status = array_unique($this->allDbAdapter->fetchAll($select->__toString()));
	    if($status[0]['pc_blocked'] == 0){
	    	$data = array('pc_blocked'  => 1);
	    	$this->allDbAdapter->update('product_changelog_'.$lng, $data, 'pc_pbid = '.$build);
	    	return 1;
	    }else{
	    	$data = array('pc_blocked'  => 0);
	    	$this->allDbAdapter->update('product_changelog_'.$lng, $data, 'pc_pbid = '.$build);
	    	return 0;
	    }

  	}

   	
     /*******************************************/
        public function addChangelog($data)
	{
  		if ($data['newChLogNum'] != '')
  		{
			$set = array(
    			'pb_pid' 		=> intval($data['productId']),
				'pb_build' 		=> $data['newChLogNum'],
				'pb_version'	=> (round($data['newChLogNum'],1).'')
			);

			$this->allDbAdapter->insert('product_builds', $set);
			$lastId = $this->allDbAdapter->lastInsertId();

			$select = $this->allDbAdapter->select();
		    $select->from('languages','l_code');
		    $lngs = $this->allDbAdapter->fetchAll($select->__toString());

			if( $data['newChLogStrings'] != '' )
			{
				$set = array(
    				'pc_pbid' 		=> intval($lastId),
					'pc_order' 		=> 0,
					'pc_text'		=> $data['newChLogStrings'],
					'pc_blocked'	=> 1
				);
				foreach ($lngs as $k => $l_code){
					$this->allDbAdapter->insert('product_changelog_'.$l_code['l_code'], $set);
				}
			}
  		}
  	}


  	/*******************************************/

  	public function saveChangelog($data,$lang){
		$lng = $this->getLCode($lang);
		$set = array(
    		'pb_pid' 		=> intval($data['productId']),
			'pb_build' 		=> $data['ChLogNum'],
			'pb_version'	=> (round($data['ChLogNum'],1).'')
			);
			$this->allDbAdapter->update('product_builds', $set, 'pb_id = '.$data['buildId']);

		foreach ($data['ChLogStrings'] as $key => $val){
			if ($val['txt'] != ''){
				$set = array(
					'pc_order' 		=> intval($val['order']),
					'pc_text'		=> $val['txt']
					);
				$this->allDbAdapter->update('product_changelog_'.$lng, $set, 'pc_id = '.$val['ids']);
			}else{
				$this->allDbAdapter->delete('product_changelog_'.$lng, 'pc_id = '.$val['ids']);
			}
		}

		if(isset($data['ChLogStringsNew'])){
	  		$select = $this->allDbAdapter->select();
			$select->from('product_changelog_'.$lng,array('pc_blocked'));
			$select->where('pc_pbid = ?', $data['buildId']);
		    $status = array_unique($this->allDbAdapter->fetchAll($select->__toString()));
	        $st 	= $status[0]['pc_blocked'];

	        foreach ($data['ChLogStringsNew'] as $order => $text){
	        	if ($text != ''){
					$set = array(
    					'pc_pbid' 		=> intval($data['buildId']),
						'pc_order' 		=> $order,
						'pc_text'		=> $text,
						'pc_blocked'	=> $st
						);
					$this->allDbAdapter->insert('product_changelog_'.$lng, $set);
	        	}
			}
		}
  	}

  	private function getLCode($lang){
		$select = $this->allDbAdapter->select();
	    $select->from('languages','l_code');
		$select->where('l_id = ?', $lang);
		$lng = $this->allDbAdapter->fetchOne($select->__toString());
		return $lng;
  	}



  	public function delChangelog($buildId){
		$select = $this->allDbAdapter->select();
	    $select->from('languages','l_code');
	    $lngs = $this->allDbAdapter->fetchAll($select->__toString());
		foreach ($lngs as $k => $l_code){
			$this->allDbAdapter->delete('product_changelog_'.$l_code['l_code'], 'pc_pbid = '.$buildId);
		}
		$this->allDbAdapter->delete('product_builds', 'pb_id = '.$buildId);
  	}

  	public function getChangelogByBuildId($buildId,$lang){
	    $lng = $this->getLCode($lang);
  	    $select = $this->allDbAdapter->select();
	    $select->from('product_changelog_'.$lng, array('pc_id',
							   'pc_text',
							   'pc_order',
							   'pc_blocked'));

	    $select->joinLeft('product_builds', 'pb_id = pc_pbid', array('pb_build','pb_id'));
	    $select->where('pc_pbid = ?', $buildId);
	    $select->order(array('pc_order'));
	    $chLog = $this->allDbAdapter->fetchAll($select->__toString());
	    return $chLog;
  	}


  	public function getSubscribers($productId)
  	{
  	    $select = $this->siteDbAdapter->select();
	    $select->from('update_subscribers_products', array('suser_id'));
	    $select->joinLeft('update_subscibers', 'user_id = suser_id', array('user_email'));
	    $select->where('prod_id = ?', $productId);
	    $subscribers = $this->siteDbAdapter->fetchAll($select->__toString());

	    return $subscribers;
  	}

  	public function getSetSendStatus($info){
	    foreach ($info as $key => $val){
		$set   = array('pc_issend' => 1);
		$where = $this->allDbAdapter->quoteInto('pc_id = ?', $val['pc_id']);
		$this->allDbAdapter->update('product_changelog_en', $set, $where);
	    }
  	}



  	public function oa($a){
  	echo'<pre>';
  	echo var_dump($a);
  	echo'</pre>';
  	}

    public function getmax($field='p_order')
	 {
			$select = $this->allDbAdapter->select();
			$select->from($this->tablename, array('maxval'=>'max('.$field.')'));
	    	$res = $this->allDbAdapter->fetchRow($select->__toString());
			return $res['maxval'];
    }

}
?>