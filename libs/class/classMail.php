<?php 
function _HMAC_MD5($key, $data)
{
	if (strlen($key) > 64) {
		$key = pack('H32', md5($key));
	}

	if (strlen($key) < 64) {
		$key = str_pad($key, 64, chr(0));
	}

	$k_ipad = substr($key, 0, 64) ^ str_repeat(chr(0x36), 64);
	$k_opad = substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64);

	$inner  = pack('H32', md5($k_ipad . $data));
	$digest = md5($k_opad . $inner);

	return $digest;
}

class SMTP_Mail
{
	var $email_from			= '';
	var $email_to			= '';
	
	var $socket 	= null;
	var $timeout 	= 10.0;

	var $port		= 25;
	var $host		= 'localhost';
	
	var $user		= '';
	var $pass		= '';
	
	var $from		= '';
	var $mail_to	= '';
	
	var $mime		= '';
	var $headers	= '';
	var $html		= '';
	var $multipart 	= '';
	var $plain_text	= '';
	
	var $parts		= array();

    var $auth_methods = array('DIGEST-MD5', 'CRAM-MD5', 'LOGIN', 'PLAIN');
	
	var $smtp_params= array();

	function SMTP_Mail($host="localhost", $user="", $pass="")
	{
		$this->host = $host;
		$this->user = $user;		
		$this->pass = $pass;		
		
		$this->clearAll();
	}
	
	function send($to, $from, $subject)
	{
		$this->from		= $from;
		$this->mail_to	= $to;
		$this->subject	= $subject;
		if ($this->host=='localhost')
			return mail($this->mail_to, $this->subject, $this->getData(), $this->headers);	
		else
		{
			$this->connect();
			$this->parse_response(220);
			$this->sendHello();
			$this->auth();
			$this->sendFrom();
			$this->sendTo();
			$this->sendData();
			$this->sendQuit();
		}
	}
	
	function readLine()
	{
		if(!$this->socket)
			$this->error('Íå óñòàíîâëåíî ñîåäèíåíèå');

		$text = fread($this->socket, 4096);
		$text = trim($text);
		$line = explode("\n", $text);

		if(count($line) == 1 && substr($text, 3, 1) == "-")
			return $this->readLine();
		
        return $line;
	}
	
	function parse_response($_code)
	{
		$lines = $this->readLine();
		$this->smtp_params = array();

		foreach($lines as $line)
		{
			$code = (int) substr($line, 0, 3);	
			$text = substr($line, 4);
			$text = trim($text);
			
			if($code > 400)
				$this->error($text);

			if($code != $_code)
				$this->error('Íåîæèäàííûé îòâåò ñåðâåðà');
	
	
			$params = explode(' ', $text);
			foreach($params as $param)
				$this->smtp_params[] = trim($param);
		}
	}
	
	function sendHello()
	{

		$this->put('EHLO', $_SERVER['SERVER_NAME']);	
		$this->parse_response(250);
	}
	
	function auth()
	{
		if(array_search('AUTH', $this->smtp_params) === false)
			return;

		foreach($this->auth_methods as $method)
		{
			if(!array_search($method, $this->smtp_params))
				continue;
				
			switch($method)
			{
				case 'LOGIN':
					$this->auth_PLANE();
					break;
				case 'CRAM-MD5':
					$this->auth_CRAM_MD5();
					return;
				case 'PLAIN':
					$this->auth_PLANE();
					return;
				case 'DIGEST-MD5':
					break;
			}
		}
	}
	
	function getFrom()
	{
		return "FROM: ".$this->from;
	}
	function sendFrom()
	{
		$this->put('MAIL', $this->getFrom());
		$this->parse_response(250);
	}
	function getTo()
	{
		return "TO: ".$this->mail_to;
	}	
	function sendTo()
	{
		$this->put('RCPT', $this->getTo());
		$this->parse_response(250);
	}
	
	function getData()
	{
//		$this->headers .= "To: $this->mail_to\r\nFrom: $this->from\r\nReply-to: $this->from\r\nSubject: $this->subject\r\n";
		$this->headers .= "From: $this->from\r\nReply-to: $this->from\r\nSubject: $this->subject\r\n";		
		$text = "$this->headers\r\n$this->mime\r\n.";
		return $text;
	}
	function sendData()
	{
		$this->put('DATA');
		$this->parse_response(354);

		$this->put($this->getData());
		$this->parse_response(250);
	}
	
	function sendQuit()
	{
		$this->put('QUIT');
	}

