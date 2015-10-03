<?php
include_once('classDbmodel.php');
class Case_study  extends dbmodel{

	public $tablename='case_study';
	public $attributeLabels = array();

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
			array('cs_autor,cs_lang_id,cs_text', 'required'),
			array('cs_link','string','max'=>255),
		);
	}

	public function getattributeLabels()
	{
		return array(
			'cs_autor' => 'Autor',
			'cs_text' => 'Text',
			'cs_lang_id' => 'Language',
			'cs_link' => 'Link',			
		);
	}

	public function get_pages_not_view()
	{
		return explode(',', $this->attributes['cs_pages_not_view']);
	}	
 	
	public function get_max($lang= NULL)
	{
		$row = parent::_fetchRow(array('where'=>$lang,'order'=>'cs_order desc'));
		return $row['cs_order'];
 	}
}
?>