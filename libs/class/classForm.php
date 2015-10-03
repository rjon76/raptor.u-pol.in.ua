<?php

class Form {

    private $mSettings;
    private $mFormFields;
    private $mStrFormFields;


    public function __construct()
    {

		$this->mFormFields 		= array();
		$this->mStrFormFields 	= '';
    }

    public function __destruct()
    {
		$this->mSettings 		= NULL;
		$this->mFormFields 		= NULL;
		$this->mStrFormFields 	= NULL;
    }

    public function __toString()
    {
		if(func_num_args()>0) {
	        $toHtml=func_get_arg(0);
    	} else {
        	$toHtml=false;
	    }
		return ($toHtml) ? nl2br($this->mStrFormFields) : $this->mStrFormFields;
    }

    public function ParseSettings($fileName)
    {

		if(!defined('LOCAL_PATH'))
		{
	    	return '1';
	    	return FALSE;
		}

		if(file_exists(LOCAL_PATH.'application/forms/'.$fileName))
		{
	    	$ini_content = parse_ini_file(LOCAL_PATH.'application/forms/'.$fileName, TRUE);

	    	foreach ($ini_content as $key => $val)
	    	{
				if('settings' == $key)
				{
		    		$this->mSettings = $val;
					
				}
				if(1 == substr_count($key,'field'))
				{
		    		$this->mFormFields = array_merge($this->mFormFields,array($key => $val));
				}
	    	}
	    	return TRUE;
		}
		return FALSE;
    }

   

