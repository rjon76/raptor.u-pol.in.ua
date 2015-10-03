<?php
class AdminSection {

    # PRIVATE VARIABLES

    public static $active = false;
    public static $debug = false;
    public static $edit = false;
    public $sites;
    public $page_id;
    public $site_id;
    public static $data = array();
    public $localPath;
    public $template_dir;
    public $localstrings;
    public $lstrings;
    public $blocks;
    public $page_css;
    public $page_js;

    public function __construct($_edit=true, $_debug=true) {
        
        if (defined('WWW2')){
            if(WWW2){self::$active = true;}
        }
        
        if (isset($_GET['debug']) && self::$active && $_debug){
            self::$debug = true;
        }
        
        if (isset($_GET['edit']) && self::$active && $_edit){
            self::$edit = true;
        }

    }

    public function init() {

        if (self::$active)
        {
            
            //search in text to blocks and in *.tpl lstrings
            $this->initLstrings();
            
            //search in text to blocks extensions
            $this->initExtensions();
            
            //search in text to blocks images
            $this->initImages();
            
            //search in text to blocks links
            $this->initLinks();
            
            //search in blocks images, links, globalExt, localExt, name variables to blocks
            $this->initBlocks();
            
            $this->initJsCss();
            
            if (self::$debug){
                $this->debug();
            }
            
            if (self::$edit){
                $this->edit();
            }
        }
    }

    public function __destruct() {
        if (self::$active && self::$edit)
        {
            self::showData();
        }
    }
    
    public function getCookie($cookie_name) {
	   return (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : false);
    }
    
    public function setCookie($cookie_name,$value, $days=365) {
	    setcookie($cookie_name, $value, time()+60*60*24*$days, '/', $_SERVER['HTTP_HOST'], false);
    } 
    public function isEdit(){
        if (self::$edit){
            return true;
        }
        return false;
    }
    public function isDebug(){
        if (self::$debug){
            return true;
        }
        return false;
    }
    public function isActive(){
        if (self::$active){
            return true;
        }
        return false;
    }
    public static function getXadmin(){
       
        return $_SERVER['HTTP_HOST'].'/admin';
    }
    public static function getHost($port=false,$alias=false){
        
        return ($port ? 'http://' : '').$_SERVER['HTTP_HOST'].($alias ? $_SERVER['REQUEST_URI'] : '');

    }
    private function redirect(){
        if(self::$active && self::$edit){
            
            $url = $_SERVER['REQUEST_URI'];
            $get = strpos($url,'?');

            
            if ($get !== false){
                if(!isset($_GET['edit'])){
                    header('Location: http://'.$_SERVER['HTTP_HOST'].$url.'&edit=true');
                }
            }
            else{
                header('Location: http://'.$_SERVER['HTTP_HOST'].$url.'?edit=true');
            }
        }
        
    }
    
