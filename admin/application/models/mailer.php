<?php

class Mailer {
    #PIVATE VARIABLES
    private $allDbAdapter;

    #PUBLIC VARIABLES


    public function __construct() {
        $config = Zend_Registry::get('config');

        $params = $config->db->config->toArray();

        $this->allDbAdapter = Zend_Db::factory($config->db->adapter, $params);
    }

    public function __destruct() {
        $this->allDbAdapter = NULL;
    }

    public function addMailer($table,
                              $field,
                              $text,
                              $subject,
                              $sender) {

        $userRow = array(
            'm_table' => $table,
            'm_field_name' => $field,
            'm_text' => $text,
            'm_subject' => $subject,
            'm_sender' => $sender,
            'm_additional_fields' => ''
        );

        $this->allDbAdapter->insert('mailers', $userRow);
        return $this->allDbAdapter->lastInsertId();

    }

    public function getMailers() {
        $select = $this->allDbAdapter->select();
        $select->from('mailers', array('m_id',
                                       'm_table',
                                       'm_field_name',
                                       'm_text',
                                       'm_subject'));
        return $this->allDbAdapter->fetchAll($select->__toString());
    }

    public function getMailer($mailerId) {
        $select = $this->allDbAdapter->select();
        $select->from('mailers', array('m_id',
                                       'm_table',
                                       'm_field_name',
                                       'm_text',
                                       'm_subject',
                                       'm_sender',
                                       'm_additional_fields'));

        $select->where('m_id = ?', $mailerId);

        return $this->allDbAdapter->fetchRow($select->__toString());
    }

    public function deleteMailer($mailerId) {
        $where = $this->allDbAdapter->quoteInto('m_id = ?', $mailerId);
        $this->allDbAdapter->delete('mailers', $where);
    }

    public function editMailer($id,
                               $table,
                               $field,
                               $text,
                               $subject,
                               $sender) {

        $set = array(
            'm_table' => $table,
            'm_field_name' => $field,
            'm_text' => $text,
            'm_subject' => $subject,
            'm_sender' => $sender
        );

        $where = $this->allDbAdapter->quoteInto('m_id = ?', $id);
        $this->allDbAdapter->update('mailers', $set, $where);
    }

    private function getEmails($table, $field, $additionalFields = NULL) {
        
        $queryFields = array($field);

        if(!empty($additionalFields)) {
            $queryFields = array_merge($queryFields, $additionalFields);
        }

        $select = $this->allDbAdapter->select();
        $select->from($table, $queryFields);
        $result = $this->allDbAdapter->fetchAll($select->__toString());

        $emails = array();
        $i = 0;
        foreach($result AS $row) {
            if(strlen(trim($row[$field]))) {
                $emails[$i]['email'] = $row[$field];
                
                if(!empty($additionalFields)) {
                    foreach($additionalFields AS $addField) {
                        $emails[$i][$addField] = $row[$addField];
                    }
                }
                
                $i++;
            }
        }

        return $emails;
    }

    public function executeMailer($id) {

        $mailer = $this->getMailer($id);
        $emails = $this->getEmails($mailer['m_table'], $mailer['m_field_name'], (strlen($mailer['m_additional_fields']) ? explode(',', $mailer['m_additional_fields']) : NULL));


        $headers  = "MIME-Version: 1.0\n";
        $headers .= "X-Priority: 1\n";
        $headers .= "X-Mailer: PHP mailer (v0.1)\n";
        $headers .= "X-MSMail-Priority: High\n";
        $headers .= "From: ".$this->company_params['name']." <".$mailer['m_sender'].">\n";

        foreach($emails as $email) {
            
            $text = $mailer['m_text'];
            
            foreach($email AS $key => $value) {
                if($key != 'email') {
                    $text = str_replace('['.$key.']', $value, $text);
                }
            }
            
            mail($email['email'], $mailer['m_subject'], $text, $headers);
        }

    }
}

?>