    public function BuildFormFields($httpMethod = array())
    {
    	$must_validate 	= (empty($httpMethod)) ? FALSE : TRUE;
		$texts       	= array('text', 'hidden', 'textarea', 'captcha');
		$switches    	= array('select', 'radio');
		$form_fields 	= array('fields' 	=> array(),
						     'error' 	=> array() );
//						 

		foreach ($this->mFormFields AS $field) {
	    	$form_to_string = '';

	    	// for the checkboxes
	    	if ('checkbox' == $field['type'])
	    	{
				if (!empty($httpMethod[$field['name']]))
				{
				    if (is_array($httpMethod[$field['name']])){
						$form_to_string = $form_fields['fields'][$field['name']] = implode(',',array_values($httpMethod[$field['name']]));
					}else{
						$form_fields['fields'][$field['name']] = '1';
			    		$form_to_string = 'yes';
					}
				}
				else
				{
		    		$form_fields['fields'][$field['name']] = '0';
		    		$form_to_string = 'no';
                    if($must_validate && !empty($field['dependedonfeel']))
                    {
						$tmp = explode(',',$field['dependedonfeel']);
						$keys = array_keys($httpMethod);
						//var_dump($field['name'], $tmp);
						$result = array_intersect($tmp, $keys); 
						//var_dump($keys, $tmp, $result);
						if (count($result)==0)
							$form_fields['error'][$field['name']] = '1';
                    }
					
				}
		    }

		    // for the text, textraea, hidden
		    elseif(in_array($field['type'], $texts))
		    {
				if (empty($httpMethod[$field['name']]))
				{
				    $form_fields['fields'][$field['name']] = '';

				    if ($must_validate && $field['required'])
				    {
						$form_fields['error'][$field['name']] = '1';
		    		}

                    if($must_validate && !empty($field['depended']) && empty($httpMethod[$field['depended']]))
                    {
                        $form_fields['error'][$field['name']] = '1';
                    }

                    if($must_validate && !empty($field['dependedonfeel']) && !empty($httpMethod[$field['dependedonfeel']]))
                    {
                        $form_fields['error'][$field['name']] = '1';
                    }

					if ( $field['type'] == 'captcha'){
							session_start();
							unset($_SESSION[$field['name']]);
						}

				}
				else
				{
		    		$form_fields['fields'][$field['name']] = $httpMethod[$field['name']];
		    		if ($must_validate)
		    		{
		    			if ($field['type'] == 'hidden')
		    			{
			    			if (!empty($field['cookie']))
			    			{
								if (!isset($_COOKIE[$httpMethod[$field['name']]]))
								{
				    				$form_fields['error'][$field['name']] = '1';
								}
			    			}
			    			elseif(!empty($field['value']) && $httpMethod[$field['name']] != $field['value'])
			    			{
								$form_fields['error'][$field['name']] = '1';
			    			}
						}

						if (!empty($field['email']) && !preg_match("/^[\w|_|\-|\.|\d]+\@[\w|\-|\d]+?\.[\w|\-|\.|\d]+$/", $httpMethod[$field['name']]))
						{
			    			$form_fields['error'][$field['name']] = '1';
						}

						if ( $field['type'] == 'captcha'){
							session_start();
							if (isset($_SESSION[$field['name']]) && $_SESSION[$field['name']]===$httpMethod[$field['name']]){
									 //echo "Текс введен верно";
							}else{
					    			$form_fields['error'][$field['name']] = '1';
							}
							unset($_SESSION[$field['name']]);
						}
						
		    		}
				}
				$form_to_string = $form_fields['fields'][$field['name']];
	    	}

	    	 // for the select, option
	    	elseif (in_array($field['type'], $switches))
	    	{
	    		if (!empty($httpMethod[$field['name']]))
	    		{

						$form_fields['fields'][$field['name']]['selected'] = $httpMethod[$field['name']];
                        
				}
				else
				{
		    		$form_fields['fields'][$field['name']]['selected'] = '';

		    		if ($must_validate && $field['required'])
		    		{
						$form_fields['error'][$field['name']] = '1';
		    		}

                    if($must_validate && !empty($field['depended']) && empty($httpMethod[$field['depended']]))
                    {
                        $form_fields['error'][$field['name']] = '1';
                    }

                    if($must_validate && !empty($field['dependedonfeel']) && !empty($httpMethod[$field['dependedonfeel']]))
                    {
                        $form_fields['error'][$field['name']] = '1';
                    }
				}

				if ('select' == $field['type'])
				{
				    
		    		$form_fields['fields'][$field['name']]['options'] = array();
				}

				$form_to_string = $form_fields['fields'][$field['name']]['selected'];
	    	}

	    	 // for the file
	    	elseif ('file' == $field['type'])
	    	{
				if($must_validate)
	    		{
					if(!empty($_FILES[$field['name']]))
                    {
                        $this->moveUploadedFile($_FILES[$field['name']]['name'],
                                                $_FILES[$field['name']]['tmp_name'],
                                                $field['dir']);

						$form_fields['AttachFile']['filepath'] = LOCAL_PATH.$field['dir'].$_FILES[$field['name']]['name'];
						$form_fields['AttachFile']['filename'] = $_FILES[$field['name']]['name'];

                    }
                    elseif($field['required'])
                    {
                        $form_fields['error'][$field['name']] = '1';
                    }
                }
            }

             // for something undefined
            else
            {
				if (empty($httpMethod[$field['name']]))
				{
				    $form_fields['fields'][$field['name']] = '';
		    		if ($must_validate && $field['required'])
		    		{
						$form_fields['error'][$field['name']] = '1';
		    		}
				}
				else
				{
		    		$form_fields['fields'][$field['name']] = $httpMethod[$field['name']];
				}
		    } // for the __toString conversion
	   		if (is_array($form_to_string) || !empty($field['title']))
	   		{
				$this->mStrFormFields .= $field['title'].': '.$form_to_string."\n";
	    	}
		}

		return $form_fields;
    }

