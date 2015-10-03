<?php

class DataModel{

    private $allDbAdapter;
    private $siteDbAdapter;
    
    public $tablename = 'data';
    public $langs = array();
    private $data = array();

    public function __construct($siteId) {
        
        
		$config = Zend_Registry::get('config');

        $params = $config->db->config->toArray();

		$params['dbname'] = $config->db->config->dballname;
        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
		$this->allDbAdapter->query('SET NAMES utf8');

        $dbAdapter = Zend_Registry::get('dbAdapter');
        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname'));
        $select->where('s_id = ?', $siteId);
        $siteDbName = $dbAdapter->fetchOne($select->__toString());

        $params['dbname'] = $siteDbName;

        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);
        $this->siteDbAdapter->query('SET NAMES utf8');
        
        if($this->preloaderLangs()){
           $this->preloaderData(); 
        }
        
    }

    public function __destruct() {
        $this->allDbAdapter = null;
        $this->siteDbAdapter = null;

    }
    
    public function preloaderData() {

        $select = $this->allDbAdapter->select();
        $select->from($this->tablename);	

        foreach($this->langs as $lang){
            $select->joinLeft($this->tablename."_$lang", "d_id = d{$lang}_d_id", array("{$lang}" => $this->tablename."_{$lang}.d{$lang}_value"));   
        }

        $select->order(array('d_nick'));
        
		$results =  $this->allDbAdapter->fetchAll($select->__toString());
        
        foreach($results as $item){
            $this->data[$item['d_id']] = $item;
        }

    }
    
    public function getData($_id=null, $_lang=null, $replace = true) {
        
        $data = $this->data;

          foreach($data as $key=>$item)
          {  
            
                if($replace)
                {
                    if($item['d_type'] == 'A')
                    {
                        foreach($this->langs as $lang){
    
                                ob_start();
                                print_r(unserialize($item["{$lang}"]));
                                $data[$key]["{$lang}"] = '<pre>'.ob_get_contents().'</pre>';
                                ob_clean();
                        }
                    
                    }  
                }
          }        

        if(isset($_id)){
            $data   =  $data[$_id];
        }
        
        if(isset($_lang)){
            $data   =  $data[$_lang];
        }
    
        return $data;
      
    }
    
    public function getDataToEdit($id=null) {
      
          $data = isset($this->data[$id]) ? $this->data[$id] : array();
          
          
          if(count($data))
          {
           
                if($data['d_type'] == 'A')
                {
                    foreach($this->langs as $lang)
                    { 

                        $html = '';
                        $this->parseArray2XML(unserialize($data["{$lang}"]),$html);
                        $data["{$lang}"] = $html;
                        
                        
                    }
                }     
            
          }
        
        return $data;
 
    }
    
    public function edit($id=null, $row) {

        if(isset($id)){
            $rowData = array();
            $rowData['d_type']      = $row['d_type'];
            $rowData['d_comment']   = $row['d_comment'];
            $rowData['d_nick']      = $row['d_nick'];
            
       	    $this->allDbAdapter->update($this->tablename,$rowData,$this->allDbAdapter->quoteInto('d_id = ?', $id));
        }

        foreach($this->langs as $lang){
            $row_lang = array();
            $field = "d{$lang}_value";
            
            $row_lang[$field] = $row[$field];
            
            if($row['d_type'] == 'A' && !empty($row_lang[$field])){
                $array = array();
                $this->parseXML2Array($row_lang[$field],$array);
                $row_lang[$field] =  serialize($array);
            }

            $this->allDbAdapter->update($this->tablename."_$lang",$row_lang,$this->allDbAdapter->quoteInto("d{$lang}_d_id = ?", $id));
        }
        
    }

    public function add($row) {

            $rowData = array();
            $rowData['d_type']      = $row['d_type'];
            $rowData['d_comment']   = $row['d_comment'];
            $rowData['d_nick']      = $row['d_nick'];
            
       	    $this->allDbAdapter->insert($this->tablename,$rowData);
            $pk = (int)$this->allDbAdapter->lastInsertId();


        foreach($this->langs as $lang){
            $row_lang = array();
            $field = "d{$lang}_value";
            
            $row_lang[$field] = $row[$field];
            $row_lang["d{$lang}_d_id"] = $pk;
            
            if($row['d_type'] == 'A' && !empty($row_lang[$field])){
                $array = array();
                $this->parseXML2Array($row_lang[$field],$array);
                $row_lang[$field] =  serialize($array);
            }

            $this->allDbAdapter->insert($this->tablename."_$lang",$row_lang);
        }
        
        return $pk;
    }

    public function delete($id=null) {
        if(isset($id)){
    		$id = intval($id);
            $this->allDbAdapter->delete($this->tablename, $this->allDbAdapter->quoteInto('d_id = ?', $id));         
            
            foreach($this->langs as $lang){    
                $this->allDbAdapter->delete($this->tablename."_$lang", $this->allDbAdapter->quoteInto("d{$lang}_d_id = ?", $id));
            }
        }
   }
   
   public function deletes($ids=null) {
   
       foreach($ids as $id){
            $this->delete($id);
       }
       
       return json_encode($ids);
   }  
    
   private function checkFields($fields, $required){

        foreach($fields as $key=>$value){
            
            if(!in_array($key,$required)){
                return false;
                echo 'false';
            }
        }
        
        return true;
        echo 'true';

   }
   
   public function parseXML2Array($xmlstr = NULL, &$array, $xml = NULL) 
   {

        if(isset($xmlstr)) {
            $xml = new SimpleXMLElement('<body>'.$xmlstr.'</body>');
        }
        
        $nodes = $xml->children();
        $index = 0;

        foreach($nodes AS $node) 
        {
            $attrs = $node->attributes();
            if(isset($attrs['key'])) {
                $key = strval($attrs['key']);    
            } 
            else 
            { 
                $key = $index; 
                $index++; 
            }

            if(isset($node->i)) {
                self::parseXML2Array(NULL, $array[$key], $node);   
            } 
            else 
            {
                if(count($node->children())) 
                {      
                    $subject = $node->asXML();
                    $pattern = '#\<\i.+?>(.+?)\<\/\i\>#is';
                    preg_match($pattern, $subject, $matches);
                                
                    if (isset($matches[1]) && !empty($matches[1]))
                    {
                        $node = $matches[1];
                    }
                }
                            
                $array[$key] = strval($node);
            }
        }
    }

    public function parseArray2XML($array, &$xmlstr)
    {
        if(!empty($array))
        {
            foreach($array AS $key => $value)
            {
					$xmlstr .= '
<i key="'.$key.'">';

				if(is_array($value))
				{
					$this->parseArray2XML($value, $xmlstr);
                }
                else
                {
					$xmlstr .= $value;
				}
                $xmlstr .= '</i>';
            }
        }
    }
    
    private function preloaderLangs(){
        $select = $this->siteDbAdapter->select();
        $select->from('languages', array('l_id', 'l_code', 'l_name'));
        $select->where('l_blocked = ?', 0);
        $select->order('l_order'); 
        
        $langs = array();
        $langs = $this->siteDbAdapter->fetchAll($select->__toString());    
            
        foreach($langs as $item){
                $this->langs[] = $item['l_code'];
        }
        
        if(count($this->langs)){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function openEditWindow($data,$lang) {
    	$data['en'] 	= htmlentities($data['en']);
    	$data['lang'] 		= $lang;
    	echo json_encode($data);
    }
    
    public function setData($id,$lang,$params, $canEdit=false) {
		$result = FALSE;
        $id 	= intval($id);
        
		if(in_array($lang,$this->langs) && isset($this->data[$id]) && $canEdit) {
  
            $result = true;
		              
            $field = "d{$lang}_value";
            $row_lang[$field] = $params['text'];
            
            if($this->data[$id]['d_type'] == 'A' && !empty($row_lang[$field])){
                $array = array();
                $this->parseXML2Array($row_lang[$field],$array);
                $row_lang[$field] =  serialize($array);
            }

            $select = $this->allDbAdapter->select();
            $select->from($this->tablename."_$lang");	
            $select->where("d{$lang}_d_id = ?", $id);
    		$issetChild =  $this->allDbAdapter->fetchRow($select->__toString());
            
            if(!$issetChild){
                $row_lang["d{$lang}_d_id"] = $id;
                $result = (bool)$this->allDbAdapter->insert($this->tablename."_$lang",$row_lang);    
            }
            else{
                $result = (bool)$this->allDbAdapter->update($this->tablename."_{$lang}",$row_lang,$this->allDbAdapter->quoteInto("d{$lang}_d_id = ?", $id));    
            }
		}
        
		return $result;
    }

}

?>