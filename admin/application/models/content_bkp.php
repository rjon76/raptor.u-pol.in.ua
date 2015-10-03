<?php

include_once('blocks.php');

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
                                            'bp_order'));

        $select->joinLeft('blocks_data', 'bp_id = bd_bp_id',  array('bd_id',
                                                                    'bd_bp_id',
                                                                    'bd_field_id',
                                                                    'bd_value'));

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
    public function getChildBlocksList($bpId) {
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

        $this->siteDbAdapter->insert('blocks2pages', $row);
        return $this->siteDbAdapter->lastInsertId();
    }

    // Óäàëèòü áëîê èç ñòðàíèöè
    public function deleteBlock($bpId) {

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

        $this->siteDbAdapter->insert('blocks_data', $row);
        return $this->siteDbAdapter->lastInsertId();
    }

    // Óäàëèòü ïåðåìåííóþ ñ äàííûìè èç áëîêà
    public function deleteBlockField($bdId) {
        $this->siteDbAdapter->delete('blocks_data', $this->siteDbAdapter->quoteInto('bd_id = ?', $bdId));
    }

    // Ðåäàêòèðîâàòü ïåðåìåííóþ ñ äàííûìè â áëîêå
    public function editBlockField($bdId, $value) {
        $set = array(
            'bd_value' => $value
        );

        $this->siteDbAdapter->update('blocks_data', $set, $this->siteDbAdapter->quoteInto('bd_id = ?', $bdId));
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

}
?>