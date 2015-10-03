<?php

include_once('models/usersHandler.php');
include_once('models/controllersHandler.php');
include_once('models/user.php');

class UsersController extends MainApplicationController {

    #PIVATE VARIABLES
    private $users;

    #PUBLIC VARIABLES

    public function init() {
        parent::init();

        $this->users = new UsersHandler();

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Users list')
        );
    }

    public function __destruct() {
        $this->display();
    }

    public function indexAction() {
        $this->_redirect('/users/list/');
    }

    public function listAction() {

        if ($this->_request->isPost()) {
            if($this->_request->getPost('user')) {
                $this->addUser();
            }
            if($this->_request->getPost('groupadd')) {
                $this->addGroup();
            }
        }

        $this->tplVars['users']['usersList'] = $this->users->listUsers();
        $this->tplVars['users']['groupsList'] = $this->users->listGroups();

        array_push($this->viewIncludes, 'users/usersList.tpl');
        array_push($this->viewIncludes, 'users/usersAdd.tpl');
        array_push($this->viewIncludes, 'users/groupsList.tpl');
        array_push($this->viewIncludes, 'users/groupsAdd.tpl');
    }


    public function deleteAction() {

        if($this->_hasParam('id')) {
            $this->users->deleteUser($this->_getParam('id'));
            $this->users->deleteUserPermissions($this->_getParam('id'));
        }
        $this->_redirect('/users/list/');
    }

    public function editAction() {
        if($this->_hasParam('id')) {
            $userId = $this->_getParam('id');

            if ($this->_request->isPost()) {
                if($this->_request->getPost('user')) {
                    $this->changeUser($userId);
                }

                if($this->_request->getPost('addPerm')) {
                    $this->addPermission($userId);
                }

                if($this->_request->getPost('updatePerms')) {
                    $this->updatePermissions($userId);
                }
            }

            $user = new User($userId);
            $controllers = new ControllersHandler();
            $contList = $controllers->getControllersList($this->getSiteId());
            $permissions = $this->users->listUserPermissions($userId);

            foreach($permissions AS $perm) {
                if(isset($contList['list'][$perm['uc_controller_id']])) {
                    unset($contList['list'][$perm['uc_controller_id']]);
                }
            }

            foreach($permissions AS $key => $value) {
                if(strstr($value['uc_permission'], 'r')) {
                    $permissions[$key]['read'] = true;
                }
                if(strstr($value['uc_permission'], 'w')) {
                    $permissions[$key]['write'] = true;
                }
                if(strstr($value['uc_permission'], 'd')) {
                    $permissions[$key]['delete'] = true;
                }
            }

            $this->tplVars['users']['val']['login'] = $user->getLogin();
            $this->tplVars['users']['val']['group'] = $user->getGroupId();
            $this->tplVars['users']['groupsList'] = $this->users->listGroups();
            $this->tplVars['users']['controllers'] = $contList['list'];
            $this->tplVars['users']['permissions'] = $permissions;
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit User');

            array_push($this->viewIncludes, 'users/usersEdit.tpl');
            array_push($this->viewIncludes, 'users/usersPermissions.tpl');
        } else {
            $this->_redirect('/users/list/');
        }
    }

    private function changeUser($userId) {

        Zend_Loader::loadClass('Zend_Filter_StripTags');
        $filter = new Zend_Filter_StripTags();
        $username = $filter->filter($this->_request->getPost('login'));
        $password = $filter->filter($this->_request->getPost('passwd'));
        $repPassword = $filter->filter($this->_request->getPost('rep_passwd'));
        $groupId = $filter->filter($this->_request->getPost('group'));


        if(!strlen($password)) {
            $this->users->changeUser($userId, NULL, NULL, $groupId);
        } else {
            if($password != $repPassword) {
                $this->tplVars['users']['err']['rep'] = true;
            } else {
                $this->users->changeUser($userId, NULL, md5($password), $groupId);
            }
        }
    }

    private function addUser() {
        Zend_Loader::loadClass('Zend_Filter_StripTags');
        $filter = new Zend_Filter_StripTags();
        $username = $filter->filter($this->_request->getPost('login'));
        $password = $filter->filter($this->_request->getPost('passwd'));
        $repPassword = $filter->filter($this->_request->getPost('rep_passwd'));
        $groupId  = $filter->filter($this->_request->getPost('group'));

        if(!strlen($username)) {
            $this->tplVars['users']['err']['login'] = true;
        }

        if(!isset($this->tplVars['users']['err']['login']) && $this->users->checkUserLogin($username)) {
            $this->tplVars['users']['err']['loginExist'] = true;
        }

        if(!isset($this->tplVars['users']['err']['loginExist']) && !strlen($password)) {
            $this->tplVars['users']['err']['passwd'] = true;
        }

        if(!isset($this->tplVars['users']['err']['loginExist']) &&
           !isset($this->tplVars['users']['err']['passwd']) &&
           $password != $repPassword) {
            $this->tplVars['users']['err']['rep'] = true;
        }

        if(!isset($this->tplVars['users']['err']['loginExist']) && !isset($this->tplVars['users']['err'])) {
            $this->users->addUser($username, md5($password), $groupId);
        } else {
            $this->tplVars['users']['val']['login'] = $username;
            $this->tplVars['users']['val']['group'] = $groupId;
        }
    }

    private function addPermission($userId) {
        $controllerId  = $this->_request->getPost('controller');
        $read  = $this->_request->getPost('read');
        $write  = $this->_request->getPost('write');
        $delete  = $this->_request->getPost('delete');

        $perms = (isset($read) ? 'r' : '').(isset($write) ? 'w' : '').(isset($delete) ? 'd' : '');

        $this->users->addUserPermissions($userId, $controllerId, $perms);
    }

    private function updatePermissions($userId) {

        $permissions = $this->users->listUserPermissions($userId);

        foreach($permissions AS $perm) {
            $perms = ($this->_request->getPost($perm['uc_id'].'_read') ? 'r' : '').
                     ($this->_request->getPost($perm['uc_id'].'_write') ? 'w' : '').
                     ($this->_request->getPost($perm['uc_id'].'_delete') ? 'd' : '');

            $this->users->changeUserPermissions($perm['uc_id'], $perms);
        }
    }

    public function delpermAction() {
        if($this->_hasParam('user')) {
            $userId = $this->_getParam('user');
            $contId = $this->_getParam('cont');

            $this->users->deleteUserPermissions($userId, $contId);

            $this->_redirect('/users/edit/id/'.$userId.'/');
        }
        $this->_redirect('/users/list/');
    }

    private function addGroup() {
        Zend_Loader::loadClass('Zend_Filter_StripTags');
        $filter = new Zend_Filter_StripTags();
        $groupname = $filter->filter($this->_request->getPost('group_name'));

        if(!strlen($groupname)) {
            $this->tplVars['users']['err']['groupName'] = true;
        }

        if(!isset($this->tplVars['users']['err']['groupName']) && $this->users->checkGroupName($groupname)) {
            $this->tplVars['users']['err']['groupNameExist'] = true;
        }

        if(!isset($this->tplVars['users']['err'])) {
            $this->users->addGroup($groupname);
        } else {
            $this->tplVars['users']['val']['groupName'] = $groupname;
        }
    }

    public function gdeleteAction() {

        if($this->_hasParam('id')) {
            $this->users->deleteGroup($this->_getParam('id'));
            $this->users->deleteGroupPermissions($this->_getParam('id'));
            $this->users->deleteUserByGroup($this->_getParam('id'));
        }
        $this->_redirect('/users/list/');
    }

    public function geditAction() {
        if($this->_hasParam('id')) {

            $groupId = $this->_getParam('id');

            if ($this->_request->isPost()) {

                if($this->_request->getPost('addPerm')) {
                    $this->addGroupPermissions($groupId);
                }

                if($this->_request->getPost('updatePerms')) {
                    $this->updateGroupPermissions($groupId);
                }
            }

            $controllers = new ControllersHandler();
            $contList = $controllers->getControllersList($this->getSiteId());
            $permissions = $this->users->listGroupPermissions($groupId);

            foreach($permissions AS $perm) {
                if(isset($contList['list'][$perm['gc_controller_id']])) {
                    unset($contList['list'][$perm['gc_controller_id']]);
                }
            }

            foreach($permissions AS $key => $value) {
                if(strstr($value['gc_permission'], 'r')) {
                    $permissions[$key]['read'] = true;
                }
                if(strstr($value['gc_permission'], 'w')) {
                    $permissions[$key]['write'] = true;
                }
                if(strstr($value['gc_permission'], 'd')) {
                    $permissions[$key]['delete'] = true;
                }
            }

            $this->tplVars['groups']['groupsList'] = $this->users->listGroups();
            $this->tplVars['groups']['controllers'] = $contList['list'];
            $this->tplVars['groups']['permissions'] = $permissions;
            $this->tplVars['header']['actions']['names'][] = array('name' => 'gedit', 'menu_name' => 'Edit Group');

            array_push($this->viewIncludes, 'users/groupsPermissions.tpl');
        } else {
            $this->_redirect('/users/list/');
        }
    }

    private function addGroupPermissions($groupId) {
        $controllerId  = $this->_request->getPost('controller');
        $read  = $this->_request->getPost('read');
        $write  = $this->_request->getPost('write');
        $delete  = $this->_request->getPost('delete');

        $perms = (isset($read) ? 'r' : '').(isset($write) ? 'w' : '').(isset($delete) ? 'd' : '');

        $this->users->addGroupPermissions($groupId, $controllerId, $perms);
    }

    private function updateGroupPermissions($groupId) {
        $permissions = $this->users->listGroupPermissions($groupId);

        foreach($permissions AS $perm) {
            $perms = ($this->_request->getPost($perm['gc_id'].'_read') ? 'r' : '').
                     ($this->_request->getPost($perm['gc_id'].'_write') ? 'w' : '').
                     ($this->_request->getPost($perm['gc_id'].'_delete') ? 'd' : '');

            $this->users->changeGroupPermissions($perm['gc_id'], $perms);
        }
    }

    public function gdelpermAction() {
        if($this->_hasParam('group')) {
            $groupId = $this->_getParam('group');
            $contId = $this->_getParam('cont');

            $this->users->deleteGroupPermissions($groupId, $contId);

            $this->_redirect('/users/gedit/id/'.$groupId.'/');
        }
        $this->_redirect('/users/list/');
    }
}

?>