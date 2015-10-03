<?php
include_once('classFormModel.php');

class Downloadlinks  extends classFormModel{

	public $dl_date_expired;
	public $dl_download_link;
	public $dl_link_expired;
	public $dl_download_dir;
	
	public $params = array();
	
  	public function __construct($siteId) {
        $config = Zend_Registry::get('config');
        $this->params = $config->downloadlinks->toArray();
		$this->dl_download_dir = $this->params['prefix'];
    }

	public function rules()
	{
		return array(
			array('dl_date_expired, dl_download_link, dl_download_dir', 'required'),
			array('dl_download_link, dl_download_dir','string','max'=>255),
		);
	}

	public function attributeLabels()
	{
		return array(
					'dl_download_link'=>'Link to bild',
					'dl_date_expired'=>'Date expired',
					'dl_download_dir'=>'Parent dir',
		);
	}
	
	public function generateDownloadLink($prefix = null) {
		$params = base64_encode(http_build_query(array('f'=>$this->dl_download_link, 'd'=>strtotime($this->dl_date_expired))));		
		$path = isset($prefix) ? $prefix : $this->dl_download_dir;
		return $path.$params;
	}
}
?>