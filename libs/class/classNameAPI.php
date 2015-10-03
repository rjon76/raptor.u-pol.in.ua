<?php
/*
//#1 for NameApi API   
use org\nameapi\client\services as Services;

include_once(ENGINE_PATH.'org/nameapi/client/services/ServiceFactory.php');

use org\nameapi\ontology\input\context\Context;
use org\nameapi\ontology\input\context\Priority;
use org\nameapi\ontology\input\context\TextCase;  

//include_once(ENGINE_PATH.'org/nameapi/ontology/input/context/Context.php');
//include_once(ENGINE_PATH.'org/nameapi/ontology/input/context/Priority.php');
//include_once(ENGINE_PATH.'org/nameapi/ontology/input/context/TextCase.php');

//#1 end
*/



class NameAPI {
    protected $topApi=null;
    private $key = null;
    private $db = null ;
    private static $service = null;
    private $connected = true;
    public $lstrings = array();
	private $xMashapeKey = "HU0WTPFT4ZmshemqAqek8sq5Awovp12Kds1jsnaEQLNbR07U1x";
	private $blacklistTimeOld = 2592000; // 30 day

	public function __construct($_key = null) 
    {

        if(!VBox::isExist('Page') && isset($_POST['page_id'])) {
            include_once(ENGINE_PATH.'class/classPage.php');
            $page = new Page($_POST['page_id']);
            $this->lstrings = $page->getLocalStrings();
        }
        
        $this->db = VBox::get('ConstData')->getConst('langsDb');

/*
        //#1 for NameApi API  
        
        $array_keys = array(
                    '0b38c46364a919ef3ba2887081286c82-user1', //italiano
                    'bda2473939f5755ff0fbeabdbc033946-user1', //garbagecat76
                    'a950795667d63b9e3d37bcd31f7657b1-user1', //Andrew / andrewtest@rambler.ru / 5w25j3x8
                    'f8284e714c1539a8ebb06ddbb6409ffd-user1', //Svaran
        );

        shuffle($array_keys);
        $this->key = $array_keys[0];   

        if(isset($_key)){
            $this->key = $_key;    
        }        
     
        $context = Context::builder()
        ->apiKey($this->key)
        ->priority(Priority::REALTIME())
        //->textCase(TextCase::TITLE_CASE())
        ->build();

        if (!isset(self::$service)) {
            self::$service = new Services\ServiceFactory($context);    
        }            

        // ping API service
        $pinger = self::$service->systemServices()->pinger();
        $this->connected = $pinger->ping();   
        //#1 end 
*/        
    }
    

    public function __destruct(){
		$this->key = null;
    }

    private function localeText($text,$lstring=null)
    {
        if (isset($lstring))
        {
            if(isset($this->lstrings[$lstring])){
                return $this->lstrings[$lstring];    
            }  
        }
        
        return $text;

    }

    public function checkEmail($email=null,$table='blacklist_email')
	{

        $callback = array('status'=>YES, 'data'=>''); /* answer if email domain no trust */  
                 
        $email = trim($email);
        $domain = end(explode('@',$email));

        if(empty($email)){
            $callback['data'] = '1';
            return $callback;
        }        
        
        if(!self::checkRegEmail($email)){
            $callback['data'] = '1';
            return $callback;
        }
  
        if(isset($email))
        {
			
			$time = time();
			
            $q = 'SELECT * FROM '.$this->db.'.'.$table.' WHERE be_domain = ? AND be_date > ?  LIMIT 1';
            if( DB::executeQuery($q, 'selecttrushdomain', array( $domain, $time - $this->blacklistTimeOld )) ) {
				$row = DB::fetchRow('selecttrushdomain');
				if ( $row['be_status'] ) {
					$callback['data'] = self::localeText('Please, try another email address', 'please_try_another_email_address');
					return $callback;  
				} else {
					return null;
				}
            }
/*
            //for NameApi API              
            if($this->connected && false)
            {
                $deaDetector = self::$service->emailServices()->disposableEmailAddressDetector();
                $result = $deaDetector->isDisposable($email);
                $result = $result->getDisposable()->__toString();

                if ($result == $callback['status'])
                {
                    $q = 'insert into '.$this->db.'.'.$table.' (be_domain,be_date) values(\''.$domain.'\','.time().')';
                    DB::executeAlter($q, 'inserttrushdomain');     
                    $callback['data'] = self::localeText('Please, try another email address', 'please_try_another_email_address');
                    return $callback;
                }
            }// end
*/		
			//for NameApi API
			if ($this->connected) {
				
				$result = $this->mashapeCheckEmail($email);
				if ($result) {
					$q = 'INSERT INTO ' . $this->db . '.' . $table . ' (be_domain,be_status,be_date) values(\'' . $domain . '\',' . $result['status'] . ' ,' . $time . ')
						 ON DUPLICATE KEY UPDATE be_status = ' . $result['status'] . ' , be_date = ' . $time . '';
					DB::executeAlter($q, 'inserttrushdomain');
					if ($result['status']) {
						$callback['data'] = self::localeText('Please, try another email address', 'please_try_another_email_address');
						return $callback;
					}
				}
				
			}// end
			
        }
        return null;
    }
	
