<?php
/* requrements
  class VException
  class IniParser
*/
class Error {

    private static $_instance;
    private $_settings;
    private $_messages;

    private function __construct($settings)
	{
        if(!is_array($settings) || sizeof($settings)<1)
		{
            throw new VException('Wrong config parameters for Error class.');
        }
        $this->_settings 			= array();
        $this->_messages 			= array();
        $this->_settings['toMail'] 	= isset($settings['toMail']) ? (bool)$settings['toMail'] : FALSE;
        if(!empty($settings['adminMail']))
		{
            $this->_settings['adminMail'] = $settings['adminMail'];
        }
        else
		{
            $this->_settings['adminMail'] = '';
            $this->_settings['toMail'] = FALSE;
        }
        $this->_settings['toLog'] = isset($settings['toLog']) ? (bool)$settings['toLog'] : FALSE;
        
		if(!empty($settings['logPath']))
		{
            $this->_settings['logPath'] = $settings['logPath'];
            if(!file_exists($settings['logPath']))
			{
                if($fp =  fopen($settings['logPath'],'w'))
				{
                    fclose($fp);
                    chmod($settings['logPath'],0664);
                }
                else
				{
                    $this->_settings['toLog'] = FALSE;
                }
            }
        }
        else
		{
            $this->_settings['logPath'] = '';
            $this->_settings['toLog'] = FALSE;
        }
    }

    private function __clone() {}

    public static function getInstance()
	{
		if (self::$_instance === NULL)
		{
			self::$_instance = new self(IniParser::getInstance()->getSection('error'));
		}
		return self::$_instance;
    }

    public static function unsetInstance()
	{
        self::getInstance()->_settings 	= NULL;
        self::getInstance()->_messages 	= NULL;
		self::$_instance 				= NULL;
    }

    //switch on/off error logging to file
    //Does not return anything
    public static function setFileLogging($write)
	{
        $instanse 						= self::getInstance();
        $instanse->_settings['toLog'] 	= (bool)$write;
        
		if($instanse->_settings['toLog'] && empty($instanse->_settings['logPath']))
		{
            $instanse->_settings['toLog'] = FALSE;
        }
    }

    //switch on/off error mailing
    //Does not return anything
    public static function setMailing($send)
	{
        $instanse 						= self::getInstance();
        $instanse->_settings['toMail'] 	= (bool)$send;
    }

    // Warns about 404 errors. Such errors occures, when requested address is not found in the database
    // Does not return anything
    public static function mail404($address)
	{
        if(self::getInstance()->_settings['toMail'])
		{
            $message = 'An 404 error occured.'."\n";
            $message.= 'Unrecognized address: '.$_SERVER['HTTP_HOST'].'/'.$address.'"'."\n";
            $message.= 'Site: '.$_SERVER['HTTP_HOST']."\n";
            $message.= 'Referer: '.(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'undefined')."\n";
            $message.= 'User agent: '.(isset($_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT']  : 'undefined')."\n";
            $message.= 'Remote addr (IP): '.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'undefined')."\n";
            $message.= 'Time: '.date('m/d/Y H:i:s')."\n";
            self::mailError('404 error occured', $message, '2');
        }
    }

    public static function mailUrgent($className, $message)
	{
        if(self::getInstance()->_settings['toMail'])
		{
            self::mailError('Urgent error in '.$className, $message, '1');
        }
    }

    public static function mailResults($subj, $message, $urgent = FALSE)
	{
        if(self::getInstance()->_settings['toMail'])
		{
            $priority = ($urgent ? '1' : '2');
            self::mailError($subj, $message, $priority);
        }
    }

    // Log Error Method (receives Name and Description)
    // Does not return anything
    public static function logError($varTitle, $varDescription)
	{
        // Check Parameters
        if (strlen(trim($varTitle)) && strlen(trim($varDescription)))
		{
            $instanse 	= self::getInstance();
            $msg 		= "ERROR:\n Address: ".$_SERVER['REQUEST_URI']."\n".
            'Date: '.date('d/m/Y H:i:s')."\n".$varTitle."\n".$varDescription."\n";
            array_push($instanse->_messages,$msg);
            if($instanse->_settings['toLog'])
			{
                file_put_contents($instanse->_settings['logPath'],$msg."\n",FILE_APPEND);
            }
        }
    }

    // Show Error Messages
    // Returns the Error Message Output (in HTML format)
    public static function showErrorMessages()
	{
        $instanse 	= self::getInstance();
        $tsize 		= sizeof($instanse->_messages);
        if ($tsize > 0)
		{
            $output = '';
            for ($i = 0; $i < $tsize; $i++)
			{
                $output .= nl2br($instanse->_messages[$i]);
            }
            return '<div style="background-color:#f0f0f0; width:500px;">'.$output.'</div>';
        }
        return '';
    }

    //general private function for sending mails. Not for use outside of class
    // Does not return anything
    private static function mailError($subject, $message, $priority)
	{
        $pstatus = array('1' => 'High', '2' => 'Medium', '3' => 'Low');
        $headers = 'MIME-Version: 1.0'."\n";
        $headers .= 'X-Priority: '.$priority."\n";
        $headers .= 'X-Mailer: PHP mailer (v0.1)'."\n";
        $headers .= 'X-MSMail-Priority: '.$pstatus[$priority]."\n";
        $headers .= 'From: "Venginse mailer" <norepeat@'.$_SERVER['HTTP_HOST'].'>'."\n";
        mail(self::getInstance()->_settings['adminMail'], $subject, $message, $headers);
    }
}
?>