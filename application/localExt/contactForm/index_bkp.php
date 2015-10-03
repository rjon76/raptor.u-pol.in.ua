<?php



include_once(ENGINE_PATH.'class/classForm.php');


class contactForm {



    private $formData;



    public function __construct() {

	$this->formData = array();



        if(isset($_POST['contactFormSubmit'])) {

			$this->sendForm('contactForm.ini', $_POST['id_like_to']);

        }

		if(isset($_POST['FileTypeRequestFormSubmit'])) {
			$this->sendForm('FileTypeRequestForm.ini', $_POST['subject']);
        }
		

		if(isset($_POST['competeFormSubmit'])) {

            $this->sendForm('competeForm.ini', $_POST['subject']);

        }

		
		if(isset($_POST['noprofitFormSubmit'])) {

            $this->sendForm('noprofitForm.ini', $_POST['subject'] );

        }

		if(isset($_POST['requestDiscountFormSubmit'])) {
            $this->sendForm('requestDiscount.ini', $_POST['subject']);

        }

 		if(isset($_POST['requestGimmeFormSubmit'])) {
            $this->sendForm('requestGimme.ini', $_POST['subject']);

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

				$subject.=' from '.$_SERVER['HTTP_HOST'];

                $msg 		= 	'Contact request \n\n'.

								'Contact IP: '.$_SERVER['REMOTE_ADDR'].'\n'.

                       			$form->__toString();

				

                $form->SendMail($subject, $msg, $this->formData['fields']['email'], $this->formData['fields']['email']);

            }

	}

    }	

}

?>