<?php
class BlocksData {

    private $index;
    private $data;
	//private $_data;
    public $lang='en';
	private	$_alias = array(
				'lstrings' => "VBox::get('Page')->getLocalizedString('%s')",
                'globalExt'=>array(
                        'getPrice'=>array(
                                'class'=>'purchaseInfo',
                                'func'=>'getPriceInfo',
                            ),
                        'getProductName'=>array(
                                'class'=>'productInfo',
                                'func'=>'getName',
                            ),
                        'getName'=>array(
                                'class'=>'productInfo',
                                'func'=>'getName',
                            ),
                        'getDownloads'=>array(
                                'class'=>'productInfo',
                                'func'=>'getDownloads',
                            ),
                ),
			);

    public function __construct($data) {
        $this->index = 0;
        $this->data = $data;
    }
/*
    public function initEdit($data) {
        $this->_data = $data;
    }
*/
    public function __destruct() {
        $this->index = null;
        $this->data = null;
    }


	
    public function getVars() {
		
		//if (in_array(VBox::get('ConstData')->getConst('siteId'), array(105))) {
		
			$this->data[$this->index] = $this->my_walk_recursive($this->data[$this->index]);

//		}

        //added, italiano, 12.01.2014 
        // />>     
        if(VBox::isExist('AdminSection')) { 
            VBox::get('AdminSection')->showEdit($this->data[$this->index]);
        }
        // /<<
        return $this->data[$this->index++]; 
    }
	
/*    
   	private function my_walk_recursive($a) {
		if ( is_array($a) ) {
			foreach ($a as $key=>$node) {
				if (is_array($node)) {
					$result[$key] =  $this->my_walk_recursive($node);
				} else {
					if ( preg_match_all('#(\{\$(.*?)\})#', $node, $class_metot) ) {
						for ($i=0; $i< count($class_metot[0]); $i++) {
						
							$trans[$class_metot[1][$i]] = $this->_get_var($class_metot[2][$i]);
						
						}
						$result[$key] = strtr($node,$trans);
					} else {
						$result[$key] = $node;
					}
				}
			}
			return $result;
		} else return $a;
	}
*/    
    /**
     * @edit 12.12.2014, italiano
     * added function get result work globalExt & localExt functions
     */
	private function my_walk_recursive($a) {
		if ( is_array($a) ) {
			foreach ($a as $key=>$node) {
				if (is_array($node)) {
					$result[$key] =  $this->my_walk_recursive($node);
				} else {
				    
				    $usePregMatch = false;
                    
					preg_match_all('#(\{\$(.*?)\})#', $node, $class_metot);
                    if(count($class_metot[0]))
                    {
                        $usePregMatch=true;
						for ($i=0; $i< count($class_metot[0]); $i++) {
							$trans[$class_metot[1][$i]] = $this->_get_var($class_metot[2][$i]);
						}
                        
						$result[$key] = strtr($node,$trans);
					}
                    
                    preg_match_all('#(\{globalExt (.*?)\})#', $node, $class_metot);
                    if(count($class_metot[0]))
                    {
                        $usePregMatch=true;
						for ($i=0; $i< count($class_metot[0]); $i++) {
							$trans[$class_metot[1][$i]] = $this->_getGlobalExt($class_metot[2][$i]);
						}
                        
						$result[$key] = strtr($node,$trans);
					}
                    
                    preg_match_all('#(\{localExt (.*?)\})#', $node, $class_metot);
                    if(count($class_metot[0]))
                    {
                        $usePregMatch=true;
						for ($i=0; $i< count($class_metot[0]); $i++) {
							$trans[$class_metot[1][$i]] = $this->_getLocalExt($class_metot[2][$i]);
						}
                        
						$result[$key] = strtr($node,$trans);
					}
                   
                    preg_match_all('#(\{lang(.*?)\})#', $node, $class_metot);
                    if(count($class_metot[0]))
                    {
                        $usePregMatch=true;
						for ($i=0; $i< count($class_metot[0]); $i++) {
							$trans[$class_metot[1][$i]] = $this->_getLang($class_metot[2][$i]);
						}
                        
						$result[$key] = strtr($node,$trans);
					}
                    

                    if(!$usePregMatch) {
						$result[$key] = $node;
					}
				}
			}
			return $result;
		} else return $a;
	}
	
