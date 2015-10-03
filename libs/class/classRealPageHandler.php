<?php
class RealPageHandler {

    # PRIVATE VARIABLES
    private $blocksData;
    private $blocks;
    private $smarty;
    private $prepared;
    private $page;
    private $extensions;
    private $siteDB;
    private $localPath;
    private $localStrings=array(); //04.03.2015

    # PUBLIC VARIABLES

    // Конструктор, в качестве параметра принимает обьект класса Page
    public function __construct($localPath = '', $db = '') {

        $this->page = VBox::get('Page');

        if($localPath == '' && defined('LOCAL_PATH')) {
            $this->localPath = LOCAL_PATH;
        } else {
            $this->localPath = $localPath;
        }

        $this->siteDb = (!empty($db) ? $db.'.' : '');

        // Создаём обьект Смарти
        $this->smarty = new Smarty;
        $this->smarty->template_dir = $this->localPath.'templates/';
        $this->smarty->compile_dir = $this->localPath.'templates_c/';
        //$this->smarty->cache_dir = $this->localPath.'templates_cache/';
        $this->smarty->caching = FALSE;
        $this->prepared = FALSE;

        // Инклудим классы экстеншенов привязаных к странице
        $this->includeExtensions();
        // Инклудим классы глобальных экстеншенов привязаных к сайту
        $this->includeGlobalExtensions();

        // Выбираем данные блоков из базы и подготавливаем их к выводу
        if($this->getBlocksData()) {
            $this->prepareData();
        }
        
        // @author italiano, init class ClientScript 25.11.2014
        // [start]
        if (file_exists(ENGINE_PATH.'class/ClientScript.php')){
            if(!VBox::isExist('ClientScript')) { 
                    include_once(ENGINE_PATH.'class/ClientScript.php');
                    VBox::set('ClientScript', new ClientScript());
                }
        }
        // [end]
        // @author italiano, init class Banners 25.11.2014
        // [start]
        if (file_exists(ENGINE_PATH.'class/classBanners.php')){
                if(!VBox::isExist('Banners')) { 
                    include_once(ENGINE_PATH.'class/classBanners.php');
                    VBox::set('Banners', new Banners());
                }
        }
        // [end]
        // @author italiano, init class AdminSection 10.01.2015
        // [start]
        if (file_exists(ENGINE_PATH.'class/classAdminSection.php')){
                    if(!VBox::isExist('AdminSection')) { 
                        include_once(ENGINE_PATH.'class/classAdminSection.php');
                        VBox::set('AdminSection', new AdminSection());
                    }
        }
        // [end]
        
    }

    // Деструктор
    public function __destruct() {

        $this->smarty     = NULL;
        $this->blocksData = NULL;
        $this->blocks     = NULL;
        $this->prepared   = NULL;
        $this->page       = NULL;
        $this->extensions = NULL;
        $this->siteDb     = NULL;
    }


