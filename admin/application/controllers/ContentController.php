<?php

include_once('pages.php');
include_once('blocks.php');
include_once('content.php');
include_once('localString.php');

class ContentController extends MainApplicationController {

    #PIVATE VARIABLES
    private $isAjax;
    private $blocks;
	private $pages;

    #PUBLIC VARIABLES

    public function init() {
        parent::init();
		$ControllerName = $this->getRequest()->getControllerName();
        $controllerId = $this->controllers->getControllerIdByName($ControllerName);
        $writePerm = $this->user->checkWritePerm($controllerId);
        $deletePerm = $this->user->checkDelPerm($controllerId);
        $groupId = $this->user->getGroupId();
		$SiteDir = $this->getSiteDir();
		$SiteHostname = $this->getNCSiteHostname();

		$adminPerm = ($groupId == 1) ? 1 : 0;

		$this->tplVars[$ControllerName]['perms']['write'] = $writePerm;
        $this->tplVars[$ControllerName]['perms']['delete'] = $deletePerm;
        $this->tplVars[$ControllerName]['perms']['admin'] = $adminPerm;
        $this->isAjax = FALSE;
        $this->blocks = new Blocks($this->getSiteId());

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'list', 'menu_name' => 'Pages list'),
            array('name' => 'blocks', 'menu_name' => 'Blocks'),
            array('name' => 'addblock', 'menu_name' => 'Add new block')
        );
    }

    public function __destruct() {
        if(!$this->isAjax) {
           $this->display();
        }

        $this->isAjax = NULL;
        $this->blocks = NULL;
        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/content/list/');
    }

    public function listAction() {
        $pages = new Pages($this->getSiteId());
        $localString = new LocalString($this->getSiteId());
        $langs = $localString->getLangs();

        if($this->_hasParam('lang') && $this->_getParam('lang') != 0) {
            $this->tplVars['content']['lang'] = $this->_getParam('lang');
            $pagesList = $pages->getPagesList($this->_getParam('lang'));
        } else {
            $pagesList = $pages->getPagesList();
        }

        for($i = 0; $i < count($pagesList); $i++) {
            $pagesList[$i]['lang_code'] = $langs[$pagesList[$i]['pg_lang']]['code'];
            $pagesList[$i]['lang'] = strtoupper($langs[$pagesList[$i]['pg_lang']]['code']);
        }

        $this->tplVars['content']['pagesList'] = $pagesList;

        array_push($this->viewIncludes, 'content/contentPagesList.tpl');
    }

    public function blocksAction() {

        $this->tplVars['content']['blocks'] = $this->blocks->getBlocksList();
        
        array_push($this->viewIncludes, 'content/contentBlocksList.tpl');
    }

    public function editblockAction_bkp() {
        if($this->_hasParam('id')) {
            $blockId = $this->_getParam('id');
            $block = $this->blocks->getBlock($blockId);

            if($this->_request->isPost()) {
                if($this->_request->getPost('updateBlock')) {
                    $name   = $this->_request->getPost('name');
                    $file   = $this->_request->getPost('file');
                    $parent = $this->_request->getPost('parent');
                    // >> added italiano
                    $text   = $this->_request->getPost('text');
                    $base64 = $this->_request->getPost('base64');
					// <<
					$this->to_log('updateBlock '.$name);

                    $this->blocks->editBlock($blockId, $name, $file, $parent, $text, $base64);
		            $this->_redirect('/content/editblock/id/'.$blockId);
//                    $this->_redirect('/content/blocks/');
                }

                if($this->_request->getPost('addField')) {
                    $name = $this->_request->getPost('name');
                    $default = $this->_request->getPost('default');
                    $type = $this->_request->getPost('type');
                    
					$this->to_log('addField '.$name);

                    if($type == 'A' && strlen($default)) {
                        $default = serialize(explode(',', $default));
                    }

                    $this->blocks->addBlockField($blockId, $name, $type, $default);
                }
            }

            $this->tplVars['content']['val']['name'] = $block['b_name'];
            $this->tplVars['content']['val']['file'] = $block['b_file'];
            $this->tplVars['content']['val']['text'] = $block['b_text'];
            $this->tplVars['content']['val']['base64'] = $block['b_base64'];
            $this->tplVars['content']['val']['parentId'] = $block['b_parent'];
            $this->tplVars['content']['blocks'] = $this->blocks->getBlocksList();
            $this->tplVars['content']['pages'] = $this->blocks->getPagesUsedBlocks($blockId);
            $this->tplVars['content']['fields'] = $this->blocks->getBlockFields($blockId);
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editblock', 'menu_name' => 'Edit block', 'params' => array('id' => $blockId));

            array_push($this->viewIncludes, 'content/contentBlockEdit.tpl');
            array_push($this->viewIncludes, 'content/contentBlockInPages.tpl');
            array_push($this->viewIncludes, 'content/contentBlockFieldsList.tpl');
            array_push($this->viewIncludes, 'content/contentBlockFieldAdd.tpl');

        } else {
            $this->_redirect('/content/blocks/');
        }
    }
    
    public function editblockAction() {
        if($this->_hasParam('id')) {
            
            $blockId = $this->_getParam('id');
            $block = $this->blocks->getBlock($blockId);

            if($this->_request->isPost()) {
                
                if($this->_request->getPost('updateBlock')) {
                    $name   = $this->_request->getPost('name');
                    $file   = $this->_request->getPost('file');
                    $parent = is_array($this->_request->getPost('parent')) ? serialize($this->_request->getPost('parent')) : '';

                    // >> added italiano
                    $text   = $this->_request->getPost('text');
                    $base64 = self::imageToBaze64($block['b_base64']);
					// <<
					
                    $this->to_log('updateBlock '.$name);

                    $this->blocks->editBlock($blockId, $name, $file, $parent, $text, $base64);
		            $this->_redirect('/content/editblock/id/'.$blockId);
//                    $this->_redirect('/content/blocks/');
                }

                if($this->_request->getPost('addField')) {
                    $name = $this->_request->getPost('name');
                    $default = $this->_request->getPost('default');
                    $type = $this->_request->getPost('type');
                    
					$this->to_log('addField '.$name);

                    if($type == 'A' && strlen($default)) {
                        $default = htmlentities($default);
                        //$default = serialize(explode(',', $default));
                    }

                    $this->blocks->addBlockField($blockId, $name, $type, $default);
                }
            }

            $this->tplVars['content']['val']['name'] = $block['b_name'];
            $this->tplVars['content']['val']['file'] = $block['b_file'];
            $this->tplVars['content']['val']['text'] = $block['b_text'];
            $this->tplVars['content']['val']['base64'] = $block['b_base64'];
            $this->tplVars['content']['val']['parent'] = unserialize($block['b_parent']);
            $this->tplVars['content']['blocks'] = $this->blocks->getBlocksList();
            $this->tplVars['content']['pages'] = $this->blocks->getPagesUsedBlocks($blockId);
            $this->tplVars['content']['fields'] = $this->blocks->getBlockFields($blockId);
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editblock', 'menu_name' => 'Edit block', 'params' => array('id' => $blockId));

            array_push($this->viewIncludes, 'content/contentBlockEdit.tpl');
            array_push($this->viewIncludes, 'content/contentBlockInPages.tpl');
            array_push($this->viewIncludes, 'content/contentBlockFieldsList.tpl');
            array_push($this->viewIncludes, 'content/contentBlockFieldAdd.tpl');

        } else {
            $this->_redirect('/content/blocks/');
        }
    }
    public function addblockAction_bkp() {

        if($this->_request->isPost()) {
            $name   = $this->_request->getPost('name');
            $file   = $this->_request->getPost('file');
            $parent = $this->_request->getPost('parent');
            // >> added italiano
            $text   = $this->_request->getPost('text');
            $base64 = $this->_request->getPost('base64');
            // <<

            if ($id = $this->blocks->addBlock($name, $file, $parent,$text,$base64)) {
				$this->to_log('addblock '.$id);
	            $this->_redirect('/content/editblock/id/'.$id);
			} else
	            $this->_redirect('/content/blocks/');
        }

        $this->tplVars['content']['blocks'] = $this->blocks->getBlocksList();
        array_push($this->viewIncludes, 'content/contentBlockAdd.tpl');
    }
    public function addblockAction() {

        if($this->_request->isPost()) {
            $name   = $this->_request->getPost('name');
            $file   = $this->_request->getPost('file');
            $parent = is_array($this->_request->getPost('parent')) ? serialize($this->_request->getPost('parent')) : '';
            
            // >> added italiano
            $text   = $this->_request->getPost('text');
            $base64 = self::imageToBaze64();
            // <<

            if ($id = $this->blocks->addBlock($name, $file, $parent,$text,$base64)) {
				$this->to_log('addblock '.$id);
	            $this->_redirect('/content/editblock/id/'.$id);
			} else
	            $this->_redirect('/content/blocks/');
        }

        $this->tplVars['content']['blocks'] = $this->blocks->getBlocksList();
        array_push($this->viewIncludes, 'content/contentBlockAdd.tpl');
    }
    public function deleteblockAction() {
        if($this->_hasParam('id')) {
            $this->blocks->deleteBlock($this->_getParam('id'));
            $this->_redirect('/content/blocks/');
        }
    }
    
   public function cloneblockAction() {
        if($this->_hasParam('id')) {
            $this->blocks->cloneBlock($this->_getParam('id'));
            $this->_redirect('/content/blocks/');
        }
    }

    public function editfieldAction() {
        if($this->_hasParam('bid') && $this->_hasParam('fid')) {
            $blockId = $this->_getParam('bid');
            $fieldId = $this->_getParam('fid');

            if($this->_request->isPost()) {
                $name = $this->_request->getPost('name');
                $default = $this->_request->getPost('default');
                $type = $this->_request->getPost('type');
				
				$this->to_log('editfield '.$id);
                $this->blocks->editBlockField($fieldId, $name, $type, $default);
                $this->_redirect('/content/editblock/id/'.$blockId.'/');
            }

            $field = $this->blocks->getBlockField($fieldId);

            $this->tplVars['content']['val']['name'] = $field['bf_name'];
            $this->tplVars['content']['val']['type'] = $field['bf_type'];
            $this->tplVars['content']['val']['default'] = $field['bf_default'];
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editblock', 'menu_name' => 'Edit block', 'params' => array('id' => $blockId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'editfield', 'menu_name' => 'Edit block field', 'params' => array('bid' => $blockId, 'fid' => $fieldId));

            array_push($this->viewIncludes, 'content/contentBlockFieldEdit.tpl');
        } else {
            $this->_redirect('/content/blocks/');
        }
    }

    public function deletefieldAction() {
        if($this->_hasParam('bid') && $this->_hasParam('fid')) {
            $blockId = $this->_getParam('bid');
            $fieldId = $this->_getParam('fid');

            $this->blocks->deleteBlockField($fieldId);
            $this->_redirect('/content/editblock/id/'.$blockId.'/');
        } else {
            $this->_redirect('/content/blocks/');
        }
    }

    public function editAction() {
        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');
            $content = new Content($this->getSiteId(), $pageId);
            $pages = new Pages($this->getSiteId());
            $page = $pages->getPage($pageId);

            $html = '';
            $this->renderBlocksHTML($content, $html);

//            $this->tplVars['page_js'][] = 'tiny_mce/tiny_mce.js';
            $this->tplVars['page_js'][] = 'jquery-1.8.3.js'; //italiano, 21/09/2015

			$this->tplVars['page_js'][] = 'colorbox.js';
            $this->tplVars['page_js'][] = 'jquery.scroll.js';
            $this->tplVars['page_js'][] = 'content.js';
            $this->tplVars['page_js'][] = 'jquery.filetree.js';
            $this->tplVars['page_js'][] = 'jquery.dimensions.js';
            $this->tplVars['page_js'][] = 'browser.js';

            $this->tplVars['page_js'][] = 'ui.core.min.js';
            $this->tplVars['page_js'][] = 'ui.draggable.min.js';
            $this->tplVars['page_js'][] = 'ui.droppable.min.js';
   
            $this->tplVars['page_css'][] = 'browser.css';
            $this->tplVars['page_css'][] = 'jquery_file_tree.css';
			$this->tplVars['page_css'][] = 'colorbox.css';

            $this->tplVars['content']['siteHostname'] = $this->getNCSiteHostname();
            $this->tplVars['content']['pageAddress'] = $page->address['uri_address'];

            $this->tplVars['content']['pageId'] = $pageId;
            $this->tplVars['content']['cached'] = $content->chekingCacheFile($page->address['uri_address']);
            $this->tplVars['content']['blocksList'] = $html;
	        $this->tplVars['header']['actions']['names'][] = array('name' => 'params', 'menu_name' => 'Edit page', 'params' => array('id' => $pageId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'meta', 'menu_name' => 'Edit page meta', 'params' => array('id' => $pageId));
            $this->tplVars['header']['actions']['names'][] = array('name' => 'edit', 'menu_name' => 'Edit content', 'params' => array('id' => $pageId));
			
            array_push($this->viewIncludes, 'content/blocks/blocksList.tpl');
        }
    }

    private function renderBlocksHTML($content, &$html, $parentBpId = 0) {
        $types = array(
            'S' => 'String',
            'A' => 'Array',
            'J' => 'Json Array',
            'G' => 'Global Extension',
            'I' => 'Image',
            'E' => 'Extension',
            'W' => 'Flash',
            'L' => 'List'
        );

        $contentBlocks = $content->getContent();
        foreach($contentBlocks AS $bpId => $block) 
        {
            if($block['bp_parent'] != $parentBpId) {
                continue;
            }
            
            if($block['bp_hidden'] == 1) {
                $disabled = ' disabled="disabled"';
                $css = " disabled";
            }
            else
            {
                $disabled='';
                $css = "";
            }

            $html .= '<fieldset class="block'.(($block["bp_order"] > 0) ? ' draggeble' : '').$css.'" value="'.$bpId.'" id="fs'.$bpId.'" style="z-index:'.(100+$block["bp_order"]).'"'.$disabled.'>
                <legend><a href="'.ADMIN_DIR.'/content/editblock/id/'.$block['b_id'].'/" class="" title="Edit block  - '.$block['b_name'].'">'.$block['b_name'].' ID - '.$block["bp_order"].'</a></legend>
                <table border="0" cellpadding="0" cellspacing="0" class="blockTable" id="tb'.$bpId.'">
                <tr>
                    <th width="140">'.($block['is_fields_exist'] ? 'Name' : '&nbsp;').'</th>
                    <th width="140">'.($block['is_fields_exist'] ? 'Type' : '&nbsp;').'</th>
                    <th colspan="2">
                        <span style="float:right;width:auto;" class="bctrl">
						<img src="'.ADMIN_DIR.'/images/addblock.gif" width="16" height="16" title="Add block" alt="Add block" class="pointer" onclick="addBlock(\'fs'.$bpId.'\', '.$block['bp_page_id'].', '.$bpId.', '.$block['b_id'].')" />'.($block['is_fields_exist'] ? '<img src="'.ADMIN_DIR.'/images/addfield.gif" width="16" height="16" title="Add field" alt="Add field" class="pointer" onclick="addBlockField(\'tb'.$bpId.'\', '.$block['bp_page_id'].', '.$bpId.')"/>' : '').'
						<img src="'.ADMIN_DIR.'/images/edit.gif" class="editblock pointer" width="16" height="16" title="Edit block" alt="Edit block" onclick="editBlock(\'fs'.$bpId.'\', '.$block['bp_page_id'].', '.$bpId.')" />
                        <img src="'.ADMIN_DIR.'/images/top.gif" width="16" height="16" title="Lift" alt="Lift" class="pointer" onclick="liftBlock('.$bpId.', '.$block['bp_page_id'].');" />
                        <img src="'.ADMIN_DIR.'/images/bottom.gif" width="16" height="16" title="Pull down" alt="Pull down" class="pointer" onclick="pullDownBlock('.$bpId.', '.$block['bp_page_id'].');" />
						<img src="'.ADMIN_DIR.'/images/copy.png" width="16" height="16" title="Copy block" alt="Copy block" class="pointer" onclick="copyBlock('.$bpId.', '.$block['bp_page_id'].');" />
						<img src="'.ADMIN_DIR.'/images/export.png" width="16" height="16" title="Export block" alt="Export block" class="pointer" onclick="window.location=\''.ADMIN_DIR.'/content/myexport/pid/'.$block['bp_page_id'].'/bpid/'.$bpId.'\';" />
						<img src="'.ADMIN_DIR.'/images/import.png" width="16" height="16" title="Import block" alt="Import block" class="pointer" onclick="exportColorbox('.$bpId.', '.$block['bp_page_id'].'); return false; window.location=\'/content/myimport/pid/'.$block['bp_page_id'].'/bpid/'.$bpId.'\';" />
                        <img src="'.ADMIN_DIR.'/images/delete.gif" width="16" height="16" title="Delete block ID - '.$bpId.'" alt="Delete block ID - '.$bpId.'" class="pointer" onclick="deleteBlock('.$bpId.', '.$block['bp_page_id'].');"/>
                        <img src="'.ADMIN_DIR.'/images/preview.png" class="pointer" width="16" height="16" title="Block info" alt="Block info" onclick="clickBlockInfo(\''.$block['b_id'].'\', \''.$block['bp_page_id'].'\')" /> 
                        <img src="'.ADMIN_DIR.'/images/disabled.png" class="pointer" width="16" height="16" title="Disabled" alt="Disabled" onclick="clickHiddenBlock(\''.$bpId.'\', \''.$block['bp_page_id'].'\')" />
                        </span>
                        '.($block['is_fields_exist'] ? 'Value' : '&nbsp;').'
                    </th>
                </tr>';

            if(isset($block['fields'])) {
            foreach($block['fields'] AS $field) {
                if($field['bf_type'] == 'A' || $field['bf_type'] == 'I' || $field['bf_type'] == 'W'  || $field['bf_type'] == 'J' || $field['bf_type'] == 'L') {
                    ob_start();
                    print_r(unserialize($field['bd_value']));
                    $field['bd_value'] = '<pre>'.ob_get_contents().'</pre>';
                    ob_clean();
                }

            
            if($field['bd_hidden'] == 1) {
                $css = " class=\"disabled\"";
            }
            else{
                $disabled='';
                $css = "";
            }

                $html .= '
                <tr'.$css.' id="tr'.$field['bd_id'].'">
                    <td>'.$field['bf_name'].'</td>
                    <td>'.$types[$field['bf_type']].'</td>
                    <td style="text-align:justify;" id="tdval'.$field['bd_id'].'">'.$field['bd_value'].'&nbsp;</td>
                    <td class="bctrl" id="tdctrl'.$field['bd_id'].'">
                        <img src="'.ADMIN_DIR.'/images/edit.gif" width="16" height="16" title="Edit" alt="Edit" class="pointer" onclick="editBlockField('.$field['bd_id'].', '.$block['bp_page_id'].');" />
                        <img src="'.ADMIN_DIR.'/images/disabled.png" width="16" height="16" title="Disabled" alt="Disabled" class="pointer" onclick="clickHiddenField('.$field['bd_id'].', '.$block['bp_page_id'].');" />
                        <img src="'.ADMIN_DIR.'/images/delete.gif" width="16" height="16" title="Delete" alt="Delete" class="pointer" onclick="deleteBlockField('.$field['bd_id'].', '.$block['bp_page_id'].');" />
                    </td>
                </tr>
                <tr id="sep'.$field['bd_id'].'">
                    <td colspan="4" class="sep"></td>
                </tr>';
            }}

            $html .= '</table>';

            $this->renderBlocksHTML($content, $html, $bpId);

            $html .= '</fieldset>';
        }
    }

    public function getblocksAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $bid = $this->_getParam('bid');
            $content = new Content($this->getSiteId(), $pageId);
			$params = $content->getBlockParams($bpId);
			$result = array('params'=>$params, 'blocks'=>$content->getChildBlocksList($bpId));
            
            echo Zend_Json::encode($result);
