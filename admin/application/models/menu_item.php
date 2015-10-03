<?php
include_once('classDbmodel.php');

class Menu_item extends dbmodel {

    #PIVATE VARIABLES
    public $tablename = 'menu_item';
	public $attributeLabels = array();	

    #PUBLIC VARIABLES


    public function __construct($siteId) {
		parent::__construct($siteId);
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
			array('mi_name, mi_menu_id', 'required'),
			array('mi_menu_id', 'integer', array('integerOnly')),			
		);
	}
/*-------------------------*/	
	public function getattributeLabels()
	{
		return array(
			'mi_name' => 'Name',
			'mi_menu_id' => 'Menu',
			'mi_parent_id' => 'Parent',			
			'mi_level' => 'Level',			
			'mi_order' => 'Order',			
			'mi_link' => 'Link',			
			'mi_attr' => 'Attributes',			
			'mi_title' => 'title',			
			'mi_link_alias' => 'Link alias',			
			'mi_hidden' => 'Hidden',			
			'mi_class' => 'CSS Class',			
		);
	}
/*-------------------------*/	
	public function get_pages_not_view()
	{
		return explode(',', $this->attributes['mi_pages_not_view']);
	}

}


?>