<?php
include_once('page.php');

class Pages {

    # PRIVATE VARIABLES
    private $siteDbAdapter;
    private $siteDbName;
    private $sitePath;
    private $page;
    private $siteId;

    # PUBLIC VARIABLES

    // constructor
    public function __construct($siteId) {

        $this->siteId = $siteId;
        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname', 's_path'));
        $select->where('s_id = ?', $siteId);

        $config = $dbAdapter->fetchRow($select->__toString());
        $this->siteDbName = $config['s_dbname'];
        $this->sitePath = $config['s_path'];

        include_once($this->sitePath.'application/includes.inc.php');
//		var_dump($this->sitePath.'application/includes.inc.php');
//		var_dump(file_exists($this->sitePath.'application/includes.inc.php'));

        IniParser::getInstance()->setIni($this->sitePath.'application/config.ini', TRUE);

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $params['dbname'] = $this->siteDbName;

        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);
        Zend_Registry::set('siteDbAdapter', $this->siteDbAdapter);
        $this->siteDbAdapter->query('SET NAMES utf8');

    }

    // destuctor
    public function __destruct() {
        $this->siteDbAdapter = NULL;
        $this->siteDbName    = NULL;
        $this->sitePath      = NULL;
    }

    /*
     Ïîëó÷àåì ñïèñîê ñòðàíèö ñàéòà
    */
    public function getPagesList($lang = NULL, $order = array('pg_lang', 'pg_id')) {
        $select = $this->siteDbAdapter->select();

        $select->from('pages', '*');
        if(isset($lang)) {
            $select->where('pg_lang = ?', $lang);
        }
//        $select->order(array('pg_lang', 'pg_id'));
        $select->order($order);
        return $this->siteDbAdapter->fetchAll($select->__toString());
    }
    /*
     Äîáîâëÿåì ñòðàíèöó
    */
    public function addPage($address,
                            $title,
                            $menuTitle,
                            $lang,
                            $parent,
                            $relative,
                            $css,
                            $jscript,
                            $priority,
                            $cacheable,
                            $hidden,
                            $indexed,
                            $extensions,
							$headers) {

        $row = array(
            'pg_address'    => $address,
            'pg_title'      => $title,
            'pg_menu_title' => $menuTitle,
            'pg_lang'       => $lang,
            'pg_parent'     => $parent,
            'pg_relative'   => $relative,
            'pg_css'        => $css,
            'pg_jscript'    => $jscript,
            'pg_priority'   => $priority,
            'pg_cacheable'  => $cacheable,
            'pg_hidden'     => $hidden,
            'pg_indexed'    => $indexed,
            'pg_extensions' => $extensions,
			'pg_headers' => $headers
        );

        $this->siteDbAdapter->insert('pages', $row);
        $newPageId = $this->siteDbAdapter->lastInsertId();

        $bpRow = array(
            'bp_block_id' => 1,
            'bp_page_id' => $newPageId,
            'bp_parent' => 0,
            'bp_order' => 0
        );
        $this->siteDbAdapter->insert('blocks2pages', $bpRow);

        return $newPageId;
    }

    /*
     Ïîëó÷èòü îáúåêò Page
    */
    public function getPage($pageId) {

        VBox::set('ConstData', new ConstData($this->siteDbName));

        return new AdminPage($pageId, $this->siteDbName);
    }

    /*
     Ïðîâåðèòü íà ñóùåñòâîâàíèå àäðåññà
    */
    public function checkPageAddress($address, $pageId = NULL) {
        
        $select = $this->siteDbAdapter->select();
        $select->from('pages', array('pg_id_cnt' => 'COUNT(pg_id)'));
        $select->where('pg_address = ?', trim($address));

        if(isset($pageId)) {
            $select->where('pg_id <> ?', $pageId);
        }

        return $this->siteDbAdapter->fetchOne($select->__toString());
    }
    
    public function checkPageAddressAndReturn($address, $pageId = NULL) {
        
        $select = $this->siteDbAdapter->select();
        $select->from('pages',array('pg_id','pg_lang','pg_hidden','pg_address','pg_relative'));
        
        $select->where('pg_address = ?', trim($address));

        if(isset($pageId)) {
            $select->where('pg_id <> ?', $pageId);
        }

        $result = $this->siteDbAdapter->fetchRow($select->__toString());
        
        if($result) {
            return $result;
        } else {
            return false;
        }
    }
    /*
     Ïðîâåðèòü íà ñóùåñòâîâàíèå ñòðàíèöè ïî id
    */   
    public function checkPageById($pageId,$return=false) {
        $select = $this->siteDbAdapter->select();
        $select->from('pages', '*');
        $select->where('pg_id = ?', $pageId);
        $result = $this->siteDbAdapter->fetchRow($select->__toString());

        if(!empty($result)) {
            
            if($return){
                return $result;
            }
            else{
                return true;    
            }
            
        } else {
            return false;
        }
    }

    /*
     Óäàëèòü ñòðàíèöó ñî âñåìè ïàòðàõàìè
    */
    public function deletePage($pageId) {

        $select = $this->siteDbAdapter->select();
        $select->from('blocks_data', array('bd_id'));
        $select->joinLeft('blocks2pages', 'bp_id = bd_bp_id');
        $select->where('bp_page_id = ?', $pageId);
        $result = $this->siteDbAdapter->fetchAll($select->__toString());

        $bdIds = array();
        foreach($result AS $value) {
            $bdIds[] = $value['bd_id'];
        }

        if(!empty($bdIds)) {
            $this->siteDbAdapter->delete('blocks_data', $this->siteDbAdapter->quoteInto('bd_id IN(?)', $bdIds));
        }

        $this->siteDbAdapter->delete('blocks2pages', $this->siteDbAdapter->quoteInto('bp_page_id = ?', $pageId));
        $this->siteDbAdapter->delete('metas', $this->siteDbAdapter->quoteInto('mt_page_id = ?', $pageId));

        $this->siteDbAdapter->delete('pages', $this->siteDbAdapter->quoteInto('pg_id = ?', $pageId));
    }
    
    public function cleareLogByPageId($pageId) {

        $this->siteDbAdapter->delete('pages_logs', $this->siteDbAdapter->quoteInto('pl_page_id = ?', $pageId));
    }
    /*
     Óäàëèòü íàáîð ñòðàíèö ñî âñåìè ïàòðàõàìè
    */
    public function deletePages($pageIds) {
        foreach($pageIds AS $id) {
            $this->deletePage($id);
        }
    }
    public function cleareLogsPages($pageIds) {
        foreach($pageIds AS $id) {
            $this->cleareLogByPageId($id);
        }
    }
    /*
     Ñáðîñèòü ôëàã "pg_cached"
    */
    public function cachePages($pageIds) {

        include_once($this->sitePath.'application/includes.inc.php');

        include_once(ENGINE_PATH.'class/classPageReCacher.php');
        include_once(ENGINE_PATH.'class/classReCacher.php');

        $reCacher = new ReCacher($this->sitePath, 'cache');
	$reCacher->setLogMode(2);

        $res = TRUE;
	if(is_array($pageIds)) {
	    $tsize = sizeof($pageIds);
	    for($i = 0; $i < $tsize; $i++) {
		$res = $res && $reCacher->rebuildCachePageAdress($pageIds[$i]);
//		$result = $reCacher->rebuildCachePageAdress($pageIds[$i]);
//		if (!$result) $res = false;
	//sleep(10);
	    }
	}

        return $res;
    }

    public function clearCachePages($pageIds) {

        include_once($this->sitePath.'application/includes.inc.php');
        include_once(ENGINE_PATH.'class/classPageReCacher.php');
        include_once(ENGINE_PATH.'class/classReCacher.php');

        $reCacher = new ReCacher($this->sitePath, 'cache');
	$reCacher->setLogMode(2);

        $res = TRUE;

	if(is_array($pageIds)) {
	    $tsize = sizeof($pageIds);
	    for($i = 0; $i < $tsize; $i++) {
		$result = $reCacher->clearCachePage($pageIds[$i]);
		if (!$result) $res = false;
//		$res = $reCacher->clearCachePage($pageIds[$i]);
		
		}
	}

        return $res;
    }

    /*
     Êëîíèðîâàòü ñòðàíèöó
    */
    public function clonePage($sourcePageId, $targetPageId, $clonePageData = FALSE, $clonePageMeta = FALSE) {

        if($clonePageData) {
            $select = $this->siteDbAdapter->select();
            $select->from('pages', array('pg_title',
                                         'pg_css',
                                         'pg_jscript',
                                         'pg_extensions',
                                         'pg_indexed',
                                         'pg_priority',
                                         'pg_cacheable',
                                         'pg_cached',
                                         'pg_menu_title',
										 'pg_headers'));
            $select->where('pg_id = ?', $sourcePageId);
            $sourcePageData = $this->siteDbAdapter->fetchRow($select->__toString());

            $this->siteDbAdapter->update('pages', $sourcePageData, $this->siteDbAdapter->quoteInto('pg_id = ?', $targetPageId));
        }

        if($clonePageMeta) {
            $select = $this->siteDbAdapter->select();
            $select->from('metas', array('mt_name',
                                         'mt_content',
                                         'mt_lang'));
            $select->where('mt_page_id = ?', $sourcePageId);
            $sourcePageMetas = $this->siteDbAdapter->fetchAll($select->__toString());

            $this->siteDbAdapter->delete('metas', $this->siteDbAdapter->quoteInto('mt_page_id = ?', $targetPageId));

            foreach($sourcePageMetas AS $meta) {
                $row = array(
                    'mt_page_id' => $targetPageId,
                    'mt_name' => $meta['mt_name'],
                    'mt_content' => $meta['mt_content'],
                    'mt_lang' => $meta['mt_lang']
                );

                $this->siteDbAdapter->insert('metas', $row);
            }
        }

        $select = $this->siteDbAdapter->select();
        $select->from('blocks_data', array('bd_id'));
        $select->joinLeft('blocks2pages', 'bp_id = bd_bp_id');
        $select->where('bp_page_id = ?', $targetPageId);
        $result = $this->siteDbAdapter->fetchAll($select->__toString());

        $targetBdIds = array();
        foreach($result AS $value) {
            $targetBdIds[] = $value['bd_id'];
        }

        if(!empty($targetBdIds)) {
            $this->siteDbAdapter->delete('blocks_data', $this->siteDbAdapter->quoteInto('bd_id IN(?)', $targetBdIds));
        }

        $this->siteDbAdapter->delete('blocks2pages', $this->siteDbAdapter->quoteInto('bp_page_id = ?', $targetPageId));

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', '*');
        $select->where('bp_page_id = ?', $sourcePageId);
        $select->order('bp_order');
        $sourceBpData = $this->siteDbAdapter->fetchAll($select->__toString());

        $select = $this->siteDbAdapter->select();
        $select->from('blocks_data', '*');
        $select->joinLeft('blocks2pages', 'bp_id = bd_bp_id');
        $select->where('bp_page_id = ?', $sourcePageId);
        $sourceBlocksData = $this->siteDbAdapter->fetchAll($select->__toString());

        $parentBpIds = array();
        foreach($sourceBpData AS $bpData) {

            $bpRow = array(
                'bp_block_id' => $bpData['bp_block_id'],
                'bp_page_id' => $targetPageId,
                'bp_parent' => $bpData['bp_parent'],
                'bp_order' => $bpData['bp_order'],
                'bp_hidden' => $bpData['bp_hidden'],
            );

            $this->siteDbAdapter->insert('blocks2pages', $bpRow);
            $newBpId = $this->siteDbAdapter->lastInsertId();

            $parentBpIds[$bpData['bp_id']] = $newBpId;

            foreach($sourceBlocksData AS $blockData) {
                if($blockData['bd_bp_id'] == $bpData['bp_id']) {
                    $bdRow = array(
                        'bd_bp_id' => $newBpId,
                        'bd_field_id' => $blockData['bd_field_id'],
                        'bd_hidden' => $blockData['bd_hidden'],
                        'bd_value' => $blockData['bd_value']
                    );
                    $this->siteDbAdapter->insert('blocks_data', $bdRow);
                }
            }
        }

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', '*');
        $select->where('bp_page_id = ?', $targetPageId);
        $select->order('bp_order');
        $targetBpData = $this->siteDbAdapter->fetchAll($select->__toString());

        foreach($targetBpData AS $bpData) {
            if($bpData['bp_parent'] > 0) {
                $this->siteDbAdapter->update('blocks2pages', array('bp_parent' => $parentBpIds[$bpData['bp_parent']]), $this->siteDbAdapter->quoteInto('bp_id = ?', $bpData['bp_id']));
            }
        }
    }

	
    /**
     *  
     * @author levada@mail.ua
    */
    public function getPageData($pageId,$theme=null) 
    {
        $select = $this->siteDbAdapter->select();
        $select->from('pages', '*');
        $select->where('pg_id = ?', $pageId);
        $result['pages'] = $this->siteDbAdapter->fetchRow($select->__toString());
		
        $select = $this->siteDbAdapter->select();
        $select->from('metas', '*');
        $select->where('mt_page_id = ?', $pageId);
        $result['metas'] = $this->siteDbAdapter->fetchAll($select->__toString());

		$select = $this->siteDbAdapter->select();
		$select->from('blocks', '*');
		$select->joinLeft('blocks2pages', 'bp_block_id = b_id', '');
		$select->where('bp_page_id = ?', $pageId);
		$select->group('b_id');
		$result['blocks'] = $this->siteDbAdapter->fetchAll($select->__toString());
        
        if(isset($theme)){
            $count = count($result['blocks']);
            for ($i=0; $i<=$count; $i++){
                if($result['blocks'][$i]['b_file'] !='' && $result['blocks'][$i]['b_name'] != '')
                {
                    $result['blocks'][$i]['b_file'] = $theme.'/'.$result['blocks'][$i]['b_file'];
                    $result['blocks'][$i]['b_name'] = $theme.' / '.$result['blocks'][$i]['b_name'];
                }
            }
        }
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks_fields', '*');
		$select->joinLeft('blocks2pages', 'bp_block_id = bf_block_id', '');
		$select->where('bp_page_id = ?', $pageId);
		$select->group('bf_id');
		$result['blocks_fields'] = $this->siteDbAdapter->fetchAll($select->__toString());
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks2pages', '*');
		$select->where('bp_page_id = ?', $pageId);
		$select->order('bp_order');
		$result['blocks2pages'] = $this->siteDbAdapter->fetchAll($select->__toString());
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks_data', '*' );
		$select->joinLeft('blocks2pages', 'bp_id = bd_bp_id', '');
		$select->where('bp_page_id = ?', $pageId);
		$result['blocks_data'] = $this->siteDbAdapter->fetchAll($select->__toString());
        
        $relativePages = unserialize($result['pages']['pg_relative']); //checkPageById
        $relatives = array();
        if(count($relativePages)){
            
            foreach($relativePages as $id){
                
                if($alias = self::checkPageById($id, true)){
                    $relatives[] = trim($alias['pg_address']);
                }
            }
            
            $result['relative_pages_alias'] = $relatives;
            
        }

		return $result;
    }
    /**
     *  
     * @author levada@mail.ua
    */
    public function getSitePath() 
    {
		return $this->sitePath;
	}
    /**
     *  
     * @author levada@mail.ua
    */
    public function getBlocksData() 
    {
		$select = $this->siteDbAdapter->select();
		$select->from('blocks_fields', '*');
		$fields = $this->siteDbAdapter->fetchAll($select->__toString());
		foreach($fields as $field) {
			$fields_array[$field['bf_block_id']][$field['bf_name']] = $field;
		}
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks', '*');
		$blocks = $this->siteDbAdapter->fetchAll($select->__toString());
		$result = array();
		foreach($blocks as $block) {
			$result[$block['b_file']] = $block;
            
			$result[$block['b_file']]['field'] = $fields_array[$block['b_id']];
		}
		return $result;
	}
	
    public function importPage($data) {

        $row = array(
            'pg_address'    => $data['pg_address'],
            'pg_title'      => $data['pg_title'],
            'pg_menu_title' => $data['pg_menu_title'],
            'pg_lang'       => $data['pg_lang'],
            'pg_parent'     => '0',
            'pg_relative'   => $data['pg_relative'],
            'pg_css'        => $data['pg_css'],
            'pg_jscript'    => $data['pg_jscript'],
            'pg_priority'   => $data['pg_priority'],
            'pg_cacheable'  => $data['pg_cacheable'],
            'pg_hidden'     => $data['pg_hidden'],
            'pg_indexed'    => $data['pg_indexed'],
            'pg_extensions' => $data['pg_extensions'],
			'pg_headers' 	=> $data['pg_headers'],
        );

        $this->siteDbAdapter->insert('pages', $row);
        $newPageId = $this->siteDbAdapter->lastInsertId();
		
		return $newPageId;
		
	}
	
	public function importMetas( $bpMetas, $pageId ) {
		foreach($bpMetas AS $bpData) {
		
			$bpRow = array(
				'mt_page_id' => $pageId,
				'mt_name' => $bpData['mt_name'],
				'mt_content' => $bpData['mt_content'],
				'mt_lang' => $bpData['mt_lang']
			);
			
			$this->siteDbAdapter->insert('metas', $bpRow);
		}
	}

	 public function importBlock($bpData, $sourceBlocksData) {
	
		$bpRow = array(
			'bp_block_id' => $bpData['bp_block_id'],
			'bp_page_id' => $bpData['bp_page_id'],
			'bp_parent' => $bpData['bp_parent'],
			'bp_order' => $bpData['bp_order'],
            'bp_hidden' => $bpData['bp_hidden'],
		);

		$this->siteDbAdapter->insert('blocks2pages', $bpRow);
		$newBpId = $this->siteDbAdapter->lastInsertId();
		
		foreach($sourceBlocksData AS $blockData) {
			$bdRow = array(
				'bd_bp_id' => $newBpId,
				'bd_field_id' => $blockData['bd_field_id'],
                'bd_hidden' => $blockData['bd_hidden'],
				'bd_value' => $blockData['bd_value']
			);
			//var_dump( $bdRow );
			if ( $blockData['bd_field_id'] ) {
				$this->siteDbAdapter->insert('blocks_data', $bdRow);
			}
		}		
		
		return $newBpId;
		
	}


	public function importCreateBlock( $bData, $blockFields ) {
	
		$select = $this->siteDbAdapter->select();
		$select->from('blocks', '*');
		$select->where('b_file = ?', $bData['b_file']);
		$result = $this->siteDbAdapter->fetchRow($select->__toString());
		
		if (!$result) {

			$bRow = array(
				'b_name' => $bData['b_name'],
				'b_file' => $bData['b_file'],
                'b_parent' => $bData['b_parent'],
			);
			$this->siteDbAdapter->insert('blocks', $bRow);
			$newBId = $this->siteDbAdapter->lastInsertId();
			//$newBId = 0;
			
			foreach($blockFields AS $field) {
				$bfRow = array(
					'bf_block_id' => $newBId,
					'bf_name' => $field['bf_name'],
					'bf_type' => $field['bf_type'],
					'bf_default' => $field['bf_default'] ? $field['bf_default'] : ''
				);
				//var_dump($bfRow);
				$this->siteDbAdapter->insert('blocks_fields', $bfRow);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	
	}
	
	public function importCreateField( $bData, $blockField ) {
	
		//var_dump($bData);
		//var_dump($blockField);
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks', '*');
		$select->where('b_file = ?', $bData['b_file']);
		$result = $this->siteDbAdapter->fetchRow($select->__toString());
		
		//var_dump($result);
		
		if ($result) {
		
			$select = $this->siteDbAdapter->select();
			$select->from('blocks_fields', '*');
			$select->where('bf_name = ?', $blockField['bf_name']);
			$select->where('bf_block_id = ?',  $result['b_id']);
			$res = $this->siteDbAdapter->fetchRow($select->__toString());
			//var_dump($res);
			
			if (!$res) {
				$bfRow = array(
					'bf_block_id' => $result['b_id'],
					'bf_name' => $blockField['bf_name'],
					'bf_type' => $blockField['bf_type'],
					'bf_default' => $blockField['bf_default'] ? $blockField['bf_default'] : ''
				);
				//var_dump($bfRow);
				$this->siteDbAdapter->insert('blocks_fields', $bfRow);
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
		
	}
    
    /**
     *  /22.08.2014
     * Function get all lstrings for export page in blocks and block data
     * @source pageId, Blocks array, block data array
     * @return array
     */
   	public function getLstringsForImport($pageId=null, $blocks=null,$data=null) 
    {
   	    
   	    $lstrings = $match = $notExist = $result = array();    
        
        if (isset($blocks))
        {
    			
    			$path = $this->sitePath;
                
                foreach($blocks as $tpl)
                {
                    $tpl = $path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$tpl['b_file'];
                    
                    if ( file_exists($tpl) )
                    {
                        $str = file_get_contents($tpl);
                        $pattern = '#\{\$lstrings\.(.*?)\}#';
                        preg_match_all($pattern,$str,$match);
                    }
                    
                    if (isset($match[1]))
                    {
            			foreach($match[1] as $lstring ) {
            			     $lstring =  str_replace('$','',$lstring);
							 $qpost = strpos($lstring,'|');
                             
                             if ($qpost !== false)
                             {
                                $lstring = substr($lstring, 0, $qpost);
                             }
                             
                         
                            $lstrings[] = $lstring;
            			}
                    }
                }
        }
            
        if (isset($data))
        {
    			$match = array();
                
                foreach($data as $item)
                {
                    $value = $item['bd_value'];

                    if ( !empty($value))
                    {
                        $pattern = '#\{\$lstrings\.(.*?)\}#';
                        preg_match_all($pattern,$value,$match);
                    }
                    
                    if (isset($match[1]))
                    {
            			foreach($match[1] as $lstring ) {
 							
							$lstring =  str_replace('$','',$lstring);
                         
                            $lstrings[] = $lstring;
            			}
                    }
                }
            }
            
            $lgs = self::getAllLstrings();

            $lstrings = array_flip(array_unique($lstrings));

            foreach($lstrings as $key=>$value)
            {
                if (isset($lgs[$key]))
                {
                    $lstrings[$key] = $lgs[$key];
                }
                else{
                    unset($lstrings[$key]);
                    $notExist[$key] = 'null';
                }
            }
            
            $result = array('exist'=>$lstrings,'notexist'=>$notExist);
            
            return $result;
	}
    
    /**
     *  /22.08.2014
     * Function get all lstrings for page
     * @return array or null
     */
    public function getAllLstrings(){
        
            $localString = new LocalString($this->siteId);
            $langs = $localString->getLangs();
            
            $select = $this->siteDbAdapter->select();
            $select->from("lstrings AS t",array("nick"=>"ls_nick"));
            
            foreach($langs AS $lang) {
                $select->joinLeft(
                    array("{$lang['code']}"=>"{$lang['code']}"), 
                    "{$lang['code']}.ll_nick_id = t.ls_id",
                    array("{$lang['code']}"=>"{$lang['code']}.ll_text")
                );
            }

            //$select->where('t.ls_site = ?', $this->siteId);
            $rows = $this->siteDbAdapter->fetchAll($select->__toString());

        	$lgs = array();
            
            if (count($rows))
            {
            	foreach($rows AS $row) {
            	   
                   foreach($langs AS $lang) {
                        $code = $lang['code'];
                        
                        if(empty($row[$code]) || $row[$code] == '' || $row[$code] === null)
                            $row[$code] = $row['en'];
                            
           	                $lgs[$row['nick']][$code] = $row[$code];
                   }

            	}
                
                return $lgs;
            }
            
            return null;
            
            
    }
    
    public function getLangs(){
        
            $localString = new LocalString($this->siteId);
            $langs = $localString->getLangs();
            
            return $langs;
   
    }
    /**
     *  /22.08.2014
     * Function add no exist lstrings for site in export page
     * @source array
     */
    public function addLstringsNotExist($data=null)
    {
        $callback = '';
        if (isset($data) && count($data))
        {
            $localString = new LocalString($this->siteId);
            
            foreach($data as $params)
            {
                $result = $localString->addString($params);
                
                if (!$result)
                    $callback .= "<br/>error to insert lstring {$params['nick']}<br/>";
            }
        }
        return $callback;
    }
    /**
     *  /22.08.2014
     * Function update lstrings if exist in site 
     * @source array
     */
    public function updateLstrings($data=null)
    {
        if (isset($data))
        {

            $id = 0;
            $select = $this->siteDbAdapter->select();
            $select->from('lstrings', array('id'=>'ls_id'));
            $select->where('ls_nick = ?', $data['nick']);
            $row = $this->siteDbAdapter->fetchRow($select->__toString());
            
            if ($row)
            {   
                $id = $row['id'];
                $rows = array('ll_text'=>$data['value']);
                
                $select = $this->siteDbAdapter->select();
                $select->from($data['lang'], array('ll_nick_id'));
                $select->where('ll_nick_id = ?', $id);
                $rowChild = $this->siteDbAdapter->fetchRow($select->__toString());
                
                if($rowChild)
                {
                    $this->siteDbAdapter->update($data['lang'], $rows, $this->siteDbAdapter->quoteInto('ll_nick_id = ?', $id));    
                }
                else{
                    $this->siteDbAdapter->insert($data['lang'], array('ll_nick_id'=>$id, 'll_text'=>$data['value']));    
                }
                
                return true;
            }          
        }
        return false;
    }
    
    public function getPageLogs($id=null,$test=false,$limit=100) {
        $select = $this->siteDbAdapter->select();

        $select->from('pages_logs', '*');
        if(isset($id)) {
            $select->where('pl_page_id = ?', $id);
        }
        $select->limit($limit);
        $select->order(array('pl_date DESC'));
        $rows = $this->siteDbAdapter->fetchAll($select->__toString());
        
        if ($test && count($rows))
        {
            return true;
        }
        else{
            if (count($rows))
            {
                return $rows;
            }
        }
        
        return false;
    }
    
    public function getPageLogsHtml($id,$rows=null) 
    {
        $text = '';
        
        if (isset($rows))
        {
        
                $text = "<tr class=\"info-row{$id}\">
                    <td colspan=\"9\">
                        <div class=\"pre-scrollable\" style=\"background-color: #f5f5f5;border: 1px solid #ccc;padding:10px;max-height: 340px;overflow-y: scroll;color:#000!important\">
                        <ul class=\"info-block\">";
                        
                        foreach($rows as $row)
                        {
                            $text .= '<img class="log-icon" src="/images/calendar.png" width="16" title="Date" alt="Date"/> ';
                            $text .= '<em>'.date("m/d/Y H:i:s",$row['pl_date']).', <img class="log-icon" src="/images/persons.png" width="16" title="Author" alt="Author"/> <strong>'.$row['pl_author'].'</strong></em>';
                            $text .= '<blockquote>'.html_entity_decode($row['pl_text']).'</blockquote>'; 
                        }
            
            
               
               $text .= '         </ul>
                    </div>
                    </td>
                </tr>';
        }
        
        return $text;

    }
    
    
     /**
      * function for get screen page
      * @author italiano
      * @date 08/01/2015
      * @version 0.1
      * @return string
      * @param pageId, host, update(bool)
     */
    public function save_screenshot($pageId,$host,$refresh=false,$update=false, $test=false, $screen="980", $size="980", $ext="jpg") 
    {
  		$select = $this->siteDbAdapter->select();
		$select->from('pages', array('pg_screen','pg_address'));
		$select->where('pg_id = ?', $pageId);
		$page = $this->siteDbAdapter->fetchRow($select->__toString());
        
        if ($test){
            //test for mac.eltima.com site, delete on out server
            $host = 'http://'.$host;            
        }

        if ($page)
        {
            $imagePath = $this->sitePath.'tmp/';
            $imageUrl = "http://$host/tmp/";
            $url = 'http://'.$host.$page['pg_address'];
            $alias = $page['pg_address'];
            $image = $page['pg_screen'];
            
            if (!empty($image) && !$refresh){
                if (file_exists($imagePath.$image)){
                    return $image;
                }
                else{
                    return null;
                }
            }
            else
            {
                $image = "";
                $request = "http://mini.s-shot.ru/$screen/$size/$ext/?$url";
                $request = file_get_contents($request);
                $file = md5($alias).'.'.$ext;
                $image = $imageUrl.$file;
                $imagePath .= $file;
                
                file_put_contents($imagePath, $request);
                            
                //$fd = fopen ($path, 'rb');
                //$size=filesize($path);
                //$data = fread($fd, $size);
                //fclose($fd);
                            
                //$data = file_get_contents($file);
            
                //$base64 = 'data:image/jpg;base64,' . base64_encode($data);   
                if ($update){
                    $rows = array('pg_screen'=>$image);
                    $this->siteDbAdapter->update('pages', $rows, $this->siteDbAdapter->quoteInto('pg_id = ?', $pageId));   
                }

                if (file_exists($imagePath)){
                    return $image;
                }
                else{
                    return null;
                }
                
            }
        
        
        }

    }
    
    public function chekingCacheFile($uri_address)
    {
            
    	if(!substr_count($uri_address,'.html')) 
    	{
            $dirname=LOCAL_PATH.'cache/cache'.$uri_address.((substr($uri_address,-1) != '/') ? '/':'');
            $filename='index';
            $extension='.html';			
    	}
    	else
    	{
        	$dirname=LOCAL_PATH.'cache/cache'.((substr($uri_address,0,1) != '/') ? '/':'').$uri_address;
        	$filename='';
        	$extension='';					
    	}
        
    	$cache_uri_address = $dirname.$filename.$extension;	
        		
    	if(file_exists($cache_uri_address)){
    			return true;
    		}
      
            return false;

    }

}

?>