//            echo Zend_Json::encode($content->getChildBlocksList($bpId));
        }
    }

    public function addblock2pageAction() {
        $this->isAjax = true;
        if($this->_hasParam('pid') && $this->_hasParam('bpid') && $this->_request->isPost()) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $blockId = $this->_request->getPost('block');

            $content = new Content($this->getSiteId(), $pageId);
            $newBpId = $content->addBlock($blockId, $bpId);

            $recData = array('bpid' => $newBpId,
                             'pageid' => $pageId,
                             'blockid' => $blockId,
                             'bp_parent' => $bpId,
                             'blockname' => $content->blocks->getBlockName($blockId),
                             'fieldsexist' => $content->blocks->isBlockFieldsExist($blockId),
                             'childsexist' => $content->blocks->isChildBlocksExist($blockId),
							 );

            echo Zend_Json::encode($recData);
        }
    }

    public function editblock2pageAction() {
        $this->isAjax = true;
        if($this->_hasParam('pid') && $this->_hasParam('bpid') && $this->_hasParam('bp_parent')  && $this->_request->isPost()) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $bp_parent = $this->_getParam('bp_parent');			
            $blockId = $this->_request->getPost('block');
			$order = $this->_request->getPost('order');
            
			$content = new Content($this->getSiteId(), $pageId);
            $content->editBlock(array('bpId'=>$bpId, 'blockId'=>$blockId, 'bp_parent'=>$bp_parent, 'bp_order'=>$order));
//	        $orderparams = $content->getBlockParams($bpId);
//			$order = $orderparams['bp_order'];
            $recData = array('bpid' => $bpId,
                             'pageid' => $pageId,
                             'bp_parent' => $bp_parent,
                             'blockid' => $blockId,
                             'order' => $order,							 
                             'blockname' => $content->blocks->getBlockName($blockId),
                             'fieldsexist' => $content->blocks->isBlockFieldsExist($blockId),
                             'childsexist' => $content->blocks->isChildBlocksExist($blockId),
							 );

            echo Zend_Json::encode($recData);
        }
    }

    public function getblockfieldsAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $content = new Content($this->getSiteId(), $pageId);
            $blockFields = $content->getNotAddedBlockFields($bpId);

            echo Zend_Json::encode($blockFields);
        }
    }

    public function addblockfield2pageAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid') && $this->_request->isPost()) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $fieldId = $this->_request->getPost('field');

            $content = new Content($this->getSiteId(), $pageId);
            $bdId = $content->addBlockField($bpId, $fieldId);
            $field = $content->blocks->getBlockField($fieldId);

            $recData = array(
                'bdId' => $bdId,
                'fieldName' => $field['bf_name'],
                'fieldType' => $field['bf_type']
            );

            echo Zend_Json::encode($recData);
        }
    }

    public function getfieldcontentAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bdid')) {
            Zend_Loader::loadClass('Zend_Json');

            $bdId = $this->_getParam('bdid');
            $pageId = $this->_getParam('pid');

            $content = new Content($this->getSiteId(), $pageId);
            $value = $content->getBlockFieldContent($bdId);
            $defaultValue = $content->getBlockFieldDefaultValue($bdId);
            $type = $content->getBlockFieldType($bdId);

            if($type == 'A' || $type == 'I' || $type == 'W' || $type == 'J') {
                if(strlen($value)) {
                    $valueStr = '';
                    $content->parseArray2XML(unserialize($value), $valueStr);
                    $value = $valueStr;
                } else {
                    if($type == 'I') {
                        $value = '<i key="src"></i>
<i key="width"></i>
<i key="height"></i>
<i key="alt"></i>
<i key="title"></i>';
                    } elseif($type == 'W') {
                        $value = '<i key="src"></i>
<i key="width"></i>
<i key="height"></i>
<i key="id"></i>
<i key="version"></i>
<i key="background"></i>';
                    }
                }
            }//>edit italiano 12.11.2014
            elseif($type == 'L') {
                $valueStr = '';
                $content->parseString2Select($defaultValue, $valueStr, $value);
                $value = $valueStr;
            }//<

            $recData = array(
                'type' => $type,
                'value' => (strlen($value) || $type == 'G' ? $value : $defaultValue)
            );

            echo Zend_Json::encode($recData);
        }
    }

    public function editfieldcontentAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bdid') && $this->_request->isPost()) {
            Zend_Loader::loadClass('Zend_Json');

            $bdId = $this->_getParam('bdid');
            $pageId = $this->_getParam('pid');
            $value = $this->_request->getPost('value');
            $content = new Content($this->getSiteId(), $pageId);
            $fieldType = $content->getBlockFieldType($bdId);


            switch($fieldType) {
                case 'S':
                    $value = trim($value);
                    if(substr($value, strlen($value) - 6, strlen($value)) == '<br />') {
                        $value = substr($value, 0, strlen($value) - 6);
                    }
                break;

                case 'F':
                    $value = trim($value);
                break;

                case 'A':
                case 'I':
                case 'W':
                case 'J':
                    $valArray = array();
                    $content->parseXML2Array(trim($value), $valArray);
                    $value = serialize($valArray);
                break;
                case 'L':
                    $valArray = array();
                    //var_dump($value);
                    $content->parseString2Array(trim($value), $valArray);
                    $value = serialize($valArray);
                break;
                case 'E':
                    $value = trim($value);
                break;
            }

            $content->editBlockField($bdId, $value);

            if($fieldType == 'A' || $fieldType == 'I' || $fieldType == 'W' || $fieldType == 'J' || $fieldType == 'L') {
                ob_start();
                print_r($valArray);
                $value = '<pre>'.ob_get_contents().'</pre>';
                ob_clean();
            }

            echo Zend_Json::encode($value);
        }
    }

    public function deletepageblockAction() {
        Zend_Loader::loadClass('Zend_Json');
        $this->isAjax = true;
		$result = array('error'=>array());
        if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
            $bpId = (int)$this->_getParam('bpid');
            $pageId = (int)$this->_getParam('pid');

            $content = new Content($this->getSiteId(), $pageId);
            $value = $content->deleteBlock($bpId);
			if ($value > 0 )
				$result['error'] = 1;
			 echo Zend_Json::encode($result);
        }
    }

    public function liftblockAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');

            $content = new Content($this->getSiteId(), $pageId);
            if($content->isCanLiftBlock($bpId)) {
                $content->liftBlock($bpId);
                echo Zend_Json::encode(TRUE);
            } else {
                echo Zend_Json::encode(FALSE);
            }
        }
    }

    public function pulldownblockAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');

            $content = new Content($this->getSiteId(), $pageId);
            if($content->isCanPullDownBlock($bpId)) {
                $content->pullDownBlock($bpId);
                echo Zend_Json::encode(TRUE);
            } else {
                echo Zend_Json::encode(FALSE);
            }
        }
    }

    public function copyblockAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');

            $content = new Content($this->getSiteId(), $pageId);
			$content->copyBlock($bpId);
			
			$content1 = new Content($this->getSiteId(), $pageId);
			$html = '';
			$this->renderBlocksHTML($content1, $html);
			echo $html;
        } else {
			echo '<h2>Error</h2>';
		}
    }	

    public function deleteblockfieldAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bdid')) {

            $bdId = $this->_getParam('bdid');
            $pageId = $this->_getParam('pid');

            $content = new Content($this->getSiteId(), $pageId);
            $content->deleteBlockField($bdId);
        }
    }

    public function getimginfoAction() {
        $this->isAjax = true;
        Zend_Loader::loadClass('Zend_Json');

        if($this->_request->isPost()) {
            $imgPath = $this->_request->getPost('path');
            $imgInfo = getimagesize($imgPath);

            if($imgInfo === false) {
                echo Zend_Json::encode(FALSE);
            } else {

$str = '<i key="src">'.strstr($imgPath, '/images/').'</i>
<i key="width">'.$imgInfo[0].'</i>
<i key="height">'.$imgInfo[1].'</i>
<i key="alt"></i>
<i key="title"></i>';

                echo Zend_Json::encode($str);
            }
        } else {
            echo Zend_Json::encode(FALSE);
        }
    }

	public function paramsAction() {
        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');
            $this->_redirect('/pages/edit/id/'.$pageId.'/');
        }
    }
	public function metaAction() {
        if($this->_hasParam('id')) {
            $pageId = $this->_getParam('id');
            $this->_redirect('/pages/meta/id/'.$pageId.'/');
        }
    }	