    public function debug(){

                echo '<div class="pre-scrollable" style="background-color: rgba(255,255,255,1);border: 1px solid #ccc;padding:10px;max-height: 200px;overflow-y: scroll;color:#000;z-index:100;position:fixed; bottom:0;">';
    
                    self::getDebug('Output value of the variable $_POST',$_POST);
                    self::getDebug('Output value of the variable $_GET',$_GET);
                    self::getDebug('Output value of the variable $_SERVER',$_SERVER);
                    //self::preDebug('Conclusion existing variables, get_defined_vars()',get_defined_vars());
                    self::getDebug('The conclusion of the class declaration, get_declared_classes()',get_declared_classes(),true,true);
                    //self::preDebug('The conclusion of the functions declaration, get_defined_functions()',get_defined_functions());
                    self::getDebug('Output value of the variable $GLOBAL',$GLOBAL);
                    //self::preDebug('Output value of the constants',get_defined_constants(),false,true);

                echo '</div>';

    }
    public function edit()
    {
                $host = self::getXadmin();
                
                if(VBox::isExist('ClientScript')){ 
                    
                    if (!in_array('jquery.js',$this->page_js)){
                        VBox::get('ClientScript')->registerScriptFile($args=array('url'=>"//$host/js/jquery.js"));
                    }
                    
                    VBox::get('ClientScript')->registerScriptFile($args=array('url'=>"//$host/js/adminSection.js"));
                    VBox::get('ClientScript')->registerCssFile($args=array('url'=>"//$host/styles/adminSection.css"));
                    
                    VBox::get('ClientScript')->getScriptFiles();
                    VBox::get('ClientScript')->getCssFiles();
                }   


                
                
                echo '<div id="adminPanel" class="hidden-xs"><a class="admin-btn-close" href="#">Admin panel </a><div class="inner" style="display:none;">';
                echo "<h4><a class=\"link\" href=\"http://".$host."/pages/editpage/site/{$this->site_id}/page/{$this->page_id}/\" target=\"_blank\"><strong>Click for edit page</strong></a></h4>";
                echo "<h3>Site / Page info</h3>";
                echo "<ul class=\"items\">";
                echo "<li><b>Site ID / Page ID:</b> {$this->site_id} / {$this->page_id}</li>";
                echo "<li><b>Cached domain:</b> ".VBox::get('ConstData')->getConst('cachedDomain')."</li>";
                echo "<li><b>Real domain:</b> ".VBox::get('ConstData')->getConst('realDomain')."</li>";
                echo "<li><b>Use js/css min:</b> ".((bool)VBox::get('ConstData')->getConst('use_min') ? 'true': 'false')."</li>";
                echo "</ul>";
                echo "<h3>Functions</h3>";
                echo "<ul class=\"items\">";
                echo "<li>- <a data-type=\"console\" class=\"toggle\" href=\"#\"><span>show</span> consoles</a> <span class=\"count\"></span></li>";
                echo "<li>- <a data-type=\"ext\" class=\"toggle\" href=\"#\"><span>show</span> extensions</a></li>";
                echo "<li>- <a data-type=\"lstring\" class=\"toggle\" href=\"#\"><span>show</span> lstrings</a></li>";
                echo "<li>- <a data-type=\"image\" class=\"toggle\" href=\"#\"><span>show</span> images</a></li>";
                echo "<li>- <a data-type=\"link\" class=\"toggle\" href=\"#\"><span>show</span> links</a></li>";
                echo "<li>- <a data-type=\"variable\" class=\"toggle\" href=\"#\"><span>show</span> \$vars.variables</a></li>";
                echo "<li>- <a data-type=\"js\" class=\"toggle\" href=\"#\"><span>show</span> *.js files</a></li>";
                echo "<li>- <a data-type=\"css\" class=\"toggle\" href=\"#\"><span>show</span> *.css files</a></li>";
                echo "<h3>Select site for edit</h3>";
                echo '<form method="get" action="//'.$host.'/pages/setsite/" target="_blank"><select name="site_id" onchange="this.form.submit()">';
                foreach($this->sites as $site)
                {  
                    if ($this->site_id === $site['s_id'])
                    {
                        $selected = " selected=\"selected\"";
                    }
                    else{
                        $selected = "";
                    }
                    
                    echo '<option value="'.$site['s_id'].'"'.$selected.'>'.$site['s_hostname'].'</option>';
                }
                echo "</select></form>";        
                echo '</div><div style="clear: both;"></div></div>'; 
    }

