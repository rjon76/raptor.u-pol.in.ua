<?php
include_once('classDbmodel.php');

class Banners_item extends dbmodel {

    #PIVATE VARIABLES
    public $tablename = 'banners_item';
	public $attributeLabels = array();	

    #PUBLIC VARIABLES


    public function __construct($siteId) {
		parent::__construct($siteId);
        
        $this->testFields($this->getattributeLabels());
        
		$this->attributeLabels = $this->getattributeLabels();
    }

    public function __destruct() {
		$this->attributeLabels = NULL;
    	$this->tablename = NULL;
    	$this->isNewRecord = NULL;		
    }
	
	public function rules()
	{
		return array(
			array('bi_name, bi_banner_id, bi_link', 'required'),
			array('bi_banner_id', 'integer', array('integerOnly')),			
		);
	}
/*-------------------------*/	
	public function getattributeLabels()
	{
		return array(
			'bi_name' => 'Name',
			'bi_banner_id' => 'Banner',
			'bi_parent_id' => 'Parent',			
			'bi_level' => 'Level',			
			'bi_order' => 'Order',			
			'bi_link' => 'Link template banner',			
			'bi_attr' => 'Attributes',			
			'bi_title' => 'title',			
			'bi_link_alias' => 'Link alias',			
			'bi_hidden' => 'Hidden',			
			'bi_class' => 'CSS Class',
            'bi_type' => 'Type',
            'bi_pages' => 'Pages',
            'bi_assign' => 'Assign to pages',		
		);
	}
/*-------------------------*/	
	public function get_pages()
	{
		return explode(',', $this->attributes['bi_pages']);
	}
    
    /**
     * function test column for table
     * @author italiano
     * @param $data = array(), $_table = name table 
     * @date 26.11.2014
     * @return bool
     */
    public function testFields($data=null, $_table=null) 
    {
        if (isset($_table))
        {
            $table = $_table;
        }
        else
        {
            $table = 'banners_item';
        }
        
        $fieldsError = array();
        $fieldsToAdd = array(
            'bi_pages' => '`bi_pages` text DEFAULT NULL',
            'bi_assign' => "`bi_assign` enum('0','1','2') NOT NULL DEFAULT '2'",
        );
        
        if (isset($data) && is_array($data))
        {
                $this->siteDbAdapter->query('SET NAMES utf8');
                
                foreach($data as $key=>$item)
                {
                        $q = "SELECT IF($key IS NULL or $key = '', 'empty', $key) as $key FROM `$table`";
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
                                $q = "ALTER TABLE `$table` ADD $fieldsToAdd[$key]";
                            } 
                            else{
                                $q = "ALTER TABLE `$table` ADD `$key` VARCHAR(30)";
                            }
                            
                            try{
                                $result = $this->siteDbAdapter->query($q);
                            }
                            catch (Exception $e) {
                                echo $e->getMessage();
                                $fieldsError[]=$key;
                            }
                            
                            // >> delete block where all bazed will do for rename column bi_pages_not_view to bi_pages
                            if ($key == "bi_pages")
                            {
                                $q = "UPDATE `$table` SET `$key`=bi_pages_not_view";
                                
                                try{
                                    $result = $this->siteDbAdapter->query($q);
                                    
                                    $q = "ALTER TABLE `$table` DROP `bi_pages_not_view`";
                                    
                                    try{
                                        $result = $this->siteDbAdapter->query($q);
                                    }
                                    catch (Exception $e) {
                                        //echo $e->getMessage();
                                    }

                                }
                                catch (Exception $e) {
                                    //echo $e->getMessage();
                                    //$fieldsError[]=$key;
                                }
                            }
                            // <<
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