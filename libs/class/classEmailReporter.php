<?php 

class EmailReporter
{
	private $sendmailCommand = '/usr/sbin/sendmail -bs';	
	private $toEmail;
	private $subject = 'Support request';
	private $fromEmail;
	private $mailer; 
	private $messageBody; 
	private $mes;
	private $contenttype = 'text/html';
	private $useAttach = false;
	private $path = "http://www.Garbagecat.com/submit/mailtpl/";	
    private $params =  array(
			'transportType' => 'mail', // sendmail , mail, smtp
			'smtpServer' 	=> '192.168.0.1',
			'smtpPort' 		=> '25',
			'smtpSequre' 	=> '',
			'smtpUsername' 	=> 'user',
			'smtpPassword' 	=> '',
		);	
    
	function __construct()
	{
		// include swift mailer
        require ENGINE_PATH.'swiftmailer/classes/Swift.php';
        
        Swift::init();
        Swift::registerAutoload();

		//Yii::import('system.vendors.swiftMailer.classes.Swift', true);
		//Yii::registerAutoloader(array('Swift','autoload'));
		require_once(ENGINE_PATH.'swiftmailer/swift_init.php');

        //Yii::import('system.vendors.swiftMailer.swift_init', true);

        switch($this->params['transportType'])
        {
		
			case 'smtp' : 	$transport = Swift_SmtpTransport::newInstance($this->params['smtpServer'],$this->params['smtpPort'], $this->params['smtpSequre'])
  							->setUsername($this->params['smtpUsername'])
  							->setPassword($this->params['smtpPassword']);
			break;
            
			case 'sendmail' : $transport = Swift_SendmailTransport::newInstance($this->params['sendmailCommand']);
			break;		
			
			default:
			case 'mail' : $transport = Swift_MailTransport::newInstance();
			break;	
            		
			
		}   
		$this->toEmail = 'noreplay@'.$_SERVER['HTTP_HOST']; 
		$this->fromEmail  = 'noreplay@'.$_SERVER['HTTP_HOST']; 
		$this->path = "http://".$_SERVER['HTTP_HOST']."/submit/mailtpl/";	
		$this->mailer 	= Swift_Mailer::newInstance($transport);
		$this->mes = Swift_Message::newInstance();
	}

    public function __destruct(){
   	
        $this->sendmailCommand = null;	
        $this->toEmail = null;
        $this->subject = null;
        $this->fromEmail = null;
        $this->mailer = null;
        $this->messageBody = null;
        $this->mes = null;
        $this->contenttype =  null;  
        $this->useAttach =  null;  
        $this->path =  null;  	
        $this->params = null;  
        
    }

/*--------Send message  ------------*/
    public function send($toEmail = null, $fromEmail=null, $subject = null)
    {
		$this->subject = ($subject) ? $subject : $this->subject;
		$this->toEmail = ($toEmail) ? $toEmail : $this->toEmail;
        $this->fromEmail = ($fromEmail) ? $fromEmail : $this->fromEmail;
        
        if (empty($this->messageBody)){
            return;
        }
        
        $mail_body = $this->messageBody;

        if($this->useAttach)
        { 
            if ( preg_match_all('#src="([^"]*)"#', $mail_body, $imgs) ) {
    			
    			$imgs[1] = array_unique($imgs[1]);
    			$replace = array();
    			foreach ($imgs[1] as $key => $img){
    				$img_file = file_get_contents($img);
    				if ($img_file) {
    					$img_name = $ext=(basename($img));
    					$img_type = $ext=strtolower(substr(strrchr($img_name, '.'),1));
    					switch ( $img_type ){
    						case 'jpg':		$mine_type = 'image/jpeg';	break;
    						case 'jpeg':	$mine_type = 'image/pjpeg';	break;
    						case 'gif':		$mine_type = 'image/gif';	break;
    						case 'png':		$mine_type = 'image/png';	break;
    						case 'ico':		$mine_type = 'image/x-icon';	break;
    						// другие типы...
    						default:	continue;	break;
    					 }
    					$data = array(
    						'data' => $img_file ,
    						'filename' => $img_name,
    						'filetype' => $mine_type
    					);
    					$md5_name = md5($img_name);
    					$replace['src="'.$img] = 'src="cid:'.$md5_name;
    					$this->_attach( $data['data'], $data['filename'], $data['filetype'], $md5_name );
    				}
    			}
    			$mail_body = strtr($mail_body,$replace );
    			
    		}
        }
        
        if ($mail_body) {
           try{
               $this->mes->setSubject($this->subject)
                ->setFrom($this->fromEmail)
                ->setTo($this->toEmail)
                ->setBody($mail_body, $this->contenttype);
                
				$this->mailer->send($this->mes, $failedRecipients);
				$this->_clear_attach();
				
				return count($failedRecipients) == 0;
	
		   }catch (Exception $e) {

    	   }
        }
		
		$this->_clear_attach();
		return false;	
    }
	
	/* Attache file to mail	*/
	public function _attach($data=NULL, $filename='', $filetype = 'text/html', $contentid = false)
	{
	   if(isset($this->theme))
       {
            $theme = explode('/',$this->theme);
            $theme =$theme[0];
            $path = $this->path.$theme.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$filename;
            
    		if (file_get_contents($this->path.$theme.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$filename)) {
    			$attach = Swift_Attachment::fromPath($path, $filetype);
    			if ($contentid)
    				$attach->getHeaders()->addTextHeader('Content-ID', $contentid);
    			$this->mes->attach($attach);
                
    		} elseif($data!==NULL) {
    			$attach = Swift_Attachment::newInstance($data,  $filename, $filetype);
    			if ($contentid)
    				$attach->getHeaders()->addTextHeader('Content-ID', $contentid);
    			$this->mes->attach($attach);
    		}
        }
	}
	
	/* Clear attache files to mail */
	public function _clear_attach() {
		$this->mes->setChildren( array() );
	}

	/* Set content type */
	public function set_content_type($type = 'text/html')
	{
		$this->contenttype = $type;
	}
	
	public function setBody($body,$replaces = array())
	{
		$this->messageBody  = $this->replaceString($body, $replaces);
	}	

	public function renderingTpl($tpl=null)
	{
		$messageBody = '';
		
		if(isset($tpl))
        {
           //delete first slash
            if(mb_substr($tpl, 0, 1) == '/'){
                $tpl = substr($tpl,1);
            }

            if($messageBody = file_get_contents($this->path.$tpl, false)){
                return $messageBody;
            }
        }

        return $messageBody;
        
	}		
    
   	public function replaceString($messageBody, $replaces=array())
	{
	   if(count($replaces))
       {
    	   foreach($replaces as $key=>$string)
           {
                $messageBody = str_replace('{'.$key.'}',$string,$messageBody);
           }
       }
       
       return $messageBody;
	
	}
}

?>