    public function setData($data=null,$type=null, $title=null,$link=null){
        if (self::$edit)
        {
            $host = self::getXadmin();

            if ($type == "lstring")
            {
                if(!isset($this->localstrings[$data])){
                    
                    $data = '<a title="not added" href="//'.$host.'/pages/addlstring/site/'.$this->site_id.'/nick/'.$data.'/" target="_blank">'.$data.'</a>';
                    $data = '<span class="unset">'.$data.'</span>'; 
                }
                elseif(empty($this->localstrings[$data])){
                    $data = '<a title="empty" href="//'.$host.'/pages/editlstring/site/'.$this->site_id.'/search/'.$data.'/" target="_blank">'.$data.'</a>';
                    $data = '<span class="empty">'.$data.'</span>';
                }
                else{
                    $data = '<a href="//'.$host.'/pages/listlstring/site/'.$this->site_id.'/search/'.$data.'/" target="_blank">'.$data.'</a>';
                    $data = '<span>'.$data.'</span>';
                } 
            }
            
            
            if (isset($link)){
                $data = sprintf($link, $data);
            }

            if (isset($title)){
                $key = substr(md5($title),0,10);

                if (!in_array($data,self::$data[$type][$key]['fields'])){
                    self::$data[$type][$key]['title'] = $title; 
                    self::$data[$type][$key]['fields'][] = $data; 
                }
            }
            else{
                if (!in_array($data,self::$data[$type])){
                    self::$data[$type][] = $data; 
                }
            }
             
        }
    }
    private function initLstrings(){

        foreach($this->blocks as $data)
        {
            if (!isset($data['files_to_include'])){
                self::StrToInclude($data);
            }
        }
        foreach($this->blocks as $file)
        {
            if (isset($file['files_to_include'])){
                self::filesToInclude($file['files_to_include']);
            }
        } 
        
    }
    private function initExtensions(){

        foreach($this->blocks as $data)
        {
            if (!isset($data['files_to_include'])){
                self::StrToInclude($data,'ext');
            }
        } 
    }
    private function initImages(){

        foreach($this->blocks as $data)
        {
            if (!isset($data['files_to_include'])){
                self::StrToInclude($data,'image');
            }
        } 
    }
    private function initLinks(){

        foreach($this->blocks as $data)
        {
            if (!isset($data['files_to_include'])){
                self::StrToInclude($data,'link');
            }
        } 
    }
    private function initBlocks(){
                
        $host = self::getXadmin();
        foreach($this->blocks as $args)
        {
                if (isset($args['edit_params'])){
                        
                        $category = $args['edit_params']['block'].' <br/> [ file: templates/'.$args['edit_params']['file'].' ]';
                        
                        foreach($args['edit_params']['fields'] as $params)
                        {  

                            switch($params['type']){
                                
                                case "S":
                                    $text = '';
                                    $text = strip_tags(substr($params['value'],0,255)).(strlen($params['value'])>255 ? '...':'');
                                        break;
                                        
                                case "A":
                                case "I":
                                case "L":
                                case "W":
                                    $text = array();
                                    
                                    ob_start();
                                    echo "<pre>";
                                    print_r($params['value']);
                                    echo "</pre>";
                                    $text = ob_get_contents();
                                    ob_end_clean(); 
                                    
                                    if ($src = $this->getStrByKey($params['value'],'src')){
                                        $this->setData($src,'image'); 
                                    }
                                    if ($src = $this->getStrByKey($params['value'],'href')){
                                        $this->setData($src,'link'); 
                                    }
                                    
                                    
                                        break;
                                case "G": 
                                        $text = '';
                                        $value = trim($params['value']);
                                        $funcData = array();
                                        if(strlen($value)) {
                                            $funcData = explode('|', $value);
                                        }
                    
                                        $defData = explode('|', $params['default']);
                    
                                        $func = $defData[0];
                                        $class = $defData[1];
                    
                                        $arg = array();
                    
                                        for($i = 0; $i < count($funcData); $i++) {
                                            $argData = explode(':', $funcData[$i]);
                    
                                            switch($argData[0]) {
                                                case 'n': // variable Name
                                                    $arg[] = $$argData[1];
                                                break;
                                                case 'v': // variable Value
                                                    $arg[] = $argData[1];
                                                break;
                                            }
                                        }
                                        $text = 'globalExt/'.$class.'-&gt;'.$func.'('.implode(',',$arg).')';
                                        $this->setData($text,'ext');

                                        break;
                                case "E": 
                                        $text = '';
                                            $class = $params['name'];
                                            $text = trim($params['value']);
                                            if(!empty($text)) {
                                                $funcData = explode('|', $row['bd_value']);
                                                $arg = array();
                                                for($i = 0; $i < count($funcData); $i++) {
                                                    $argData = explode(':', $funcData[$i]);
                                                    switch($argData[0]) {
                                                    case 'n': // variable Name
                                                        $arg[] = $$argData[1];
                                                    break;
                                                    case 'v': // variable Value
                                                        $arg[] = $argData[1];
                                                    break;
                                                    }
                                                }
                                            }

                                        $text = 'localExt/'.$class.'-&gt;getResult('.implode(',',$arg).')';
                                        $this->setData($text,'ext');
                                        break;
                                
                            }

                            $value = "{$params['name']} ({$params['type']})";
                            
                            //$link = "<a href=\"http://{$host}/pages/editpage/site/{$this->site_id}/page/{$this->page_id}/fs/{$params['fsid']}/\" target=\"_blank\">%s</a>";
                            //$link .='<br/>'.$text.'<br/><br/>';
                            $this->setData($value,'variable',$category);
                        }
                }    
        }   
    }
    private function initJsCss(){

        foreach($this->page_css as $css)
        {
            $link = '<a href="'.$this->getHost(true,false).'/styles/'.$css.'" target="_blank">%s</a>';
            self::setData($css,'css',null,$link);
        } 
        
        foreach($this->page_js as $js)
        {
            $link = '<a href="'.$this->getHost(true,false).'/js/'.$js.'" target="_blank">%s</a>';
            self::setData($js,'js',null,$link);
        } 
    }
    public function filesToInclude($data=null,$type='lstrings'){

            foreach($data as $_file) 
            {
                $file = $this->template_dir.$_file;
                
                if (file_exists($file))
                {
                    $content = file_get_contents($file);
                    if ($type == 'lstrings')
                    {
       					preg_match_all('#(\{\$lstrings.(.*?)\})#', $content, $results);
                        
                        if(count($results[0]))
                        {
    						for ($i=0; $i< count($results[0]); $i++) 
                            {
                                $lstring = '';
                                if (isset($results[2][$i]))
                                {
                                    $lstring = $results[2][$i];
                                    
                                    $length = strpos($lstring,'|');
                                    
                                    if ($length>0)
                                    {
                                       $lstring = substr($lstring,0,$length);
                                    }

                                    $this->setData($lstring,'lstring','[ file: templates/'.$_file.' ]');

                                }
    						}
                        } 
                    }//lstrings
                }  
            }
    }
    
