<?php

class Blocks {

    #PIVATE VARIABLES
    private $siteDbAdapter;
    private $blocks;

    #PUBLIC VARIABLES


    /*
     Конструктор
    */
    public function __construct($siteId) {
        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname'));
        $select->where('s_id = ?', $siteId);
        $this->siteDbName = $dbAdapter->fetchOne($select->__toString());

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $params['dbname'] = $this->siteDbName;

        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);
        $this->siteDbAdapter->query('SET NAMES utf8');

        $this->blocks = array();
        $this->loadBlocks();
    }

    /*
     Деструктор
    */
    public function __destruct() {
        $this->siteDbAdapter = NULL;
        $this->blocks        = NULL;
    }

    /*
     Получить список блоков
    */
    public function getBlocksList() {
        
        //italiano 13.11.2014
        $this->blocksUsed();
        
        return $this->blocks;
    }

    /*
     Загрузить все данные блоков на сайте
    */
    private function loadBlocks() {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks', '*');
       // $select->order('b_name');

        $blocks = $this->siteDbAdapter->fetchAll($select->__toString());

        foreach($blocks AS $block) {
            $this->blocks[$block['b_id']] = $block;
        }
    }

    /*
     Получить данные блока
    */
    public function getBlock($blockId) {
        return $this->blocks[$blockId];
    }

    /*
     Редактировать данные блока
    */
    public function editBlock($blockId, $name, $file, $parent, $text, $base64) {
        $set = array(
            'b_name'   => $name,
            'b_file'   => $file,
            'b_parent' => $parent,
        );
        
        if ($this->testFields(array('b_text'=>$text,'b_base64'=>$base64),'blocks')){
            $set['b_text'] = htmlentities($text);
            $set['b_base64'] = $base64;
        }
        
        
        $this->siteDbAdapter->update('blocks', $set, $this->siteDbAdapter->quoteInto('b_id = ?', $blockId));
    }

    /*
     Добавить новый блок
    */
    public function addBlock($name, $file, $parent,$text,$base64) {
        $row = array(
            'b_name'   => $name,
            'b_file'   => $file,
            'b_parent' => $parent
        );
        
                
        if ($this->testFields(array('b_text'=>$text,'b_base64'=>$base64),'blocks')){
            $row['b_text'] = htmlentities($text);
            $row['b_base64'] = $base64;
        }
        
        $this->siteDbAdapter->insert('blocks', $row);
        return $this->siteDbAdapter->lastInsertId();
    }

    /*
     Удалить блок
    */
    public function deleteBlock($blockId) {
        $this->siteDbAdapter->delete('blocks', $this->siteDbAdapter->quoteInto('b_id = ?', $blockId));
    }
	
    /*
     Удалить блок
    */
    public function cloneBlock($blockId) {
		
		$block = $this->getBlock($blockId);
		$block_fields = $this->getBlockFields($blockId);

		$copy = 0;
		do {
			$block_isset = false;
			$select = $this->siteDbAdapter->select();
			$select->from('blocks', 'COUNT(b_id)');
			$name = $block['b_name'].' (copy '.++$copy.')';
			$select->where('b_name = ?', $name );
			if( $this->siteDbAdapter->fetchOne($select->__toString()) > 0 ) {
				$block_isset = true;
			}
		} while($block_isset);
		
		$block_id = $this->addBlock( $name, $block['b_file'], $block['b_parent'], $block['b_text'], $block['b_base64']);
		foreach ($block_fields as $fields){
			$this->addBlockField($block_id, $fields['bf_name'], $fields['bf_type'], $fields['bf_default']);
		}
	
    }

    /*
     Получить все переменные привязаные к блоку
    */
    public function getBlockFields($blockId) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks_fields', '*');
        $select->where('bf_block_id = ?', $blockId);

        return $this->siteDbAdapter->fetchAll($select->__toString());
    }

    /*
     Получить данные переменной привязаной к блоку
    */
    public function getBlockField($fieldId) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks_fields', '*');
        $select->where('bf_id = ?', $fieldId);

        return $this->siteDbAdapter->fetchRow($select->__toString());
    }

    /*
     Добавить переменную в блок
    */
    public function addBlockField($blockId, $name, $type, $default) {
        $row = array(
            'bf_block_id' => $blockId,
            'bf_name'     => $name,
            'bf_type'     => $type,
            'bf_default'  => $default
        );
        $this->siteDbAdapter->insert('blocks_fields', $row);
        return $this->siteDbAdapter->lastInsertId();
    }

    /*
     Редактировать переменную
    */
    public function editBlockField($fieldId, $name, $type, $default) {
        $set = array(
            'bf_name'    => $name,
            'bf_type'    => $type,
            'bf_default' => $default
        );
        $this->siteDbAdapter->update('blocks_fields', $set, $this->siteDbAdapter->quoteInto('bf_id = ?', $fieldId));
    }

    /*
     Удалить переменную
    */
    public function deleteBlockField($fieldId) {
        $this->siteDbAdapter->delete('blocks_fields', $this->siteDbAdapter->quoteInto('bf_id = ?', $fieldId));
    }

    /*
     Проверить на существование дочерних блоков
    */
    public function isChildBlocksExist($blockId)
    {
    	if ((int)$blockId > 0)
		{
			$select = $this->siteDbAdapter->select();
	        $select->from('blocks', 'COUNT(b_id)');
    	    $select->where('b_parent = ?', $blockId);

        	if($select->__toString())
	        {
    		    if($this->siteDbAdapter->fetchOne($select->__toString()) > 0)
        		{
            		return TRUE;
	        	}
	        }
		}
	        return FALSE;
    }

    /*
     Проверить на существование переменных привязаных к блоку
    */
    public function isBlockFieldsExist($blockId) {
	if ((int)$blockId > 0)
		{
	        $select = $this->siteDbAdapter->select();
    	    $select->from('blocks_fields', 'COUNT(bf_id)');
        	$select->where('bf_block_id = ?', $blockId);

	        if($this->siteDbAdapter->fetchOne($select->__toString()) > 0) {
    	        return TRUE;
        	}
		}
        return FALSE;
    }


    // Получить имя блока
    public function getBlockName($blockId) {
		if ((int)$blockId > 0)
		{	
        	$blocksList = $this->getBlocksList();
	        return $blocksList[$blockId]['b_name'];
		}
		return false;	
    }
	
    /**
     * function for find block who used in any page
     * @author italiano
     * 13/11/2014
     */
    public function getPagesUsedBlocks($blockId) {
        $select = $this->siteDbAdapter->select();
        $select->from('pages', array('DISTINCT(pages.pg_id)','pages.pg_address'));
        $select->join('blocks2pages','blocks2pages.bp_page_id = pages.pg_id' );
        $select->where('blocks2pages.bp_block_id = ?', $blockId);
        $select->group("pages.pg_id");
        $rows = $this->siteDbAdapter->fetchAll($select->__toString());
        
        if (count($rows)>0)
        {
            return $rows;
        }
        else{
            return null;
        }
            
            
    }
    
    /**
     * function for find block who used in any page
     * @author italiano
     * 13/11/2014
     */
    public function blocksUsed() 
    {
        $result = array();
        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('DISTINCT(bp_block_id) as b_id'));
        
        $rows =  $this->siteDbAdapter->fetchAll($select->__toString());
        
        foreach($rows as $key=>$item)
        {
            $_rows[$item['b_id']]='';
        }
        
        foreach($this->blocks as $key=>$item)
        {
            if (isset($_rows[$item['b_id']]))
                $this->blocks[$key]['used'] = "true";
        }
    
    }
    
    /**
     * function test column for table
     * @author italiano
     * @param $data = array(), $_table = name table 
     * @date 26.11.2014
     * @return bool
     */
    public function testFields($data=null, $_table=null) 
    {
        if (isset($_table))
        {
            $table = $_table;
        }
        else
        {
            $table = 'blocks';
        }
        
        $fieldsError = array();
        $fieldsToAdd = array(
            'b_text' => '`b_text` text DEFAULT NULL',
            'b_base64' => '`b_base64` text DEFAULT NULL',
        );
        
        if (isset($data) && is_array($data))
        {
                $this->siteDbAdapter->query('SET NAMES utf8');
                
                foreach($data as $key=>$item)
                {
                        $q = "SELECT IF($key IS NULL or $key = '', 'empty', $key) as $key FROM `$table`";
                        $issetField = true;
                        
                        //find column
                        try{
                            $result = $this->siteDbAdapter->query($q);
                        }
                        catch (Exception $e) {
                            $issetField = false;
                        }
                        
                        //add column if not find
                        if (!$issetField)
                        {
                            if (isset($fieldsToAdd[$key]))
                            {
                                $q = "ALTER TABLE `$table` ADD $fieldsToAdd[$key]";

                                try{
                                    $result = $this->siteDbAdapter->query($q);
                                }
                                catch (Exception $e) {
                                    echo $e->getMessage();
                                    $fieldsError[]=$key;
                                }
                            }
                        }  
                }
        }
           
        if (count($fieldsError)>0)
        {
            return false;
        }
        else{
            return true;
        }

    }
    
}


?>