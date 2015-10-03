<?php

include_once('blocks.php');
include_once('class/classFineDiff.php');

class Content {

    #PIVATE VARIABLES
    private $siteDbAdapter;
    private $pageId;
    private $content;
    #PUBLIC VARIABLES
    public $blocks;

    // Êîñòðóêòîð
    public function __construct($siteId, $pageId) {
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

        $this->pageId = $pageId;

        $this->blocks = new Blocks($siteId);

        $this->loadContent();

    }

    // Äåñòðóêòîð
    public function __destruct() {
    }

    // Çàãðóçèòü áëîêè äàííûõ ñòðàíèöè â ïàìÿòü
    private function loadContent() {
        $select = $this->siteDbAdapter->select();

        $select->from('blocks2pages', array('bp_id',
                                            'bp_parent',
                                            'bp_block_id',
                                            'bp_page_id',
                                            'bp_order',
                                            'bp_hidden'));

        $select->joinLeft('blocks_data', 'bp_id = bd_bp_id',  array('bd_id',
                                                                    'bd_bp_id',
                                                                    'bd_field_id',
                                                                    'bd_value',
                                                                    'bd_hidden',));

        $select->joinLeft('blocks_fields', 'bf_id = bd_field_id', array('bf_id',
                                                                        'bf_name',
                                                                        'bf_type',
                                                                        'bf_default'));

        $select->joinLeft('blocks', 'b_id = bp_block_id', array('b_id',
                                                                'b_name',
                                                                'b_parent'));

        $select->where('bp_page_id = ?', $this->pageId);
        $select->order('bp_order');

        $result = $this->siteDbAdapter->fetchAll($select->__toString());


        $content = array();
        $index = 0;

        foreach($result AS $row) {

            if(!empty($row['bf_name'])) {
                $content['content'][$row['bp_id']]['fields'][$index] = $row;
            }

            $content['content'][$row['bp_id']]['b_name'] = $row['b_name'];
            $content['content'][$row['bp_id']]['b_id'] = $row['b_id'];
            $content['content'][$row['bp_id']]['bp_parent'] = $row['bp_parent'];
            $content['content'][$row['bp_id']]['bp_page_id'] = $row['bp_page_id'];
            $content['content'][$row['bp_id']]['bp_order'] = $row['bp_order'];
            $content['content'][$row['bp_id']]['bp_hidden'] = $row['bp_hidden'];
            $content['content'][$row['bp_id']]['is_child_exist'] = $this->blocks->isChildBlocksExist($row['b_id']);
            $content['content'][$row['bp_id']]['is_fields_exist'] = $this->blocks->isBlockFieldsExist($row['b_id']);

            if(!empty($row['bd_id'])) {
                $content['srch'][$row['bd_id']] = $row['bp_id'];
            }

            $childsCount = 0;
            for($i = 0; $i < count($result); $i++) {
                if($result[$i]['bp_parent'] == $row['bp_id']) {
                    $childsCount++;
                }
            }
            $content['content'][$row['bp_id']]['childsCnt'] = $childsCount;
            $index++;
        }

        $this->content = $content;
    }

    // Ïîëó÷èòü ñïèñîê áëîêîâ äàííûõ ïðèâÿçàíûõ ê ñòðàíèöå
    public function getContent() {
        return $this->content['content'];
    }

    // Ïîëó÷èòü ñïèñîê ïåðåìåííûõ ïðèâÿçàíûõ ê áëîêó íî åù¸ íå äîáàâëåíûõ
    public function getNotAddedBlockFields($bpId) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks_data', array('bd_field_id'));
        $select->where('bd_bp_id = ?', $bpId);
        $existFields = $this->siteDbAdapter->fetchAll($select->__toString());

        $fields = $this->blocks->getBlockFields($this->content['content'][$bpId]['b_id']);

        $toDelete = array();

        foreach($existFields AS $field) {
            for($i = 0; $i < count($fields); $i++) {
                if($fields[$i]['bf_id'] == $field['bd_field_id']) {
                    $toDelete[$i] = $fields[$i];
                }
            }
        }

        foreach($toDelete AS $key => $field) {
            unset($fields[$key]);
        }

        /*foreach($existFields AS $field) {
            for($i = 0; $i < count($fields); $i++) {
                if(isset($fields[$i]) && $fields[$i]['bf_id'] == $field['bd_field_id']) {
                    unset($fields[$i]);
                }
            }
        }*/


