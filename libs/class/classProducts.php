<?php

class Products
{
    private $mLanguage;
    private $mAddressPrefix;

    public function __construct()
	{
		if (VBox::isExist('Page'))
		{
			$this->mLanguage = VBox::get('Page')->language;
            
			if(isset(VBox::get('Page')->address['prefix']))
			{
                $this->mAddressPrefix = VBox::get('Page')->address['prefix'];
            }
			else
			{
                $this->mAddressPrefix = '';
            }
		}
		else
		{
			$this->mLanguage = 'en';
			$this->mAddressPrefix = '';
		}
    }

    public function setProductLocalization($ProductLanguage) {
	$q = 'SELECT l_code, l_addrcode
              FROM '.VBox::get('ConstData')->getConst('langsDb').'.languages';
        DB::executeQuery($q, 'langs');
        if ($rows = DB::fetchResults('langs')) {
	    for($i=0; $i<sizeof($rows); $i++) {
		if ($ProductLanguage == $rows[$i]['l_code']) {
		    $this->mLanguage = $ProductLanguage;
		    if (!empty($rows[$i]['l_addrcode'])) {
			$this->mAddressPrefix = '/'.$rows[$i]['l_addrcode'];
		    }
		    break;
		}
	    }
	}
    }

    public function getProductsShort($wtFree = TRUE, $wtBlocked = FALSE, $platformId = NULL, $categoryId = NULL) {
	$q = 'SELECT p_id,
		     p_title,
		     p_nick,
		     p_page_link,
		     p_cat
		FROM '.VBox::get('ConstData')->getConst('langsDb').'.products
		LEFT JOIN '.VBox::get('ConstData')->getConst('langsDb').'.category ON p_cat = cat_id';

        if(!$wtFree || !$wtBlocked) {
	    $q.= ' WHERE p_hidden = 0';
	    $q.= (!$wtFree ? ' AND p_free = 0' : '');
	    $q.= (!$wtBlocked ? ' AND p_blocked = 0' : '');
	    $q.= (isset($platformId) ? ' AND p_platform = '.$platformId : '');
            $q.= (isset($categoryId) ? ' AND p_cat = '.$categoryId : '');
	}

	//$q .= ' ORDER BY cat_order, p_order';
	$q .= ' ORDER BY p_title';
		
	DB::executeQuery($q, 'prods');
        $rows = DB::fetchResults('prods');
	$result = array();
	if(!empty($rows)) {
	    $tsize = sizeof($rows);
	    for($i=0; $i<$tsize; $i++) {
		$result[] = array('id' => $rows[$i]['p_id'],
				  'title' => $rows[$i]['p_title'],
				  'nick' => $rows[$i]['p_nick'],
				  'page_link' => $rows[$i]['p_page_link'],
				  'category_id' => $rows[$i]['p_cat']);
	    }
	}
	return $result;
    }

