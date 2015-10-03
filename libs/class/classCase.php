<?php

class CCase {

    private $languageId;
	private $PageId;
    
	public function __construct() {
	if (VBox::isExist('Page')) {
	    $this->languageId = VBox::get('Page')->languageId;
		$this->PageId = VBox::get('Page')->getPageId();
          }
	else {
	    $this->languageId = 1;
		$this->PageId = 1;
	}
    }
 /*-----------------------*/	
	public function getCases($limit = NULL, $order = 'cs_id') {

//        $db = VBox::get('ConstData')->getConst('langsDb');

        $q = 'SELECT cs_autor, cs_text, cs_pages_not_view, ifnull(cs_link,"") as cs_link
              FROM case_study
			  WHERE cs_lang_id = '.$this->languageId.' and cs_hidden="0"			  
              ORDER BY '.$order;
		if ($limit !== NULL){
			$q .=' LIMIT '.$limit;
		}

        DB::executeQuery($q, 'caseList');
		$rows = DB::fetchResults('caseList');
        $caseList = array();
        foreach($rows AS $row) {
			$page_not_chow = explode(',',$row['cs_pages_not_view']);
            if (!in_array($this->PageId, $page_not_chow)){
				$caseList[] = $row;
			}
        }

        return $caseList;
    }
	
}

?>