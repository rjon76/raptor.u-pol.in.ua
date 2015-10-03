<?php
/*
 Класс для управления контроллерами админки
*/
class ControllersHandler {
    #PIVATE VARIABLES
    private $dbAdapter;

    #PUBLIC VARIABLES

    public function __construct() {
        $this->dbAdapter = Zend_Registry::get('dbAdapter');
    }

    public function __destruct() {
        $this->dbAdapter = NULL;
    }

    /*
     Добавить контроллер
    */
    public function addController($name, $menuName, $isSiteDependent = TRUE) {

        $contRow = array(
            'c_name' => $name,
            'c_menu_name' => $menuName,
            'c_is_site_dependent' => ($isSiteDependent ? 1 : 0));

        $this->dbAdapter->insert('controllers', $contRow);
        return $this->dbAdapter->lastInsertId();
    }

    /*
     Изменить контроллер
    */
    public function changeController($contId, $name, $menuName, $isSiteDependent = TRUE) {

        $set = array(
            'c_name' => $name,
            'c_menu_name' => $menuName,
            'c_is_site_dependent' => ($isSiteDependent ? 1 : 0));

        $where = $this->dbAdapter->quoteInto('c_id = ?', $contId);
        $this->dbAdapter->update('controllers', $set, $where);
    }

    /*
     Удалить контроллер. Удаляется вместе со всеми записями в таблицах прав
     users2controllers, groups2controllers и sites2controllers где
     встерчается этот конторллер
    */
    public function deleteController($contId) {

        $where = $this->dbAdapter->quoteInto('c_id = ?', $contId);
        $this->dbAdapter->delete('controllers', $where);

        $where = $this->dbAdapter->quoteInto('uc_controller_id = ?', $contId);
        $this->dbAdapter->delete('users2controllers', $where);

        $where = $this->dbAdapter->quoteInto('gc_controller_id = ?', $contId);
        $this->dbAdapter->delete('groups2controllers', $where);

        $this->deleteController2SiteRelation($contId);
    }

    /*
     Получить список всех конроллеров
    */
    public function getControllersList($siteId = NULL) {
        $select = $this->dbAdapter->select();
        $select->from('controllers', array('c_id',
                                           'c_name',
                                           'c_menu_name',
                                           'c_is_site_dependent'));
        $select->order('c_is_site_dependent');
        $contRes = $this->dbAdapter->fetchAll($select->__toString());

        if(isset($siteId)) {
            $select = $this->dbAdapter->select();
            $select->from('sites2controllers', array('sc_controller_id', 'sc_id'));
            $select->where('sc_site_id = ?', $siteId);

            $siteRelsRes = $this->dbAdapter->fetchPairs($select->__toString());
        }
        $controllers = array();

        foreach($contRes AS $controller) {
            $controllers['list'][$controller['c_id']] = $controller['c_menu_name'];
            $controllers['all'][$controller['c_id']] = $controller;
            if(isset($siteRelsRes[$controller['c_id']]) || !$controller['c_is_site_dependent']) {
                $controllers['site_related'][$controller['c_id']] = $controller;
            }
        }

        return $controllers;

    }

    /*
     Получить ID конторллера по имени
    */
    public function getControllerIdByName($contName) {
        $select = $this->dbAdapter->select();
        $select->from('controllers', array('c_id'));
        $select->where('c_name = ?', $contName);
        return $this->dbAdapter->fetchOne($select->__toString());
    }

    /*
     Привязать "site dependent" контроллер к сайту
    */
    public function addController2SiteRelation($contId, $siteId) {
        $cont2SiteRow = array(
            'sc_site_id' => $siteId,
            'sc_controller_id' => $contId);

        $this->dbAdapter->insert('sites2controllers', $cont2SiteRow);
        return $this->dbAdapter->lastInsertId();
    }

    /*
     Удалить привязку "site dependent" контроллера к сайту,
    */
    public function deleteController2SiteRelation($contId, $siteId = NULL) {

        $where[] = $this->dbAdapter->quoteInto('sc_controller_id = ?', $contId);

        if(isset($siteId)) {
            $where[] = $this->dbAdapter->quoteInto('sc_site_id = ?', $siteId);
        }

        $this->dbAdapter->delete('sites2controllers', $where);

    }

    /*
     Получить список связей контроллер - сайт
    */
    public function getController2SiteRelationList($contId) {
        $select = $this->dbAdapter->select();
        $select->from('sites2controllers', array('sc_id', 'sc_site_id', 'sc_controller_id'));
        $select->joinLeft('controllers', 'c_id = sc_controller_id', 'c_menu_name');
        $select->joinLeft('sites', 's_id = sc_site_id', 's_hostname');
        $select->where('sc_controller_id = ?', $contId);
        $select->order('c_menu_name');

        return $this->dbAdapter->fetchAll($select->__toString());
    }

    public function getControllerData($contId) {
        $select = $this->dbAdapter->select();
        $select->from('controllers', '*');
        $select->where('c_id = ?', $contId);
        return $this->dbAdapter->fetchRow($select->__toString());
    }
}
?>