    public function getProductsFull($wtFree = TRUE, $wtBlocked = FALSE,$_platform=3) {
    //if $platform == 0  all products, if 1 - only PC, etc
	$mac_platform = $_platform;
    
	$db = VBox::get('ConstData')->getConst('langsDb');
	$q = 'SELECT c_name, p_page_link, p_id, p_title, p_nick, p_cat, p_download, p_downloads, p_featured,
	p_relation, p_rel_url, p_platform, platform_nick, platform_acronim, platform_order
		FROM '.$db.'.category
		    LEFT JOIN '.$db.'.category_'.$this->mLanguage.' ON cat_id = c_id
		    LEFT JOIN '.$db.'.products ON cat_id = p_cat
		    LEFT JOIN '.$db.'.platforms ON platform_id = p_platform';
	if(!$wtFree || !$wtBlocked) {
	    $q.= ' WHERE ';
	    $free = (!$wtFree ? 'p_free = 0' : '');
	    $block = (!$wtBlocked ? 'p_blocked = 0' : '');
	    $condition = $free.((!empty($free) && !empty($block)) ? ' AND ' : '').$block;
	    $q.= $condition;
	}
	$q.= ' ORDER BY cat_order, p_featured DESC, p_order';
	DB::executeQuery($q, 'prods');
        $rows = DB::fetchResults('prods');
	if(!empty($rows)) {
	    $tsize = sizeof($rows);
	    $all = array();
	    for($i=0; $i<$tsize; $i++) {
		$all[$rows[$i]['p_id']] = array('p_title' => $rows[$i]['p_title'],
				    'p_nick' => $rows[$i]['p_nick'],
				    'platform_nick' => $rows[$i]['platform_nick'],
				    'platform_acronim' => $rows[$i]['platform_acronim'],
				    'platform_order' => $rows[$i]['platform_acronim']);
	    }

	    $prods = array();
	    for($i=0; $i<$tsize; $i++) {
	      if($rows[$i]['p_platform'] != $mac_platform) {
		$prods[$rows[$i]['p_cat']]['category'] = $rows[$i]['c_name'];
		$row = array('p_title' => $rows[$i]['p_title'],
			    'p_nick' => $rows[$i]['p_nick'],
			    'p_download' => $rows[$i]['p_download'],
                'p_downloads' => $rows[$i]['p_downloads'],
			    'url' => $this->mAddressPrefix.'/products/'.$rows[$i]['p_nick'].'/',
			    'p_relation' => (!empty($rows[$i]['p_relation']) ? explode('|',$rows[$i]['p_relation']) : array()));
		$row['platform'][$rows[$i]['platform_order']] = array_merge($all[$rows[$i]['p_id']], array('url' => $row['url']));
		if(!empty($row['p_relation'])) {
		    foreach($row['p_relation'] as $val) {
			$pr = $all[$val];
			$url = ($pr['platform_acronim'] != 'Mac' ?
				$this->mAddressPrefix.'/products/'.$pr['p_nick'].'/' :
				'http://'.$_SERVER['HTTP_HOST'].$this->mAddressPrefix.$rows[$i]['p_rel_url']);
			$row['platform'][$pr['platform_order']] = array_merge($pr, array('url' => $url));
		    }
		}
		ksort($row['platform'], SORT_NUMERIC);
		if($rows[$i]['p_featured'] == '1') {
		    $prods[$rows[$i]['p_cat']]['featured'][$rows[$i]['p_id']] = $row;
		}
		else {
		    $prods[$rows[$i]['p_cat']]['other'][$rows[$i]['p_id']] = $row;
		}
	      }
	    }
	    $q = 'SELECT p_id, p_cat, feat_text
		    FROM '.$db.'.products
			LEFT JOIN '.$db.'.product_features_'.$this->mLanguage.' ON feat_pid = p_id
		   WHERE p_featured = 1 AND feat_promo = 1';
	    //if($platform != 0) {
		$q.= ' AND p_platform <> '.$mac_platform;
	    //}
	    if(!empty($condition)) {
		$q.= ' AND '.$condition;
	    }
	    $q.= ' ORDER BY p_id, feat_promo DESC, feat_order';
	    DB::executeQuery($q, 'feats');
	    $rows = DB::fetchResults('feats');
	    if(!empty($rows)) {
		$tsize = sizeof($rows);
		for($i=0; $i<$tsize; $i++) {
		    $prods[$rows[$i]['p_cat']]['featured'][$rows[$i]['p_id']]['feat'][] = $rows[$i]['feat_text'];
		}
	    }
	}

	return $prods;
    }

    public function getProductById($id) {
	$id = intval($id);
	$rows = array();
	if($id > 0) {
	   $q = 'SELECT p_id, p_title, p_version, p_faq_link, p_wiki_link, p_downloads 
		 FROM '.VBox::get('ConstData')->getConst('langsDb').'.products
		 WHERE p_id = '.$id.' LIMIT 1';
	    DB::executeQuery($q, 'prods');
	    $rows = DB::fetchRow('prods');
	}
	return $rows;
    }

