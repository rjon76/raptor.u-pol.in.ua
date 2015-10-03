<?php
include_once('classDbmodel.php');
include_once('menu_item.php');

class Menu extends dbmodel {

    #PIVATE VARIABLES
	
    public $tablename = 'menu';
	public $attributeLabels = array();

    #PUBLIC VARIABLES

    public function __construct($siteId) {
		parent::__construct($siteId);
		$this->attributeLabels = $this->getattributeLabels();
    }

    public function __destruct() {
		$this->attributeLabels = NULL;
    	$this->tablename = NULL;
    }

	public function rules()
	{
		return array(
			array('m_name, m_alias', 'required'),
		);
	}

	public function getattributeLabels()
	{
		return array(
			'm_name' => 'Name',
			'm_alias' => 'Alias',
		);
	}


    public function getMenuItems() {
        $select = $this->siteDbAdapter->select();
        $select->from('menu_item', '*');
        $select->joinLeft('menu_item as p', 'menu_item.mi_parent_id = p.mi_id',  'p.mi_name as parent');		
        $select->where('menu_item.mi_menu_id = ?', $this->pk);
        $select->order('menu_item.mi_id');
		return $this->siteDbAdapter->fetchAll($select->__toString());
    }
}


?>