	private function _get_var($string) {

		$string = explode(".", $string);

		if (array_key_exists($string[0], $this->_alias) ){
			//return sprintf( $this->_alias[$string[0]], $string[1] );
			eval( '$str = '.sprintf( $this->_alias[$string[0]], $string[1] ).';'  );
		//	eval( '$str = '.vsprintf( $this->_alias[$string[0]], $string ).';'  );
            
            return $str;
		}
		
		if ( count($string) == 2 ) {
			
			if ( VBox::isExist($string[0]) ) {
				$class = VBox::get($string[0]);
			} else {
				if (class_exists($string[0]))
				{
					eval( '$class = new '.$string[0].'();' );
					VBox::set($string[0], $class);
				}
			}

			if ($class) {
				eval('$var = $class->'.$string[1].';');
				return $var;
			}
			
		} elseif ( count($string) == 1 ) {
			if(!VBox::isExist($string[0])) {
				return VBox::get($string[0]);
			}
		}
		return '';
	}
    /**
     * @author italiano
     * @date 12.12.2014
     * return result globalExt function
     */
   	private function _getGlobalExt($string=null) {

        $string = trim($string);
        $content = '';
        $data = array();
        if(isset($string)){
            $data = explode(' ', $string);
        }

        $class = $method = $func = $args = null;
        
        foreach($data as $value)
        {
            list($type,$attr)=explode('=',$value);
            $attr = trim(str_replace('"','',$attr));
            $type = trim($type);

            switch($type){
            
                case "method":
                    $method = $attr;
                        break;
                        
                case "class":
                    $class = $attr;
                        break;
                    
                case "func":
                    $func = $attr;
                        break;
                    
                case "params":
                    $args =  explode(',', $attr);
                        break;
            }
        }

        if (isset($method)){
            
            if (isset($this->_alias['globalExt'][$method])){
                
                $class = $this->_alias['globalExt'][$method]['class'];
                $func = $this->_alias['globalExt'][$method]['func'];
            }
        }

        if (file_exists(LOCAL_PATH.'application/globalExt/'.$class.'/index.php'))
        {
            if(!VBox::isExist($class)) { 
                
                VBox::set($class, new $class());
            }

            if (isset($func))
            {
                
                if(isset($args)) {
                    $content =  VBox::get($class)->$func($args);
                }else{
                    $content =  VBox::get($class)->$func();
                }
                
            }else{
                if (method_exists(VBox::get($class), 'getResult')){
                    $func = 'getResult';
                    $content = VBox::get($class)->$func();
                }
            }
            
            VBox::clear($class);
                    
            return $content;
        
        }
        
        
        return '';

	}
    
    /**
     * @author italiano
     * @date 12.12.2014
     * return result localExt function
     */
   	private function _getLocalExt($string=null) {

        $string = trim($string);
        $content = '';
        $data = array();
        if(isset($string)){
            $data = explode(' ', $string);
        }

        $class = $args = null;
        
        foreach($data as $value)
        {
            list($type,$attr)=explode('=',$value);
            $attr = trim(str_replace('"','',$attr));
            $type = trim($type);

            switch($type){
                case "class":
                    $class = $attr;
                        break;
                    
                case "params":
                    $args =  explode(',', $attr);
                        break;
            }
        }
       
        if (file_exists(LOCAL_PATH.'application/localExt/'.$class.'/index.php'))
        {
            if(isset($args)){
                VBox::set($class, new $class($args));
            }else{
                VBox::set($class, new $class());
            }
                
            $content =  VBox::get($class)->getResult();
                      
            return $content;
            VBox::clear($class);
        }
        
        return '';

	}
    
    /**
     * @author italiano
     * @date 31.03.2015
     */
   	private function _getLang($method=null) 
    {
        $content = '';
        
        if (isset($method))
        {
            $method = trim($method);
        
            switch($method){
                        
                case "locale":
                    $content = $this->lang;    
                        break;  
                case "after":
                    if ($this->lang !='en'){
                        $content = "/$this->lang/";    
                    }   
                        break;    
                case "end":
                    if ($this->lang !='en'){
                        $content = "$this->lang/";    
                    }   
                        break;  
                case "before":
                    if ($this->lang !='en'){
                        $content = "/$this->lang";    
                    }   
                        break;  
                default:
                    if ($this->lang !='en'){
                        $content = "/$this->lang";    
                    }
                        break;
            }

        }
        
        return $content;

	}
	
    //get index block for function getVars()
    public function getIndex() {
        return $this->index;
    }
	
}

?>