<?php

class FilesController extends MainApplicationController {

    #PIVATE VARIABLES
    private $isAjax;

    #PUBLIC VARIABLES

    public function init() {
        parent::init();

        $this->isAjax = FALSE;

        $this->tplVars['header']['actions']['names'] = array(
            array('name' => 'browser', 'menu_name' => 'Browser'),
            array('name' => 'uploader', 'menu_name' => 'Uploader')
        );
    }

    public function __destruct() {
        if(!$this->isAjax) {
           $this->display();
        }

        $this->isAjax = NULL;

        parent::__destruct();
    }

    public function indexAction() {
        $this->_redirect('/files/browser/');
    }

    public function browserAction() {

        $this->tplVars['page_js'][] = 'jquery.filetree.js';
        $this->tplVars['page_js'][] = 'browser.js';
        $this->tplVars['page_css'][] = 'jquery_file_tree.css';
        $this->tplVars['browser']['site_dir'] = $this->getSiteDir();

        array_push($this->viewIncludes, 'file/browser.tpl');
    }

    public function deleteAction() {
        $this->isAjax = TRUE;
        Zend_Loader::loadClass('Zend_Json');

        if($this->_request->isPost()) {
            $filePath = $this->_request->getPost('path');
            if(@unlink($filePath)) {
                echo Zend_Json::encode(TRUE);
            } else {
                echo Zend_Json::encode(FALSE);
            }
        }

    }

    public function uploaderAction() {

        $this->tplVars['page_js'][] = 'fancy_upload/mootools.js';
	$this->tplVars['page_js'][] = 'fancy_upload/Swiff.Uploader.js';
	$this->tplVars['page_js'][] = 'fancy_upload/Fx.ProgressBar.js';
	$this->tplVars['page_js'][] = 'fancy_upload/FancyUpload2.js';
        $this->tplVars['page_js'][] = 'fancy_upload/uploader.js';

        $this->tplVars['page_css'][] = 'uploader.css';
        $this->tplVars['page_css'][] = 'browser.css';
        $this->tplVars['page_css'][] = 'jquery_file_tree.css';

        $this->tplVars['browser']['site_dir'] = $this->getSiteDir();

        array_push($this->viewIncludes, 'file/uploader.tpl');
    }

    public function filetreeAction() {

        $this->isAjax = TRUE;

        $root = '';

        $_POST['dir'] = urldecode($_POST['dir']);

        if(file_exists($root.$_POST['dir'])) {

            $files = scandir($root.$_POST['dir']);

            natcasesort($files);

            if(count($files) > 2) { /* The 2 accounts for . and .. */

                echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";

                // All dirs
                foreach( $files as $file ) {
                    if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
                        echo "
                        <li class=\"directory collapsed\">
                        <a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a>
                        </li>";
                    }
                }

                // All files
                foreach( $files as $file ) {
                    if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {

                        $ext = preg_replace('/^.*\./', '', $file);

                        $filepath = $root . $_POST['dir'] . $file;

                        if(isset($_GET['info']) && $_GET['info'] == 0) {
                            echo "
                            <li class=\"file ext_$ext\">
                                    <a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a>
                            </li>";

                        } else {
                            $owner = posix_getpwuid(fileowner($filepath));
                            $imgsize = getimagesize($filepath);
                            $filesize = round((filesize($filepath) / 1024), 2);

                            echo "
                            <li class=\"file ext_$ext\">
                                    <a href=\"#\" class=\"myDel\" onclick=\"deleteFile(this, '".$filepath."');\">delete</a>
                                    <span class=\"perms\">".$owner['name']."</span>
                                    ".($imgsize !== false ? "<span class=\"size\">(".$imgsize[0]."px*".$imgsize[1]."px)</span>" : "")."
                                    <span class=\"size\">".$filesize." kb </span>
                                    <a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a>
                            </li>";
                        }
                    }
                }
                echo "</ul>";
            }
        }
    }
}

?>