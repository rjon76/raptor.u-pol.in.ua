<?php

class Menu {
    private $mLanguage;
    private $mAddressPrefix;
    private $mPageAddress;
	private $page;
	private $PageId;
	
    public function __construct() {
	if (VBox::isExist('Page')) {
		$this->page = VBox::get('Page');
	    $this->mLanguage = $this->page->language;
            if(isset($this->page->address['prefix'])) {
                $this->mAddressPrefix = $this->page->address['prefix'];
            } else {
                $this->mAddressPrefix = '';
            }
		  $this->mPageAddress = $this->page->address['uri_address'];	
    	  $this->PageId = $this->page->getPageId();
	}
	else {
	    $this->mLanguage = 'en';
	    $this->mAddressPrefix = '';
		$this->PageId = 0;

	}

    }
    public function __destruct() {
		 $this->mLanguage = NULL;
		 $this->mAddressPrefix = NULL;
		 $this->mPageAddress = NULL;
		 $this->page = NULL; 
		 $this->PageId = NULL;
    }
	
    public function getMenu($alias, $level = 0) {
	$q = 'SELECT mi.*
		FROM menu as m, menu_item as mi
		 WHERE m.m_id = mi.mi_menu_id 
		 and m.m_alias = "'.$alias.'"
	     and mi.mi_level <='.$level.'
		 and mi.mi_hidden = "0"
		 ORDER BY mi.mi_menu_id, mi.mi_parent_id, mi.mi_level, mi.mi_order, mi.mi_name';

		DB::executeQuery($q, 'menu');
        $rows = DB::fetchResults('menu');
		$result = array();
		if(!empty($rows))
		 {
	    	$tsize = sizeof($rows);
		    for($i=0; $i<$tsize; $i++)
			{
//					$mi_link_alias = explode('|',$rows[$i]['mi_link_alias']);
					$mi_link_alias = preg_split("/[\s,]+/", $rows[$i]['mi_link_alias']);
					$page_not_view = explode(',',$rows[$i]['mi_pages_not_view']);
    		        if (!in_array($this->PageId, $page_not_view))
					{
						if ($this->checkurl($rows[$i]['mi_link'])){
							$mPageAddress = $rows[$i]['mi_link'];
						}else{
							$mPageAddress = (($this->mLanguage !='en' && strpos($rows[$i]['mi_link'], '/'.$this->mLanguage.'/')===false && strpos($rows[$i]['mi_link'], 'http://')===false && strpos($rows[$i]['mi_link'], 'https://')===false) ? '/'.($this->mLanguage) : '').$rows[$i]['mi_link'];
						}
                         
						if ( $this->mPageAddress  == $mPageAddress || in_array($this->mPageAddress, $mi_link_alias)){
							$selected = true;
						}
						else{
							$selected = false;
						}
						$LocalStrings = $this->page->getLocalStrings();
						$result[] = array('name' => $LocalStrings[$rows[$i]['mi_name']],
						  'title' => $LocalStrings[$rows[$i]['mi_title']],
						  'link' => $rows[$i]['mi_link'],
						  'attr' => $rows[$i]['mi_attr'],
						  'level' => $rows[$i]['mi_level'],
						  'class' => $rows[$i]['mi_class'],				  
						  'selected'=>$selected,
						  'pageadres'=>$mPageAddress
						  );
					}
			}
		 }

		return $result;
    }
/*------------------*/	
    public function getMenuByParentId($alias='', $parent_id = 0) {
	$q = 'SELECT mi.*
		FROM menu as m, menu_item as mi
		 WHERE m.m_id = mi.mi_menu_id 
		 and m.m_alias = "'.$alias.'"
	     and mi.mi_parent_id='.$parent_id.'
		 and mi.mi_hidden = "0"
		 ORDER BY mi.mi_order';
		// var_dump($q);

		DB::executeQuery($q, 'menu'.$parent_id);
        $rows = DB::fetchResults('menu'.$parent_id);
		$result = array();
		if(!empty($rows))
		 {
	    	$tsize = sizeof($rows);
		    for($i=0; $i<$tsize; $i++)
			{
//					$mi_link_alias = explode('|',$rows[$i]['mi_link_alias']);
					$mi_link_alias = preg_split("/[\s,]+/", $rows[$i]['mi_link_alias']);
					$page_not_view = explode(',',$rows[$i]['mi_pages_not_view']);
    		        if (!in_array($this->PageId, $page_not_view))
					{
						$mPageAddress = (($this->mLanguage!=='en' && strpos($rows[$i]['mi_link'], '/'.$this->mLanguage.'/')===false) ? '/'.($this->mLanguage) : '').$rows[$i]['mi_link'];
			
						if ( $this->mPageAddress  == $mPageAddress || in_array($this->mPageAddress, $mi_link_alias)){
							$selected = true;
						}
						else{
							$selected = false;
						}
						$LocalStrings = $this->page->getLocalStrings();
						$result[] = array('name' => $LocalStrings[$rows[$i]['mi_name']],
						  'title' => $LocalStrings[$rows[$i]['mi_title']],
						  'link' => $rows[$i]['mi_link'],
						  'attr' => $rows[$i]['mi_attr'],
						  'level' => $rows[$i]['mi_level'],
						  'class' => $rows[$i]['mi_class'],				  
						  'selected'=>$selected,
						  'pageadres'=>$mPageAddress,
						  'items'=>$this->getMenuByParentId( $alias, $rows[$i]['mi_id'])
						  );
					}
			}
		 }
		return $result;
    }
/*-------*/
	public function getSelected(&$menuitems)
	{
    	$tsize = sizeof($menuitems);
	    for($i=0; $i<$tsize; $i++)
		{
			
			if ($menuitems[$i]['selected']){
				 return true;
			}
			 else{
				$menuitems[$i]['selected'] =  $this->getSelected($menuitems[$i]['items']);

			}
		}
		return false;
	}
/*------------------------------*/
	private function pregtrim($str) {
		   return preg_replace("/[^\x20-\xFF]/","",@strval($str));
	}
/*-------*/
	private function checkurl($url) {
	   // режем левые символы и крайние пробелы
	   $url=trim($this->pregtrim($url));
	   // если пусто - выход
	   if (strlen($url)==0) return false;
	   //проверяем УРЛ на правильность
	   return preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}".
	   "(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
	   "org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
	   "!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&amp;".
	   "?+=\~/-]*)?(?:#[^ '\"&amp;&lt;&gt;]*)?$~i",$url,$ok);
	   
//	   return false; // если не правильно - выход
	   // если нет протокала - добавить
	   if (!strstr($url,"://")) $url="http://".$url;
	   // заменить протокол на нижний регистр: hTtP -&gt; http
	   $url=preg_replace("~^[a-z]+~ie","strtolower('\\0')",$url);
	   return $url;
	}	
	
}

?>