	private function StrToInclude($a,$type='lstrings') 
    {
        if ($type == 'lstrings')
        {
    		if ( is_array($a) ) {
    			foreach ($a as $key=>$node) {
    				if (is_array($node)) {
    					$result[$key] =  $this->StrToInclude($node);
    				} else {
    					preg_match_all('#(\{\$lstrings.(.*?)\})#', $node, $class_metot);
                        if(count($class_metot[0]))
                        {
    						for ($i=0; $i< count($class_metot[0]); $i++) {
                              
                                $lstring = $class_metot[2][$i];
                                if ($lstring){
                                    $this->setData($lstring,'lstring');
                                }
                                
    						}
    					}
    				}
    			}
    		}
        }//lstrings
        
        if ($type == 'ext')
        {
    		if ( is_array($a) ) {
    			foreach ($a as $key=>$node) {
    				if (is_array($node)) {
    					$result[$key] =  $this->StrToInclude($node);
    				} else {
                        preg_match_all('#(\{globalExt (.*?)\})#', $node, $class_metot);
                        if(count($class_metot[0]))
                        {
    						for ($i=0; $i< count($class_metot[0]); $i++) {
    							$ext = $class_metot[2][$i];
                                if ($ext){
                                   $this->setData('globalExt/'.$ext,'ext'); 
                                }
                                
                            
                            }
    					} 
    				}
    			}
    		}
        }//ext
        
        if ($type == 'image')
        {   
    		if (is_array($a) ) {
    			foreach ($a as $key=>$node) {
    				if (is_array($node)) {
    					$result[$key] =  $this->StrToInclude($node);
    				} else {
                        preg_match_all('~<img[^>]*(?<!_mce_)src\s?=\s?([\'"])((?:(?!\1).)*)[^>]*>~i', $node, $class_metot);
                        if(count($class_metot[0]))
                        {
    						for ($i=0; $i< count($class_metot[0]); $i++) {
    							$src = $class_metot[2][$i];
                                if ($src){
                                    $this->setData($src,'image'); 
                                }
                                
                            
                            }
    					} 
    				}
    			}
    		}
        }//image
        if ($type == 'link')
        {   
    		if (is_array($a) ) {
    			foreach ($a as $key=>$node) {
    				if (is_array($node)) {
    					$result[$key] =  $this->StrToInclude($node);
    				} else {
                        preg_match_all('~<a[^>]*(?<!_mce_)href\s?=\s?([\'"])((?:(?!\1).)*)[^>]*>~i', $node, $class_metot);
                        if(count($class_metot[0]))
                        {
    						for ($i=0; $i< count($class_metot[0]); $i++) {
    							$href = $class_metot[2][$i];
                                if ($href){
                                    $this->setData($href,'link'); 
                                }
                                
                            
                            }
    					} 
    				}
    			}
    		}
        }//link
	}

