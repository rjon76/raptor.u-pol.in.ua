<?php
include_once('menu.php');
include_once('menu_item.php');
include_once('pages.php');
include_once('languages.php');


class MenuController extends MainApplicationController {

    #PIVATE VARIABLES
    private $isAjax;
    private $menumodel;	
    private $menuitemmodel;	
	private $langs;
	private $pages;
	private $langsList;
	private $pagesList;
	
    #PUBLIC VARIABLES

    public function init() {
        parent::init();
        $this->isAjax = FALSE;

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Menu list'),
            array('name' => 'addmenu', 'menu_name' => 'Add new menu')
        );
		$this->menumodel = new Menu($this->getSiteId());
		$this->menuitemmodel = new Menu_item($this->getSiteId());		
		$this->langs = new Languages($this->getSiteId());
		$this->pages = new Pages($this->getSiteId());
		$this->pagesList =  $this->pages->getPagesList();
		$this->langsList =  $this->langs->getLanguagesList();
		
		
    }

    public function __destruct() {
        if(!$this->isAjax) {
           $this->display();
        }

        $this->isAjax = NULL;
        $this->menumodel = NULL;
		$this->menuitemmodel = NULL;
        $this->pages = NULL;
		$this->langs = NULL;

        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/menu/list/');
    }

    public function listAction() {
        $this->tplVars['content']['menus'] = $this->menumodel->_fetchAll();
        array_push($this->viewIncludes, 'menu/menuList.tpl');
    }

    public function editmenuAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $menu = $this->menumodel->findByPk($id);

            if($this->_request->isPost()) {
				
                if($this->_request->getPost('updateMenu')) {
					$menu->setAttributes($this->_request->getPost());
					$menu->validate();
					if (!$menu->hasErrors()){
						 $menu->_update();
					}
    	           
                }

                if($this->_request->getPost('addItem')) {
					$level = 0;
                    $parent_id = $this->_request->getPost('mi_parent_id');
					if ($parent_id && $parent_id > 0)
					{
						$parent = new Menu_item($this->getSiteId());	
						$parent->findByPk($parent_id);
						$level = (int)$parent->getAttribute('mi_level')+1;
					}
						$this->menuitemmodel->setAttributes($this->_request->getPost());
						$this->menuitemmodel->setAttributes(array('mi_menu_id'=>$id, 'mi_level'=>$level));
						$mi_pages_not_view = ($this->_request->getPost('mi_pages_not_view')) ? implode(',',$this->_request->getPost('mi_pages_not_view')) : '';
						$this->menuitemmodel->setAttribute('mi_pages_not_view', $mi_pages_not_view);
						
						$this->menuitemmodel->validate();	
						if (!$this->menuitemmodel->hasErrors()){					
			            	if ($this->menuitemmodel->_insert()){
					           $this->_redirect('/menu/editmenu/id/'.$id);
							}
						}
                }
            }
			$pages = $this->pagesList;
			$pages_not_view = $this->menuitemmodel->get_pages_not_view();
			$pages_count = count($pages);
            for($i = 0; $i < $pages_count; $i++) {
                    if(in_array($pages[$i]['pg_id'], $pages_not_view))
                        $pages[$i]['selected'] = true;
            }

            $this->tplVars['content']['pages'] = $pages;
            $this->tplVars['content']['langs'] = $this->langsList;

            $this->tplVars['content']['val'] = $menu->getAttributes();
            $this->tplVars['content']['menu_item'] = $this->menuitemmodel->getAttributes();			
            $this->tplVars['model'] = $menu;			
            $this->tplVars['menu_item'] = $this->menuitemmodel;						
            $this->tplVars['content']['menu_items'] = $menu->getMenuItems();
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editmenu', 'menu_name' => 'Edit menu', 'params' => array('id' => $id));

            array_push($this->viewIncludes, 'menu/menuEdit.tpl');
            array_push($this->viewIncludes, 'menu/menuItemList.tpl');
            array_push($this->viewIncludes, 'menu/menuItemAdd.tpl');

        } else {
            $this->_redirect('/menu/list/');
        }
    }

    public function addmenuAction() {

        if($this->_request->isPost()) {
			$this->menumodel->setAttributes($this->_request->getPost());
			$this->menumodel->validate();

			if (!$this->menumodel->hasErrors()){
	            if ($id = $this->menumodel->_insert()){
		            $this->_redirect('/menu/editmenu/id/'.$id);
				}
			}
        }

		$this->tplVars['model'] = $this->menumodel;
		$this->tplVars['content']['val'] = $this->menumodel->getAttributes();
        array_push($this->viewIncludes, 'menu/menuAdd.tpl');
    }

    public function deletemenuAction() {
        if($this->_hasParam('id')) {
            if ($this->menumodel->deleteByPk($this->_getParam('id'))){
				$this->menuitemmodel->deleteAll('mi_menu_id = '.$this->_getParam('id'));
			}
            $this->_redirect('/menu/list/');
        }
    }

    public function edititemAction() {
        if($this->_hasParam('id') && $this->_hasParam('miid')) {
            $mid= $this->_getParam('id');
            $miid = $this->_getParam('miid');
			$menu = $this->menumodel->findByPk($mid);
			$menuitem = $this->menuitemmodel->findByPk($miid);
			
            if($this->_request->isPost()) {
                $parent_id = $this->_request->getPost('mi_parent_id');
				$level = 0;
				if ($parent_id && $parent_id > 0)
					{
						$parent = new Menu_item($this->getSiteId());	
						$parent->findByPk($parent_id);
						$level = (int)$parent->getAttribute('mi_level')+1;
					}
				$menuitem->setAttributes($this->_request->getPost());
				$mi_pages_not_view = ($this->_request->getPost('mi_pages_not_view')) ? implode(',',$this->_request->getPost('mi_pages_not_view')) : '';
				$menuitem->setAttribute('mi_pages_not_view', $mi_pages_not_view);
				$menuitem->setAttributes(array('mi_menu_id'=>$mid, 'mi_level'=>$level));
				
				if (!$this->_request->getPost('mi_hidden'))
					$menuitem->setAttribute('mi_hidden', '0');

				$menuitem->validate();	
				if (!$menuitem->hasErrors()){					
	   	            if ($menuitem->_update()){
		               $this->_redirect('/menu/editmenu/id/'.$mid.'/');
					}
				}
            }
			$pages =  $this->pagesList;
			$pages_not_view = $menuitem->get_pages_not_view();
			$pages_count = count($pages);
            for($i = 0; $i < $pages_count; $i++) {
                    if(in_array($pages[$i]['pg_id'], $pages_not_view))
                        $pages[$i]['selected'] = true;
            }
			$this->tplVars['content']['pages'] = $pages;
	        $this->tplVars['content']['langs'] = $this->langsList;

            $this->tplVars['content']['menu_items'] = $menu->getMenuItems();
			$this->tplVars['content']['menu_item'] = $menuitem->getAttributes();
            $this->tplVars['menu_item'] = $menuitem;						
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editmenu', 'menu_name' => 'Edit menu', 'params' => array('id' => $mid));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edititem', 'menu_name' => 'Edit menu item', 'params' => array('id' => $mid, 'miid' => $miid));

            array_push($this->viewIncludes, 'menu/menuItemEdit.tpl');
        } else {
            $this->_redirect('/menu/list/');
        }
    }

    public function deleteitemAction() {
        if($this->_hasParam('id') && $this->_hasParam('miid')) {
            $this->menuitemmodel->deleteByPk($this->_getParam('miid'));
			$this->_redirect('/menu/editmenu/id/'.$this->_getParam('id'));
        } 
		else
			$this->_redirect('/menu/list');
    }

}

?>