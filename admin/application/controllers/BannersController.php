<?php
include_once('banners.php');
include_once('banners_item.php');
include_once('pages.php');
include_once('languages.php');


class BannersController extends MainApplicationController {

    #PIVATE VARIABLES
    private $isAjax;
    private $bannermodel;	
    private $banneritemmodel;	
	private $langs;
	private $pages;
	private $langsList;
	private $pagesList;
	
    #PUBLIC VARIABLES

    public function init() {
        parent::init();
        $this->isAjax = FALSE;

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Banners list'),
            array('name' => 'addbanner', 'menu_name' => 'Add new category')
        );
		$this->bannermodel = new Banners($this->getSiteId());
		$this->banneritemmodel = new Banners_item($this->getSiteId());		
		$this->langs = new Languages($this->getSiteId());
		$this->pages = new Pages($this->getSiteId());
		$this->pagesList =  $this->pages->getPagesList(NULL, array('pg_lang','pg_address'));
		$this->langsList =  $this->langs->getLanguagesList();
		
		
    }

    public function __destruct() {
        if(!$this->isAjax) {
           $this->display();
        }

        $this->isAjax = NULL;
        $this->bannermodel = NULL;
		$this->banneritemmodel = NULL;
        $this->pages = NULL;
		$this->langs = NULL;

        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/banners/list/');
    }

    public function listAction() {
        $this->tplVars['content']['banners'] = $this->bannermodel->_fetchAll();
        array_push($this->viewIncludes, 'banners/bannerList.tpl');
    }

    public function editbannerAction() {
        if($this->_hasParam('id')) {
            $id = $this->_getParam('id');
            $banner = $this->bannermodel->findByPk($id);

            if($this->_request->isPost()) {
				
                if($this->_request->getPost('updateBanner')) {
					$banner->setAttributes($this->_request->getPost());
					$banner->validate();
					if (!$banner->hasErrors()){
						 $banner->_update();
					}
    	           
                }

                if($this->_request->getPost('addItem')) {
					$level = 0;
                    $parent_id = $this->_request->getPost('bi_parent_id');
					if ($parent_id && $parent_id > 0)
					{
						$parent = new Banners_item($this->getSiteId());	
						$parent->findByPk($parent_id);
						$level = (int)$parent->getAttribute('bi_level')+1;
					}
						$this->banneritemmodel->setAttributes($this->_request->getPost());
						$this->banneritemmodel->setAttributes(array('bi_banner_id'=>$id, 'bi_level'=>$level));
						$bi_pages = ($this->_request->getPost('bi_pages')) ? implode(',',$this->_request->getPost('bi_pages')) : '';
						$this->banneritemmodel->setAttribute('bi_pages', $bi_pages);
						
						$this->banneritemmodel->validate();	
						if (!$this->banneritemmodel->hasErrors()){					
			            	if ($this->banneritemmodel->_insert()){
					           $this->_redirect('/banners/editbanner/id/'.$id);
							}
						}
                }
            }
			$pages = $this->pagesList;
			$pages_not_view = $this->banneritemmodel->get_pages();
			$pages_count = count($pages);
            for($i = 0; $i < $pages_count; $i++) {
                    if(in_array($pages[$i]['pg_id'], $pages_not_view))
                        $pages[$i]['selected'] = true;
            }

            $this->tplVars['content']['pages'] = $pages;
            $this->tplVars['content']['langs'] = $this->langsList;

            $this->tplVars['content']['val'] = $banner->getAttributes();
            $this->tplVars['content']['banner_item'] = $this->banneritemmodel->getAttributes();			
            $this->tplVars['model'] = $banner;			
            $this->tplVars['banner_item'] = $this->banneritemmodel;						
            $this->tplVars['content']['banner_items'] = $banner->getBannerItems();
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editbanner', 'menu_name' => 'Edit banner', 'params' => array('id' => $id));

            array_push($this->viewIncludes, 'banners/bannerEdit.tpl');
            array_push($this->viewIncludes, 'banners/bannerItemList.tpl');
            array_push($this->viewIncludes, 'banners/bannerItemAdd.tpl');

        } else {
            $this->_redirect('/banners/list/');
        }
    }

    public function addbannerAction() {

        if($this->_request->isPost()) {
			$this->bannermodel->setAttributes($this->_request->getPost());
			$this->bannermodel->validate();

			if (!$this->bannermodel->hasErrors()){
	            if ($id = $this->bannermodel->_insert()){
		            $this->_redirect('/banners/editbanner/id/'.$id);
				}
			}
        }

		$this->tplVars['model'] = $this->bannermodel;
		$this->tplVars['content']['val'] = $this->bannermodel->getAttributes();
        array_push($this->viewIncludes, 'banners/bannerAdd.tpl');
    }

    public function deletebannerAction() {
        if($this->_hasParam('id')) {
            if ($this->bannermodel->deleteByPk($this->_getParam('id'))){
				$this->banneritemmodel->deleteAll('bi_banner_id = '.$this->_getParam('id'));
			}
            $this->_redirect('/banners/list/');
        }
    }

    public function edititemAction() {
        if($this->_hasParam('id') && $this->_hasParam('miid')) {
            $mid= $this->_getParam('id');
            $miid = $this->_getParam('miid');
			$banner = $this->bannermodel->findByPk($mid);
			$banneritem = $this->banneritemmodel->findByPk($miid);
			
            if($this->_request->isPost()) {
                $parent_id = $this->_request->getPost('bi_parent_id');
				$level = 0;
				if ($parent_id && $parent_id > 0)
					{
						$parent = new Banners_item($this->getSiteId());	
						$parent->findByPk($parent_id);
						$level = (int)$parent->getAttribute('bi_level')+1;
					}
				$banneritem->setAttributes($this->_request->getPost());
				$bi_pages = ($this->_request->getPost('bi_pages')) ? implode(',',$this->_request->getPost('bi_pages')) : '';
				$banneritem->setAttribute('bi_pages', $bi_pages);
				$banneritem->setAttributes(array('bi_banner_id'=>$mid, 'bi_level'=>$level));
				
				if (!$this->_request->getPost('bi_hidden'))
					$banneritem->setAttribute('bi_hidden', '0');

				$banneritem->validate();	
				if (!$banneritem->hasErrors()){					
	   	            if ($banneritem->_update()){
		               //$this->_redirect('/banners/editbanner/id/'.$mid.'/');
                       $this->_redirect("/banners/edititem/id/$mid/miid/$miid/");
                       
					}
				}
            }
			$pages =  $this->pagesList;
			$pages_not_view = $banneritem->get_pages();
			$pages_count = count($pages);
            for($i = 0; $i < $pages_count; $i++) {
                    if(in_array($pages[$i]['pg_id'], $pages_not_view))
                        $pages[$i]['selected'] = true;
            }
			$this->tplVars['content']['pages'] = $pages;
	        $this->tplVars['content']['langs'] = $this->langsList;

            $this->tplVars['content']['banner_items'] = $banner->getBannerItems();
			$this->tplVars['content']['banner_item'] = $banneritem->getAttributes();
            $this->tplVars['banner_item'] = $banneritem;						
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editbanner', 'menu_name' => 'Edit banner', 'params' => array('id' => $mid));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edititem', 'menu_name' => 'Edit banner item', 'params' => array('id' => $mid, 'miid' => $miid));

            array_push($this->viewIncludes, 'banners/bannerItemEdit.tpl');
        } else {
            $this->_redirect('/banners/list/');
        }
    }

    public function deleteitemAction() {
        if($this->_hasParam('id') && $this->_hasParam('miid')) {
            $this->banneritemmodel->deleteByPk($this->_getParam('miid'));
			$this->_redirect('/banners/editbanner/id/'.$this->_getParam('id'));
        } 
		else
			$this->_redirect('/banners/list');
    }

}

?>