	public function mashapeCheckEmail($email) {
		
        $domain = end(explode('@',$email));
		
		$head=array(
			"X-Mashape-Key : " . $this->xMashapeKey,
			"Accept : application/json"
		 );
		$url = "https://bdeacc-block-disposable-e-mail.p.mashape.com/".$domain;
		
		$process = curl_init($url);
		curl_setopt($process, CURLOPT_HTTPHEADER, $head );
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
	    curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
		$return = curl_exec($process);
		curl_close($process);
		
		$return = json_decode($return, true);

		$res = false;
		
		if ($return['request_status'] == 'success') {
			if ($return['domain_status'] == 'ok'){
				$res = array('status' => 0);
			} else {
				$res = array('status' => 1);
			}
		} else {
			//todo error report
		}
		return $res;
	}

	public function checkRegEmail($email){
        
        if (!preg_match("/^[\w|_|\-|\.|\d]+\@[\w|\-|\d]+?\.[\w|\-|\.|\d]+$/", $email))
        {
            return false;
        }
        
        return true;
    }

    public function checkEmailDomain($email=null)
    {
        $arrays = array(
                    "trbvm.com",
                    "0815.ru0clickemail.com", 
                    "0wnd.net", 
                    "0wnd.org", 
                    "10minutemail.com", 
                    "20minutemail.com", 
                    "2prong.com", 
                    "3d-painting.com", 
                    "4warding.com", 
                    "4warding.net", 
                    "4warding.org", 
                    "9ox.net", 
                    "a-bc.net", 
                    "amilegit.com", 
                    "anonbox.net", 
                    "anonymbox.com", 
                    "antichef.com", 
                    "antichef.net", 
                    "antispam.de", 
                    "baxomale.ht.cx", 
                    "beefmilk.com", 
                    "binkmail.com", 
                    "bio-muesli.net", 
                    "bobmail.info", 
                    "bodhi.lawlita.com", 
                    "bofthew.com", 
                    "brefmail.com", 
                    "bsnow.net", 
                    "bugmenot.com", 
                    "bumpymail.com", 
                    "casualdx.com", 
                    "chogmail.com", 
                    "cool.fr.nf", 
                    "correo.blogos.net", 
                    "cosmorph.com", 
                    "courriel.fr.nf", 
                    "courrieltemporaire.com", 
                    "curryworld.de", 
                    "cust.in", 
                    "dacoolest.com", 
                    "dandikmail.com", 
                    "deadaddress.com", 
                    "despam.it", 
                    "devnullmail.com", 
                    "dfgh.net", 
                    "digitalsanctuary.com", 
                    "discardmail.com", 
                    "discardmail.de", 
                    "disposableaddress.com", 
                    "disposemail.com", 
                    "dispostable.com", 
                    "dm.w3internet.co.uk example.com", 
                    "dodgeit.com", 
                    "dodgit.com", 
                    "dodgit.org", 
                    "dontreg.com", 
                    "dontsendmespam.de", 
                    "dump-email.info", 
                    "dumpyemail.com", 
                    "e4ward.com", 
                    "email60.com", 
                    "emailias.com", 
                    "emailinfive.com", 
                    "emailmiser.com", 
                    "emailtemporario.com.br", 
                    "emailwarden.com", 
                    "ephemail.net", 
                    "explodemail.com", 
                    "fakeinbox.com", 
                    "fakeinformation.com", 
                    "fastacura.com", 
                    "filzmail.com", 
                    "fizmail.com", 
                    "frapmail.com", 
                    "garliclife.com", 
                    "get1mail.com", 
                    "getonemail.com", 
                    "getonemail.net", 
                    "girlsundertheinfluence.com", 
                    "gishpuppy.com", 
                    "great-host.in", 
                    "gsrv.co.uk", 
                    "guerillamail.biz", 
                    "guerillamail.com", 
                    "guerillamail.net", 
                    "guerillamail.org", 
                    "guerrillamail.com", 
                    "guerrillamailblock.com", 
                    "haltospam.com", 
                    "hotpop.com", 
                    "ieatspam.eu", 
                    "ieatspam.info", 
                    "ihateyoualot.info", 
                    "imails.info", 
                    "inboxclean.com", 
                    "inboxclean.org", 
                    "incognitomail.com", 
                    "incognitomail.net", 
                    "ipoo.org", 
                    "irish2me.com", 
                    "jetable.com", 
                    "jetable.fr.nf", 
                    "jetable.net", 
                    "jetable.org", 
                    "junk1e.com", 
                    "kaspop.com", 
                    "kulturbetrieb.info", 
                    "kurzepost.de", 
                    "lifebyfood.com", 
                    "link2mail.net", 
                    "litedrop.com", 
                    "lookugly.com", 
                    "lopl.co.cc", 
                    "lr78.com", 
                    "maboard.com", 
                    "mail.by", 
                    "mail.mezimages.net", 
                    "mail4trash.com", 
                    "mailbidon.com", 
                    "mailcatch.com", 
                    "maileater.com", 
                    "mailexpire.com", 
                    "mailin8r.com", 
                    "mailinator.com", 
                    "mailinator.net", 
                    "mailinator2.com", 
                    "mailincubator.com", 
                    "mailme.lv", 
                    "mailnator.com", 
                    "mailnull.com", 
                    "mailzilla.org", 
                    "mbx.cc", 
                    "mega.zik.dj", 
                    "meltmail.com", 
                    "mierdamail.com", 
                    "mintemail.com", 
                    "moncourrier.fr.nf", 
                    "monemail.fr.nf", 
                    "monmail.fr.nf", 
                    "mt2009.com", 
                    "mx0.wwwnew.eu", 
                    "mycleaninbox.net", 
                    "mytrashmail.com", 
                    "neverbox.com", 
                    "nobulk.com", 
                    "noclickemail.com", 
                    "nogmailspam.info", 
                    "nomail.xl.cx", 
                    "nomail2me.com", 
                    "no-spam.ws", 
                    "nospam.ze.tc", 
                    "nospam4.us", 
                    "nospamfor.us", 
                    "nowmymail.com", 
                    "objectmail.com", 
                    "obobbo.com", 
                    "onewaymail.com", 
                    "ordinaryamerican.net", 
                    "owlpic.com", 
                    "pookmail.com", 
                    "proxymail.eu", 
                    "punkass.com", 
                    "putthisinyourspamdatabase.com", 
                    "quickinbox.com", 
                    "rcpt.at", 
                    "recode.me", 
                    "recursor.net", 
                    "regbypass.comsafe-mail.net", 
                    "safetymail.info", 
                    "sandelf.de", 
                    "saynotospams.com", 
                    "selfdestructingmail.com", 
                    "sendspamhere.com", 
                    "shiftmail.com", 
                    "****mail.me", 
                    "skeefmail.com", 
                    "slopsbox.com", 
                    "smellfear.com", 
                    "snakemail.com", 
                    "sneakemail.com", 
                    "sofort-mail.de", 
                    "sogetthis.com", 
                    "soodonims.com", 
                    "spam.la", 
                    "spamavert.com", 
                    "spambob.net", 
                    "spambob.org", 
                    "spambog.com", 
                    "spambog.de", 
                    "spambog.ru", 
                    "spambox.info", 
                    "spambox.us", 
                    "spamcannon.com", 
                    "spamcannon.net", 
                    "spamcero.com", 
                    "spamcorptastic.com", 
                    "spamcowboy.com", 
                    "spamcowboy.net", 
                    "spamcowboy.org", 
                    "spamday.com", 
                    "spamex.com", 
                    "spamfree24.com", 
                    "spamfree24.de", 
                    "spamfree24.eu", 
                    "spamfree24.info", 
                    "spamfree24.net", 
                    "spamfree24.org", 
                    "spamgourmet.com", 
                    "spamgourmet.net", 
                    "spamgourmet.org", 
                    "spamherelots.com", 
                    "spamhereplease.com", 
                    "spamhole.com", 
                    "spamify.com", 
                    "spaminator.de", 
                    "spamkill.info", 
                    "spaml.com", 
                    "spaml.de", 
                    "spammotel.com", 
                    "spamobox.com", 
                    "spamspot.com", 
                    "spamthis.co.uk", 
                    "spamthisplease.com", 
                    "speed.1s.fr", 
                    "suremail.info", 
                    "tempalias.com", 
                    "tempemail.biz", 
                    "tempemail.com", 
                    "tempe-mail.com", 
                    "tempemail.net", 
                    "tempinbox.co.uk", 
                    "tempinbox.com", 
                    "tempomail.fr", 
                    "temporaryemail.net", 
                    "temporaryinbox.com", 
                    "thankyou2010.com", 
                    "thisisnotmyrealemail.com", 
                    "throwawayemailaddress.com", 
                    "tilien.com", 
                    "tmailinator.com", 
                    "tradermail.info", 
                    "trash2009.com", 
                    "trash-amil.com", 
                    "trashmail.at", 
                    "trash-mail.at", 
                    "trashmail.com", 
                    "trash-mail.com", 
                    "trash-mail.de", 
                    "trashmail.me", 
                    "trashmail.net", 
                    "trashymail.com", 
                    "trashymail.net", 
                    "tyldd.com", 
                    "uggsrock.com", 
                    "wegwerfmail.de", 
                    "wegwerfmail.net", 
                    "wegwerfmail.org", 
                    "wh4f.org", 
                    "whyspam.me", 
                    "willselfdestruct.com", 
                    "winemaven.info", 
                    "wronghead.com", 
                    "wuzupmail.net", 
                    "xoxy.net", 
                    "yogamaven.com", 
                    "yopmail.com", 
                    "yopmail.fr", 
                    "yopmail.net", 
                    "yuurok.com", 
                    "zippymail.info", 
                    "jnxjn.com", 
                    "trashmailer.com", 
                    "klzlk.com",
                );
                
        if(in_array(trim(end(explode('@',$email))),$arrays) && isset($email)){
            return false;
        }
        
        return true;
    }
}
?>