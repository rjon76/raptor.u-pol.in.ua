<?php
class ClientScript {

    # PRIVATE VARIABLES
    const POS_HEAD  = 0;
    const POS_BEGIN = 1;
    const POS_END   = 2;
    
    public static $script = array();
    public static $scriptFiles = array();
    public static $cssFiles = array();
    public static $css = array();

    # PUBLIC VARIABLES
    public function __construct() {
        
    }


    public function __destruct() {
        
    }
    /**
     * POS_HEAD=0, POS_BEGIN=1, POS_END=2
     */
    public function registerScript($args=array('content'=>null,'position'=>2,'type'=>'ready')){
        
        if (!isset($args['type']))
        {
            $args['type'] = 'ready';
        }
        
        if (!isset($args['position']))
        {
            $args['position'] = 2;
        }
        
        self::$script[$args['position']][] = array('type'=>$args['type'], 'content'=>$args['content'], 'position'=>$args['position']);
        
    }
    public function registerScriptFile($args=array('url'=>null,'htmlOptions'=>array())){
        
        self::$scriptFiles[] = array('url'=>$args['url'], 'htmlOptions'=>$args['htmlOptions']);
        
    }
    public function registerCss($args=array('content'=>null,'description'=>null, 'media'=>'')){
        
        
        self::$css[] = array('content'=>$args['content'],'description'=>$args['description'], 'media'=>$args['media']);
        
    }
    public function registerCssFile($args=array('url'=>null,'media'=>'')){
                
        self::$cssFiles[] = array('url'=>$args['url'],'media'=>$args['media']);
        
    }
    public function getScripts($args = array('position'=>2)){
        
        if (!isset($args['position']))
        {
            $args['position'] = 2;
        }

        if (isset(self::$script[$args['position']]) && count(self::$script[$args['position']]))
        {
            
            //var_dump(__FILE__);
            
            $out    = '<script type="text/javascript">';
            $load   = "";
            $ready  = "
    if (window.jQuery) {      
        $(document).ready(function() {";
    
    
            foreach(self::$script[$args['position']] as $script){
                
                $script['content'] = str_replace('[[','{',str_replace(']]','}',$script['content']));
                
                switch($script['type']){
                case "ready":
                    $ready .= $script['content'];
                    break;
                    
                default:
                    $load .= $script['content'];
                    break;
                }  
            }
            
            $ready .= "
        }); 
    }";
            
            $out = $out.$load.$ready;
            $out .= '</script>';
            
            echo $out;
        
        } 
    }
    public function getScriptFiles(){
        
        if ( count(self::$scriptFiles))
        {            
            foreach(self::$scriptFiles as $file){
                 $out    .= "<script type=\"text/javascript\"";
                 
                 if (count($file['htmlOptions'])){
                    foreach($file['htmlOptions'] as $options){
                        $out .= ' '.$options;
                    }
                 }
                 
                 $out    .= " src=\"{$file['url']}\"></script>";
            }
            
            echo $out;
        
        }
    }
    public function getCss(){

        if (count(self::$css))
        {            
            
            $out = "<style type=\"text/css\">";
            foreach(self::$css as $css){
                
                $css['content'] = str_replace('[[','{',str_replace(']]','}',$css['content']));
                
                if (isset($css['description'])){
                   $out .= "\r" . "/* {$css['description']} */" . "\r"; 
                }
                $out .= $css['content'];
            }
            
            $out    .= "</style>";
            echo $out;
        } 
    }
    public function getCssFiles(){
        
        if (count(self::$cssFiles))
        {                
            foreach(self::$cssFiles as $file){
                 $out .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$file['url']}\" />";
            }
            
            echo $out;
        }
    }
    public function isScriptRegistered($position){

        if (isset(self::$script[$position]) && count(self::$script[$position]))
        {
            return true;
        }
        return false;
 
    }
    public function isScriptFileRegistered($url, $position){

        if (isset(self::$script[$position]) && count(self::$script[$position]))
        {
            return true;
        }
        return false;
 
    }
}
?>