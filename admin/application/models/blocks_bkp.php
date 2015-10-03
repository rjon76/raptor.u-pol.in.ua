<?php

class Blocks {

    #PIVATE VARIABLES
    private $siteDbAdapter;
    private $blocks;

    #PUBLIC VARIABLES


    /*
     �����������
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
     ����������
    */
    public function __destruct() {
        $this->siteDbAdapter = NULL;
        $this->blocks        = NULL;
    }

    /*
     �������� ������ ������
    */
    public function getBlocksList() {
        return $this->blocks;
    }

    /*
     ��������� ��� ������ ������ �� �����
    */
    private function loadBlocks() {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks', '*');
        $select->order('b_name');

        $blocks = $this->siteDbAdapter->fetchAll($select->__toString());

        foreach($blocks AS $block) {
            $this->blocks[$block['b_id']] = $block;
        }
    }

    /*
     �������� ������ �����
    */
    public function getBlock($blockId) {
        return $this->blocks[$blockId];
    }

    /*
     ������������� ������ �����
    */
    public function editBlock($blockId, $name, $file, $parent) {
        $set = array(
            'b_name'   => $name,
            'b_file'   => $file,
            'b_parent' => $parent
        );
        $this->siteDbAdapter->update('blocks', $set, $this->siteDbAdapter->quoteInto('b_id = ?', $blockId));
    }

    /*
     �������� ����� ����
    */
    public function addBlock($name, $file, $parent) {
        $row = array(
            'b_name'   => $name,
            'b_file'   => $file,
            'b_parent' => $parent
        );
        $this->siteDbAdapter->insert('blocks', $row);
        return $this->siteDbAdapter->lastInsertId();
    }

    /*
     ������� ����
    */
    public function deleteBlock($blockId) {
        $this->siteDbAdapter->delete('blocks', $this->siteDbAdapter->quoteInto('b_id = ?', $blockId));
    }

    /*
     �������� ��� ���������� ���������� � �����
    */
    public function getBlockFields($blockId) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks_fields', '*');
        $select->where('bf_block_id = ?', $blockId);

        return $this->siteDbAdapter->fetchAll($select->__toString());
    }

    /*
     �������� ������ ���������� ���������� � �����
    */
    public function getBlockField($fieldId) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks_fields', '*');
        $select->where('bf_id = ?', $fieldId);

        return $this->siteDbAdapter->fetchRow($select->__toString());
    }

    /*
     �������� ���������� � ����
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
     ������������� ����������
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
     ������� ����������
    */
    public function deleteBlockField($fieldId) {
        $this->siteDbAdapter->delete('blocks_fields', $this->siteDbAdapter->quoteInto('bf_id = ?', $fieldId));
    }

    /*
     ��������� �� ������������� �������� ������
    */
    public function isChildBlocksExist($blockId) {
$select = $this->siteDbAdapter->select();
        $select->from('blocks', 'COUNT(b_id)');
        $select->where('b_parent = ?', $blockId);

        if($this->siteDbAdapter->fetchOne($select->__toString()) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /*
     ��������� �� ������������� ���������� ���������� � �����
    */
    public function isBlockFieldsExist($blockId) {
        $select = $this->siteDbAdapter->select();
        $select->from('blocks_fields', 'COUNT(bf_id)');
        $select->where('bf_block_id = ?', $blockId);

        if($this->siteDbAdapter->fetchOne($select->__toString()) > 0) {
            return TRUE;
        }
        return FALSE;
    }


    // �������� ��� �����
    public function getBlockName($blockId) {
        $blocksList = $this->getBlocksList();
        return $blocksList[$blockId]['b_name'];
    }
}


?>