<?php

class EmailArchive {

    #PIVATE VARIABLES
    private $allDbAdapter;

    #PUBLIC VARIABLES


    public function __construct() {
        $config = Zend_Registry::get('config');

        $params = $config->db->config->toArray();

        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
    }

    public function getReceivers() {

        $select = $this->allDbAdapter->select();
        $select->distinct()->from('email_archive', array('ea_email_to'));
        $result = $this->allDbAdapter->fetchAll($select->__toString());

        $receivers = array();

        foreach($result AS $row) {
            $receivers[] = $row['ea_email_to'];
        }

        return $receivers;

    }

    public function getSubjects($reciever = NULL,
                                $date = NULL) {

        $select = $this->allDbAdapter->select();
        $select->from('email_archive', array('ea_id',
                                             'ea_email_from',
                                             'ea_subject'));
        if(isset($reciever)) {
            $select->where('ea_email_to = ?', $reciever);
        }

        if(isset($date)) {
            $select->where('ea_date LIKE ?', '%'.$date.'%');
        } else {
            $select->where('ea_date LIKE ?', '%'.date('Y-m-d').'%');
        }

        $select->order('ea_date');

        return $this->allDbAdapter->fetchAll($select->__toString());

    }

    public function getDates($reciever = NULL) {

        $select = $this->allDbAdapter->select();
        $select->distinct()->from('email_archive', array('ea_date' => 'DATE_FORMAT(ea_date, "%Y-%m-%d")'));

        if(isset($reciever)) {
            $select->where('ea_email_to = ?', $reciever);
        }
        $select->order('ea_date');
        $result = $this->allDbAdapter->fetchAll($select->__toString());

        $dates = array();

        foreach($result AS $row) {
            $dates[] = $row['ea_date'];
        }

        return $dates;
    }

    public function getEmail($id) {
        $select = $this->allDbAdapter->select();
        $select->from('email_archive', array('ea_id',
                                             'ea_email_to',
                                             'ea_email_from',
                                             'ea_subject',
                                             'ea_message',
                                             'ea_date'));
        $select->where('ea_id = ?', $id);
        return $this->allDbAdapter->fetchRow($select->__toString());
    }

}

?>