        return $fields;
    }

    // Ïîëó÷èòü ñïèñîê äî÷ðíèõ áëîêîâ
    public function getChildBlocksList_bkp($bpId) {
        $childs =  $this->blocks->getBlocksList();
/*        $childs = array();
        $blocks = $this->blocks->getBlocksList();
        $blockId = $this->content['content'][$bpId]['b_id'];

        foreach($blocks AS $id => $block) {
            if($block['b_parent'] == $blockId)
			 {
                $childs[$id] = $block;
            }
        }
*/
        return $childs;
    }

    public function getChildBlocksList($bpId) {
        
        $childs = array();
        $blocks = $this->blocks->getBlocksList();
        
        $blockId = $this->content['content'][$bpId]['b_id'];

        foreach($blocks AS $id => $block) {
            
            $b_parent = unserialize($block['b_parent']);
            
            if(in_array($blockId, $b_parent)){
                $childs[$id] = $block;
                $childs[$id]['child'] = true;
            }
            else{
                $childs[$id] = $block;
                $childs[$id]['child'] = false;
            }
        }

        return $childs;
    }
    // Ïîëó÷èòü äàííûå ïðèâÿçàíûå ê ïåðåìåííîé áëîêà íà ñòðàíèöå
    public function getBlockFieldContent($bdId) {
        $fields = $this->content['content'][$this->content['srch'][$bdId]]['fields'];

        foreach($fields AS $field) {
            if($field['bd_id'] == $bdId) {
                return $field['bd_value'];
            }
        }
    }

    // Ïîëó÷èòü äàííûå ïî óìîë÷àíèþ ïðèâÿçàíûå ê ïåðåìåííîé áëîêà íà ñòðàíèöå
    public function getBlockFieldDefaultValue($bdId) {
        $fields = $this->content['content'][$this->content['srch'][$bdId]]['fields'];

        foreach($fields AS $field) {
            if($field['bd_id'] == $bdId) {
                return $field['bf_default'];
            }
        }
    }

    // Ïîëó÷èòü òèï ïåðåìåííîé
    public function getBlockFieldType($bdId) {
        $fields = $this->content['content'][$this->content['srch'][$bdId]]['fields'];

        foreach($fields AS $field) {
            if($field['bd_id'] == $bdId) {
                return $field['bf_type'];
            }
        }
    }

    // Äîáàâòü áëîê íà ñòðàíèöó
    public function addBlock($blockId, $b2pId) {

        $order = 0;
		if ($b2pId > 0) 
			$this->getLastOrder($b2pId, $order);
        
        $this->siteDbAdapter->query(
            'UPDATE blocks2pages SET bp_order = bp_order + 1 WHERE bp_order > '.$order.' AND bp_page_id = '.$this->pageId
        );

        $row = array(
            'bp_block_id' => $blockId,
            'bp_page_id' => $this->pageId,
            'bp_parent' => $b2pId,
            'bp_order' => ($order + 1)
        );

        $result = $this->siteDbAdapter->insert('blocks2pages', $row);
        $lastInsertId = $this->siteDbAdapter->lastInsertId();
        
        /* >> added 21.11.2014, italiano */
        if((bool)$result)
        {
            $this->setModify('addBlock',$data=array('blockId'=>$blockId));
        }
        /* << */
        
        
        return $lastInsertId;
    }

    // Äîáàâòü áëîê íà ñòðàíèöó
    public function editBlock($params=array()) {

        $row = array(
            'bp_id' => $params['bpId'],
            'bp_block_id' => $params['blockId'],
            'bp_parent' => $params['bp_parent'],
            'bp_order' => $params['bp_order'],
			
        );
        $this->siteDbAdapter->query(
            'UPDATE blocks2pages SET bp_block_id = :bp_block_id, bp_parent = :bp_parent, bp_order = :bp_order WHERE bp_id = :bp_id', $row);


      //  $this->siteDbAdapter->insert('blocks2pages', $row);
        return $params;
    }
    public function updateBlock($params=array()) {
       
		$bp_id = $params['bpId']; 
		$bp_parent = $params['bp_parent'];
		$order = $this->content['content'][$bp_id]['bp_order'];
		$this->siteDbAdapter->query(
            'UPDATE blocks2pages SET bp_order = bp_order - 1 WHERE bp_order > '.$order.' AND bp_page_id = '.$this->pageId
        );
		if ($bp_parent > 0) 
			$this->getLastOrder($bp_parent, $order);
        $this->siteDbAdapter->query(
            'UPDATE blocks2pages SET bp_order = bp_order + 1 WHERE bp_order > '.$order.' AND bp_page_id = '.$this->pageId
        );

       
	   $row = array(
            'bp_id' => $bp_id,
            'bp_parent' => $bp_parent,
			'bp_order' => $order+1,
        );
        $this->siteDbAdapter->query(
            'UPDATE blocks2pages SET  bp_parent = :bp_parent, bp_order = :bp_order WHERE bp_id = :bp_id', $row);
		$this->loadContent();
      //  $this->siteDbAdapter->insert('blocks2pages', $row);
        return $this->content['content'][$bp_id];
    }

    // Óäàëèòü áëîê èç ñòðàíèöè
    public function deleteBlock($bpId) {
        if ((int)$bpId == 0) return 1;
		$order = $this->content['content'][$bpId]['bp_order'];
        $bpIds = array();
        $this->getChildBlocks2PagesIds($bpId, $bpIds); 
        array_push($bpIds, $bpId);

        $select = $this->siteDbAdapter->select();
        $select->from('blocks_data', array('bd_id'));
        $select->where('bd_bp_id IN(?)', $bpIds);
        $result = $this->siteDbAdapter->fetchAll($select->__toString());

        $dataIds = array();
        foreach($result AS $value) {
            $dataIds[] = $value['bd_id'];
        }

        $this->siteDbAdapter->delete('blocks2pages', $this->siteDbAdapter->quoteInto('bp_id IN(?)', $bpIds));
        if(count($dataIds) > 0){
        	$this->siteDbAdapter->delete('blocks_data', $this->siteDbAdapter->quoteInto('bd_id IN(?)', $dataIds));
        }
         
		    $this->siteDbAdapter->query(
                'UPDATE blocks2pages SET bp_order = bp_order-:count WHERE bp_page_id = :bp_page_id and bp_order > :bp_order',
                array('count' =>count($bpIds),'bp_page_id' =>$this->pageId, 'bp_order' => $order)
            );
            
        /* >> added 21.11.2014, italiano */
        $this->setModify('deleteBlock', $data=array('bpIds'=>$bpIds));
        /* << */



/*        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('bp_id'));
        $select->where('bp_page_id = ?', $this->pageId);
        $select->where('bp_order > ?', $order);
        $select->order('bp_order');		
        $result = $this->siteDbAdapter->fetchAll($select->__toString());

        foreach($result AS $value) {
            $this->siteDbAdapter->query(
                'UPDATE blocks2pages SET bp_order = :order WHERE bp_id = :bpid',
                array('order' => $order++, 'bpid' => $value['bp_id'])
            );
        }*/
    }

    // Ïîëó÷èòü bpID âñåõ äî÷åðíèõ áëîêîâ íà ñòðàíèöå ïî îòíîøåíèå ê äàííîìó áëîêó
    public function getChildBlocks2PagesIds($bpId, &$arrayIds) {

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('bp_id'));
        $select->where('bp_parent = ?', $bpId);
        $select->order('bp_order');
        $result = $this->siteDbAdapter->fetchAll($select->__toString());

        if(!empty($result)) {
            foreach($result AS $value) {
                $arrayIds[] = $value['bp_id'];
                $this->getChildBlocks2PagesIds($value['bp_id'], $arrayIds);
            }
        }
    }

    // Ïîäíÿòü áëîê íà ñòðàíèöå
    public function liftBlock($bpId) {
        $order = $this->content['content'][$bpId]['bp_order'];
        $parent = $this->content['content'][$bpId]['bp_parent'];

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('bp_id', 'bp_order'));
        $select->where('bp_order < ?', $order);
        $select->where('bp_parent = ?', $parent);
        $select->order('bp_order DESC');
        $select->limit(1);
        $result = $this->siteDbAdapter->fetchRow($select->__toString());


        $topBpId = $result['bp_id'];
        $topOrder = $result['bp_order'];

        $bpIds = array();
        $this->getChildBlocks2PagesIds($bpId, $bpIds);
        array_unshift($bpIds, $bpId);

        foreach($bpIds AS $id) {
            $this->siteDbAdapter->update('blocks2pages', array('bp_order' => $topOrder++), $this->siteDbAdapter->quoteInto('bp_id = ?', $id));
        }

        $topBpIds = array();
        $this->getChildBlocks2PagesIds($topBpId, $topBpIds);
        array_unshift($topBpIds, $topBpId);

        foreach($topBpIds AS $id) {
            $this->siteDbAdapter->update('blocks2pages', array('bp_order' => $topOrder++), $this->siteDbAdapter->quoteInto('bp_id = ?', $id));
        }
    }

    // Îïóñòèòü áëîê íà ñòðàíèöå
    public function pullDownBlock($bpId) {
        $order = $this->content['content'][$bpId]['bp_order'];
        $parent = $this->content['content'][$bpId]['bp_parent'];

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('bp_id', 'bp_order'));
        $select->where('bp_order > ?', $order);
        $select->where('bp_parent = ?', $parent);
        $select->order('bp_order ASC');
        $select->limit(1);
        $result = $this->siteDbAdapter->fetchRow($select->__toString());


        $bottomBpId = $result['bp_id'];
        $bottomOrder = $result['bp_order'];

        $bottomBpIds = array();
        $this->getChildBlocks2PagesIds($bottomBpId, $bottomBpIds);
        array_unshift($bottomBpIds, $bottomBpId);

        foreach($bottomBpIds AS $id) {
            $this->siteDbAdapter->update('blocks2pages', array('bp_order' => $order++), $this->siteDbAdapter->quoteInto('bp_id = ?', $id));
        }

        $bpIds = array();
        $this->getChildBlocks2PagesIds($bpId, $bpIds);
        array_unshift($bpIds, $bpId);

        foreach($bpIds AS $id) {
            $this->siteDbAdapter->update('blocks2pages', array('bp_order' => $order++), $this->siteDbAdapter->quoteInto('bp_id = ?', $id));
        }
    }

    // Äîáàâòü ïåðåìåííóþ ñ äàííûìè â áëîê (ïåðåìåííûå èçíà÷àëüíî ñóùåñòâóþò íî íå îïèñàíû)
    public function addBlockField($b2pId, $fieldId) {
        $row = array(
            'bd_bp_id' => $b2pId,
            'bd_field_id' => $fieldId
        );

        $result = $this->siteDbAdapter->insert('blocks_data', $row);
        $lastInsertId = $this->siteDbAdapter->lastInsertId();
        
        /* >> added 21.11.2014, italiano */
        if((bool)$result)
        {
            $this->setModify('addBlockField', $data = array('bpId'=>$b2pId,'fieldId'=>$fieldId));
        }
        /* << */
        
        return $lastInsertId;
    }

    // Óäàëèòü ïåðåìåííóþ ñ äàííûìè èç áëîêà
    public function deleteBlockField($bdId) {
        $result = $this->siteDbAdapter->delete('blocks_data', $this->siteDbAdapter->quoteInto('bd_id = ?', $bdId));
        
        /* >> added 21.11.2014, italiano */
        if((bool)$result)
        {
            $this->setModify('deleteBlockField',$data=array('bdId'=>$bdId));
        }
        /* << */
    }

    // Ðåäàêòèðîâàòü ïåðåìåííóþ ñ äàííûìè â áëîêå
    public function editBlockField($bdId, $value) {
        
        $set = array(
            'bd_value' => $value
        );

        if(strlen($value)<1000){
        $this->setModify('editBlockField',$data = array('bdId'=>$bdId, 'text'=>$value));
        }
        
        $result = $this->siteDbAdapter->update('blocks_data', $set, $this->siteDbAdapter->quoteInto('bd_id = ?', $bdId));
        

    }


    // Ïðîâåðèòü ìîæíî ëè ïîäíÿòü áëîê
    public function isCanLiftBlock($bpId) {

        $bpParent = $this->content['content'][$bpId]['bp_parent'];
        $bpOrder = $this->content['content'][$bpId]['bp_order'];

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', 'bp_id');
        $select->where('bp_parent = ?', $bpParent);
        $select->where('bp_page_id = ?', $this->pageId);
        $select->where('bp_order < ?', $bpOrder);

        if($this->siteDbAdapter->fetchOne($select->__toString()) > 0) {
            return TRUE;
        }
        return FALSE;
    }


    // Ïðîâåðèòü ìîæíî ëè îïóñòèòü áëîê
    public function isCanPullDownBlock($bpId) {

        $bpParent = $this->content['content'][$bpId]['bp_parent'];
        $bpOrder = $this->content['content'][$bpId]['bp_order'];

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', 'bp_id');
        $select->where('bp_parent = ?', $bpParent);
        $select->where('bp_page_id = ?', $this->pageId);
        $select->where('bp_order > ?', $bpOrder);

        if($this->siteDbAdapter->fetchOne($select->__toString()) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    // Ïîëó÷èòü ïîñëåäíåå çíà÷åíèå ñîðòèðîâêè ó áëîêà
    private function getLastOrder($bpId, &$order) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('MAX(bp_order)'));
        $select->where('bp_parent = ?', $bpId);
        $select->where('bp_page_id = ?', $this->pageId);
        $select->orWhere('bp_id = ?', $bpId);

        $order = $this->siteDbAdapter->fetchOne($select->__toString());

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', array('bp_id'));
        $select->where('bp_order = ?', $order);
        $select->where('bp_page_id = ?', $this->pageId);
        $childBpid = $this->siteDbAdapter->fetchOne($select->__toString());

        if($bpId != $childBpid) {
            $this->getLastOrder($childBpid, $order);
        }
    }
