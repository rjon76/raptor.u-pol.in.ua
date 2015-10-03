<?php

Zend_Loader::loadClass('Zend_Log');
Zend_Loader::loadClass('Zend_Log_Writer_Stream');

class User {

    #PIVATE VARIABLES
    private $userId;
    private $login;
    private $groupId;
    private $contPerms;
    private $logger;

    #PUBLIC VARIABLES

    public function __construct($id) {

        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('users', array('u_login', 'u_group_id'));
        $select->where('u_id = ?', $id);
        $result = $dbAdapter->fetchRow($select->__toString());

        $this->userId   = $id;
        $this->groupId  = $result['u_group_id'];
        $this->login    = $result['u_login'];

        $select = $dbAdapter->select();
        $select->from('groups2controllers', array('gc_controller_id', 'gc_permission'));
        $select->where('gc_group_id = ?', $this->groupId);
        $g2cResult = $dbAdapter->fetchAll($select->__toString());

        $select = $dbAdapter->select();
        $select->from('users2controllers', array('uc_controller_id', 'uc_permission'));
        $select->where('uc_user_id = ?', $this->userId);
        $u2cResult = $dbAdapter->fetchAll($select->__toString());

        foreach($g2cResult as $row) {
            $this->contPerms[$row['gc_controller_id']] = $row['gc_permission'];
        }

        foreach($u2cResult as $row) {
            $this->contPerms[$row['uc_controller_id']] = $row['uc_permission'];
        }

        $config = Zend_Registry::get('config');
        if($config->user->tolog) {
            $writer = new Zend_Log_Writer_Stream($config->user->logpath);
            $this->logger = new Zend_Log($writer);
            $this->logAction('asdasdasdasd');
        }
    }

    public function __destruct() {
        $this->userId       = NULL;
        $this->login        = NULL;
        $this->groupId      = NULL;
        $this->contPerms    = NULL;
        $this->logger      = NULL;
    }

    /*
     Получть id юзера
    */
    public function getUserId() {
        return $this->userId;
    }

    /*
     Получть id группы юзера
    */
    public function getGroupId() {
        return $this->groupId;
    }

    /*
     Получть login юзера
    */
    public function getLogin() {
        return $this->login;
    }

    /*
     Проверка на право чтения
    */
    public function checkReadPerm($controllerId) {
        if(isset($this->contPerms[$controllerId]) &&
           strstr($this->contPerms[$controllerId], 'r')) {
            return true;
        }
        return false;
    }

    /*
     Проверка на право редактирования
    */
    public function checkWritePerm($controllerId) {
        if(isset($this->contPerms[$controllerId]) &&
           strstr($this->contPerms[$controllerId], 'w')) {
            return true;
        }
        return false;
    }

    /*
     Проверка на право удаления
    */
    public function checkDelPerm($controllerId) {
        if(isset($this->contPerms[$controllerId]) &&
           strstr($this->contPerms[$controllerId], 'd')) {
            return true;
        }
        return false;
    }

    /*
     Логировоние действия юзера
    */
    public function logAction($message) {
        $config = Zend_Registry::get('config');
        if($config->user->tolog) {
            $this->logger->info($this->login.' - '.$message);
        }
    }
}

?>