    public function SendMail($email_subject,
                             $email_message,
                             $fromName = '',
                             $email_from = NULL,
                             $fileatt = NULL,
                             $fileatt_name = NULL) {
	
		$email_to = '';
		$email_from = $email_from ? $email_from : 'norepeat@'.$_SERVER['HTTP_HOST'];
        foreach ($this->mSettings as $key => $val){
			if (1 == substr_count($key, 'mail')){$email_to .= $val.',';}
	    }

	    if (!empty($email_to)) {
	    	$email_to = trim($email_to, ',');
	    } else {
	    	return FALSE;
	    }

            $headers 	= "From: ".$email_from;
            $semi_rand 	= md5(time());
            $mime_boundary 	= "==Multipart_Boundary_x{$semi_rand}x";

            $headers .= "\nMIME-Version: 1.0\n" .
                        "Content-Type: multipart/mixed;\n" .
                        " boundary=\"{$mime_boundary}\"";

            $email_message .= "This is a multi-part message in MIME format.\n\n" .
                              "--{$mime_boundary}\n" .
                              "Content-Type:text/html; charset=\"utf-8\"\n" .
                              "Content-Transfer-Encoding: 7bit\n\n" .
                              str_replace("\n","<br>",$email_message) . "\n\n";

            /*
             FILE
            */
            $ok = false;

            if($fileatt != '') {
                    $fileatt_type 	= "application/octet-stream"; // File Type

                    $file 			= fopen($fileatt,'rb');
                    $data 			= fread($file,filesize($fileatt));
                    $data 			= chunk_split(base64_encode($data));
                    fclose($file);

                    $email_message .= "--{$mime_boundary}\n" .
                                      "Content-Type: {$fileatt_type};\n" .
                                      " name=\"{$fileatt_name}\"\n" .
                                      "Content-Transfer-Encoding: base64\n\n" .
                                      $data . "\n\n" .
                                      "--{$mime_boundary}\n";

                    $ok = @mail($email_to, $email_subject, $email_message, $headers);

                    if($ok == true) {
                            unlink($fileatt);
                    }

                    unset($data,$file,$fileatt,$fileatt_type,$fileatt_name);
            }

            if($ok == false){
                    @mail($email_to, $email_subject, $email_message, $headers);
					if ((bool)IniParser::getInstance()->getSettring('mail', 'toMail') )
					{
						$adminMail = IniParser::getInstance()->getSettring('mail', 'adminMail');		                    
						$subjectMail = IniParser::getInstance()->getSettring('mail', 'subjectMail');	
						@mail($adminMail, $email_subject.($subjectMail ? $subjectMail : ''), $email_message, $headers);
					}
					            /* Äîáîâëÿåì ïèñüìî â àðõèâ */

	            $q = 'INSERT INTO '.VBox::get('ConstData')->getConst('langsDb').'.email_archive
    	              SET ea_email_to = ?,
        	              ea_email_from = ?,
            	          ea_subject = ?,
                	      ea_message = ?';

	            DB::executeAlter($q, array($email_to, $email_from, $email_subject, $email_message));
	
            }



            return TRUE;
    }

    public function moveUploadedFile($fileName, $fileTmpName, $dirPath) {
        if(move_uploaded_file($fileTmpName, LOCAL_PATH.$dirPath.$fileName)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /* italiano, 20.02.15 */
   	public function getmSettings(){
	   return (count($this->mSettings) ? $this->mSettings : false);		
	}
	public function setmSettings($arr=array()){
		foreach($arr as $key=>$value){
			$this->mSettings[$key] = $value;
		}		
	}
    /* italiano, 20.02.15 */
	public function delmSettings($key){
        if(isset($this->mSettings[$key])){
            unset($this->mSettings[$key]); 
        }	
	}
/*-------------------------------*/
	public function getEmailTo()
	{
	    $email_to = '';

        foreach ($this->mSettings as $key => $val){
			if (1 == substr_count($key, 'mail')){
				$email_to .= $val.',';
			}
	    }

	    if (!empty($email_to)) {
	    	$email_to = trim($email_to, ',');
			return $email_to;
	    }else{
			return false;
		}
	}
    
    /** function get mails lists in ini file
     * @author garbagecat76@gmail.com
     * @version 20.02.15
     * @return array
     */
    public function getSettings(){
        return $this->mSettings;
    }
}
?>