    public function showData(){
        
            if (count(self::$data)>0 && self::$edit)
            {   
                
                echo '<div class="adminBlock hidden-xs" style="display:none;"><h2>To display information</h2>';

                foreach(self::$data as $type=>$items){

                    echo '<div class="inner '.$type.'" style="display:none;">';
                    switch($type){
                        
                        case "ext":
                            echo "<h3 class=\"title\">Extensions on page</h3>";
                            break;
                        
                        case "lstring":
                            echo "<h3 class=\"title\">Lstrings on page</h3>";
                            break;
                            
                        case "image":
                            echo "<h3 class=\"title\">Images on page</h3>";
                            break;
                        case "link":
                            echo "<h3 class=\"title\">Links on page</h3>";
                            break;
                        case "variable":
                            echo "<h3 class=\"title\">\$vars.variables in *.tpl</h3>";
                            break;
                        case "js":
                            echo "<h3 class=\"title\">*.js files on page</h3>";
                            break;
                        case "css":
                            echo "<h3 class=\"title\">*.css files on page</h3>";
                            break;
                        
                    }
                    
                    echo "<ul class=\"items\">";
                    $array=array();
                    
                    //array_multisort($items);
                    
                    foreach($items as $key=>$item)
                    {  
                        $itemli = '- ';
                        if (is_array($item)){
                            echo "<li class=\"parent\"><span class=\"title-parent\">{$item['title']}</span><ul>"; 
                            foreach($item['fields'] as $ls)
                            { 
                                if ($type == "image"){
                                    $ls = '<p align="center"><img src="'.$ls.'" style="max-width:100%"/><br/><span class="description">'.$ls.'</span></p>';
                                    $itemli = '';
                                }
                                if ($type == "link"){
                                    $ls = '<a href="'.$ls.'" target="_blank">'.$ls.'</a>';
                                    $itemli = '';
                                }

                                echo "<li>$itemli $ls</li>"; 
                            }
                            echo "</ul></li>"; 
                        }
                        else{
                            
                            if ($type == "image"){
                                $item = '<p align="center"><img src="'.$item.'" style="max-width:100%"/><br/><span class="description">'.$item.'</span></p>';
                                $itemli = '';
                            }
                            if ($type == "link"){
                                $item = '<a href="'.$item.'" target="_blank">'.$item.'</a>';
                                $itemli = '';
                            }
                            echo "<li>$itemli $item</li>"; 

                        }
                    }
                           
                    echo '</ul></div>'; 
                }
                echo '</div>';
            }
    }
    public function showEdit($args){
    
        if (self::$edit){
                
                $host = self::getXadmin();
                
                if (isset($args['edit_params'])){
    
                        $content = '<div class="adminEditNote" style="display:none;"><a class="admin-btn-close" href="#">console</a><div class="inner" style="display:none;"><dl>';
                        $content .=  "<dt>{$args['edit_params']['block']}</dt>";
                        
                        foreach($args['edit_params']['fields'] as $params)
                        {  
                            $value = '';
                            switch($params['type']){
                                
                                case "S":
                                
                                    $value = strip_tags(substr($params['value'],0,255)).(strlen($params['value'])>255 ? '...':'');
                                        break;
                                        
                                case "A":
                                case "I":
                                case "L":
                                case "W":
                                    $value = array();
                                    
                                    ob_start();
                                    echo "<pre>";
                                    print_r($params['value']);
                                    echo "</pre>";
                                    $value = ob_get_contents();
                                    ob_end_clean(); 

                                        break;
                                case "G": 
                                        $value = trim($params['value']);
                                        $funcData = array();
                                        if(strlen($value)) {
                                            $funcData = explode('|', $value);
                                        }
                    
                                        $defData = explode('|', $params['default']);
                    
                                        $func = $defData[0];
                                        $class = $defData[1];
                    
                                        $args = array();
                    
                                        for($i = 0; $i < count($funcData); $i++) {
                                            $argData = explode(':', $funcData[$i]);
                    
                                            switch($argData[0]) {
                                                case 'n': // variable Name
                                                    $args[] = $$argData[1];
                                                break;
                                                case 'v': // variable Value
                                                    $args[] = $argData[1];
                                                break;
                                            }
                                        }
                                        $value = 'globalExt/'.$class.'-&gt;'.$func.'('.implode(',',$args).')';

                                        break;
                                case "E": 
                                            $class = $params['name'];
                                            $value = trim($params['value']);
                    
                                            if(!empty($value)) {
                                                $funcData = explode('|', $row['bd_value']);
                                                $args = array();
                                                for($i = 0; $i < count($funcData); $i++) {
                                                    $argData = explode(':', $funcData[$i]);
                                                    switch($argData[0]) {
                                                    case 'n': // variable Name
                                                        $args[] = $$argData[1];
                                                    break;
                                                    case 'v': // variable Value
                                                        $args[] = $argData[1];
                                                    break;
                                                    }
                                                }
                                            }
                                        $value = 'localExt/'.$class.'-&gt;getResult('.implode(',',$args).')';

                                        break;
                                
                            }
                            $content .= "<dd><span class=\"variable\"><a href=\"http://{$host}/pages/editpage/site/{$this->site_id}/page/{$this->page_id}/fs/{$params['fsid']}/\" target=\"_blank\">edit - {$params['name']} ({$params['type']})</a> <span class=\"text\">$value</span></span></dd>";
                        }
                        
                        $content .= '</dl></div></div>';
                        
                        echo $content;
    
                }    
        }    
    }
    /**
     * Function formatting output array in tabular form
     * @data 06.01.2015 
     * @author italiano
     */
    private function getDebug($title=null,$args,$inline=false,$dump=false)
    {
        $table = '<table style="border-collapse: collapse;border: 1px solid #ddd;margin-bottom: 20px;max-width: 100%;width: 100%;">';
        
        if (isset($title)) {
            $table .= '<thead><tr>';
            $table .= '<th colspan="2" class="info" style="background-color: #d9edf7;padding: 8px;">'.$title.'</th>';
            $table .= '</tr></thead>';
        }
        
        $table .='<tbody>';
        
        if (count($args))
        {
                if (!$inline)
                {
                    foreach($args as $key=>$result)
                    {
                        if ($dump){
                            ob_start();
                            var_dump($result);
                            $result = ob_get_contents();
                            ob_end_clean(); 
                        }    
                        
                        $table .= '<tr><td width="35%" style="border: 1px solid #ddd;padding: 8px;">'.$key.'</td><td style="border: 1px solid #ddd;padding: 8px;">'.$result.'</td></tr>';
                    } 
                }
                else {
                    $table .= '<tr><td colspan="2" style="border: 1px solid #ddd;padding: 8px;">'.implode(', ', $args).'</td></tr>';
                }
        }
        else
        {
            $table .= '<tr><td colspan="2" style="border: 1px solid #ddd;padding: 8px;">No data for display...</td></tr>';
        }
        
        $table .='</tbody></table>';
        
        echo $table;
    }
    
    public function snippet($text, $maxchar=200, $allowTags=false, $onlyText=true){
                
        (!$allowTags) ? $allowTags = '<p><br>' :    $allowTags = $allowTags.'<p><br>';
        (!$onlyText) ? $text = strip_tags($text, $allowTags) : $text = strip_tags($text);
        
        if(iconv_strlen($text, 'utf-8') > $maxchar ){
            $text = iconv_substr($text, 0, $maxchar, 'utf-8' );
            $text = preg_replace('@(.*)\s[^\s]*$@s', '\\1...', $text);
        }
        $text = trim($text);
        
        return $text;
    }
    
    private function getStrByKey($array, $fkey){
            foreach ($array as $key => $item) {
                    if ($fkey == $key) {
                        if (is_array($item)) {
                                $this->getStrByKey($item, $fkey);
                            }
                            else{
                                return $item;
                            }
                    } 
                    else{
                        if (is_array($item)) {
                            $this->getStrByKey($item, $fkey);
                        }
                    }
            }
            
            return false;
    }

}
?>