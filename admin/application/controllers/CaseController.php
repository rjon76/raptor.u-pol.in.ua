<?php

include_once('pages.php');
include_once('case_study.php');
include_once('languages.php');


class CaseController extends MainApplicationController {

    #PIVATE VARIABLES
    private $cases;
    private $isAjax;
	private $langs;
	private $pages;
	private $langsList;
	private $pagesList;
	private $groupId;

    #PUBLIC VARIABLES

    public function init() {
        parent::init();

        $controllerId = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
        $writePerm = $this->user->checkWritePerm($controllerId);
        $deletePerm = $this->user->checkDelPerm($controllerId);
        $this->groupId = $this->user->getGroupId();

        $this->tplVars['case']['perms']['write'] = $writePerm;
        $this->tplVars['case']['perms']['delete'] = $deletePerm;
		$this->tplVars['case']['perms']['admin'] = ($this->groupId == 1) ? '1' : '0';
        

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'List Case study')
        );
		
        if($writePerm) {
            array_push($this->tplVars['header']['actions']['names'], array('name' => 'add', 'menu_name' => 'New case study'));
        }


        $this->model = new Case_study($this->getSiteId());
		$this->langs = new Languages($this->getSiteId());
		$this->pages = new Pages($this->getSiteId());
		$this->pagesList =  $this->pages->getPagesList();
		$this->langsList =  $this->langs->getLanguagesList();
        $this->isAjax = false;
		

    }

    public function __destruct() {
        if(!$this->isAjax) {
            $this->display();
        }

        $this->isAjax;
        $this->pages = NULL;
		$this->langs = NULL;
		$this->groupId = NULL;
        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/case/list/');
    }

    public function listAction() {
		$lang_id = ($this->_hasParam('lang') && (int)$this->_getParam('lang') > 0) ? (int)$this->_getParam('lang')  : '';
		if ($lang_id > 0){
			$case_list = $this->model->_fetchAll(array('where'=>array('cs_lang_id = ?'=>$lang_id)));
		}
		else{
			$case_list = $this->model->_fetchAll();		
		}
		
		$count = count($case_list);
		for($i = 0; $i < $count; $i++)
			{
				$case_list[$i]['lang'] = $this->langsList[$case_list[$i]['cs_lang_id']]['l_code'];
			}

			$this->tplVars['content']['lang'] = $lang_id;
			$this->tplVars['content']['langslist'] = $this->langsList;
			$this->tplVars['content']['cases'] = $case_list;
			array_push($this->viewIncludes, 'case/caseList.tpl');
	}
/*-----------------------------------*/ 
 	public function editAction()
	{
        if($this->_hasParam('id'))
		{
            $id = $this->_getParam('id');
			$lang = $this->_getParam('lang');
 			$case = $this->model->findByPk($id);
			
            if ($this->_request->isPost())
			{
				$case->setAttributes($this->_request->getPost());
				$cs_pages_not_view = ($this->_request->getPost('cs_pages_not_view')) ? implode(',',$this->_request->getPost('cs_pages_not_view')) : '';
				$case->setAttribute('cs_pages_not_view', $cs_pages_not_view);
				$cs_hidden = $this->_request->getPost('cs_hidden') ? 1 : 0;
				$case->setAttribute('cs_hidden', $cs_hidden);	
				$case->validate();
				if (!$case->hasErrors()){
						if ($case->_update()){
							$this->_redirect('/case/list/'.(($lang!=='') ? 'lang/'.$lang : ''));
						}
				}
			}
			$pages =  $this->pages->getPagesList();
			$pages_not_view = $case->get_pages_not_view();
			$pages_count = count($pages);
            for($i = 0; $i < $pages_count; $i++) {
                    if(in_array($pages[$i]['pg_id'], $pages_not_view))
                        $pages[$i]['selected'] = true;
            }

            $this->tplVars['content']['pages'] = $pages;
            $this->tplVars['content']['langs'] = $this->langsList;
            $this->tplVars['content']['val'] = $case->getAttributes();
            $this->tplVars['model'] = $case;
			$this->tplVars['header']['actions']['names'][] = array('name' => 'editcase', 'menu_name' => 'Edit case', 'params' => array('id' => $id));

            array_push($this->viewIncludes, 'case/caseEdit.tpl');
        }
		else{
			$this->_redirect('/case/list/');
		}
    }
/*------------------------------*/
	public function addAction() {
		$pages =  $this->pages->getPagesList();
        if($this->_request->isPost()) {
			$this->model->setAttributes($this->_request->getPost());
			$cs_pages_not_view = ($this->_request->getPost('cs_pages_not_view')) ? implode(',',$this->_request->getPost('cs_pages_not_view')) : '';
			$this->model->setAttribute('cs_pages_not_view', $cs_pages_not_view);
			$cs_hidden = $this->_request->getPost('cs_hidden') ? 1 : 0;
			$this->model->setAttribute('cs_hidden', $cs_hidden);	
			
			$this->model->validate();

			if (!$this->model->hasErrors()){
	            if ($id = $this->model->_insert()){
		           $this->_redirect('/case/list/');
				}
			}
        }
			$pages_not_view = $this->model->get_pages_not_view();
			$pages_count = count($pages);
            for($i = 0; $i < $pages_count; $i++) {
                    if(in_array($pages[$i]['pg_id'], $pages_not_view))
                        $pages[$i]['selected'] = true;
            }
		$this->tplVars['content']['pages'] = $pages;
        $this->tplVars['content']['langs'] = $this->langsList;
		$this->tplVars['model'] = $this->model;
		$this->tplVars['content']['val'] = $this->model->getAttributes();
        array_push($this->viewIncludes, 'case/caseAdd.tpl');
	}
/*------------------------------*/
	public function deleteAction() {
        if($this->_hasParam('id')) {
			$lang = $this->_getParam('lang');
            if ($this->model->deleteByPk($this->_getParam('id'))){
			}
            $this->_redirect('/case/list/'.(($lang!=='') ? 'lang/'.$lang : ''));
        }
    }
}

?>