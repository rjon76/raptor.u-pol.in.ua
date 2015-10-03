<?php

include_once('models/mailer.php');
include_once('models/emailArchive.php');

class MailerController extends MainApplicationController {

    #PIVATE VARIABLES
    private $mailer;

    #PUBLIC VARIABLES

    public function init() {
        parent::init();

        $this->mailer = new Mailer();

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Mailers'),
            array('name' => 'archive', 'menu_name' => 'E-mail Archive')
        );
    }

    public function __destruct() {
        $this->display();
    }

    public function indexAction() {
        $this->_redirect('/mailer/list/');
    }

    public function listAction() {

        if ($this->_request->isPost()) {

            if($this->_request->getPost('addMailer')) {
                $this->mailer->addMailer(
                    $this->_request->getPost('table'),
                    $this->_request->getPost('field'),
                    $this->_request->getPost('text'),
                    $this->_request->getPost('subject'),
                    $this->_request->getPost('sender')
                );
            }

        }

        $this->tplVars['mailer']['mailersList'] = $this->mailer->getMailers();

        array_push($this->viewIncludes, 'mailer/listMailers.tpl');
        array_push($this->viewIncludes, 'mailer/addMailer.tpl');

    }

    public function deleteAction() {

        if($this->_hasParam('id')) {
            $this->mailer->deleteMailer($this->_getParam('id'));
        }
        $this->_redirect('/mailer/list/');

    }

    public function editAction() {

        if($this->_hasParam('id')) {
            if ($this->_request->isPost()) {

                if($this->_request->getPost('editMailer')) {
                    $this->mailer->editMailer(
                        $this->_getParam('id'),
                        $this->_request->getPost('table'),
                        $this->_request->getPost('field'),
                        $this->_request->getPost('text'),
                        $this->_request->getPost('subject'),
                        $this->_request->getPost('sender')
                    );
                    $this->_redirect('/mailer/list/');
                }

            }

            $this->tplVars['mailer']['val'] = $this->mailer->getMailer($this->_getParam('id'));

            array_push($this->viewIncludes, 'mailer/editMailer.tpl');
        } else {
            $this->_redirect('/mailer/list/');
        }
    }

    public function sendAction() {

        if($this->_hasParam('id')) {
            $this->mailer->executeMailer($this->_getParam('id'));
            $this->_redirect('/mailer/list/');
        }
    }


    public function archiveAction() {

        $archive = new EmailArchive();

        if($this->_hasParam('receiver')) {

            if($this->_hasParam('date')) {
                $this->tplVars['archive']['date'] = $this->_getParam('date');
            } else {
                $this->tplVars['archive']['date'] = date('Y-m-d');
            }

            $this->tplVars['archive']['receiver'] = $this->_getParam('receiver');
            $this->tplVars['archive']['dates'] = $archive->getDates($this->_getParam('receiver'));
            $this->tplVars['archive']['emails'] = $archive->getSubjects($this->_getParam('receiver'),
                                                                        $this->_getParam('date'));
        }

        $this->tplVars['archive']['receivers'] = $archive->getReceivers();

        array_push($this->viewIncludes, 'mailer/listArchive.tpl');
    }

    public function showemailAction() {
        if($this->_hasParam('id')) {
            $archive = new EmailArchive();

            $this->tplVars['email'] = $archive->getEmail($this->_getParam('id'));
            $this->tplVars['date'] = substr($this->tplVars['email']['ea_date'], 0, 10);

            array_push($this->viewIncludes, 'mailer/showEmail.tpl');

            array_push($this->tplVars['header']['actions']['names'], array('name' => 'showemail', 'menu_name' => 'Email'));
        } else {
            $this->_redirect('/mailer/list/');
        }
    }

}

?>