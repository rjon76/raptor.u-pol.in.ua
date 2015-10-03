<?php

include_once(ENGINE_PATH.'class/classForm.php');


class contactForm {

    private $formData;

    public function __construct() {
	$this->formData = array();

        if(isset($_POST['contactFormSubmit'])) {
			$this->sendForm('contactForm.ini', $_POST['id_like_to']);
        }

        if(isset($_POST['downloadFormSubmit'])) {
            $this->downloadForm();
        }
    }

    public function __destruct() {
	$this->formData = NULL;
    }


    public function getResult() {
	return $this->formData;
    }

  	private function sendForm($ini, $subject='') {
		$form = new Form();

		if($form->ParseSettings($ini)) {
	    $this->formData = $form->BuildFormFields($_POST);

            if(empty($this->formData['error'])) {

                $msg 		= 	'Contact request from '.$_SERVER['HTTP_HOST']."\n\n".
								'Contact IP: '.$_SERVER['REMOTE_ADDR']."\n".
                       			$form->__toString();
				
                $form->SendMail($subject, $msg, $this->formData['fields']['email'], $this->formData['fields']['email']);
            }
		}
    }	
	
	private function getRandomDownloadLink($args) {
		include_once(ENGINE_PATH.'class/classRandomname.php');
		$rn = new randomname(LOCAL_PATH.'application/configRandomName.xml');
		$prefix = isset($args[1]) ? $args[1] : 'files/';
		$filename = $rn->gen_random_name($args[0], $prefix);
		return $filename;
    }	
	
	private function chackUA($ua)
	{
		$user_agent = $_SERVER["HTTP_USER_AGENT"];
		return !(strpos($user_agent, $ua) === false);
	
	}
	
	private function downloadForm() {
		$form = new Form();

		if($form->ParseSettings('downloadForm.ini')) {
	    $this->formData = $form->BuildFormFields($_POST);

            if(empty($this->formData['error'])) {
				
				$pageLanguage = (isset($_POST['pagelang']) && $_POST['pagelang']!=='') ? $_POST['pagelang'] : 'en';
		
				$rn_ek_random = $this->getRandomDownloadLink(array($_POST['filename']));
				
				$os = '_'.($_POST['os'] ? $_POST['os'] : 'win');
				
				$ua = (strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') === false) ? '' : '_chrome';
				
				//var_dump($_SERVER["HTTP_USER_AGENT"]);
				
				//var_dump($ua);
				
				//var_dump($os);
				
				if ($os == '_win'){
					//var_dump("$os == '_win'");
					$os = $os.$ua;
				}
				
				//var_dump(LOCAL_PATH.'application/dlemail_'.$pageLanguage.$os.'.inc.php');
				
				include_once(LOCAL_PATH.'application/dlemail_'.$pageLanguage.$os.'.inc.php');
				
				include_once(ENGINE_PATH.'class/classMail.php');
				
				$mail = new SMTP_Mail;
				
				$subject = isset($_POST['subject']) ? $_POST['subject'] : 'Elite Keylogger - download instructions';

				//var_dump($dl_email);
				$mail->add_text($dl_email);
				$mail->build_message(); 
				//$mail->send( $this->formData['fields']['email'], 'WideStep <support@widestep.com>', $subject);
				$mail->clearAll();
            }
		}
    }
}