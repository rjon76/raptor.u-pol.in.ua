<?php
include_once('classDbmodel.php');
include_once('banners_item.php');

class Banners extends dbmodel {

    #PIVATE VARIABLES
	
    public $tablename = 'banners';
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
			array('banner_name, banner_alias', 'required'),
		);
	}

	public function getattributeLabels()
	{
		return array(
			'banner_name' => 'Name',
			'banner_alias' => 'Alias',
		);
	}


    public function getBannerItems() {
        $select = $this->siteDbAdapter->select();
        $select->from('banners_item', '*');
        $select->joinLeft('banners_item as p', 'banners_item.bi_parent_id = p.bi_id',  'p.bi_name as parent');		
        $select->where('banners_item.bi_banner_id = ?', $this->pk);
        $select->order('banners_item.bi_id');
		return $this->siteDbAdapter->fetchAll($select->__toString());
    }
}


?>