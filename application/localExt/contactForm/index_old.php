<?php

include_once(ENGINE_PATH.'class/classForm.php');
include_once(ENGINE_PATH.'class/classProducts.php');

class contactForm {

    private $formData;

    public function __construct() {
	$this->formData = array();

        if(isset($_POST['purchaseQuestionsFromSubmit'])) {
			$this->sendForm('purchaseQuestionsForm.ini','Contact sales team ');
        }

        if(isset($_POST['upgradeQuestionsFormSubmit'])) {
            $this->upgradeQuestionsForm();
        }

        if(isset($_POST['registrationProblemsFormSubmit'])) {
            $this->registrationProblemsForm();
			$this->sendForm('otherQuestionsForm.ini','Other issue...  ');
        }

        if(isset($_POST['recoverRegistrationFormSubmit'])) {
			$this->sendForm('recoverRegistrationForm.ini','Recover lost registration info');
        }

        if(isset($_POST['didntRecieveFormSubmit'])) {
      		$this->sendForm('didntRecieveForm.ini','Report item not received');
        }

        if(isset($_POST['techicalSupportFormSubmit'])) {
			 $this->sendForm('technicalSupportForm.ini','Tech support request - '.$_POST['turgent']);
        }

        if(isset($_POST['otherQuestionsFormSubmit'])) {
      		$this->sendForm('otherQuestionsForm.ini','Other issue...  ');
        }
		if(isset($_POST['callbackFormSubmit'])) {
			$this->sendForm('callbackForm.ini','Request callback  ');
        }
		if(isset($_POST['eduFormSubmit'])) {
           $this->sendForm('eduForm.ini','ClearApps for education, government & non-profits');
        }
		if(isset($_POST['competeFormSubmit'])) {
            $this->sendForm('competeForm.ini','Tired of annual bills? Benefit from our one-time payment pricing');
        }
		if(isset($_POST['reselerFormSubmit'])) {
            $this->sendForm('reselerForm.ini',"Need to buy locally? Let us know and we'll offer an option");
        }
		if(isset($_POST['quoteFormSubmit'])) {
            $this->sendForm('quoteForm.ini','Place a PO or guarantee pricing for 30 days by getting a quote');
        }
		if(isset($_POST['upgradeFormSubmit'])) {
            $this->sendForm('upgradeForm.ini','Planning to add nodes to your license? Get a discounted upgrade!');
        }
		if(isset($_POST['extenddemoFormSubmit'])) {
            $this->sendForm('extendeddemoForm.ini','Need more time to convince your boss?');
        }	
		if(isset($_POST['formalquoteFormSubmit'])) {
            $this->sendForm('formalquoteForm.ini','Place a PO or guarantee pricing for 30 days by getting a quote');
        }	
        if(isset($_POST['uninstallFromSubmit'])) {
			$this->sendForm('uninstallForm.ini','Uninstallation feedback ');
        }		
    }

    public function __destruct() {
	$this->formData = NULL;
    }


    public function getResult() {
	return $this->formData;
    }



    private function upgradeQuestionsForm() {
	$form = new Form();

	if($form->ParseSettings('upgradeQuestionsForm.ini')) {
	    $this->formData = $form->BuildFormFields($_POST);

            if(empty($this->formData['error'])) {

		include_once(ENGINE_PATH.'class/classProducts.php');
                $subject = 'Upgrade questions';

		if(!empty($_POST['product'])) {
                    $products = new Products();
		    $product = $products->getProductById($_POST['product']);
                    $subject .= ' ['.$product['p_title'].']';
		}

                $msg = 'Contact request from '.$_SERVER['HTTP_HOST']."\n\n".
                       (isset($product) ? 'Product: '.$product['p_title']."\n" : '').
                       $form->__toString();

                $form->SendMail($subject, $msg, NULL, $this->formData['fields']['email']);
            }
	}
    }

    private function registrationProblemsForm() {
        $form = new Form();

	if($form->ParseSettings('registrationProblemsForm.ini')) {
	    $this->formData = $form->BuildFormFields($_POST);

            if(empty($this->formData['error'])) {

		include_once(ENGINE_PATH.'class/classProducts.php');
		$products = new Products();
		$product = $products->getProductById($_POST['product']);
		$form->setEmail(isset($_POST['to_email']) ? $_POST['to_email'] : '' );	
                $msg = 'Contact request from '.$_SERVER['HTTP_HOST']."\n\n".
                       'Product: '.$product['p_title']."\n".
                       $form->__toString();

                $form->SendMail('Registration problems ['.$product['p_title'].']', $msg, $this->formData['fields']['name'], $this->formData['fields']['email']);
            }
	}
    }

  	private function sendForm($ini, $subject='') {
	$form = new Form();

	if($form->ParseSettings($ini)) {
	    $this->formData = $form->BuildFormFields($_POST);

            if(empty($this->formData['error'])) {
/*				if (isset($_POST['to_email'])){
					$form->setEmail($_POST['to_email']);	
				}else{
					$form->setEmail();	
				}*/
				$form->setEmail(isset($_POST['to_email']) ? $_POST['to_email'] : '' );	
				
				$subject = isset($_POST['subject']) ? $_POST['subject'] : $subject;

                $msg 		= 	'Contact request from '.$_SERVER['HTTP_HOST']."\n\n".
								'Contact IP: '.$_SERVER['REMOTE_ADDR']."\n".
                       			$form->__toString();

                $form->SendMail($subject, $msg, isset($this->formData['fields']['name']) ? $this->formData['fields']['name'] : 'Anonymous', $this->formData['fields']['email']);
            }
	}
    }	
}
?>