    /*
     обходим массив блоков и инициализируем все смартиевые переменные
    */
    public function prepareData() {

        $index          = -1;
        $blocks2pagesId = 0;

        foreach($this->blocksData as $row) {
            
            // Проверяем новый ли блок, если новый то инкерементим индекс
            if($blocks2pagesId != $row['bp_id']) {
                $blocks2pagesId = $row['bp_id'];
                $index++;
            }

            // Проходим по массиву и ищем в нём дочерние блоки по отношинию к
            // текущему блоку, если есть таковой то добовляем их имена файлов
            // в данные текущего блока
            foreach($this->blocksData as $tmpRow) {
                if($row['bp_id'] == $tmpRow['bp_parent']) {
                    $this->blocks[$index]['files_to_include'][$tmpRow['bp_id']] = $tmpRow['b_file'];
                }
            }
            if ($row['bd_hidden'] != 1){
            // Инициализация смартевских переменных
                switch($row['bf_type']) {
    
                    case 'S': // String
                        $this->blocks[$index][$row['bf_name']] = $row['bd_value'];
                    break;
    
                    case 'A': // Array
                    case 'I': // Image
                    case 'W': // Flash
                        if(strlen($row['bd_value'])) {
                            $this->blocks[$index][$row['bf_name']] = unserialize($row['bd_value']);
                        }
                    break;
    
                    case 'G': // Gloabal Extension
                        $row['bd_value'] = trim($row['bd_value']);
                        $funcData = array();
                        if(strlen($row['bd_value'])) {
                            $funcData = explode('|', $row['bd_value']);
                        }
    
                        $defData = explode('|', $row['bf_default']);
    
                        $func = $defData[0];
                        $extName = $defData[1];
    
                        $args = array();
    
                        for($i = 0; $i < count($funcData); $i++) {
                            $argData = explode(':', $funcData[$i]);
    
                            switch($argData[0]) {
                                case 'n': // variable Name
                                    $args[] = $$argData[1];
                                break;
                                case 'v': // variable Value
                                    $args[] = $argData[1];
                                break;
                            }
                        }
    
                        if(!VBox::isExist($extName)) {
                            VBox::set($extName, new $extName());
                        }
    
                        if(empty($args)) {
                            $this->blocks[$index][$row['bf_name']] = VBox::get($extName)->$func();
                        } else {
                            $this->blocks[$index][$row['bf_name']] = VBox::get($extName)->$func($args);
                        }
    					VBox::clear($extName);
                    break;
    
                    case 'E': // Extension
                        if(isset($this->extensions[$row['bf_name']])) {
                            $extName = $row['bf_name'];
                            $row['bd_value'] = trim($row['bd_value']);
    
                            if(!empty($row['bd_value'])) {
                                $funcData = explode('|', $row['bd_value']);
                                $args = array();
                                for($i = 0; $i < count($funcData); $i++) {
                                    $argData = explode(':', $funcData[$i]);
                                    switch($argData[0]) {
                                    case 'n': // variable Name
                                        $args[] = $$argData[1];
                                    break;
                                    case 'v': // variable Value
                                        $args[] = $argData[1];
                                    break;
                                    }
                                }
                            }
    
    
                            //if(!VBox::isExist($extName)) {
                            if(empty($args)) {
                                VBox::set($extName, new $extName());
                            } else {
                                VBox::set($extName, new $extName($args));
                            }
                            //}
    
                            $this->blocks[$index][$row['bf_name']] = VBox::get($extName)->getResult();
    						VBox::clear($extName);
                        }
                    break;
    
                    case 'J': // Json Array
                        if(strlen($row['bd_value'])) {
                            $this->blocks[$index][$row['bf_name']] = json_encode(unserialize($row['bd_value']));
                        }
                    break;
                    
                    case 'L': // select
                        if(strlen($row['bd_value'])) {
                            
                            /* italiano 13.02.2015 */
                            $val = unserialize($row['bd_value']);
                            
                            if (is_array($val) && count($val) == 1){
                                $this->blocks[$index][$row['bf_name']] = key($val);   
                                $this->blocks[$index][$row['bf_name'].'_val'] = current($val);  
                            }else{
                                $this->blocks[$index][$row['bf_name']] = $val;    
                            }
                            /* end */
    
                        }
                    break;
                    

                
                
                }
                
                /* italiano, 04.03.2015 */
                    if (defined('WWW2') && WWW2){
        
                        $this->blocks[$index]['edit_params']['block'] = $row['b_name']; 
                        $this->blocks[$index]['edit_params']['file'] = $row['b_file'];
                        
                        if (isset($row['bf_type']) && !empty($row['bf_type']))
                        {
                            if($row['bf_type'] == "L" || $row['bf_type'] == "I" || $row['bf_type'] == "A" || $row['bf_type'] == "W"){
                                $value = array();
                                $value = unserialize($row['bd_value']);
                            }
                            else{
                                $value = '';
                                $value = $row['bd_value'];
                            }
                            $this->blocks[$index]['edit_params']['fields'][] = array('type'=>$row['bf_type'], 'id'=>$row['bd_id'],'name'=>$row['bf_name'],'fsid'=>$row['bd_bp_id'], 'value'=>$value, 'default'=>$row['bf_default']); 
                        }
                        
                    }
                    /* [end]*/  
                      
            } // if hidden field
            

        
        }
		$this->prepared = TRUE;
    }

