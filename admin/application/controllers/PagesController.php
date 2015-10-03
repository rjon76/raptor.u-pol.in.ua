<?php

include_once('page.php');
include_once('pages.php');
include_once('localString.php');
include_once('extensions.php');

class PagesController extends MainApplicationController {

    #PIVATE VARIABLES
    private $pages;
    private $isAjax;

    #PUBLIC VARIABLES

    public function init() {
        parent::init();

        $controllerId = $this->controllers->getControllerIdByName($this->getRequest()->getControllerName());
        $writePerm = $this->user->checkWritePerm($controllerId);
        $deletePerm = $this->user->checkDelPerm($controllerId);
        $groupId = $this->user->getGroupId();

        $this->tplVars['pages']['perms']['write'] = $writePerm;
        $this->tplVars['pages']['perms']['delete'] = $deletePerm;
        $this->tplVars['pages']['perms']['admin'] = '0';

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Pages list'),
            array('name' => 'faq', 'menu_name' => 'FAQ'),
        );

        if($groupId == 1) {
            array_push($this->tplVars['header']['actions']['names'], array('name' => 'add', 'menu_name' => 'Add new page'));
            array_push($this->tplVars['header']['actions']['names'], array('name' => 'clone', 'menu_name' => 'Cloner'));
			array_push($this->tplVars['header']['actions']['names'], array('name' => 'import', 'menu_name' => 'Import page'));
            $this->tplVars['pages']['perms']['admin'] = '1';
        }

