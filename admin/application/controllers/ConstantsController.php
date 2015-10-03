<?php
include_once('pages.php');

class ConstantsController extends MainApplicationController {

    #PRIVATE VARIABLES
    private $constants;
    private $existConstants;
    private $siteDbAdapter;

    public function init() {
        parent::init();

        $dbAdapter = Zend_Registry::get('dbAdapter');

        $select = $dbAdapter->select();
        $select->from('sites', array('s_dbname'));
        $select->where('s_id = ?', $this->getSiteId());
        $dbname = $dbAdapter->fetchOne($select->__toString());

        $config = Zend_Registry::get('config');
        $params = $config->db->config->toArray();
        $params['dbname'] = $dbname;

        $this->siteDbAdapter = Zend_Db::factory($config->db->adapter, $params);

        $this->constants = array(
            'realDomain',
            'cachedDomain',
            'siteClosed',
            'maintainPage',
            'addrType',
            'trailingSlash',
            'addrMaxLength',
            '404exist',
            '404page',
            'un404page',
            'isCacheable',
            'langsDb',
            'adminDb',
            'loginPage',
            'siteId',
			'root_en',
			'root_fr',
			'root_de',
			'root_es',
			'root_jp',
			'root_ru',
			'locales_as_subdomen',
			'use_min',
			);
        $this->loadExistConstants();

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Constants'),
            array('name' => 'add', 'menu_name' => 'Add Constant')
        );
    }

    public function __destruct() {
        $this->display();

        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/constants/list/');
    }

    public function listAction() {

        $pages = new Pages($this->getSiteId());

        foreach($this->existConstants AS $key => $const) {
            if($const['c_name'] == 'un404page' ||
               $const['c_name'] == 'loginPage') {
                $page = $pages->getPage($const['c_value']);
                $this->existConstants[$key]['c_value'] = $page->address['uri_address'];
            }
        }

        $this->tplVars['consts']['consts'] = $this->existConstants;

        array_push($this->tplVars['page_css'], 'pages.css');
        array_push($this->viewIncludes, 'constants/listConstants.tpl');
    }

    public function addAction() {

        if($this->_request->isPost()) {
            $name = $this->_request->getPost('name');
            $val = $this->_request->getPost('value');

            $row = array(
                'c_name' => $name,
                'c_value' => $val
            );

            $this->siteDbAdapter->insert('const', $row);
            $this->_redirect('/constants/list/');
        }

        $existConstants = array();
        foreach($this->existConstants AS $const) {
            $existConstants[] = $const['c_name'];
        }

        $this->tplVars['consts']['consts'] = array_diff($this->constants, $existConstants);
        array_push($this->viewIncludes, 'constants/addConstant.tpl');
    }

    public function editAction() {
        if($this->_hasParam('id')) {
            $constId = $this->_getParam('id');

            if($this->_request->isPost()) {
                $val = $this->_request->getPost('value');
                $this->siteDbAdapter->update('const', array('c_value' => trim($val)), $this->siteDbAdapter->quoteInto('c_id = ?', $constId));
                $this->_redirect('/constants/list/');
            }

            if($this->existConstants[$constId]['c_name'] == 'un404page' ||
               $this->existConstants[$constId]['c_name'] == 'loginPage') {
                $pages = new Pages($this->getSiteId());
                $pagesList = $pages->getPagesList();
                $this->tplVars['consts']['val']['pages'] = $pagesList;
            }

            $this->tplVars['consts']['val']['value'] = $this->existConstants[$constId]['c_value'];
            $this->tplVars['consts']['val']['name'] = $this->existConstants[$constId]['c_name'];
            array_push($this->tplVars['header']['actions']['names'], array('name' => 'edit', 'menu_name' => 'Edit Constant'));
            array_push($this->viewIncludes, 'constants/editConstant.tpl');
        }
    }

    public function deleteAction() {
        if($this->_hasParam('id')) {
            $constId = $this->_getParam('id');
            $this->siteDbAdapter->delete('const', $this->siteDbAdapter->quoteInto('c_id = ?', $constId));
            $this->_redirect('/constants/list/');
        }
    }

    private function loadExistConstants() {
        $select = $this->siteDbAdapter->select();
        $select->from('const', array('c_id',
                                     'c_name',
                                     'c_value',
                                     'c_comment',
                                     'c_parent'));
        $res = $this->siteDbAdapter->fetchAll($select->__toString());

        foreach($res AS $row) {
            $this->existConstants[$row['c_id']] = $row;
        }
    }
}

?>