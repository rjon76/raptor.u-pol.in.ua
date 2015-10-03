<?php
class Event
{
    public static $events = array();
        
    public static function fire($event, $args = array())
    {
        if(isset(self::$events[$event]))
        {
            foreach(self::$events[$event] as $func)
            {
                call_user_func_array($func, $args);
            }
        }
    }
    
    public static function register($event, Closure $func)
    {
        self::$events[$event][] = $func;
    }
}

Event::register('error', function($args = array('to'=>null, 'text'=>null,'subject'=>null)){
    include_once(ENGINE_PATH.'class/classMail.php');
    $subject = isset($args['subject']) ? $args['subject'] : "[error] {$_SERVER['HTTP_HOST']} ";
    $mail = new SMTP_Mail;
    $mail->add_text($args['text']);
    $mail->build_message(); 
    
    $to = isset($args['to']) ? $args['to'] : 'garbagecat76@gmail.com';
    
    @$mail->send($to, 'noreaply@'.$_SERVER['HTTP_HOST'], $subject);

 
}); 

?>