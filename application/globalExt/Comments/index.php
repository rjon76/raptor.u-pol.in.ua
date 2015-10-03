<?php

class Comments {

    public function __construct() {
        
    }
    
    public function __destruct() {

    }
	   
    /**
     *function return array coments from Disqus
     *@var forum, category id
     *@return array()
    */
    public function GetDisqusComments($args = null) 
    {

        $publickKey = 'BZmqcH7MfFiBsNdz1h0dshdrZEEDbLAk1BMdhUHF9de5aDZeMXXLbzZoRswMdJSX';
        $secretKey = 'Dt47Gxs2IXwP4F3xoDRWYJ4yStQcWXgpmZnTfEYPzEMYPO2mF7Qt7Ujr7VaUgmfM';
        $apiKey = "E8Uh5l5fHZ6gD8U3KycjAIAk46f68Zw7C6eW8WSjZvCLXebZ7p0r1yrYDrLilk2F";
    
        $forum = isset($args[0]) ? $args[0] : "maceltima";
        $category = isset($args[1]) ? $args[1] : "3347645";
        
        $thread = null;
        
        $link = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];
        
        /* get thread id */
        $url = "https://disqus.com/api/3.0/threads/list.json?api_key={$apiKey}&forum={$forum}&category={$category}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
        $result = curl_exec($ch);
        $returnInfo = curl_getinfo($ch);        
        $err = curl_error($ch);
        curl_close($ch);

        if (strlen($err) == 0 && $returnInfo['http_code'] == '200') 
        {                    
            $result = json_decode($result,true);
            
            if ($result['code'] == 0 && count($result['response']))
            {
                
                
                foreach($result['response'] as $item)
                {
                    if ($item['link'] == $link)
                    {
                        $thread = $item['id'];
                    }
                }
            }
        }

        if (isset($thread))
        {
            $url = "https://disqus.com/api/3.0/threads/listPosts.json?api_key={$apiKey}&limit=25&thread={$thread}";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
            $result = curl_exec($ch);
            $returnInfo = curl_getinfo($ch);        
            $err = curl_error($ch);
            curl_close($ch);
    
            if (strlen($err) == 0 && $returnInfo['http_code'] == '200') 
            {                    
                $result = json_decode($result,true);
                
                if ($result['code'] == 0)
                {
                    $result = $this->getDisqusHtml($result['response']);
                    
                    return isset($result) ? $result : null;
                    
                }
            }
        }
        
        return array();
        
    }

    private function authDisqus()
    {
        $publickKey = 'BZmqcH7MfFiBsNdz1h0dshdrZEEDbLAk1BMdhUHF9de5aDZeMXXLbzZoRswMdJSX';
        $secretKey = 'Dt47Gxs2IXwP4F3xoDRWYJ4yStQcWXgpmZnTfEYPzEMYPO2mF7Qt7Ujr7VaUgmfM';
        $thread = "3001863510";
        $apiKey="E8Uh5l5fHZ6gD8U3KycjAIAk46f68Zw7C6eW8WSjZvCLXebZ7p0r1yrYDrLilk2F";
        
        $url = "https://disqus.com/api/oauth/2.0/authorize/?client_id={$publickKey}&scope=read,write&response_type=1aed353549ea4fefae7bd11861295c0c&redirect_uri=http://mac.eltima.com"; 
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
        $result = curl_exec($ch);
        $returnInfo = curl_getinfo($ch);        
        $err = curl_error($ch);
        curl_close($ch);
        
                
        //if (strlen($err) == 0 && $returnInfo['http_code'] == '200') 
        //{                    
            return $result;
        //}
        
        return null;
        
    }
    
    private function getDisqusHtml($response=null)
    {
        if (isset($response))
        {
            
            $text = '<ul id="post-list" class="post-list">';
              
            	foreach($response as $item)
                {

                      if ($item['isApproved'] == 1)
                      {
                            $text .= $this->getDisqusCommentHtml($response,$item);
                      }     
                            
                }
            $text .= '</ul>';
            
            return $text;
  
        }
        
        return null;
    }
    
    private function getDisqusCommentHtml($items=null, $item=null)
    {
        
            if (empty($item['parent']))
            {
                $text .='<li id="post-'.$item['id'].'" class="post">';
                
                $text .= '<span class="author">'.$item['author']['name'].'</span><br/>'.$item['message'];
                          
                $text .='</li>';
                    
                    
                    foreach($items as $it)
                    {
                        if ($item['id'] == $it['parent'])
                        {
                            $it['parent'] = null;
                            $text .= '<ul class="children" data-role="children">';
                            $text .= $this->getDisqusCommentHtml($items,$it);
                            $text .= '</ul>';
                        }
                    }
  
                }

                          
        return $text;
    }
    
    /**
     * @var args[0] product ID, args[1] comment ID
     * for multiselect use ',' in @var, exp args[0] = 123,143
     */
    public function getComments($args=null){
        include_once(ENGINE_PATH.'class/classComment.php');

            $comments = new Comment();
            
            $result = $comments->getComments($args);
            
            if(count($result)){
                return $result;
            }
        
        return null;
    }
    
}

?>