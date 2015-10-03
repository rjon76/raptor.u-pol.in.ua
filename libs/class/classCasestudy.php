<?php
class cl_casestudy 
{
	private $siteDb;
	public $table_name = 'case_studies_last';
   	public function __construct() 
	{
	 DB::_set_table_name(VBox::get('ConstData')->getConst('langsDb').'.'.$this->table_name);  
    }
	public function get_few_data($fields_arr = '*',$where_arr = array(), $num_page = '*', $order_arr = array('date'=>'desc'))
	{
		return DB::_get_few_data('casestudy',$fields_arr, $where_arr,$num_page,$order_arr);
	}
	public function get_page_count_date($where_str = '')
	{
		return DB::_get_page_count_date($where_str);
	}
	public function set_table_name($table_name)
	{
		DB::_set_table_name($table_name);  
	}
	public function  get_one_data($where_arr = array())
	{
		return DB::_get_one_data($where_arr, $order_arr);  
	}	
	public function get_empty_date()
	{
		return DB::_get_empty_date();  
	}
	public function add_data()
	{
		$id=0;	
		$error ='';
		$message =array();
		
	if ($id = DB::_add_data())
		{
			/*--if gifts > 0*/
			if (isset($_POST['link']) && $_POST['link'] > 0 && $id > 0)
			{
			 $q = 'INSERT INTO case_studies_links(csl_cs_id, csl_url, csl_comment) ';
	        $firstIteration = true;
    	    	foreach ($_POST['link'] as $key => $val)
				{
	    			$q .= (!$firstIteration ? ',' : 'VALUES').'('.$id.', '.DB::_quote($val).','.DB::_quote($_POST['comment_link'][$key]).')';
                	$firstIteration = false;
    			}
       		 DB::executeAlter($q, 'update_case_studies_links');
			}
			/*--if images_and_files > 0*/
			$countfiles = count($_FILES['images_and_files']['name']);
			if ($countfiles > 0)
			{
				if (!file_exists(LOCAL_PATH.IniParser::getInstance()->getSettring('path', 'localupload').$id))
					mkdir(LOCAL_PATH.IniParser::getInstance()->getSettring('path', 'localupload').$id);
				for ($i=0; $i < $countfiles; $i++)
				{
					$resupload = true;
				// test FILE is IMAGE
					if (!getimagesize($_FILES['images_and_files']['tmp_name'][$i]))
						{
							array_push($message,'File is not image type - '.$_FILES['images_and_files']['name'][$i]);
							$resupload = false;
						}
				// test FILESIZE 
					if ($_FILES['images_and_files']['size'][$i] > IniParser::getInstance()->getSettring('path', 'maxfilesize'))
						{
							array_push($message,'Limit filesize  - '.$_FILES['images_and_files']['name'][$i]);
							$resupload = false;
						}
					if ($_FILES['images_and_files']['size'][$i] == 0)
						{
							array_push($message,'Filesize is null  - '.$_FILES['images_and_files']['name'][$i]);
							$resupload = false;
						}
					if ($resupload)
						{
						if (move_uploaded_file($_FILES['images_and_files']['tmp_name'][$i], LOCAL_PATH.IniParser::getInstance()->getSettring('path', 'localupload').$id.'/'.$_FILES['images_and_files']['name'][$i]))
							{
								$q = 'INSERT INTO case_studies_files(csf_cs_id, csf_name, csf_comment) values('.$id.','.DB::_quote($_FILES['images_and_files']['name'][$i]).','.DB::_quote($_POST['comment_is_obligatory'][$i]).')';
								 DB::executeAlter($q, 'images_and_files');
							}
						else
							array_push($message,'File not upload  - '.$_FILES['images_and_files']['name'][$i]);
					}
				}
			}
		}
		else
		{
			$error = 'Run time error';
		}
		return array('id'=>$id,'message'=>$message,'error'=>$error);
    }
}
?>