/*---garbagecat76 ---*/	
/*-- 14.09.2010---*/
   public function updateblock2pageAction() {
        $this->isAjax = true;

        if($this->_hasParam('pid') && $this->_hasParam('bpid') && $this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $bp_parent =  $this->_request->getPost('bp_parent');			
            $content = new Content($this->getSiteId(), $pageId);

            $blockParams = $content->updateBlock(array('bpId'=>$bpId, 'bp_parent'=>$bp_parent));
            $recData = array('bpid' => $bpId,
                             'pageid' => $pageId,
                             'bp_parent' => $bp_parent,
                             'blockid' => $blockParams['b_id'],
                             'order' => $blockParams['bp_order'],							 
                             'blockname' => $blockParams['b_name'],
                             'fieldsexist' => $blockParams['is_fields_exist'],
                             'childsexist' => $blockParams['is_child_exist'],
							 );

            echo Zend_Json::encode($recData);
        }
    }

	
    /**
     * Export action
     * @author levada@mail.ua
    */
    public function myexportAction() {
		$this->isAjax = true;
        if($this->_hasParam('bpid') && $this->_hasParam('pid')) {
            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
			
			$content = new Content($this->getSiteId(), $pageId);
			$data = $content->getExportData( $pageId, $bpId );
			
		//	header("Content-Type: text/xml");
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=block_'.$this->getSiteId().'_'.$pageId.'_'.$bpId.'.xml');
			header('Content-Transfer-Encoding: binary');
	
			echo $this-> _toXml($data);			
		}
	}
	
    /**
     * Export action
     * @author levada@mail.ua
    */
    public function myimportAction() {
	
		$this->isAjax = true;
		
		if($this->_hasParam('pid') && $this->_hasParam('bpid')) {
	
			$err = array();
			$file = false;
			$temp_dir = sys_get_temp_dir();

			if (substr($temp_dir, -1) != DIRECTORY_SEPARATOR){
				$temp_dir .= DIRECTORY_SEPARATOR;
			}
			
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
				$this->pages = new Pages($this->getSiteId());

				$bpId = $this->_getParam('bpid');
				$pageId = $this->_getParam('pid');
				
				//$file = './block_107_701_15195.xml';
				$err = $this->myImportBlockCheck($file);
				$import = false;
				
				if( $this->_request->getPost('import_page') && !$err ){ //$this->_request->getPost('import_page') &&
					$import = $this->myImportBlock($file, $bpId, $pageId);
					$this->to_log('Import. Page:'.$pageId.', block:'.$bpId);
					echo '<br/><h1>Import is complete. Reload the page</h1>';
				} else {
					$page['check'] = $err;
					$page['import'] = $import;
					$page['file'] = $file;
					$this->view->assign('page', $page);
					//array_push($this->viewIncludes, 'pages/pageImport.tpl');
					$this->view->display('content/blockImport.tpl');
		//			$content = new Content($this->getSiteId(), $pageId);
		//			$content->copyBlock($bpId);
		//			$content1 = new Content($this->getSiteId(), $pageId);
		//			$html = '';
		//			$this->renderBlocksHTML($content1, $html);
		//			echo $html;
				}
			} else {
				$this->view->assign('err', $err);
				//$this->tplVars['err'] = $err;
				//array_push($this->viewIncludes, 'pages/pageImport_Upload.tpl');
				$this->view->display('content/blockImport_Upload.tpl');
			}
        } else {
			echo '<h2>Error</h2>';
		}

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
	
	private function myImportBlockCheck($xml_file) {

		$errors = array();
		
		$str = file_get_contents($xml_file);
		$xml = simplexml_load_string($str);
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);
		
		$path = $this->pages->getSitePath();
		foreach($data['blocks_fields'] as $field){
			$fields[$field['bf_block_id']][$field['bf_name']] = $field;
		}
		$exist_block = $this->pages->getBlocksData();
		foreach ($data['blocks'] as $block) {
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
		
		if ( count($errors)>0 ) {
			return $errors;
		} else {
			return false;
		}

	}

	private function myImportBlock($file, $bpId, $pageId) {

		$str = file_get_contents($file);
		$xml = simplexml_load_string($str);
		$json = json_encode($xml);
		$data = json_decode($json,TRUE);
		
		$exist_block = $this->pages->getBlocksData();
		foreach( $data['blocks'] as $val ) {
			$blocks[$val['b_id']] = $val['b_file'];
		}
		foreach( $data['blocks_fields'] as $val ) {
			$fields [$val['bf_id']] = $val['bf_name'];
		}
		foreach( $data['blocks_data'] as $val ) {
			$blocks_data[$val['bd_bp_id']][$val['bd_field_id']] = $val;
		}
		
		$content = new Content($this->getSiteId(), $pageId);
		$block_order = $content->importBlockCalcOrder($bpId, count( $data['blocks2pages'] ) );

		foreach( $data['blocks2pages'] as $val ) {
			
			$bpData = array(
				'bp_block_id' => $exist_block[$blocks[$val['bp_block_id']]]['b_id'],
				'bp_page_id' => $pageId,
			//	'bp_parent' => $val['bp_parent'] ? $bp_parent[$val['bp_parent']] : $bpId ,
				'bp_parent' => ($val['bp_parent'] && $bp_parent[$val['bp_parent']]) ? $bp_parent[$val['bp_parent']] : $bpId ,
				'bp_order' => ++$block_order,
                'bp_hidden' =>  $val['bp_hidden'],
			);
            
			$sourceBlocksData = array();
			foreach ( $blocks_data[$val['bp_id']] as $bl ) {
				$sourceBlocksData[] = array(
					'bd_field_id' =>  $exist_block [$blocks[$val['bp_block_id']]] ['field'] [$fields[$bl['bd_field_id']]] ['bf_id'],
					'bd_value' => !is_array($bl['bd_value']) ? $bl['bd_value'] : '',
                    'bd_hidden' => $bl['bd_hidden'],
				);
			}
			$blokId = $this->pages->importBlock($bpData, $sourceBlocksData);
			$bp_parent[$val['bp_id']] = $blokId;
		}

		return true;

	}
    /** function for hidden block and children block */
    public function hiddenblockAction() {
        $this->isAjax = true;

        if($this->_hasParam('bpid') && $this->_hasParam('pid')) {
            
            Zend_Loader::loadClass('Zend_Json');

            $bpId = $this->_getParam('bpid');
            $pageId = $this->_getParam('pid');
            $content = new Content($this->getSiteId(), $pageId);
            
            $result = $content->hiddenBlock($bpId);
            
            echo Zend_Json::encode($result);

        }
    }
        /** function for hidden block and children block */
    public function hiddenfieldAction() {
        $this->isAjax = true;

        if($this->_hasParam('bdid') && $this->_hasParam('pid')) {
            
            Zend_Loader::loadClass('Zend_Json');

            $bdid = $this->_getParam('bdid');
            $pid = $this->_getParam('pid');
            $content = new Content($this->getSiteId(), $pid);
            
            $result = $content->hiddenField($bdid);
            
            echo Zend_Json::encode($result);

        }
    }
    /** function for hidden block and children block */
    public function blockinfoAction() {
        
        $this->isAjax = true;

        if($this->_hasParam('bid') && $this->_hasParam('pid')) {
            
            Zend_Loader::loadClass('Zend_Json');

            $bid = $this->_getParam('bid');
            $pageId = $this->_getParam('pid');
            $content = new Content($this->getSiteId(), $pageId);
            
            $result = $content->blockInfo($bid);
            
            if ($result)
            {                
                $this->view->assign('vars', $result);
            }
            
            $this->view->display('content/contentOverlayBlockInfo.tpl');
        }
    }
    
    public function imageToBaze64($base64=null){
        
        if(is_uploaded_file($_FILES["userfile"]["tmp_name"])) 
        {

            $exts = array("gif", "jpeg", "jpg", "png");
            $ext = end(explode(".", $_FILES["userfile"]["name"]));
            
            if (($_FILES["userfile"]["type"] == "image/gif" || $_FILES["userfile"]["type"] == "image/jpeg" || $_FILES["userfile"]["type"] == "image/jpg" || $_FILES["userfile"]["type"] == "image/png") && $_FILES["userfile"]["size"] < 102400 && in_array($ext, $exts)){
                
                if (move_uploaded_file($_FILES["userfile"]["tmp_name"], sys_get_temp_dir().$_FILES["userfile"]["name"]))
                {

    				$file = sys_get_temp_dir().$_FILES["userfile"]["name"];
                    
                    $fd = fopen ($file, 'rb');
                    $size=filesize($file);
                    $data = fread($fd, $size);
                    fclose($fd);
                    
                    //$data = file_get_contents($file);
    
                    $base64 = 'data:image/' . $_FILES["userfile"]["type"] . ';base64,' . base64_encode($data); 
                }

            }
        }
        else{
            var_dump($_FILES["userfile"]["error"]);
        }

        return $base64;
    }

}

?>