    /*
     Выводим готовую страницу
    */
    public function printPage()
    {
		if($this->prepared)
		{
            $headers = $this->page->getHeaders();

            foreach($headers AS $header)
            {
                header($header);
            }
            
            $this->smarty->assign('page_title',		$this->page->title);
			$this->smarty->assign('page_id',		$this->page->getPageId()); /*added 30.01.2014*/
            $this->smarty->assign('page_lang', 		$this->page->language);
            $this->smarty->assign('page_meta', 		$this->page->getMetas());
            $this->smarty->assign('page_css', 		$this->page->getCssList());
            $this->smarty->assign('page_js', 		$this->page->getJSList());

            $this->smarty->assign('lstrings', 		$this->page->getLocalStrings());
            //$this->smarty->assign('lstrings', 		$this->getLstrings()); /* italiano, 04.03.2015 */
            
            $this->smarty->assign('related_pages', 	$this->page->getRelativePageAddresses());
			$this->smarty->assign('page_address', $this->page->address['uri_address']);
            $this->smarty->assign('page_options', $this->page->options); /* added 18.11.2014, italiano */
			if(VBox::get('ConstData')->getConst('realDomain'))
	            $this->smarty->assign('realDomain', VBox::get('ConstData')->getConst('realDomain'));
				
			if(VBox::get('ConstData')->getConst('use_min')) /*added 2014-07-29*/
	            $this->smarty->assign('use_min', VBox::get('ConstData')->getConst('use_min'));
				
			if(VBox::get('ConstData')->getConst('cachedDomain'))
	            $this->smarty->assign('cachedDomain', VBox::get('ConstData')->getConst('cachedDomain'));

			if(VBox::get('ConstData')->getConst('siteId'))
	            $this->smarty->assign('siteid', VBox::get('ConstData')->getConst('siteId'));

			if($addMessenger = VBox::get('ConstData')->getConst('addMessenger'))
			{
                $this->smarty->assign('is_messenger_on', true);            
	    	}
            
            $blocks = new BlocksData($this->blocks);
            $blocks->lang = $this->page->language; //added italiano 31.03.2015
            
            $this->smarty->register_object('blocks', $blocks);
			$this->smarty->register_object('page',	$this->page); /*added garbagecat76 23.10.2014*/
            
            
            // @author italiano, init class Banners 25.11.2014
            // [start]
                if(VBox::isExist('Banners')) { 
                    $this->smarty->register_object('banners', VBox::get('Banners'));
                }
            // [end]
            
            // @author italiano, register_object ClientScript for Smarty  25.11.2014
            // [start]
            /*
                if(VBox::isExist('ClientScript')) { 
                    $this->smarty->register_object('ClientScript', VBox::get('ClientScript'));
                }
            */
            // [end]
            
            // @author italiano, init class AdminSection 10.01.2015
            // [start]
            if(VBox::isExist('AdminSection') && VBox::get('AdminSection')->isEdit()) { 
                VBox::get('AdminSection')->page_id = $this->page->getPageId();
                VBox::get('AdminSection')->site_id = VBox::get('ConstData')->getConst('siteId');
                VBox::get('AdminSection')->sites = self::getSites();
                VBox::get('AdminSection')->localPath = $this->localPath;
                VBox::get('AdminSection')->blocks = $this->blocks;
                VBox::get('AdminSection')->template_dir = $this->smarty->template_dir;
                VBox::get('AdminSection')->localstrings = $this->page->getLocalStrings();
                VBox::get('AdminSection')->page_css = $this->page->getCssList();
                VBox::get('AdminSection')->page_js = $this->page->getJSList();

            }
            // [end] 
            
			$maintpl = $this->getMainTpl();
            $this->smarty->display($maintpl);
            
            if(VBox::isExist('AdminSection')) { 
                VBox::get('AdminSection')->init();
            }

        }
    }

    /*
     Выбираем данные из базы
    */
    private function getBlocksData() {

        $q = 'SELECT bd.bd_value,
                     bd.bd_hidden,
                     bf.bf_name,
                     bf.bf_type,
                     bf.bf_default,
                     bp.bp_id,
                     bp.bp_parent,
                     bp.bp_block_id,
                     bd.bd_bp_id,
                     bd.bd_id,
                     b.b_name,
                     b.b_file

              FROM '.$this->siteDb.'blocks2pages AS bp
                LEFT JOIN '.$this->siteDb.'blocks_data AS bd ON bp.bp_id = bd.bd_bp_id
                LEFT JOIN '.$this->siteDb.'blocks_fields AS bf ON bf.bf_id = bd.bd_field_id
                LEFT JOIN '.$this->siteDb.'blocks AS b ON b.b_id = bp.bp_block_id
              WHERE bp.bp_page_id = '.$this->page->getPageId().' AND bp.bp_hidden != \'1\'  
              ORDER BY bp.bp_order';

        DB::executeQuery($q, 'blocks_data');
        $result = DB::fetchResults('blocks_data');

        if(!empty($result)) {
            $this->blocksData = $result;
            return TRUE;
        }

        return FALSE;
    }
    
