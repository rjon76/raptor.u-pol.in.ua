<?php
include_once('classDbmodel.php');


class Blacklist extends dbmodel {

    #PIVATE VARIABLES
	
    public $tablename = 'black_list';
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
			array('bl_name', 'required'),
			array('bl_name','unique'),
		);
	}

	public function getattributeLabels()
	{
		return array(
			'bl_name' => 'Key number',
			'bl_count' => 'Count',
			'bl_hidden' => 'Not active',
		);
	}
	
}


?>