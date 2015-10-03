<?php

class Extensions {

    # PRIVATE VÀRIABLES
    private $siteDb;
    private $extensions;

    # PUBLIC VARIABLES

    public function __construct($siteId) {

        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname'));
        $select->where('s_id = ?', $siteId);
        $dbname = $dbAdapter->fetchOne($select->__toString());

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $params['dbname'] = $dbname;

        $this->siteDb = Zend_Db::factory($config->db->adapter, $params);

        $this->loadExtensions();
    }

    private function loadExtensions() {
        $select = $this->siteDb->select();
        $select->from('extensions', array('id' => 'ext_id',
                                          'name' => 'ext_name',
                                          'nick' => 'ext_nick',
                                          'type' => 'ext_type',
                                          'blocked' => 'ext_blocked'));
        $select->order('ext_name');
        $this->extensions = $this->siteDb->fetchAll($select->__toString());
    }

    public function getExtensions() {
        return $this->extensions;
    }

}

?>