	function auth_PLANE()
	{
		$this->put('AUTH', 'LOGIN');
		$this->parse_response(334);

		$auth_str = base64_encode($this->user);
		$this->put($auth_str);
		$this->parse_response(334);

		$auth_str = base64_encode($this->pass);
		$this->put($auth_str);
		$this->parse_response(235);
	}

	function auth_CRAM_MD5()
	{
		$this->put('AUTH', 'CRAM-MD5');
		$this->parse_response(334);
		
        $key 	= base64_decode($this->smtp_params[0]);
		$auth_str = base64_encode($this->user . ' ' ._HMAC_MD5($this->pass, $key));
		$this->put($auth_str);
		$this->parse_response(235);
	}
	
	function connect()
	{
		$this->socket = fsockopen($this->host, $this->port, $errno, $errmsg, $this->timeout);
		if(!$this->socket && isset($php_errormsg))
			$this->error($php_errormsg);
			
		stream_set_blocking($this->socket, true);
	}

	function put($command, $arguments = '')
	{
		if($arguments == '')		
			fwrite($this->socket, "$command\r\n");
		else
			fwrite($this->socket, "$command $arguments\r\n");
	}

	function error($errmsg)
	{
		die("<font color='red'><b>Error:</b> $errmsg</font><br>");
	}
	
	function clearAll()
	{
		$this->html 		= '';
		$this->plain_text	= '';
		$this->multipart 	= '';
		$this->headers 		= '';
		$this->mime			= '';
		$this->parts		= array();
	}
	
	function add_html($html="") 
	{ 
    	$this->html.=$html; 
	} 
	
	function add_text($text = '')
	{
		$this->plain_text .= $text;
	}

	function build_html($orig_boundary,$kod='utf-8') { 
		if($this->html == '') 
			return;
			
		$this->multipart.="--$orig_boundary\r\n"; 
		if ($kod=='w' || $kod=='win' || $kod=='windows-1251') $kod='windows-1251';
		else $kod='utf-8';
		$this->multipart.="Content-Type: text/html; charset=$kod\r\n"; 
		$this->multipart.="Content-Transfer-Encoding: Quot-Printed\r\n\r\n"; 
		$this->multipart.="$this->html\r\n\r\n"; 
	} 
	
	function build_text($orig_boundary,$kod='utf-8') { 
		if($this->plain_text == '') 
			return;
			
		$this->multipart.="--$orig_boundary\r\n"; 
		if ($kod=='w' || $kod=='win' || $kod=='windows-1251') $kod='windows-1251';
		else $kod='utf-8';
		$this->multipart.="Content-Type: text/plain; charset=$kod\r\n"; 
		$this->multipart.="Content-Transfer-Encoding: Quot-Printed\r\n\r\n"; 
		$this->multipart.=$this->plain_text."\r\n\r\n"; 
	} 
	
	function build_message($kod = 'utf-8') 
	{ 
		$boundary="=_".md5(uniqid(time())); 
		$this->headers.="MIME-Version: 1.0\r\n"; 
		$this->headers.="Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"; 
		$this->multipart=""; 
		$this->multipart.="This is a MIME encoded message.\r\n\r\n"; 
		$this->build_html($boundary,$kod); 
		$this->build_text($boundary,$kod); 
		for ($i=(count($this->parts)-1); $i>=0; $i--)
		  $this->multipart.="--$boundary\r\n".$this->build_part($i); 
		$this->mime = "$this->multipart--$boundary--\r\n"; 
	}
	
	function add_attachment($path="", $name = "", $c_type="application/octet-stream") 
	{ 
		if (!file_exists($path.$name)) {
		 // print "File $path.$name dosn't exist.";
		  return;
		}
		$fp=fopen($path.$name,"r");
		if (!$fp) {
		//  print "File $path.$name coudn't be read.";
		  return;
		} 
		$file=fread($fp, filesize($path.$name));
		fclose($fp);
		$this->parts[] = array("body"=>$file, "name"=>$name,"c_type"=>$c_type); 
	} 
	
	function build_part($i) 
	{ 
		$message_part=""; 
		$message_part.="Content-Type: ".$this->parts[$i]["c_type"]; 
		if ($this->parts[$i]["name"]!="") 
		   $message_part.="; name = \"".$this->parts[$i]["name"]."\"\n"; 
		else 
		   $message_part.="\n"; 
		$message_part.="Content-Transfer-Encoding: base64\n"; 
		$message_part.="Content-Disposition: attachment; filename = \"".
		   $this->parts[$i]["name"]."\"\n\n"; 
		$message_part.=chunk_split(base64_encode($this->parts[$i]["body"]))."\n";
		return $message_part; 
	} 
};

//$mail = new SMTP_Mail($options->mail->smtp, $options->mail->name, $options->mail->passwd);
?>
