<?php

include_once(ENGINE_PATH.'class/classPage.php');

class AdminPage extends Page {

    # PRIVATE VARIABLES
    private $siteDbAdapter;

    # PUBLIC VARIABLES


    // constructor
    public function __construct($pg_id, $db = '') {
        parent::__construct($pg_id, $db);

        $this->siteDbAdapter = Zend_Registry::get('siteDbAdapter');
        $this->siteDbAdapter->query('SET NAMES utf8');
    }

    // destuctor
    public function __destruct() {
        $this->siteDbAdapter = NULL;
    }

    /*
     Èçìåíÿåì äàííûå ñòðàíèöè
    */
    public function editPage($address,
                             $title,
                             $menuTitle,
                             $lang,
                             $parent,
                             $relative,
                             $css,
                             $jscript,
                             $priority,
                             $cacheable,
                             $hidden,
                             $indexed,
                             $extensions,
							 $headers,
                             $options/* added 18.11.2014, italiano */) {        


        $set = array(
            'pg_address'    => $address,
            'pg_title'      => $title,
            'pg_menu_title' => $menuTitle,
            'pg_lang'       => $lang,
            'pg_parent'     => $parent,
            'pg_relative'   => $relative,
            'pg_css'        => $css,
            'pg_jscript'    => $jscript,
            'pg_priority'   => $priority,
            'pg_cacheable'  => $cacheable,
            'pg_hidden'     => $hidden,
            'pg_indexed'    => $indexed,
            'pg_extensions' => $extensions,
			'pg_headers'    => $headers,
            'pg_options'    => $options,/* added 25.11.2014, italiano */
            'pg_lastmodify' => date("Y-m-d H:i:s",time()) /* added 21.11.2014, italiano */
        );

        /* added $this->testFields() 25.11.2014, italiano */
        if ($this->testFields($set))
        {
            $where = $this->siteDbAdapter->quoteInto('pg_id = ?', $this->id);
            return $this->siteDbAdapter->update('pages', $set, $where);
			
        }
		return false;
    }

    /*
     Ïðîâåðÿåì êåøèðóåìàÿ ëè ñòðàíöà
    */
    public function isCacheable() {
        return $this->isCacheable;
    }

    /*
     Ïðîâåðÿåì ñêðûòàÿ ëè ñòðàíöà
    */
    public function isHidden() {
        return $this->isHidden;
    }

    /*
     Ïîëó÷àåì ìàññèâ id-øíèêîâ åêñòýíøàíîâ ïðèâÿçíàíûõ ê ñòðàíèöå
    */
    public function getExtensionsIds() {
        return $this->extensions;
    }

    /*
     Óñòàíàâëèâàåì ôëàã hidden
    */
    public function setHidden($flag) {
        $set = array('pg_hidden' => $flag ? 1 : 0);
        $where = $this->siteDbAdapter->quoteInto('pg_id = ?', $this->id);
        $this->siteDbAdapter->update('pages', $set, $where);
    }

    /*
     Óñòàíàâëèâàåì ôëàã cacheable
    */
    public function setCacheable($flag) {
        $set = array('pg_cacheable' => $flag ? 1 : 0);
        $where = $this->siteDbAdapter->quoteInto('pg_id = ?', $this->id);
        $this->siteDbAdapter->update('pages', $set, $where);
    }

    public function setReCaching($flag) {
        $set = array('pg_cached' => ($flag ? 1 : 0));
        $where = $this->siteDbAdapter->quoteInto('pg_id = ?', $this->id);
        $this->siteDbAdapter->update('pages', $set, $where);
    }

    /*
     Óäàëèòü ìåòó
    */
    public function deleteMeta($metaId) {
        $this->siteDbAdapter->delete('metas', $this->siteDbAdapter->quoteInto('mt_id = ?', $metaId));
    }

    /*
     Äîáàâòü ìåòó
    */
    public function addMeta($name, $content, $lang) {
        $row = array(
            'mt_page_id' => $this->id,
            'mt_name' => $name,
            'mt_content' => $content,
            'mt_lang' => $lang);
        $this->siteDbAdapter->insert('metas', $row);
        return $this->siteDbAdapter->lastInsertId();
    }

    /*
     Ðåäàêòèðîâàòü ìåòó
    */
    public function editMeta($metaId, $name, $content, $lang) {
        $set = array(
            'mt_page_id' => $this->id,
            'mt_name' => $name,
            'mt_content' => $content,
            'mt_lang' => $lang);
        $this->siteDbAdapter->update('metas', $set, $this->siteDbAdapter->quoteInto('mt_id = ?', $metaId));
    }

    /*
     Ïîëó÷èòü ñïñîê ìåò
    */
    public function getMetas() {
        $select = $this->siteDbAdapter->select();
        $select->from('metas', array('mt_id', 'mt_name', 'mt_content', 'mt_lang', 'mt_page_id'));
        $select->where('mt_page_id = ?', $this->id);
        $rows = $this->siteDbAdapter->fetchAll($select->__toString());

        $tsize = sizeof($rows);
        $res = array();
        if($tsize) {
            for($i = 0; $i < $tsize; $i++) {
                $res[$i] = array('id' => $rows[$i]['mt_id'], 'name' => $rows[$i]['mt_name'], 'description' => $rows[$i]['mt_content'], 'lang' => $rows[$i]['mt_lang'], 'page_id' => $rows[$i]['mt_page_id']);
            }
        }
        return $res;
    }
    
    /**
     * function test column table `pages`
     * @author italiano
     * @param $data = array()
     * @date 25.11.2014
     * @return bool
     */
    public function testFields($data=null) 
    {
        $fieldsError = array();
        $fieldsToAdd = array(
            'pg_options' => '`pg_options` VARCHAR(255)',
        );
           
        if (isset($data) && is_array($data))
        {
                $this->siteDbAdapter->query('SET NAMES utf8');
                
                foreach($data as $key=>$item)
                {
                        $q = "SELECT IF($key IS NULL or $key = '', 'empty', $key) as $key FROM `pages`";
                        $issetField = true;
                        
                        //find column
                        try{
                            $result = $this->siteDbAdapter->query($q);
                        }
                        catch (Exception $e) {
                            $issetField = false;
                        }
                        
                        //add column if not find
                        if (!$issetField)
                        {
                            if (isset($fieldsToAdd[$key])){
                                $q = "ALTER TABLE `pages` ADD $fieldsToAdd[$key]";
                            } 
                            else{
                                $q = "ALTER TABLE `pages` ADD `$key` VARCHAR(30)";
                            }
    
                            try{
                                $result = $this->siteDbAdapter->query($q);
                            }
                            catch (Exception $e) {
                                echo $e->getMessage();
                                $fieldsError[]=$key;
                            }
                        }  
                }
        }
           
        if (count($fieldsError)>0)
        {
            return false;
        }
        else{
            return true;
        }

    }
}

?>