        $this->pages = new Pages($this->getSiteId());
        $this->isAjax = false;

    }

    public function __destruct() {
        if(!$this->isAjax) {
            $this->display();
        }

        $this->isAjax;
        $this->pages = NULL;

        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/pages/list/');
    }

    /**
     * function for set site
     * added italiano, 12/01/2014
     */
    public function setsiteAction() {
        if($this->_hasParam('site_id') && $this->_getParam('site_id') != 0) {
            $this->setCookie('cur_site_id',$this->_getParam('site_id'));
            $this->_redirect('/pages/list/');
        }
    }
    
    /* italiano, 14/01/2014 */
    public function editpageAction() {
        if($this->_hasParam('site') && $this->_hasParam('page')) {
            
            if ($this->getSiteId() != $this->_getParam('site')){
                $this->setCookie('cur_site_id',$this->_getParam('site'));
            }
            
            $this->_redirect('/content/edit/id/'.$this->_getParam('page').'/'.($this->_hasParam('fs') ? '#fs'.$this->_getParam('fs') : ''));
        }
    }
    /* italiano, 14/01/2014 */
    public function editlstringAction() {
        if($this->_hasParam('site') && $this->_hasParam('search')) {
            
            if ($this->getSiteId() != $this->_getParam('site')){
                $this->setCookie('cur_site_id',$this->_getParam('site'));
            }

            $this->_redirect('/localstring/search/search/'.$this->_getParam('search').'/');
        }
    }
    /* italiano, 14/01/2014 */
    public function addlstringAction() {
        if($this->_hasParam('site') && $this->_hasParam('nick')) {
            
            if ($this->getSiteId() != $this->_getParam('site')){
                $this->setCookie('cur_site_id',$this->_getParam('site'));
            }
            
            $this->_redirect('/localstring/add/nick/'.$this->_getParam('nick').'/');
        }
    }
    /* italiano, 14/01/2014 */
    public function listlstringAction() {
        if($this->_hasParam('site')) {
        
            if ($this->getSiteId() != $this->_getParam('site')){
                $this->setCookie('cur_site_id',$this->_getParam('site'));
            }
            
            $this->_redirect('/localstring/list/search/'.$this->_getParam('search').'/');
        }
    }
    public function listAction() {

        if($this->_hasParam('lang') && $this->_getParam('lang') != 0) {
            $this->tplVars['pages']['lang'] = $this->_getParam('lang');
            $pagesList = $this->pages->getPagesList($this->_getParam('lang'));
        } else {
            $pagesList = $this->pages->getPagesList();
        }

        $localString = new LocalString($this->getSiteId());
        $langs = $localString->getLangs();



        for($i = 0; $i < count($pagesList); $i++) {
            $pagesList[$i]['pg_title'] = substr($pagesList[$i]['pg_title'], 0, 100).(strlen($pagesList[$i]['pg_title']) < 100 ? '' : '...');
            $pagesList[$i]['lang_code'] = $langs[$pagesList[$i]['pg_lang']]['code'];
            $pagesList[$i]['lang'] = strtoupper($langs[$pagesList[$i]['pg_lang']]['code']);
            $pagesList[$i]['cached'] =$this->pages->chekingCacheFile($pagesList[$i]['pg_address']);
            $pagesList[$i]['logs'] = $this->pages->getPageLogs($pagesList[$i]['pg_id'],true);
        }

        array_push($this->tplVars['page_css'], 'pages.css');
        $this->tplVars['pages']['list'] = $pagesList;
		$this->tplVars['langs'] = $langs;
        array_push($this->viewIncludes, 'pages/pagesList.tpl');
    }

    public function addAction() {

        if ($this->_request->isPost()) {
            $this->addPage();
        }

        $exts = new Extensions($this->getSiteId());
        $localString = new LocalString($this->getSiteId());
        $langs = $localString->getLangs();

        $extsList = $exts->getExtensions();
        $pagesList = $this->pages->getPagesList();

        if ($this->_request->isPost()) {
            if($ralativePagesIds = $this->_request->getPost('relative')) {
                for($i = 0; $i < count($pagesList); $i++) {
                    if(in_array($pagesList[$i]['pg_id'], $ralativePagesIds)) {
                        $pagesList[$i]['selected'] = true;
                    }
                }
            }

            if($extensionsIds = $this->_request->getPost('extensions')) {
                for($i = 0; $i < count($extsList); $i++) {
                    if(in_array($extsList[$i]['id'], $extensionsIds)) {
                        $extsList[$i]['selected'] = true;
                    }
                }
            }
        }

        $this->tplVars['page']['pages'] = $pagesList;
        $this->tplVars['page']['langs'] = $langs;
        $this->tplVars['page']['exts'] = $extsList;

        array_push($this->viewIncludes, 'pages/pageAdd.tpl');
    }

    public function editAction() {

        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');

            if ($this->_request->isPost()) {
                if ($this->editPage($pageId)){
					$this->setFlash('success', 'Update success');
				}
            }

            $page = $this->pages->getPage($pageId);
            $exts = new Extensions($this->getSiteId());
            $localString = new LocalString($this->getSiteId());
            $langs = $localString->getLangs();

            $pages = $this->pages->getPagesList( NULL, array('pg_lang', 'pg_address'));
            $exts = $exts->getExtensions();

            if($ralativePagesIds = $this->_request->isPost() ? $this->_request->getPost('relative') : $page->getRelativePageIds()) {
                for($i = 0; $i < count($pages); $i++) {
                    if(in_array($pages[$i]['pg_id'], $ralativePagesIds)) {
                        $pages[$i]['selected'] = true;
                    }
                }
            }

            if($extensionsIds = $this->_request->isPost() ? $this->_request->getPost('extensions') : $page->getExtensionsIds()) {
                for($i = 0; $i < count($exts); $i++) {
                    if(in_array($exts[$i]['id'], $extensionsIds)) {
                        $exts[$i]['selected'] = true;
                    }
                }
            }

            $this->tplVars['page']['pages']     = $pages;
            $this->tplVars['page']['langs']     = $langs;
            $this->tplVars['page']['exts']      = $exts;
            $this->tplVars['page']['options']   = $page->options;/* added 18.11.2014, italiano */
            $this->tplVars['page']['screen']    = $page->screen;/* added 08.01.2015, italiano */
            $this->tplVars['page']['page_id']   = $pageId;
            
            

            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit page', 'params' => array('id' => $pageId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'meta', 'menu_name' => 'Edit page meta', 'params' => array('id' => $pageId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'content', 'menu_name' => 'Edit page content', 'params' => array('id' => $pageId));
            
            if(!isset($this->tplVars['page']['err'])) {
                $this->tplVars['page']['val']['address']    = $page->address['uri_address'];
                $this->tplVars['page']['val']['title']      = $page->title;
                $this->tplVars['page']['val']['menu_title'] = $page->menuTitle;
                $this->tplVars['page']['val']['lang']       = $page->languageId;
                $this->tplVars['page']['val']['parent']     = $page->parentPageId;
                $this->tplVars['page']['val']['css']        = sizeof($page->getCssList()) ? implode(',', $page->getCssList()) : '';
                $this->tplVars['page']['val']['jscript']    = sizeof($page->getJSList()) ? implode(',', $page->getJSList()) : '';
                $this->tplVars['page']['val']['headers']    = sizeof($page->getHeaders()) ? implode(',', $page->getHeaders()) : '';				
                $this->tplVars['page']['val']['priority']   = $page->priority;
                $this->tplVars['page']['val']['cacheable']  = $page->isCacheable();
                $this->tplVars['page']['val']['hidden']     = $page->isHidden();
                $this->tplVars['page']['val']['indexed']    = $page->isIndexed;
                
            }

            array_push($this->viewIncludes, 'pages/pageEdit.tpl');
        }
    }

    private function addPage() {
        $address    = $this->_request->getPost('address');
        $title      = $this->_request->getPost('title');
        $menuTitle  = $this->_request->getPost('menu_title');
        $lang       = $this->_request->getPost('lang');
        $parent     = $this->_request->getPost('parent');
        $relative   = $this->_request->getPost('relative');
        $css        = $this->_request->getPost('css');
        $jscript    = $this->_request->getPost('jscript');
        $headers    = $this->_request->getPost('headers');		
        $priority   = $this->_request->getPost('priority');
        $cacheable  = $this->_request->getPost('cacheable');
        $hidden     = $this->_request->getPost('hidden');
        $indexed    = $this->_request->getPost('indexed');
        $extensions = $this->_request->getPost('extensions');
        $options    = $this->_request->getPost('options');/* added 18.11.2014, italiano */

        if($this->pages->checkPageAddress($address)) {
            $this->tplVars['page']['err']['address'] = true;

            $this->tplVars['page']['val']['address']    = $address;
            $this->tplVars['page']['val']['title']      = $title;
            $this->tplVars['page']['val']['menu_title'] = $menuTitle;
            $this->tplVars['page']['val']['lang']       = $lang;
            $this->tplVars['page']['val']['parent']     = $parent;
            $this->tplVars['page']['val']['css']        = $css;
            $this->tplVars['page']['val']['jscript']    = $jscript;
            $this->tplVars['page']['val']['priority']   = $priority;
            $this->tplVars['page']['val']['cacheable']  = $cacheable;
            $this->tplVars['page']['val']['hidden']     = $hidden;
            $this->tplVars['page']['val']['indexed']    = $indexed;
            $this->tplVars['page']['val']['headers']    = $headers;
			
        }

        $relative = isset($relative) ? serialize($relative) : '';
        $css = strlen($css) ? serialize(explode(',', $css)) : '';
        $jscript = strlen($jscript) ? serialize(explode(',', $jscript)) : '';
        $headers = strlen($headers) ? serialize(explode(',', $headers)) : '';		
        $cacheable = isset($cacheable) ? 1 : 0;
        $hidden = isset($hidden) ? 1 : 0;
        $indexed = isset($indexed) ? 1 : 0;
        $extensions = isset($extensions) ? serialize($extensions) : '';
        $options = isset($options) ? serialize($options = array_flip($options)) : '';/* added 18.11.2014, italiano */

        if(!isset($this->tplVars['page']['err'])) {
			$this->to_log();
            $this->pages->addPage($address, $title, $menuTitle, $lang, $parent, $relative, $css, $jscript, $priority, $cacheable, $hidden, $indexed, $extensions, $headers, $options/* added 18.11.2014, italiano */);
            $this->_redirect('/pages/list/');
        }
    }

    private function editPage($pageId) {
        $address    = $this->_request->getPost('address');
        $title      = $this->_request->getPost('title');
        $menuTitle  = $this->_request->getPost('menu_title');
        $lang       = $this->_request->getPost('lang');
        $parent     = $this->_request->getPost('parent');
        $relative   = $this->_request->getPost('relative');
        $css        = $this->_request->getPost('css');
        $jscript    = $this->_request->getPost('jscript');
        $headers    = $this->_request->getPost('headers');		
        $priority   = $this->_request->getPost('priority');
        $cacheable  = $this->_request->getPost('cacheable');
        $hidden     = $this->_request->getPost('hidden');
        $indexed    = $this->_request->getPost('indexed');
        $extensions = $this->_request->getPost('extensions');
        $options    = $this->_request->getPost('options');/* added 18.11.2014, italiano */

        if($this->pages->checkPageAddress($address, $pageId)) {
            $this->tplVars['page']['err']['address'] = true;

            $this->tplVars['page']['val']['address']    = $address;
            $this->tplVars['page']['val']['title']      = $title;
            $this->tplVars['page']['val']['menu_title'] = $menuTitle;
            $this->tplVars['page']['val']['lang']       = $lang;
            $this->tplVars['page']['val']['parent']     = $parent;
            $this->tplVars['page']['val']['css']        = $css;
            $this->tplVars['page']['val']['jscript']    = $jscript;
            $this->tplVars['page']['val']['priority']   = $priority;
            $this->tplVars['page']['val']['cacheable']  = $cacheable;
            $this->tplVars['page']['val']['hidden']     = $hidden;
            $this->tplVars['page']['val']['indexed']    = $indexed;
            $this->tplVars['page']['val']['headers']    = $headers;			
        }

        $relative = isset($relative) ? serialize($relative) : '';
        $css = strlen($css) ? serialize(explode(',', $css)) : '';
        $jscript = strlen($jscript) ? serialize(explode(',', $jscript)) : '';
        $headers = strlen($headers) ? serialize(explode(',', $headers)) : '';		
        $cacheable = isset($cacheable) ? 1 : 0;
        $hidden = isset($hidden) ? 1 : 0;
        $indexed = isset($indexed) ? 1 : 0;
        $extensions = isset($extensions) ? serialize($extensions) : '';
        $options = isset($options) ? serialize($options = array_flip($options)) : '';/* added 18.11.2014, italiano */

        if(!isset($this->tplVars['page']['err'])) {
			$this->to_log();
            $page = $this->pages->getPage($pageId);
            return $page->editPage($address, $title, $menuTitle, $lang, $parent, $relative, $css, $jscript, $priority, $cacheable, $hidden, $indexed, $extensions, $headers, $options/* added 18.11.2014, italiano */);
        }else{
			return false;	
		}
    }

    public function hiddenAction() {
        $this->isAjax = true;

        if($this->_hasParam('id')) {
            $page = $this->pages->getPage($this->_getParam('id'));
            if($page->isHidden()) {
                $page->setHidden(FALSE);
                echo 0;
            } else {
                $page->setHidden(TRUE);
                echo 1;
            }
        }
    }

    public function cacheableAction() {
        $this->isAjax = true;

        if($this->_hasParam('id')) {
            $page = $this->pages->getPage($this->_getParam('id'));
            if($page->isCacheable()) {
                $page->setCacheable(FALSE);
                echo 0;
            } else {
                $page->setCacheable(TRUE);
                echo 1;
            }
        }
    }

    public function instantcacheAction() {
        $this->isAjax = true;

        if($this->_hasParam('id')) {
            $page = $this->pages->getPage($this->_getParam('id'));
            if($page->checkReCaching()) {
                $page->setReCaching(FALSE);
                echo 0;
            } else {
                $page->setReCaching(TRUE);
                echo 1;
            }
        }
    }

    public function deleteAction() {
        $this->isAjax = true;

        if($this->_hasParam('id')) {
            $this->pages->deletePage($this->_getParam('id'));
        }
    }

    public function deleteselAction() {
        $this->isAjax = true;

        if ($this->_request->isPost()) {
            $pageIds = $this->_request->getPost('chx');
            $this->pages->deletePages($pageIds);
        }
    }
    public function logselAction() {
        $this->isAjax = true;

        if ($this->_request->isPost()) {
            $pageIds = $this->_request->getPost('chx');
            $this->pages->cleareLogsPages($pageIds);
        }
    }
    public function cacheselAction() {
        $this->isAjax = true;
        $result = 0;
        if ($this->_request->isPost()) {
            $pageIds = $this->_request->getPost('chx');
            $result = (int)$this->pages->cachePages($pageIds);
//            $result = $this->pages->cachePages($pageIds);
		}
        echo $result;
    }

    public function cacheclearAction() {
        $this->isAjax = true;
        $result = 0;
        if ($this->_request->isPost()) {
            $pageIds = $this->_request->getPost('chx');
            $result = (int)$this->pages->clearCachePages($pageIds);
//            $result = $this->pages->cachePages($pageIds);
		}
        echo $result;
    }
    
    public function metaAction() {
        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');

            if($this->_request->isPost()) {
                if($this->_request->getPost('addMeta')) {
                    $this->addMeta($pageId);
                }

                if($this->_request->getPost('editMeta')) {
                    $this->editMeta($pageId);
                }
            }

            $page = $this->pages->getPage($pageId);

            $this->tplVars['page']['metaList'] = $page->getMetas();
            $this->tplVars['page']['id'] = $pageId;

            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit page', 'params' => array('id' => $pageId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'meta', 'menu_name' => 'Edit page meta', 'params' => array('id' => $pageId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'content', 'menu_name' => 'Edit page content', 'params' => array('id' => $pageId));

            array_push($this->viewIncludes, 'pages/pageEditMeta.tpl');
            array_push($this->viewIncludes, 'pages/pageAddMeta.tpl');
        }
    }

    private function addMeta($pageId) {
		$this->to_log();
        $name = $this->_request->getPost('name');
        $content = $this->_request->getPost('content');
        $lang = $this->_request->getPost('lang');

        $page = $this->pages->getPage($pageId);
        $page->addMeta($name, $content, $lang);
    }

    private function editMeta($pageId) {
		$this->to_log();

        $page = $this->pages->getPage($pageId);
        $metas = $page->getMetas();

        foreach($metas AS $meta) {
            $page->editMeta($meta['id'],
                            $this->_request->getPost('name_'.$meta['id']),
                            $this->_request->getPost('content_'.$meta['id']),
                            $this->_request->getPost('lang_'.$meta['id']));
        }
    }

    public function deletemetaAction() {
        if($this->_hasParam('id') && $this->_hasParam('mid')) {
            $pageId = $this->_getParam('id');
            $metaId = $this->_getParam('mid');

            $page = $this->pages->getPage($pageId);
            $page->deleteMeta($metaId);

            $this->_redirect('/pages/meta/id/'.$pageId.'/');
        } else {
            $this->_redirect('/pages/list/');
        }
    }

    public function contentAction() {
        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');
            $this->_redirect('/content/edit/id/'.$pageId.'/');
        }
    }

    public function cloneAction_bkp() {

        $localString = new LocalString($this->getSiteId());
        $langs = $localString->getLangs();
        $pagesList = $this->pages->getPagesList(NULL, array('pg_lang', 'pg_address'));

        if($this->_request->isPost()) {
            $sourceId = $this->_request->getPost('source_id');
            $targetId = $this->_request->getPost('target_id');
            $clonePageData = $this->_request->getPost('page_data');
            $clonePageMeta = $this->_request->getPost('page_meta');

            if(strlen($sourceId) && strlen($targetId)) {
                $sourceId = intval($sourceId);
                $targetId = intval($targetId);

                if($sourceId == $targetId) {
                    $this->tplVars['cloner']['err']['equalIds'] = true;
                }

                if(!$this->pages->checkPageById($sourceId)) {
                    $this->tplVars['cloner']['err']['sourceNotExist'] = true;
                }

                if(!$this->pages->checkPageById($targetId)) {
                    $this->tplVars['cloner']['err']['targetNotExist'] = true;
                }
            } else {
                $sourceId = intval($this->_request->getPost('source'));
                $targetId = intval($this->_request->getPost('target'));

                if($sourceId == $targetId) {
                    $this->tplVars['cloner']['err']['equalIds'] = true;
                }
            }

            if(!isset($this->tplVars['cloner']['err'])) {
                $this->pages->clonePage($sourceId, $targetId, (isset($clonePageData) ? TRUE : FALSE),
                                                              (isset($clonePageMeta) ? TRUE : FALSE));
                $this->tplVars['cloner']['hasBeenCloned'] = true;
            }
        }

        $this->tplVars['cloner']['langs'] = $langs;
        $this->tplVars['cloner']['pagesList'] = $pagesList;
        array_push($this->viewIncludes, 'pages/cloner.tpl');
    }
    public function cloneAction() {

        $localString = new LocalString($this->getSiteId());
        $langs = $localString->getLangs();
        $pagesList = $this->pages->getPagesList();

        if($this->_request->isPost()) {
            $sourceId = $this->_request->getPost('source_id');
            $targetId = $this->_request->getPost('target_id');
            $clonePageData = $this->_request->getPost('page_data');
            $clonePageMeta = $this->_request->getPost('page_meta');

			
			
            if(strlen($sourceId) && strlen($targetId)) {
                $sourceId = intval($sourceId);
                $targetId = intval($targetId);

                if($sourceId == $targetId) {
                    $this->tplVars['cloner']['err']['equalIds'] = true;
                }

                if(!$this->pages->checkPageById($sourceId)) {
                    $this->tplVars['cloner']['err']['sourceNotExist'] = true;
                }

                if(!$this->pages->checkPageById($targetId)) {
                    $this->tplVars['cloner']['err']['targetNotExist'] = true;
                }
				$targetIds[0] = $targetId;
            } else {
                
				$sourceId = intval($this->_request->getPost('source'));
               // $targetId = intval($this->_request->getPost('target'));
				$targetIds = $this->_request->getPost('target');
				 if(!$targetIds) {
                    $this->tplVars['cloner']['err']['targetNotExist'] = true;
                }else{
					foreach ($targetIds as $targetId){
                		if($sourceId == intval($targetId)) {
                    		$this->tplVars['cloner']['err']['equalIds'] = true;
                		}
					}
				}
            }

            if(!isset($this->tplVars['cloner']['err'])) {
				foreach($targetIds as $targetId){
					$this->to_log();
                	$this->pages->clonePage($sourceId, $targetId, (isset($clonePageData) ? TRUE : FALSE),
                                                              (isset($clonePageMeta) ? TRUE : FALSE));
				}
                $this->tplVars['cloner']['hasBeenCloned'] = true;
				
            }
        }

        $this->tplVars['cloner']['langs'] = $langs;
        $this->tplVars['cloner']['pagesList'] = $pagesList;
        
        $this->tplVars['page_js'][] = 'jquery-1.8.3.js';
        $this->tplVars['page_js'][] = 'select2.min.js';
        $this->tplVars['page_css'][] = 'select2.css';
        

        array_push($this->viewIncludes, 'pages/cloner.tpl');
    }

    public function myexportAction() {
		$this->isAjax = true;
        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');
            $theme = $this->_hasParam('theme') ? trim($this->_getParam('theme')) : null;
			
            if (is_numeric($pageId) && $pageId>0)
            {            
    			$data = $this->pages->getPageData($pageId,$theme);
                
                //garbagecat76@gmail.com /22.08.2014
                $lstrings = $this->pages->getLstringsForImport($pageId,$data['blocks'],$data['blocks_data']);

                if (count($lstrings['exist']))
                    $data['lstrings'] = $lstrings['exist'];
                    
                if (count($lstrings['notexist']))
                $data['lstringsNotExist'] = $lstrings['notexist'];

                //end
		
                //header("Content-Type: text/xml");
    			header('Content-Description: File Transfer');
    			header('Content-Type: application/octet-stream');
    			header('Content-Disposition: attachment; filename='.strtr(trim($data['pages']['pg_address']),array('/'=>'_','.'=>'_') ).'.xml');
    			header('Content-Transfer-Encoding: binary');

    			echo $this-> _toXml($data);


            }			
		}
	}

	public function importcorrectAction() {
	
        
		$this->isAjax = true;
		$fixed = false;
		Zend_Loader::loadClass('Zend_Json');
		
		if ( $this->_request->getPost('load_file') && isset($_GET['b_id']) && $_GET['b_id'] ) {
		
			$b_id = $_GET['b_id'];
		
			$str = file_get_contents($this->_request->getPost('load_file'));
			$xml = simplexml_load_string($str);
			$json = json_encode($xml);
			$data = json_decode($json,TRUE);
			
			foreach ($data['blocks'] as $b) {
				if ($b['b_id'] == $b_id) {
					$block = $b;
					break;
				}
			}
			$fields= array();
			foreach ($data['blocks_fields'] as $field) {
				if ( $field['bf_block_id'] == $block['b_id'] )
					$fields[] = $field;
			}

			$fixed = $this->pages->importCreateBlock($block, $fields);
			
		} else if ( $this->_request->getPost('load_file') && isset($_GET['bf_id']) && $_GET['bf_id'] ) {
		
			$bf_id = $_GET['bf_id'];
		
			$str = file_get_contents($this->_request->getPost('load_file'));
			$xml = simplexml_load_string($str);
			$json = json_encode($xml);
			$data = json_decode($json,TRUE);
			
			foreach ($data['blocks_fields'] as $f) {
				if ($f['bf_id'] == $bf_id) { 
					$field = $f;
					break;
				}
			}
			foreach ($data['blocks'] as $b) {
				if ($b['b_id'] == $field['bf_block_id']) {
					$block = $b;
					break;
				}
			}			
			
			$fixed = $this->pages->importCreateField($block, $field);
			
		} else if ( $this->_request->getPost('load_file') && isset($_GET['ls_id']) && $_GET['ls_id'] && isset($_GET['default']) ) {
		
			$ls_id = (string)$_GET['ls_id'];
            $lang = (string)$_GET['default'];
		
			$str = file_get_contents($this->_request->getPost('load_file'));
			$xml = simplexml_load_string($str);
			$json = json_encode($xml);
			$data = json_decode($json,TRUE);

            $difference = array();//массив различий существующих lstrings в базе - получателе и в базе отправителе
            
    		foreach($data['lstrings'] as $key=>$lstring){

                if ($ls_id == $key)
                {
                    if (isset($lstring[$lang]))
                    {
                        $difference['lang'] = $lang;
                        $difference['value'] = $lstring[$lang];
                        $difference['nick'] = $key;
                    }
                        

                }

            }
                
            if (count($difference)){
               $fixed = $this->pages->updateLstrings($difference);
    		}
        }
	
		if($fixed) {
			echo Zend_Json::encode(TRUE);
		} else {
			echo Zend_Json::encode(FALSE);
		}
	
	}
    
	public function importAction() {
		
		$err = array();
		$file = false;
        $temp_dir = sys_get_temp_dir();
        
        if (substr($temp_dir, -1) != DIRECTORY_SEPARATOR){
            $temp_dir .= DIRECTORY_SEPARATOR;
        }
        
		$checkFiles = (bool)$this->_request->getPost('checkFiles');
        $checkLocales = (bool)$this->_request->getPost('checkLocales');
        $redirectToPage = (bool)$this->_request->getPost('redirectToPage');
  
		if ( $this->_request->getPost('loadpage') ) {
		
			if(is_uploaded_file($_FILES["import_file"]["tmp_name"])) {
				if ( $_FILES['import_file']['type'] == 'text/xml' ) {
					move_uploaded_file($_FILES["import_file"]["tmp_name"], $temp_dir.$_FILES["import_file"]["name"]);
					$file = $temp_dir.$_FILES["import_file"]["name"];
				} else {
					$err[] = 'Incorrect file format';
				}
			} else {
				$err[] = 'Error loading file';
			}
		}
		
		if ( $this->_request->getPost('load_file') ) {
			if ( file_exists( $this->_request->getPost('load_file') ) ) {
				$file = $this->_request->getPost('load_file');
			} else {
				$err[] = 'Something wrong! download file again.';
			}
		}

		if ($file) {
		
			$img = $this->myimportcheck_img($file);
			$err = $this->myimportcheck($file,$checkFiles);
            
            //garbagecat76@gmail.com /22.08.2014
            $lstrings = $this->myimportcheck_lstrings($file);
            //end
            
			$import = false;
			
			if( $this->_request->getPost('import_page') && !$err ){
				$import = $this->myimport($file,$checkLocales);
				$this->to_log($import);
                
                if($redirectToPage){
                    $this->_redirect("/content/edit/id/{$import['page_id']}/");
                }
                
				//$import = 'Test';
			}
			
			$this->tplVars['page']['check'] = $err;
			$this->tplVars['page']['img'] = $img;
			$this->tplVars['page']['import'] = $import;
			$this->tplVars['page']['file'] = $file;
            
            //garbagecat76@gmail.com /22.08.2014
            $this->tplVars['page']['lstrings'] = $lstrings;
            //end
            
			array_push($this->viewIncludes, 'pages/pageImport.tpl');
			
		} else {
		
			$this->tplVars['err'] = $err;
			array_push($this->viewIncludes, 'pages/pageImport_Upload.tpl');
			
		}
		
	}
	
	private function myimportcheck_img($xml_file) 
    {
			$img = array();
			$path = $this->pages->getSitePath();
			$str = file_get_contents($xml_file);
            
     		$xml = simplexml_load_string($str);
    		$json = json_encode($xml);
    		$data = json_decode($json,TRUE);
            
			preg_match_all("#/(images/[^\"<> ]*)#", $str, $matches );
            
            if (isset($matches[1]))
            {
    			foreach( $matches[1] as $im ) {
    				$im = $path.$im;
    				if ( !file_exists($im) )
    					$img[] = $im;
    			}
            }
            
            //garbagecat76@gmail.com /22.08.2014
            // search lstring in *.css files
    		$css = unserialize( $data['pages']['pg_css'] );
            
            if (!in_array('styles.css',$css))
            {
                $css[]  = 'styles.css';
            }
            
    		foreach($css as $file) {
    		  
    			$file = $path.'styles/'.$file;
    			if ( file_exists($file) ) 
                {

                    $str = file_get_contents($file);
        			preg_match_all("(images/.*\.(jpg|gif|png|jpeg|bmp))", $str, $matches);
                    
                    if (isset($matches[0]))
                    {
                        
                        $matches[0] = array_unique($matches[0]);
            			
                        /*
                        foreach( $matches[0] as $im ) {

                            $result = self::search_file($path.'images', $im);

                            if(!$result)
                            {
                                $img[] = $im;
                            }
            			}                     
                        */

            			foreach( $matches[0] as $im ) {
            				$im = $path.$im;
                            
            				if ( !file_exists($im) )
                            {
                                $img[] = $im;
                            }	
            			}  
                        
                    }
    			}
    		}
            //end
            
			unset($str,$matches);
			return $img;
	}
	
    /**
    * @author garbagecat76@gmail.com /22.08.2014
    * Checking lstrings for update difference
    * @source $xml_file
    * @return array
    */
	public function myimportcheck_lstrings($xml_file) 
    { 
        $lgs = $this->pages->getAllLstrings();

		$str = file_get_contents($xml_file);
		$xml = simplexml_load_string($str);
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);

        $difference = $noExist = $notExistSender = array();
        $callback = array();
        
        if (isset($data['lstrings']))
        {
    		foreach($data['lstrings'] as $key=>$lstring){
    
                    if (isset($lgs[$key]))
                    {
                        $i=0;
                        foreach($lstring as $keyItem=>$item)
                        {
                            if (isset($lgs[$key][$keyItem]) && isset($lstring[$keyItem]))
                            {
                                
                                if ($lgs[$key][$keyItem] != $lstring[$keyItem] )
                                {
                                    //array for update lstrings
                                    //$callback['difference'][$key][$i]['data'] = "<strong>difference {$keyItem} lstring \"$key\"</strong><br/>Sender: {$lstring[$keyItem]};<br/> Recipient: {$lgs[$key][$keyItem]}<br/><br/>";
                                    $callback['difference'][$key][$i]['data'] = array('lang'=>$keyItem,'nick'=>$key, 'sender'=>$lstring[$keyItem], 'recipient'=>$lgs[$key][$keyItem]);
                                    //$callback['difference'][$key][$i]['lang'] = $keyItem;
                                }
                            } 
                            $i++;
                        }
                    }
                    else
                    {
                        //array for insert lstrings
                        $callback['forInsert'][$key]['data'] = array('nick'=>$key, 'data'=>$lstring);
                    }
    		}
        }
                
        if (isset($data['lstringsNotExist']))
        {
    		foreach($data['lstringsNotExist'] as $key=>$lstring){
    
                    if (!isset($lgs[$key]) && !isset($callback['forInsert'][$key]))
                    {
                        $callback['notExist'][$key] = array('nick'=>$key);                        
                    }
    		}
        }
        
        return $callback;


	}

    //public function myimportcheckAction() {
	private function myimportcheck($xml_file, $checkFiles=true) {
	
		//$this->isAjax = true;//	$file = '/home/www/venginse_admin/export_page.xml';
		$errors = array();	
		
		$str = file_get_contents($xml_file);
		$xml = simplexml_load_string($str);
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);

		if ( $this->pages->checkPageAddress( $data['pages']['pg_address'] ) ) {
			$errors['pages']['pg_address'] = $data['pages']['pg_address'];
			//$errors[] = 'Page address "'.$data['pages']['pg_address'].'" already exists';
		}
		$path = $this->pages->getSitePath();
        
        if ($checkFiles){
    		$css = unserialize( $data['pages']['pg_css'] );
    		foreach($css as $file) {
    			$file = $path.'styles/'.$file;
    			if ( !file_exists($file) ) {
    				$errors['pages'][] = 'File "'.$file.'" does not exist';
    				//$errors[] = 'File "'.$file.'" does not exist';
    			}
    		}
    		$js = unserialize( $data['pages']['pg_jscript'] );
    		foreach($js as $file) {
    			$file = $path.'js/'.$file;
    			if ( !file_exists($file) ) {
    				$errors['pages'][] = 'File "'.$file.'" does not exist';
    				//$errors[] = 'File "'.$file.'" does not exist';
    			}
    		}
		}
        
		foreach($data['blocks_fields'] as $field){
			$fields[$field['bf_block_id']][$field['bf_name']] = $field;
		}
		$exist_block = $this->pages->getBlocksData();
		foreach ($data['blocks'] as $block) {
//			$file = $path.'templates/'.$block['b_file'];
			if ( !isset($exist_block[$block['b_file']]) ) {
				$errors['blocks'][$block['b_id']] = 'Block "'.$block['b_file'].'" does not exist';
				//$errors['_'.$block['b_id']] = 'Block "'.$block['b_file'].'" does not exist';
			} else if ( !file_exists($path.'templates/'.$block['b_file']) ) {
				$errors['blocks_file'][$block['b_id']] = 'Block file "'.$block['b_file'].'" does not exist';
				//$errors['_'.$block['b_id']] = 'Block file "'.$block['b_file'].'" does not exist';
			} else {
				foreach ($fields[$block['b_id']] as $field )
				{
					if ( isset( $exist_block[$block['b_file']]['field'][$field['bf_name']] ) ) {
						if ( $exist_block[$block['b_file']]['field'][$field['bf_name']]['bf_type']!= $field['bf_type']) {
							$errors['blocks_fields_type'][] = 'Field "'.$field['bf_name'].'" has an incorrect format in block "'.$block['b_file'].'". has "'.$exist_block[$block['b_file']]['field'][$field['bf_name']]['bf_type'].'" need "'.$field['bf_type'].'"';
							//$errors[] = 'Field "'.$field['bf_name'].'" has an incorrect format in block "'.$block['b_file'].'". has "'.$exist_block[$block['b_file']]['field'][$field['bf_name']]['bf_type'].'" need "'.$field['bf_type'].'"';
						}
					} else {
						$errors['blocks_fields'][$field['bf_id']] = 'Field "'.$field['bf_name'].'"('.$field['bf_type'].') does not exist in block "'.$block['b_file'].'"';
						//$errors[] = 'Field "'.$field['bf_name'].'"('.$field['bf_type'].') does not exist in block "'.$block['b_file'].'"';
					}
				}
			}
		}
		
