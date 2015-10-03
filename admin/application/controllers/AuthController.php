<?php

class AuthController extends MainApplicationController {

    #PIVATE VARIABLES

    #PUBLIC VARIABLES

    public function init() {
        if($this->_request->action != 'logout') {
            parent::init();
        }
    }

    public function __destruct() {
        $this->display();
    }

    public function indexAction() {
        array_push($this->viewIncludes, 'login.tpl');
    }

    public function loginAction() {

        if ( $this->_request->isPost() && $this->_request->getPost('username') && $this->_request->getPost('password') && $this->_request->getPost('captcha') ) 
        {

            // collect the data from the user
            Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($this->_request->getPost('username'));
            $password = $filter->filter($this->_request->getPost('password'));
            $captcha = (int)$filter->filter($this->_request->getPost('captcha'));
            
            
            if ($captcha == $_SESSION['captcha'])
            {
                // setup Zend_Auth adapter for a database table
                Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
                $dbAdapter = Zend_Registry::get('dbAdapter');
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
    
                $authAdapter->setTableName('users');
                $authAdapter->setIdentityColumn('u_login');
                $authAdapter->setCredentialColumn('u_passwd');
    
                // Set the input credential values
                // to authenticate against
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential(md5($password));
    
    
                // do the authentication
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                              
                if ($result->isValid()) {
                    // success: store database row to auth's storage
                    // system. (Not the password though!)
                    $data = $authAdapter->getResultRowObject(null, 'password');
                    $auth->getStorage()->write($data);
                    $this->_redirect('/pages/');
    
                } else {
                    $this->_redirect('/auth/');
                    // failure: clear database row from session
                }
             } 
             else
             {
                $this->_redirect('/auth/');
             }
             
            
        } else {

            $this->_redirect('/auth/');
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/auth/');
    }
}

?>