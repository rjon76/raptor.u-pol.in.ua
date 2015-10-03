<?php

/**
*  Class export pages
* @author garbagecat76@gmail.com
* @version 0.1
* 
*/

class ExportPages {
    private $blocksData = array();
    private $sitePath;
    private $pageIds;
    private $siteDb;
    private $method;
    private $blocks;

    public function __construct($sitePath='', $siteDb, $method='export', $pageIds) 
    {
    
        if($sitePath == '' && defined('LOCAL_PATH')) {
            $this->sitePath = LOCAL_PATH;
        } else {
            $this->sitePath = $sitePath;
        }
        
        $this->siteDb = (!empty($siteDb) ? $siteDb.'.' : '');

        $this->pageIds = $pageIds;
        $this->method = $method;

        if (self::getBlocksData())
        {
            self::prepareData();
        }
 
    }

    public function __destruct() 
    {
        $this->blocksData = NULL;
    }

    private function getBlocksData() 
    {
        $callback = false;
        
        if (is_array($this->pageIds))
        {
            foreach($this->pageIds as $pageId)
            {
                $q = 'SELECT b.b_id,
                             b.b_file,
                             b.b_name,
                             bf.bf_id,
                             bf.bf_type,
                             bf.bf_block_id,
                             bf.bf_name,
                             bf.bf_default,
                             bp.bp_id,
                             bp.bp_page_id,
                             bp.bp_block_id,
                             bp.bp_parent,
                             bp.bp_order,
                             bd.bd_id,
                             bd.bd_value,
                             bd.bd_bp_id,
                             bd.bd_field_id
        
                      FROM '.$this->siteDb.'blocks2pages AS bp
                        LEFT JOIN '.$this->siteDb.'blocks_data AS bd ON bp.bp_id = bd.bd_bp_id
                        LEFT JOIN '.$this->siteDb.'blocks_fields AS bf ON bf.bf_id = bd.bd_field_id
                        LEFT JOIN '.$this->siteDb.'blocks AS b ON b.b_id = bp.bp_block_id
                      WHERE bp.bp_page_id = '.$pageId.'
                      ORDER BY bp.bp_order';
        
                    DB::executeQuery($q, 'blocks_data');
                    $result = DB::fetchResults('blocks_data');
            
                    if(!empty($result)) {
                        $this->blocksData[$pageId] = $result;
                        $callback = true;
                    }
            }
        }
        
        return $callback;
    }
    
    private function prepareData() {

        require_once('classXML2Array.php');        

            $xml =  Array2XML::createXML('blocks', $this->blocksData);
            $file =  $this->sitePath .'tmp/'. date('Y-m-d', time()).'.xml';            
            
            ob_start (); 
            
            echo $xml->saveXML();
            
            $data = ob_get_contents();
            
            ob_end_clean ();
            
            $fp = @fopen ($file, "w");
            @fwrite ($fp, $data);
            @fclose ($fp); 

    }
    
}
?>