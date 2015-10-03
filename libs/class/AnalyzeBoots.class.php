<?php

class AnalyzeBoots{
    
    private $HTTP_USER_AGENT;
    private $REMOTE_ADDR;
    private $analyze = false;
    private $db;
    
    
    public function __construct($path=null){
                
        $this->REMOTE_ADDR = self::getIp();
        $this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

        if (isset($path))
        {
            try{
                $this->db = new PDO('sqlite:'.$path); 
                $this->db->exec('CREATE TABLE IF NOT EXISTS boots 
                    ( 
                      b_id INTEGER primary key autoincrement, 
                      b_ip VARCHAR(30) NOT NULL, 
                      b_agent VARCHAR(250) DEFAULT NULL, 
                      b_ftime INTEGER NOT NULL, 
                      b_ltime INTEGER NOT NULL, 
                      b_etime INTEGER NOT NULL, 
                      b_count INTEGER NOT NULL, 
                      b_block INTEGER DEFAULT 0, 
                      b_block_last INTEGER NOT NULL DEFAULT 0, 
                      b_report INTEGER DEFAULT 0 
                    )'); 
                
                if (!preg_match("/(Googlebot|Slurp|Yahoo! Slurp|MSNBot|Teoma|Scooter|ia_archiver|Lycos|Yandex|StackRambler|Mail.Ru|Aport|WebAlta|Googlebot-Mobile|Googlebot-Image|Mediapartners-Google|Adsbot-Google|MSNBot-NewsBlogs|MSNBot-Products|MSNBot-Media)/i",$this->HTTP_USER_AGENT))
                {
                    $this->analyze = true;
                }
                
            }
            catch (PDOException $e) {
                die($e->getMessage());
            }

        }
    }
    
    public function init(){

        if ($this->analyze)
        {
            self::func_blocked();
        }
        
    }

    protected function func_blocked()
    {

    
            $ipArray = array('192.168.27.67');

            if (isset($this->REMOTE_ADDR) && !in_array($this->REMOTE_ADDR, $ipArray))
            {
                $time = time();
                
                $endTime = $time + 60;
                $allcount = 25;
            
            	$block = '<div style="text-align:center; background-color: #ccc; margin:150px auto; padding:30px;border: 2px double #efefef;">
                             Your ip address, '.$this->REMOTE_ADDR.' was blocked. <br/> <br/> 
                             If it happened by mistake, please email our support team at support@'.$_SERVER['HTTP_HOST'].'</div>';

                $st = $this->db->query("SELECT * FROM boots WHERE b_ip='$this->REMOTE_ADDR'"); 
                
                $results = $st->fetchAll();
                
                if (count($results) > 0)
                {
                    $row = $results[0];
                    
                    if ($row['b_block'] == '1')
                    {
                            
							if ($row['b_report'] == '0')
                            {
                                $headers = 'MIME-Version: 1.0'."\n";
                                $headers .= 'X-Priority: 2'."\n";
                                $headers .= 'X-Mailer: PHP mailer (v0.1)'."\n";
                                $headers .= 'X-MSMail-Priority: Medium'."\n";
                                $headers .= 'Content-type:text/html; charset = utf-8'."\n";
                                $headers .= "From: {$_SERVER['HTTP_HOST']} <noreply@{$_SERVER['HTTP_HOST']}>"."\n";
                
                                
                                $subj = "Bots attack on {$_SERVER['HTTP_HOST']}";
                                $messg = "The number of connections per 60 seconds exceeds the limit ($allcount) <br/><br/>";
                                $messg .='IP address: '.$row['b_ip'].'<br/>
                                            User-agent: '.$row['b_agent'].'<br/>
                                                Date of registration: '.date("d-m-Y H:i:s",$row['b_ftime']).'<br/>
                                                    Date of last visit: '.date("d-m-Y H:i:s",$row['b_ltime']).'<br/>';
    
                                if (mail('garbagecat76@gmail.com', $subj, $messg, $headers))
                                {
                                    $this->db->exec("UPDATE boots SET b_report='1' WHERE b_ip='$this->REMOTE_ADDR'");
                                }
    
                            }
                            
                            header("HTTP/1.1 403 Forbidden");
                            echo $block;
                            exit();
              
                    }
                    else
                    {
                            if ($row['b_count'] > $allcount)
                            {
                                 if ( $row['b_etime'] > $time)
                                 {
                                     $this->db->exec("UPDATE boots SET b_block = '1' WHERE b_ip ='$this->REMOTE_ADDR'");
                                     header("HTTP/1.1 403 Forbidden");
                                     echo $block;
                                     exit();
                                 }
                                 else
                                 {
                                    $this->db->exec("UPDATE boots SET b_ltime='$time', b_etime='$endTime', b_count='1' WHERE b_ip ='$this->REMOTE_ADDR'");
                                 }
                                
                            }
                            else
                            {
                                $this->db->exec("UPDATE boots SET b_count=b_count+1, b_ltime = '$time' WHERE b_ip ='$this->REMOTE_ADDR'");
                            }      
                      }
                }
                else
                {
                    $this->db->exec("INSERT INTO boots (b_ip, b_agent, b_ftime, b_ltime, b_etime, b_count) VALUES ('".$this->REMOTE_ADDR."', '".$this->HTTP_USER_AGENT."', '".$time."', '".$time."', '".$endTime."', '1')");
                }
                
            }
    }

    private function getIp() 
    {
        $realip = null;
        
        if(isset($HTTP_SERVER_VARS)) 
        {
            if(isset($HTTP_SERVER_VARS[HTTP_X_FORWARDED_FOR])) 
            {
                $realip = $HTTP_SERVER_VARS[HTTP_X_FORWARDED_FOR];
            }
            elseif(isset($HTTP_SERVER_VARS[HTTP_CLIENT_IP])) 
            {
                $realip = $HTTP_SERVER_VARS[HTTP_CLIENT_IP];
            }
            else
            {
                $realip = $HTTP_SERVER_VARS[REMOTE_ADDR];
            }
        }
        else
        {
            if(getenv(HTTP_X_FORWARDED_FOR) ) 
            {
                $realip = getenv( HTTP_X_FORWARDED_FOR );
            }
            elseif(getenv(HTTP_CLIENT_IP) ) 
            {
                $realip = getenv( HTTP_CLIENT_IP );
            }
            else
            {
                $realip = getenv( REMOTE_ADDR );
            }
        }
    
        return $realip;
    }
}
?>