/*
    // Ðàñïàðñèòü XML è ïîìåñòü âñå äàííûå â ìàñèâ
    public function parseXML2Array($xmlstr = NULL, &$array, $xml = NULL) {
        if(isset($xmlstr)) {
            $xml = new SimpleXMLElement('<body>'.$xmlstr.'</body>');
        }

        $nodes = $xml->children();
        $index = 0;
        foreach($nodes AS $node) {
            $attrs = $node->attributes();
            if(isset($attrs['key'])) {
                $key = strval($attrs['key']);
            } else {
                $key = $index;
                $index++;
            }

            if(count($node->children())) {
                $this->parseXML2Array(NULL, $array[$key], $node);
            } else {
                $array[$key] = strval($node);
            }
        }
    }
*/

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

    // Ðàñïàðñèòü ìàñèâ è ïîìåñòü âñå äàííûå â XML êîä
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
 
	public function getBlockParams($bpId, $fields = false, $childblocks = false )
	{
		$params = $this->content['content'][$bpId];
		if (!$fields)
			unset($params['fields']);
		if (!$childblocks)
			unset($params['childblocks']);		
      return  $params;	
	}


    public function getChildBlocks2PagesData($bpId, &$arrayIds) {

        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages','*');
        $select->where('bp_parent = ?', $bpId);
        $select->order('bp_order');
        $result = $this->siteDbAdapter->fetchAll($select->__toString());

        if(!empty($result)) {
            foreach($result AS $value) {
                $arrayIds[] = $value;
                $this->getChildBlocks2PagesData($value['bp_id'], $arrayIds);
            }
        }
    }
	
    public function copyBlockFieldsData( $oldBlock, $newBlock) {
		$select = $this->siteDbAdapter->select();
		$select->from('blocks_data', '*');
		$select->where('bd_bp_id = ?', $oldBlock);
		$sourceBlocksData = $this->siteDbAdapter->fetchAll($select->__toString());
		foreach($sourceBlocksData AS $blockData) {
			$bdRow = array(
				'bd_bp_id' => $newBlock,
				'bd_field_id' => $blockData['bd_field_id'],
				'bd_value' => $blockData['bd_value'],
                'bd_hidden' => $blockData['bd_hidden'],
			);
			$this->siteDbAdapter->insert('blocks_data', $bdRow);
		}
	}	
	
	public function copyBlock($bpId) {
		
        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', '*');
        $select->where('bp_id = ?', $bpId);
        $Blocks2Pages = $this->siteDbAdapter->fetchRow($select->__toString());
		//var_dump($Blocks2Pages);
		
		$ChildBlocks2Pages = array();
		$this->getChildBlocks2PagesData($bpId, $ChildBlocks2Pages);
		//var_dump($ChildBlocks2Pages);
		
		$blockCount = count($ChildBlocks2Pages)+1;
		
		$this->siteDbAdapter->query(
			'UPDATE blocks2pages SET bp_order = bp_order+:count WHERE bp_page_id = :bp_page_id and bp_order >= :bp_order',
			array('count' =>$blockCount,'bp_page_id' =>$this->pageId, 'bp_order' => $Blocks2Pages['bp_order']+$blockCount)
		);
	
        $row = array(
            'bp_block_id' => $Blocks2Pages['bp_block_id'],
            'bp_page_id' => $this->pageId,
            'bp_parent' => $Blocks2Pages['bp_parent'],
            'bp_order' => $Blocks2Pages['bp_order']+$blockCount,
            'bp_hidden' => $Blocks2Pages['bp_hidden'],
            
        );
		
        $this->siteDbAdapter->insert('blocks2pages', $row);
        $insertId = $this->siteDbAdapter->lastInsertId();
		$this->copyBlockFieldsData( $Blocks2Pages['bp_id'], $insertId );
		$blocksIndex[ $Blocks2Pages['bp_id'] ] = $insertId;
		
		foreach ( $ChildBlocks2Pages as $child) {
			$row = array(
				'bp_block_id' => $child['bp_block_id'],
				'bp_page_id' => $this->pageId,
				'bp_parent' => $blocksIndex[ $child['bp_parent'] ],
				'bp_order' => $child['bp_order']+$blockCount,
                'bp_hidden' => $child['bp_hidden'],
			);
			
			$this->siteDbAdapter->insert('blocks2pages', $row);
			$insertId = $this->siteDbAdapter->lastInsertId();
			$this->copyBlockFieldsData( $child['bp_id'], $insertId );
			$blocksIndex[ $child['bp_id'] ] = $insertId;
			
		}
		
	}

    /**
     *  
     * @author levada@mail.ua
    */
    public function getExportData($pageId, $bpId) 
    {
		$select = $this->siteDbAdapter->select();
		$select->from('blocks2pages', '*');
		$select->where('bp_id = ?', $bpId);
		$Blocks2Pages = $this->siteDbAdapter->fetchRow($select->__toString());
		
		$Blocks2Pages = array($Blocks2Pages);
		$this->getChildBlocks2PagesData($bpId, $Blocks2Pages);
		$result['blocks2pages'] = $Blocks2Pages;
		
		$block_id = array();
		$bp_id = array();
		foreach ($Blocks2Pages as $block) {
			$block_id[] = $block['bp_block_id'];
			$bp_id[] = $block['bp_id'];
		}
		$block_id = array_unique($block_id);
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks', '*');
		$select->where('`b_id` IN ( '. implode(',',$block_id) .' )' );
		$result['blocks'] = $this->siteDbAdapter->fetchAll($select->__toString());
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks_fields', '*');
		$select->where('`bf_block_id` IN ( '. implode(',',$block_id) .' )' );
		$select->group('bf_id');
		$result['blocks_fields'] = $this->siteDbAdapter->fetchAll($select->__toString());
		
		
		$select = $this->siteDbAdapter->select();
		$select->from('blocks_data', '*' );
		$select->where('`bd_bp_id` IN ( '. implode(',',$bp_id) .' )' );
		$result['blocks_data'] = $this->siteDbAdapter->fetchAll($select->__toString());

		return $result;
    }
	
	public function importBlockCalcOrder($bpId, $blockCount) {

		$select = $this->siteDbAdapter->select();
        $select->from('blocks2pages', '*');
        $select->where('`bp_id` = ?', $bpId);
        $Blocks2Pages = $this->siteDbAdapter->fetchRow($select->__toString());

		if ($this->pageId == $Blocks2Pages['bp_page_id']) {
			$this->siteDbAdapter->query(
				'UPDATE `blocks2pages` SET `bp_order` = `bp_order`+:count WHERE `bp_page_id` = :bp_page_id AND `bp_order` > :bp_order',
				array('count' =>$blockCount,'bp_page_id' =>$this->pageId, 'bp_order' => $Blocks2Pages['bp_order'] )
			);
		}
		
		return $Blocks2Pages['bp_order'];
	}
    
    /**
     * @author italiano
     * function parse array to select list
     * @return string
     * 
     */
    public function parseString2Select($string, &$xmlstr, $default)
    {
        if(!empty($default))
        {
           $default = unserialize($default); 
        }
        
        if(!empty($string))
        {
            
            foreach(split('&',$string) as $key=>$value)
            {
                $value = explode('=',$value);
                
                if (is_array($default) && isset($default[$value[0]]))
                {
                   $selected = ' selected="selected"'; 
                }
                else{
                    $selected = '';
                }
                
                
                $xmlstr .= "
<option value=\"{$value[0]}\"$selected>".$value[1].'</option>';
            }

        }
    }
    
    public function parseString2Array($string, &$array)
    {

        if(!empty($string) )
        {
            foreach(split('&',$string) as $key=>$value)
            {
                $value = explode('=',$value);
                $array[$value[0]] = $value[1];
            }
        }
    }
    
    /**
     * function update page las modify time
     * @author italiano
     * @version 21.11.2014
     */
    public function setModify($action='unknown',$data=null) {

            $auth   = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $login = $identity->u_login;

            $rows = array(
                'pl_date' => time(),
                'pl_page_id' => $this->pageId,
                'pl_author' => $login,
                'pl_action' => $action,
                'pl_text' => '',
            );

            if (/*$action=="test"*/$this->testFields($rows,'pages_logs'))
            {           
                //$fs = "<p align=\"left\"><a href=\"/content/edit/id/{$this->pageId}/#fs%?%\" target=\"_blank\"><em>go if there</em><a/></p> ";
                $fs = "<p align=\"left\"><a class=\"log-link\" href=\"/content/edit/id/{$this->pageId}/#fs%d\" target=\"_blank\" title=\"go to the object changes, if it exists\">. . .<a/></p> ";
                $icon = '<img class="log-icon" src="%s" width="16" alt="%s"/> ';
                switch($action){
                    
                    case "addBlock":
                    
                        $text='';
                        
                        $select = $this->siteDbAdapter->select();
                        $select->from('blocks', array('b_name'));
                        $select->where('b_id = ?', $data['blockId']);
                        $select->limit(1);
                        $result = $this->siteDbAdapter->fetchRow($select->__toString());

                        $text = $result['b_name'];
                        
                        $icon = sprintf($icon, '/images/add2.png','added block');
                        $rows['pl_text'] = "$icon Block \"$text\" was added";
                        $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                        
                            break;
                            
                    case "deleteBlock":

                        $text = ' ';
                        
                        foreach($data['bpIds'] as $bp)
                        {

                               if ($this->content['content'][$bp]['bp_id'] == $bpid)
                               {
                                    
                                    $text .= '"'.$this->content['content'][$bp]['b_name'].' ID - '.$this->content['content'][$bp]['bp_order'].'", ';
                               }

                        }

                        $text = substr($text,0,-2);
                        $icon = sprintf($icon, '/images/delete.png','delete block');
                        $rows['pl_text'] = "$icon Block $text was delete";
                        $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                        
                            break;
                    
                    case "addBlockField":
                    
                        //$this->setModify('addBlockField', $data = array('bpId'=>$b2pId,'fieldId'=>$fieldId));
                         
                        $text = '';
                        
                        $bpId = $data['bpId'];
                        
                        $text .= '"'.$this->content['content'][$bpId]['b_name'].'ID - '.$this->content['content'][$bpId]['bp_order'].'"';
                        
                        $select = $this->siteDbAdapter->select();
                        $select->from('blocks_fields', array('bf_name'));
                        $select->where('bf_id = ?', $data['fieldId']);
                        $select->limit(1);
                        $result = $this->siteDbAdapter->fetchRow($select->__toString());
                        
                        $text .= ' &gt; "'.$result['bf_name'].'"';
                        
                        //$fs = str_replace('%?%', $this->content['content'][$bpId]['bp_id'],$fs);
                        $fs = sprintf($fs, $this->content['content'][$bpId]['bp_id']);
                        $icon = sprintf($icon, '/images/add.png','added field');
                        $rows['pl_text'] = "$icon Field $text was added".$fs;
                        $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                        
                            break;
                            
                    case "deleteBlockField":
                    
                        $text = '';
                        
                        foreach($this->content['content'] as $it)
                        {
                            
                            foreach($it['fields'] as $item)
                            {
                                   if ($item['bd_id'] == $data['bdId'])
                                   {
                                        
                                        $text .= '"'.$item['b_name'].' ID - '.$item['bp_order'].'" &gt; "'.$item['bf_name'].'"';
                                        
                                        $fs = sprintf($fs, $item['bp_id']);
                                        //$fs = str_replace('%?%', $item['bp_id'],$fs);
                                        
                                   }
                            }

                        }
                        $icon = sprintf($icon, '/images/delete.png','delete field');
                        $rows['pl_text'] = "$icon Field $text was delete".$fs;
                        $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                        
                            break;
                    
                    
                    case "hiddenBlock":
                        
                            $text = '';
                            
                            if (isset($this->content['content'][$data['bpId']]))
                            {
                                $text = '"'.$this->content['content'][$data['bpId']]['b_name'].' ID - '.$this->content['content'][$data['bpId']]['bp_order'].'", ';
                                $fs = sprintf($fs, $this->content['content'][$data['bpId']]['bp_id']);
                                //$fs = str_replace('%?%', $this->content['content'][$data['bpId']]['bp_id'],$fs);
                            }
                            
                            $icon = sprintf($icon, '/images/visible.png','displayed');
                            $rows['pl_text'] = "$icon Block $text was ".($data['type'] ? 'hidden' : 'visible').$fs;
                            $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                            
                            break;
                        case "hiddenField":
                        
                            $text = '';
                            
                            foreach($this->content['content'] as $it)
                            {
                                
                                foreach($it['fields'] as $item)
                                {
                                       if ($item['bd_id'] == $data['bdid'])
                                       {
                                            
                                            $text .= '"'.$item['b_name'].' ID - '.$item['bp_order'].'" &gt; "'.$item['bf_name'].'"';
                                            $fs = sprintf($fs, $item['bp_id']);
                                            //$fs = str_replace('%?%', $item['bp_id'],$fs);
                                            
                                       }
                                }
    
                            }
                            $icon = sprintf($icon, '/images/visible.png','displayed');
                            $rows['pl_text'] = "$icon Field $text was ".($data['type'] ? 'hidden' : 'visible').$fs;
                            $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                            
                            break;        
                    case "editBlockField":

                        $text='';
                        $different = false;
                        $select = $this->siteDbAdapter->select();
                        $select->from('blocks_data', array('bd_value','bd_field_id'));
                        $select->joinLeft('blocks_fields', 'bf_id = bd_field_id',  array('bf_type'));
                        $select->where('bd_id = ?', $data['bdId']);
                        $select->limit(1);
                        $row = $this->siteDbAdapter->fetchRow($select->__toString());
                        $old =$row['bd_value'];
                        $new = $data['text'];
                        $field_type = $row['bf_type'];

                        //$diff = FineDiff::getDiffOpcodes($old, $new);
                        //$diff = FineDiff::renderToTextFromOpcodes($old, $diff); 
                        
                        
                        if ($field_type == "L")
                        {
                            $old = implode(' ',unserialize($old));
                            $new = implode(' ',unserialize($new));
                        }
                        elseif($field_type == "A" || $field_type == "I" || $field_type == "J" || $field_type == "W"){
                            $old = self::multi_implode(' ',unserialize($old));
                            $new = self::multi_implode(' ',unserialize($new));
                        }
                        
                        if ($old != $new){
                            $different = true;
                        }
                        
                        $diff = FineDiff::getDiffOpcodes($old, $new,3);
                        $diffHTML = FineDiff::renderDiffToHTMLFromOpcodes($old, $diff);

                        //$diffHTML = FineDiff::renderToTextFromOpcodes($old, $diff);
                        
                        
                        foreach($this->content as $item)
                        {
                            if ($item['bd_id'] == $data['bdId']){
                                $text = '"'.$item['b_name'].'" ';
                            } 
                        }

                        foreach($this->content['content'] as $it)
                        {
                            
                            foreach($it['fields'] as $item)
                            {
                                
                                   if ($item['bd_id'] == $data['bdId'])
                                   {
                                        $fs = sprintf($fs, $item['bp_id']);
                                        //$fs = str_replace('%?%', $item['bp_id'],$fs);
                                        
                                        $old =$item['bd_value'];
                                        $text .= '"'.$item['b_name'].' ID - '.$item['bp_order'].'"'. '&gt; "'.$item['bf_name'].'('.$field_type.')'.'"';
                                   }
                            }
                            
                        }
                        
                        if (strlen($new)>1000){
                            
                            if (!empty($old))
                            {
                                $diffs = htmlentities('<br/><br/><del>'.self::snippet($old,1000)."</del><br/>".' '.self::snippet($new,1000));
                            }
                            else{
                                $diffs = htmlentities('<br/>'.self::snippet($new,1000));
                            }
                        
                        
                            $diffs .= '<br/><em>(see last 1000 characters)</em>';
                        }
                        else{
                        
                            if (!empty($old))
                            {
                                $diffs = htmlentities('<br/>'.$diffHTML);
                            }
                            else{
                                $diffs = htmlentities('<br/>'.$new);
                            }
                              
                        }
                        
                        
                        if (!empty($old) && $diffHTML != $old && $different)
                        {
                            $icon = sprintf($icon, '/images/edit.png','edit');
                            $rows['pl_text'] = "$icon $text $diffs".$fs;
                            $result = $this->siteDbAdapter->insert('pages_logs', $rows);
                        }

                        
                            break;
                }
            }

    }
    
    /**
     * function test column table `pages`
     * @author italiano
     * @param $data = array()
     * @date 25.11.2014
     * @return bool
     */
    public function testFields($data=null, $_table=null) 
    {
        
        if (isset($_table))
        {
            $table = $_table;
        }
        else{
            $table = 'pages';
        }
        
        $fieldsError = array();
        $fieldsToAdd = array(
            'pg_lastmodify' => '`pg_lastmodify` NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'pg_lasteditor' => '`pg_lasteditor` VARCHAR(30)',
            'bp_hidden' => "`bp_hidden` enum('0','1') NOT NULL DEFAULT '0'",
            'bd_hidden' => "`bd_hidden` enum('0','1') NOT NULL DEFAULT '0'",
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
                            if (isset($fieldsToAdd[$key])){
                                $q = "ALTER TABLE `$table` ADD $fieldsToAdd[$key]";
                            } 
                            else{
                                $q = "ALTER TABLE `$table` ADD `$key` VARCHAR(30)";
                            }
    
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
           
        if (count($fieldsError)>0)
        {
            return false;
        }
        else{
            return true;
        }

    }
    
        public function hiddenBlock($bpId=null) 
        {
                        
            if (isset($bpId))
            {
                $select = $this->siteDbAdapter->select();
                $select->from('blocks2pages', array('bp_hidden'));
                $select->where('bp_id = ?', $bpId);
                $select->limit(1);
                $result = $this->siteDbAdapter->fetchRow($select->__toString());
                
                if (count($result)>0)
                {
                    if (empty($result['bp_hidden']))
                    {
                        $result['bp_hidden'] = 0;
                    }
                    
                    $type = ($result['bp_hidden'] == 1 ? 0 : 1);
                    
                    $set = array(
                        'bp_hidden' => $type,
                    );
        
                    if ($this->testFields($set,'blocks2pages'))
                    {
                        $result = $this->siteDbAdapter->update('blocks2pages', $set, $this->siteDbAdapter->quoteInto('bp_id = ?', $bpId));
                        
                        self::setModify('hiddenBlock',$data = array('bpId'=>$bpId,'type'=>$type));
                        
                        self::hiddenBlockForChildren($bpId,$type);
                    }
                }
                
            }
            
            return (bool)$type;
    }
    
    public function hiddenBlockForChildren($bpId=null,$type) 
    {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks2pages','*');
        $select->where('bp_parent = ?', $bpId);
        
        $rows = $this->siteDbAdapter->fetchAll($select->__toString());

        if (count($rows)>0)
        {
            $set = array(
                'bp_hidden' => $type,
            );
            
            $result = $this->siteDbAdapter->update('blocks2pages', $set, $this->siteDbAdapter->quoteInto('bp_parent = ?', $bpId));
            
            
            
            foreach($rows as $row){
                
                self::hiddenBlockForChildren($row['bp_id'],$type);
                self::setModify('hiddenBlock',$data = array('bpId'=>$row['bp_id'],'type'=>$type));
            }

        }
    }
    public function hiddenField($bdid=null) 
    {        
            if (isset($bdid))
            {
                $select = $this->siteDbAdapter->select();
                $select->from('blocks_data', array('bd_hidden'));
                $select->where('bd_id = ?', $bdid);
                $select->limit(1);
                $result = $this->siteDbAdapter->fetchRow($select->__toString());
                
                if (count($result)>0)
                {
                    if (empty($result['bd_hidden']))
                    {
                        $result['bd_hidden'] = 0;
                    }
                    
                    $type = ($result['bd_hidden'] == 1 ? 0 : 1);
                    
                    $set = array(
                        'bd_hidden' => $type,
                    );
        
                    if ($this->testFields($set,'blocks_data'))
                    {
                        $result = $this->siteDbAdapter->update('blocks_data', $set, $this->siteDbAdapter->quoteInto('bd_id = ?', $bdid));
                        self::setModify('hiddenField',$data = array('bdid'=>$bdid,'type'=>$type));
                    }
                }
                
            }
            
            return (bool)$type;
    }
    public function blockInfo($bid=null) 
    {           
            if (isset($bid))
            {
                $select = $this->siteDbAdapter->select();
                $select->from('blocks');
                $select->where('b_id = ?', $bid);
                $select->limit(1);
                $result = $this->siteDbAdapter->fetchRow($select->__toString());

                return array('text'=>$result['b_text'],'image'=>$result['b_base64']);

            }

            return null;
    }
    
    public function snippet($text, $maxchar=200, $allowTags=false, $onlyText=true){
                
        (!$allowTags) ? $allowTags = '<p><br>' :    $allowTags = $allowTags.'<p><br>';
        (!$onlyText) ? $text = strip_tags($text, $allowTags) : $text = strip_tags($text);
        
        if(iconv_strlen($text, 'utf-8') > $maxchar ){
            $text = iconv_substr($text, 0, $maxchar, 'utf-8' );
            $text = preg_replace('@(.*)\s[^\s]*$@s', '\\1...', $text);
        }
        $text = trim($text);
        
        return $text;
    }
    
    private static function multi_implode($sep, $array) {
        foreach($array as $key=>$val){
            $_array[] = ' ['.$key.']=>'.(is_array($val)? self::multi_implode($sep, $val) : $val);
        }
    
        return implode($sep, $_array);
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