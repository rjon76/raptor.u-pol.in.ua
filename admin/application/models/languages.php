<?php

class Languages {

    #PIVATE VARIABLES
    private $siteDbAdapter;
    private $languages;

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

        $this->languages = array();
        $this->loadLanguages();
    }

    /*
     ����������
    */
    public function __destruct() {
        $this->siteDbAdapter = NULL;
    }

    /*
     �������� ������ ������
    */
    public function getLanguagesList() {
        return $this->languages;
    }

    /*
     ��������� ��� ������ ������ �� �����
    */
    private function loadLanguages() {
        $select = $this->siteDbAdapter->select();
        $select->from('languages', '*');
        $select->order('l_name');

        $languages = $this->siteDbAdapter->fetchAll($select->__toString());

        foreach($languages AS $lang) {
            $this->languages[$lang['l_id']] = $lang;
        }
    }

    /*
     �������� ������ �����
    */
    public function getLanguage($Id) {
        return $this->languages[$Id];
    }
}


?>