    public function getProductFeatures($productId) {

        $db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT feat_text
              FROM '.$db.'.product_features_'.$this->mLanguage.'
              WHERE feat_pid = '.$productId.'
              ORDER BY feat_order';

        DB::executeQuery($q, 'featsList');
	$rows = DB::fetchResults('featsList');
        $featList = array();

        foreach($rows AS $row) {
            $featList[] = $row['feat_text'];
        }

        return $featList;
    }

    public function getProductDemoLimits($productId) {
        $db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT d_text
              FROM '.$db.'.product_demolimits_'.$this->mLanguage.'
              WHERE d_pid = '.$productId.'
              ORDER BY d_order';

        DB::executeQuery($q, 'demoLimits');
	$rows = DB::fetchResults('demoLimits');
        $demoLimits = array();

        foreach($rows AS $row) {
            $demoLimits[] = $row['d_text'];
        }

        return $demoLimits;
    }

    public function getProductBuildInfo($productId, $downloadDir = 'download')
	{
        $db = VBox::get('ConstData')->getConst('langsDb');
    
		$prodInfo = array();

        $q = 'SELECT p_build,
                     p_nick,
                     UNIX_TIMESTAMP(p_date_release) AS p_date,
                     p_download,
                     p_downloads,
					 p_version,
					 p_title,
					 p_wiki_link
              FROM '.$db.'.products
              WHERE p_id = '.$productId;
        
		DB::executeQuery($q, 'productInfo');
	
		$prodInfo = DB::fetchRow('productInfo');

        $prodInfo['os'] 				   = $this->getProductsOs($productId); //add 06.11.2009 garbagecat76
        $prodInfo['platform'] 	           = $this->getProductPlatform($productId); //add 17.11.2014 italiano
        $prodInfo['id'] 	               = $productId; //add 20.01.2015 italiano
        
        if ($prodInfo['p_downloads'] > 0){
            $prodInfo['downloadsFormat'] 	= number_format($prodInfo['p_downloads'],0,'',' '); //add 20.01.2015 italiano
            $prodInfo['downloadsFormatUp'] 	= number_format($prodInfo['p_downloads'],0,'',' ').'+'; //add 20.01.2015 italiano
            $prodInfo['downloads'] 	        = $prodInfo['p_downloads']; //add 20.01.2015 italiano
        }
        
        $prodInfo['p_date_release'] 		= $this->getMLDate('jS F, Y', $prodInfo['p_date']);
        $prodInfo['p_date_release_short'] 	= $this->getMLDate('jS M, Y', $prodInfo['p_date']);				
        
		$file = LOCAL_PATH.$downloadDir.'/'.$prodInfo['p_download'];

        if(is_file($file))
		{
			//$this->page->getLocalStrings()
			$sizes 	= array('Bytes', 'Kb', 'Mb', 'Gb');
    	    $pos 	= 0;
			$size 	= @filesize($file);
			while ($size >= 1024)
			{
				$size /= 1024;
				$pos++;
			}
            
			$prodInfo['size'] 			= round($size,($pos < 2 ? 0 : 2)).$sizes[$pos];
            $prodInfo['ctime'] 			= $this->getMLDate('jS F, Y', @filemtime($file));
			$prodInfo['ctime_short'] 	= $this->getMLDate('jS M, Y', @filemtime($file));		
            	
		}

        return $prodInfo;
    }