/*        
        //@author garbagecat76@gmail.com /22.08.2014
        //check lstrings 
        $lgs = $this->pages->getAllLstrings();
		foreach($data['lstrings'] as $key=>$lstring){

                if (isset($lgs[$key]))
                {
                    foreach($lstring as $keyItem=>$item)
                    {
                        if ($lgs[$key][$keyItem] != $lstring[$keyItem] )
                        {
                            $errors['lstrings'][$key] = "<strong>difference lstring ($key)</strong><br/>Sender: {$lstring[$keyItem]};<br/> Recipient: {$lgs[$key][$keyItem]}<br/><br/>";
                        } 
                    }
                }
		}
        //end
 */       
        
        
		if ( count($errors)>0 ) {
			return $errors;
		} else {
			return false;
		}

	}

	
    //public function myimportAction() {
	private function myimport($file,$checkLocales=false) {
	
		//$this->isAjax = true;
		//$file = '/home/www/venginse_admin/export_page.xml';
		//echo $file;
		$str = file_get_contents($file);
		$xml = simplexml_load_string($str);
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);
		//var_dump($data);
		$result = '';
        
		foreach($data['pages'] as $key => $value) {
			$data['pages'][$key] = !is_array($value) ? $value : '';
		}
		
        //find relative pages and replace
        if($checkLocales)
        {
            $langs = $this->pages->getLangs();        
            $relativePages = $pg_relative = $relatives = array();
            
            if(count($data['relative_pages_alias']))
            {
                foreach($data['relative_pages_alias'] as $address){
                    
                    $page = null;
                    
                    if($page = $this->pages->checkPageAddressAndReturn($address)){
                        $relativePages[] = array('lang'=>$langs[$page['pg_lang']]['code'],'page_id'=>$page['pg_id'],'hidden'=>(((bool)$page['pg_hidden']) ? true : false), 'lang_name'=>ucfirst($langs[$page['pg_lang']]['name']),'href'=>$page['pg_address']);
                        $pg_relative[] = $page['pg_id']; 
                    }
                }
            }
        
            $data['pages']['pg_relative'] = serialize($pg_relative);    
        }
        else{
            $data['pages']['pg_relative'] = '';    
        }
        // end find relative pages and replace
        
		$pageId = $this->pages->importPage( $data['pages'] );
        $alias = $data['pages']['pg_address'];
		//return 'test';
		
		$this->pages->importMetas( $data['metas'], $pageId );
		
		$exist_block = $this->pages->getBlocksData();
		foreach( $data['blocks'] as $val ) {
			$blocks[$val['b_id']] = $val['b_file'];
		}
		//var_dump($blocks);
		foreach( $data['blocks_fields'] as $val ) {
			$fields [$val['bf_id']] = $val['bf_name'];
		}
		foreach( $data['blocks_data'] as $val ) {
			$blocks_data[$val['bd_bp_id']][$val['bd_field_id']] = $val;
		}

		
		foreach( $data['blocks2pages'] as $val ) {
			
			$bpData = array(
				'bp_block_id' => $exist_block[$blocks[$val['bp_block_id']]]['b_id'],
				'bp_page_id' => $pageId,
				'bp_parent' => $val['bp_parent'] ? $bp_parent[$val['bp_parent']] : '0' ,
				'bp_order' => $val['bp_order'],
                'bp_hidden' =>  $val['bp_hidden'],
			);//var_dump( $bpData );
			
			$sourceBlocksData = array();
			foreach ( $blocks_data[$val['bp_id']] as $bl ) {
				//var_dump( $fields[$bl['bd_field_id']] );
				//var_dump( $bl['bd_value'] );
				//var_dump( $exist_block[$blocks[$val['bp_block_id']]]['field'][$fields[$bl['bd_field_id']]]['bf_id'] );
				//var_dump( $bl['bd_field_id'] );
				$sourceBlocksData[] = array(
					'bd_field_id' =>  $exist_block [$blocks[$val['bp_block_id']]] ['field'] [$fields[$bl['bd_field_id']]] ['bf_id'],
					'bd_value' => !is_array($bl['bd_value']) ? $bl['bd_value'] : '',
                    'bd_hidden' => $bl['bd_hidden'],
				);
			}
			//var_dump($sourceBlocksData);

			$blokId = $this->pages->importBlock($bpData, $sourceBlocksData);
			$bp_parent[$val['bp_id']] = $blokId;

		}
        
        //@author garbagecat76@gmail.com /22.08.2014
        $lgs = $this->pages->getAllLstrings();
        $notExistSender = array();
		foreach($data['lstrings'] as $key=>$lstring){

                if (!isset($lgs[$key]))
                {
                    //$notExistSender[$key] = $lstring;
                    $lstring['nick']=$key;
                    $notExistSender[] = $lstring;
                }
		}
        
        if(count($notExistSender))
        {
            $result .= $this->pages->addLstringsNotExist($notExistSender);
        }
        //end
		
		//return array('import' => 'Page import. Page id: '.$pageId );

        
		return array(
            'page_id'=>$pageId,
            'data'=>$result,
            'alias'=>$alias,
            'relativePages'=>$relativePages,
        );

	}		


    private function _toXml($data, $rootNodeName = 'data', $xml=null)
    {
        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?><$rootNodeName />");
        }
        //цикл перебора массива 
        foreach($data as $key => $value) {
            // нельзя применять числовое название полей в XML
            if (is_numeric($key)) {
                // поэтому делаем их строковыми
                $key = "Node_". (string) $key;
            }
            // удаляем не латинские символы
            //$key = preg_replace('/[^a-z0-9]/i', '', $key);
            // если значение массива также является массивом то вызываем себя рекурсивно
            if (is_array($value)) {
                $node = $xml->addChild($key);
                // рекурсивный вызов
                $this->_toXml($value, $rootNodeName, $node);
            } else {
                // добавляем один узел
                //$value = htmlentities($value);
				$value = htmlspecialchars($value);
				//$value = base64_encode($value);
                $xml->addChild($key,$value);
            }
        }
        // возвратим обратно в виде строки  или просто XML-объект 
        return $xml->asXML();
    }
    
    /**
    * search file in dir
    * @author garbagecat76@gmail.com
    */
    public function search_file($folderName, $fileName){
    
        $dir = opendir($folderName);    
        $fileName = basename($fileName);
        
        // перебираем папку
        while (($file = readdir($dir)) !== false){
            if($file != "." && $file != ".."){ 
            
                if(is_file($folderName."/".$file))
                {
                    if($file == $fileName)
                    {
                        return $folderName."/".$file;
                    } 
                }
                else
                {
                    
                    if (is_dir($folderName."/".$file)) 
                    {
                        return self::search_file($folderName."/".$file, $fileName);
                    }
                }
            }
        }
        closedir($dir);

    }
    
    public function pagelogsAction() {
        
        $this->isAjax = true;

        if($this->_hasParam('pid')) {
            
            Zend_Loader::loadClass('Zend_Json');
            
            $pageId = $this->_getParam('pid');
            
            $rows = $this->pages->getPageLogs($pageId);
            
            if (count($rows)){
                $content = $this->pages->getPageLogsHtml($pageId,$rows);
            }
            else{
                $content = '';
            }
            
            
            echo $content;

        }
    }
    
    public function pagescreenAction() {
        
        $this->isAjax = true;

        if($this->_hasParam('pid')) {
            
            $pageId = $this->_getParam('pid');
            $type = $this->_hasParam('type') ? $this->_getParam('type') : 'refresh';
            
            switch($type){
                
                //get new screen for view
                case "refresh":
                    $image = $this->pages->save_screenshot($pageId,$this->getNCSiteHostname(),true,false);
                    break;
                    
                //update for table pages, field pg_screen & get new screen for view
                case "update":
                    $image = $this->pages->save_screenshot($pageId,$this->getNCSiteHostname(),true,true);
                    break;
            }
            

            echo $image;

        }
    }
    
    public function faqAction()
	{
		array_push($this->viewIncludes, 'pages/faq.tpl');
    }

}

?>