    private function getSites($id=null) {

        if (isset($id)){
            $where = " WHERE s_id=$id ";
        }
        else{
            $where = "";
        }
        $q = 'SELECT * FROM '.VBox::get('ConstData')->getConst('adminDb').'.sites AS s '.$where.' ORDER BY s_hostname';

        DB::executeQuery($q, 'sites');
        $result = DB::fetchResults('sites');

        if(!empty($result)) {
            return $result;
        }

        return false;
    }
    /*
     Инклудим экстеншены привязаные к странице
    */
    private function includeExtensions() {
        $extensions = $this->page->getExtensions();

        if(!empty($extensions)) {

            $q = 'SELECT ext_id, ext_name, ext_nick
                  FROM '.$this->siteDb.'extensions
                  WHERE ext_id IN('.implode(',',$extensions).')
                    AND ext_type = "L"
                    AND ext_blocked = 0';

            DB::executeQuery($q,'exts_data');
            $result = DB::fetchResults('exts_data');

            foreach($result AS $row) {
                $this->extensions[$row['ext_nick']] = $row['ext_id'];
                if(file_exists($this->localPath.'application/localExt/'.$row['ext_nick'].'/index.php')) {
                    include_once($this->localPath.'application/localExt/'.$row['ext_nick'].'/index.php');
                }
            }
        }
    }

    /*
     Инклудим глобальные экстеншены привязаные к сайту
    */
    private function includeGlobalExtensions() {

        $q = 'SELECT ext_id, ext_name, ext_nick
                  FROM '.$this->siteDb.'extensions
                  WHERE ext_type = "G"
                    AND ext_blocked = 0';

            DB::executeQuery($q,'gexts_data');
            $result = DB::fetchResults('gexts_data');

            foreach($result AS $row) {
                $this->extensions[$row['ext_nick']] = $row['ext_id'];
                if(file_exists($this->localPath.'application/globalExt/'.$row['ext_nick'].'/index.php')) {
                    include_once($this->localPath.'application/globalExt/'.$row['ext_nick'].'/index.php');
                }
            }
    }
	/*-------------*/
	private function getMainTpl()
	{
		$tpl = 'index.tpl';
		if ($this->blocksData)
		{
			 $res = array_shift($this->blocksData); 
			 $tpl = $res['b_file'];
		}
		return $tpl;
	}
    
    private function getLstrings(){
        
        
        foreach($this->blocks as $data)
        {
            if (!isset($data['files_to_include'])){
                self::StrToInclude($data);
            }
        }

        foreach($this->blocks as $file)
        {
            if (isset($file['files_to_include'])){
                self::filesToInclude($file['files_to_include']);
            }
        } 
        
        if (count($this->localStrings)){
            return $this->localStrings;
        }
        else{
            return $this->page->getLocalStrings();
        }
        
    }
    
    public function filesToInclude($data=null,$type='lstrings'){

            foreach($data as $_file) 
            {
                $file = $this->smarty->template_dir.$_file;
                
                if (file_exists($file))
                {
                    $content = file_get_contents($file);
                    
                    
                    if ($type == 'lstrings')
                    {
       					preg_match_all('#(\{\$lstrings.(.*?)\})#', $content, $results);
                        
                        if(count($results[0]))
                        {
    						for ($i=0; $i< count($results[0]); $i++) 
                            {
                                $lstring = '';
                                if (isset($results[2][$i]))
                                {
                                    $lstring = $results[2][$i];
                                    
                                    $length = strpos($lstring,'|');
                                    
                                    if ($length>0)
                                    {
                                       $lstring = substr($lstring,0,$length);
                                    }
                                    
                                    if (!isset($this->localStrings[$lstring])){
                                        $this->localStrings[$lstring] = $this->page->getLocalStrings($lstring); 
                                    }
                                }
    						}
                        } 
                    }//lstrings
                }  
            }
    }
    
	private function StrToInclude($a,$type='lstrings') 
    {
        if ($type == 'lstrings')
        {
    		if ( is_array($a) ) {
    			foreach ($a as $key=>$node) {
    				if (is_array($node)) {
    					$result[$key] =  $this->StrToInclude($node);
    				} else {
    					preg_match_all('#(\{\$lstrings.(.*?)\})#', $node, $class_metot);
                        if(count($class_metot[0]))
                        {
    						for ($i=0; $i< count($class_metot[0]); $i++) {
                              
                                $lstring = $class_metot[2][$i];
                              
                                if (!isset($this->localStrings[$lstring])){
                                    $this->localStrings[$lstring] = $this->page->getLocalStrings($lstring);
                                }
    						}
    					}
    				}
    			}
    		}
        }//lstrings
	}

}

?>