	protected function getMLDate($format, $timestamp='')
	{
		$monthes_en = $monthes_es = $monthes_jp = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$monthes_fr = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		$monthes_de = array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
		$monthes_ru = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
				
		$monthes_cont_en = $monthes_cont_es = $monthes_cont_jp = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$monthes_cont_fr = array('Jan', 'Fév', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc');
		$monthes_cont_de = array('Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez');
		$monthes_cont_ru = array('Янв', 'Фев', 'Мар', 'Апр', 'Мая', 'Июня', 'Июля', 'Авг', 'Сен', 'Окт', 'Ноября', 'Дек');


		$days_en = $days_es = $days_jp = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$days_fr = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
		$days_de = array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Sonnabend');
		$days_ru = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');


		$days_cont_en = $days_cont_es = $days_cont_jp = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		$days_cont_fr = array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
		$days_cont_de = array('Sonnt', 'Mon', 'Dien', 'Mit', 'Don', 'Fre', 'Sonna');
		$days_cont_ru = array('Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб');

		if($timestamp == '')
		{
			$timestamp = time();
		}
	
		if(strstr($format, 'jS') || strstr($format, 'dS') || strstr($format, 'tS'))
		{
			switch ($this->mLanguage)
			{
				case 'fr':
					$format = str_replace(array('jS', 'dS', 'tS'), '\l\e j', $format);
					break;
		
				case 'de':
					$format = str_replace(array('jS', 'dS', 'tS'), '\d\e\r j.', $format);
					break;
			
				case 'ru':
					$format = 'd / m / Y';
					break;
			}
		}
		
		$str_date = date($format, $timestamp);
		
		if(strstr($format, 'F'))
		{
			$lang_arr = 'monthes_'.$this->mLanguage;
			$str_date = str_replace($monthes_en, $$lang_arr, $str_date);
		}
	
		if(strstr($format, 'M'))
		{
			$lang_arr = 'monthes_cont_'.$this->mLanguage;
			$str_date = str_replace($monthes_cont_en, $$lang_arr, $str_date);
		}
		
		if(strstr($format, 'l'))
		{
			$lang_arr = 'days_'.$this->mLanguage;
			$str_date = str_replace($days_en, $$lang_arr, $str_date);
		}
	
		if(strstr($format, 'D'))
		{
			$lang_arr = 'days_cont_'.$this->mLanguage;
			$str_date = str_replace($days_cont_en, $$lang_arr, $str_date);
		}
        
		return $str_date;
        
    }

    public function getProductLanguagesIds($productId) {
        $db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT p_languages
              FROM '.$db.'.products
              WHERE p_id = '.$productId;

        DB::executeQuery($q, 'prodLangs');
	$res = DB::fetchOne('prodLangs');

        $langs = array();

        if(strlen($res)) {
            $langsIds = explode('|', trim($res, '|'));

            $q = 'SELECT lang_id, lang_value
                  FROM '.$db.'.product_languages
                  WHERE lang_id IN ('.implode(',', $langsIds).')';

            DB::executeQuery($q, 'languages');
            $res = DB::fetchResults('languages');

            foreach($res AS $lang) {
                $langs[$lang['lang_id']] = $lang['lang_value'];
            }
        }

        return $langs;
    }

    public function getProductMajorVersions($productId) {

        $q = 'SELECT DISTINCT TRIM(SUBSTRING(pb_build, 1, POSITION("." IN pb_build))) AS version
              FROM '.VBox::get('ConstData')->getConst('langsDb').'.product_builds
              WHERE pb_pid = '.$productId.'
              ORDER BY version, pb_date_release';

        DB::executeQuery($q, 'versions');
        $res = DB::fetchResults('versions');

        $toOut = array();
        foreach($res AS $row) {
            $toOut[] = $row['version'];
        }
        return $toOut;
    }

    public function getProductsPlatforms($productId, $use_locale = false) {
        $db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT p_os
              FROM '.$db.'.products
              WHERE p_id = '.$productId;

        DB::executeQuery($q, 'prodOs');
		$res = DB::fetchOne('prodOs');

        $osIds = explode('|', trim($res, '|'));
		
		if (count($osIds) > 0){
		
			if ($use_locale){
				$q = 'SELECT los.os_value
					FROM '.$db.'.os_'.$this->mLanguage.' AS los
					LEFT JOIN '.$db.'.os AS os ON os.o_id = los.os_id
					WHERE los.os_id IN('.implode(',', $osIds).')
					ORDER BY os.o_order';
			}else{
				$q = 'SELECT os.o_value as os_value, os.o_acronim as os_acronim
					FROM '.$db.'.os AS os
					WHERE os.o_id IN('.implode(',', $osIds).')
					ORDER BY os.o_order';
			}
			DB::executeQuery($q, 'acronim');
			return DB::fetchResults('acronim');

		}	
		
		return array();
        
    }

    public function getFaqLinks() {
        $db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT p_id, p_faq_link
              FROM '.$db.'.products';

        DB::executeQuery($q, 'prodOs');
	$res = DB::fetchResults('prodOs');

        $links = array();

        foreach($res AS $row) {
            $links[$row['p_id']] = $row['p_faq_link'];
        }

        return $links;
    }

    public function getProductsCategories() {

	$db = VBox::get('ConstData')->getConst('langsDb');

	$q = 'SELECT cat_id,
		     c_name
	      FROM '.$db.'.category
	      LEFT JOIN '.$db.'.category_'.$this->mLanguage.' ON cat_id = c_id
	      ORDER BY cat_order';
	DB::executeQuery($q, 'prodCats');
	$res = DB::fetchResults('prodCats');

	$categories = array();
	foreach($res AS $row) {
	    $categories[$row['cat_id']] = $row['c_name'];
	}

	return $categories;
    }

    	//added 06.11.2009 garbagecat76
	private function getProductsOs($productId) {
        $osIds = array();
		$db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT p_os
              FROM '.$db.'.products
              WHERE p_id = '.$productId;

        DB::executeQuery($q, 'prodOs');
		$res = DB::fetchOne('prodOs');

        $osIds = explode('|', trim($res, '|'));
	return $osIds;
    }
   	//added 17.11.2014 italiano
	private function getProductPlatform($productId) {
		$db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT pl.*
              FROM '.$db.'.products as p
              LEFT JOIN '.$db.'.platforms as pl on p.p_platform = pl.platform_id
              WHERE p.p_id = '.$productId;

        DB::executeQuery($q, 'productPlatform');
		$res = DB::fetchOne('productPlatform');

        return $res;
    }
    	//added 17.11.2014 garbagecat76
	private function getProductOsName($productId) {
       	
		$os = array();
		
		$db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT o.*
              FROM '.$db.'.products p
			  LEFT JOIN '.$db.'.os as o on p.p_os = o.o_id
              WHERE p.p_id = '.$productId.'
			  ORDER BY o.o_order';

        DB::executeQuery($q, 'prodOsName');

		$res = DB::fetchResults('prodOsName');

		foreach($res AS $row) {
			$os[$row['o_id']] = $row['o_value'];
		}
		
		return $os;
    }
	
	public function setmLanguage($lang='en')
	{
		$this->mLanguage = 	$lang;
	}
	
	public function getProductSupport($id) {

		$rows = array();	
		$q = '';
		if (is_numeric($id)){

			$id = intval($id);

			if($id > 0) {
			   $q = 'SELECT *
					 FROM '.VBox::get('ConstData')->getConst('langsDb').'.products_support as ps, '.VBox::get('ConstData')->getConst('langsDb').'.support_managers as sm
					 WHERE ps.ps_product_id = '.$id.' and ps.ps_support_manager_id = sm.sm_id LIMIT 1';
			}
		}else{
			if($id !=='') {
			   $q = 'SELECT *
					 FROM '.VBox::get('ConstData')->getConst('langsDb').'.support_managers as sm
					 WHERE sm.sm_login = "'.$id.'" LIMIT 1';
			}
		}
		
		if ($q!==''){
		 

		    DB::executeQuery($q, 'product_support');

		   $rows = DB::fetchRow('product_support');

		}
	
		return $rows;
    }
    
    /**
     * 
     * 
     * 
     */
    public function getProductsAll($wtFree = TRUE, $wtBlocked = FALSE, $tree=true) {
    //if $platform == 0  all products, if 1 - only PC, etc
    
    $platform = 0;
    
	$db = VBox::get('ConstData')->getConst('langsDb');
	$q = 'SELECT c_name, p_page_link, p_wiki_link, p_id, p_title, p_nick, p_cat, p_download, p_downloads, p_featured,
	p_relation, p_rel_url, p_platform, platform_nick, platform_acronim, platform_order
		FROM '.$db.'.category
		    LEFT JOIN '.$db.'.category_'.$this->mLanguage.' ON cat_id = c_id
		    LEFT JOIN '.$db.'.products ON cat_id = p_cat
		    LEFT JOIN '.$db.'.platforms ON platform_id = p_platform';
	if(!$wtFree || !$wtBlocked) {
	    $q.= ' WHERE ';
	    $free = (!$wtFree ? 'p_free = 0' : '');
	    $block = (!$wtBlocked ? 'p_blocked = 0' : '');
	    $condition = $free.((!empty($free) && !empty($block)) ? ' AND ' : '').$block;
	    $q.= $condition;
	}
	$q.= ' ORDER BY cat_order, p_order';
	DB::executeQuery($q, 'prods');
        $rows = DB::fetchResults('prods');
        
        
        $array= array();
        $category = array();
        
        if ($tree)
        {
            foreach($rows as $key=>$value)
            {
                if (!in_array($value['p_cat'],$category))
                {
                    $category[] = $value['c_name'];
                    $array[$value['p_cat']]['category'] = $value['c_name'];
                    $array[$value['p_cat']]['featured'][$value['p_id']] = $value;
                    
                }
                else
                {
                    $array[$value['p_cat']]['featured'][$value['p_id']] = $value;
                }
            }
        }
        else
        {
            $array = $rows;
        }
    
	   return $array;
       
    }
    
    //italiano, 20.01.2015
    public function updateProductById($productId,$rows,$callback=false) 
    {
  		$productId = (int)$productId;
        
        if ($productId>0 && count($rows)>0)
        {
            $q = 'UPDATE '.VBox::get('ConstData')->getConst('langsDb').'.products SET ';
                  
            foreach($rows as $field=>$value)
            { 
                $set .= $field."=".$value.",";  
            } 
            
            $set = substr($set,0,-1);
            
            $q .= "$set WHERE p_id = '".$productId."'"; 
     
            $res = DB::executeAlter($q);
    
            if ($res)
            {
                if ($callback)
                {
    
            	   $rows = array();
            	   $q = 'SELECT p_id, p_title, p_version, p_faq_link, p_wiki_link, p_download, p_downloads 
            		 FROM '.VBox::get('ConstData')->getConst('langsDb').'.products
            		 WHERE p_id = '.$productId.' LIMIT 1';
            	    DB::executeQuery($q, 'prods');
            	    $rows = DB::fetchRow('prods');
    
            	   return $rows;
                }
                else{
                    return true;
                }
            }
        }
		
		return false;
    }
    //italiano, 20.01.2015
   	public function getProductByFile($file=null,$callback=true) 
    {
        if (isset($file))
        {
            $rows = array();
            $q = 'SELECT p_id, p_title, p_version, p_faq_link, p_wiki_link, p_download, p_downloads 
        		 FROM '.VBox::get('ConstData')->getConst('langsDb').'.products
        		 WHERE p_download LIKE \'%'.$file.'%\' LIMIT 1';
      	    DB::executeQuery($q, 'prods');
      	    $rows = DB::fetchRow('prods');
            
            if ($rows){
                
                if ($callback){
                    return $rows;
                }
                
                return true;                
            }
        }
        
		return false;
    }
    
}

?>