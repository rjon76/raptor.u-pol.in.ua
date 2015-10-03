<?php
/*
 ����� ��� ���������� ������ ������
*/
class UsersHandler {

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
     ������� �����
    */
    public function addUser($login, $passwd, $groupId) {
        $userRow = array(
            'u_login' => $login,
            'u_passwd' => $passwd,
            'u_group_id' => $groupId);

        $this->dbAdapter->insert('users', $userRow);
        return $this->dbAdapter->lastInsertId();
    }

    /*
     �������� ������ �����
    */
    public function changeUser($userId, $login, $passwd, $groupId) {
        $set = array('u_group_id' => $groupId);

        if(isset($passwd)) {
            $set['u_passwd'] = $passwd;
        }

        if(isset($login)) {
            $set['u_login'] = $login;
        }

        $where = $this->dbAdapter->quoteInto('u_id = ?', $userId);
        $this->dbAdapter->update('users', $set, $where);
    }

    /*
     ������� �����
    */
    public function deleteUser($userId) {
        $where = $this->dbAdapter->quoteInto('u_id = ?', $userId);
        $this->dbAdapter->delete('users', $where);

        $this->deleteUserPermissions($userId);
    }

    /*
     ������� �����
    */
    public function deleteUserByGroup($groupId) {
        $where = $this->dbAdapter->quoteInto('u_group_id = ?', $groupId);
        $this->dbAdapter->delete('users', $where);

        $this->deleteUserPermissions($userId);
    }

    /*
     �������� ������ ������ ������ ����������� �� �������
    */
    public function listUsers($groupId = NULL) {
        $select = $this->dbAdapter->select();
        $select->from('users', array('u_id', 'u_login', 'u_group_id'));
        $select->joinLeft('groups', 'g_id = u_group_id', 'g_name');

        if(isset($groupId)) {
            $select->where('u_group_id= ?', $groupId);
        }

        $result = $this->dbAdapter->fetchAll($select->__toString());

        $users = array();
        foreach($result AS $row) {
            $users['groups'][$row['u_group_id']] = $row['g_name'];
            $users['users'][$row['u_group_id']][$row['u_id']] = $row['u_login'];
        }

        return $users;
    }

    /*
     �������� ����� ������� ����� ����������� � �����������
    */
    public function changeUserPermissions($ucId, $perms) {
        $set = array('uc_permission' => $perms);
        $where = $this->dbAdapter->quoteInto('uc_id = ?', $ucId);
        $this->dbAdapter->update('users2controllers', $set, $where);
    }

    /*
     ������� ����� ������� ����� ����������� � �����������, ���� ����������
     �� ������ �� ��������� ����� ��� ���� �����������
    */
    public function deleteUserPermissions($userId, $controllerId = NULL) {
        $where[] = $this->dbAdapter->quoteInto('uc_user_id = ?', $userId);
        if(isset($controllerId)) {
            $where[] = $this->dbAdapter->quoteInto('uc_controller_id = ?', $controllerId);
        }
        $this->dbAdapter->delete('users2controllers', $where);
    }

    /*
     �������� ����� ������� ����� ����������� � �����������
    */
    public function addUserPermissions($userId, $controllerId, $perms) {
        $permsRow = array(
            'uc_user_id' => $userId,
            'uc_controller_id' => $controllerId,
            'uc_permission' => $perms);

        $this->dbAdapter->insert('users2controllers', $permsRow);
    }

    /*
     �������� ������ ���� ����� �� �����������
    */
    public function listUserPermissions($userId) {
        $select = $this->dbAdapter->select();
        $select->from('users2controllers', array('uc_id', 'uc_user_id', 'uc_controller_id', 'uc_permission'));
        $select->joinLeft('controllers', 'c_id = uc_controller_id', 'c_menu_name');
        $select->where('uc_user_id = ?', $userId);
        return $this->dbAdapter->fetchAll($select->__toString());
    }

    /*
     �������� ������
    */
    public function addGroup($groupName) {
        $groupRow = array('g_name' => $groupName);

        $this->dbAdapter->insert('groups', $groupRow);
        return $this->dbAdapter->lastInsertId();
    }

    /*
     �������� ������ ������
    */
    public function changeGroup($groupId, $groupName) {
        $set = array('g_name' => $groupName);

        $where = $this->dbAdapter->quoteInto('g_id = ?', $groupId);
        $this->dbAdapter->update('groups', $set, $where);
    }

    /*
     ������� ������
    */
    public function deleteGroup($groupId) {
        $where = $this->dbAdapter->quoteInto('g_id = ?', $groupId);
        $this->dbAdapter->delete('groups', $where);

        $this->deleteGroupPermissions($groupId);
    }

    /*
     �������� ����� ������� ������ ����������� � �����������
    */
    public function addGroupPermissions($groupId, $controllerId, $perms) {
        $permsRow = array(
            'gc_group_id' => $groupId,
            'gc_controller_id' => $controllerId,
            'gc_permission' => $perms);

        $this->dbAdapter->insert('groups2controllers', $permsRow);

    }

    /*
     ������� ����� ������� ������ ����������� � �����������
    */
    public function changeGroupPermissions($gcId, $perms) {
        $set = array('gc_permission' => $perms);
        $where = $this->dbAdapter->quoteInto('gc_id = ?', $gcId);
        $this->dbAdapter->update('groups2controllers', $set, $where);
    }

    /*
     ������� ����� ������� ������ ����������� � �����������, ���� ���������� �� ������,
     �� ��������� ��� ����� ��� ���� ������������
    */
    public function deleteGroupPermissions($groupId, $controllerId = NULL) {
        $where[] = $this->dbAdapter->quoteInto('gc_group_id = ?', $groupId);
        if(isset($controllerId)) {
            $where[] = $this->dbAdapter->quoteInto('gc_controller_id = ?', $controllerId);
        }
        $this->dbAdapter->delete('groups2controllers', $where);
    }

    /*
     �������� ������ ���� ������ �� �����������
    */
    public function listGroupPermissions($groupId) {
        $select = $this->dbAdapter->select();
        $select->from('groups2controllers', array('gc_id', 'gc_group_id', 'gc_controller_id', 'gc_permission'));
        $select->joinLeft('controllers', 'c_id = gc_controller_id', 'c_menu_name');
        $select->where('gc_group_id = ?', $groupId);
        return $this->dbAdapter->fetchAll($select->__toString());
    }

    /*
     ��������� ���������� �� �����
    */
    public function checkUserLogin($login) {
        $select = $this->dbAdapter->select();
        $select->from('users', array('u_id'));
        $select->where('u_login = ?', $login);
        $result = $this->dbAdapter->fetchAll($select->__toString());

        if(empty($result)) {
            return false;
        }
        return true;
    }

    /*
     ��������� ���������� �� ��� ������
    */
    public function checkGroupName($groupName) {
        $select = $this->dbAdapter->select();
        $select->from('groups', array('g_id'));
        $select->where('g_name = ?', $groupName);
        $result = $this->dbAdapter->fetchAll($select->__toString());

        if(empty($result)) {
            return false;
        }
        return true;
    }

    /*
     �������� ������ ����
    */
    public function listGroups() {
        $select = $this->dbAdapter->select();
        $select->from('groups', array('g_id', 'g_name'));
        return $this->dbAdapter->fetchPairs($select->__toString());
    }
}

?>