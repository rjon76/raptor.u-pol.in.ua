<?php
//include_once(ENGINE_PATH.'class/classEvent.php');
class Agregator {

    private $requestURI;
    private $pageId;
    private $isCacher;
    private $constData;

    public function __construct($pageId = NULL) {

        if ($access = IniParser::getInstance()->getSection('access')){
            
            if(isset($access['ip']))
            {
                $ips = explode(',',$access['ip']);
                if (in_array(self::getIp(),$ips))
                {
                    if(!defined('WWW2')) {
                        define('WWW2', true);
                    }                
                }
            }
        }

        $this->isCacher = FALSE;

        if(isset($pageId)) {
            $this->pageId = $pageId;
            $this->isCacher = TRUE;
        } else {
            @$this->requestURI = '/'.$_GET['request'];
        }

        if(!VBox::isExist('ConstData')) {
            $this->constData = VBox::get('ConstData');
        } else {
            $this->constData = new ConstData();
            VBox::set('ConstData', $this->constData);
        }
        /* italiano, 14.01.2015
        if(!VBox::isExist('Event')) { 
            VBox::set('Event', new Event());
        }
        /* end */
    }

    public function __destruct() {
        $this->requestURI = NULL;
        $this->pageId = NULL;
        $this->isCacher = NULL;
        $this->constData = NULL;
    }

    public function process($dontCheckHidden=false) {
		// если pageId отсутствует
        if(!isset($this->pageId)) {
			// узнаем pageId по адресу. ($this->requestURI)

            $this->pageId = URIHandler::getPageIdByAddress($this->requestURI);
			
			if(!$this->pageId){
				if(substr($this->requestURI, (strlen($this->requestURI)-1), 1) != '/') {
					$this->pageId = URIHandler::getPageIdByAddress($this->requestURI.'/');
				}
				if($this->pageId){
					header("Location: ".$this->requestURI."/");
				}
			}	
			//if (isset($_GET['t'])) { echo $_GET['t'].' test '.$this->pageId;	die();	}
			// если в базе текущего адреса нет, считаем адрес старым или ошибочным.
            if(!$this->pageId){
				
				if ( $this->checkInGreenList() )
					return TRUE;
			/*
            	// проверяем greenList
            	$greenListData = URIHandler::checkGreenList('/'.$_GET['request']);

            	if(is_array($greenListData)){
            		// если greenList вернул в результате массив
            		// отсылаем хедеры, перенаправляем юзера.
            		URIHandler::sendGLHeaders($greenListData);
					return TRUE;
            	}
            	
            	// если greenList ничего не вернул
            	// проверяем Extended greenList
//				VAR_DUMP($_SERVER['REQUEST_URI']);
//				$greenListData = URIHandler::extCheckGreenList('/'.$_GET['request']);
				$greenListData = URIHandler::extCheckGreenList($_SERVER['REQUEST_URI']);				
				if(is_array($greenListData)) {
            		// если greenList вернул в результате массив
            		// отсылаем хедеры, перенаправляем юзера.
					URIHandler::sendGLHeaders($greenListData);
					return TRUE;
				}
			*/	
				// если грин листы не сработали прорабатываем вариант getPartialAddress (/products/что-то_левое)
                $this->pageId = URIHandler::getPartialAddress($this->requestURI);
				if(!$this->pageId){
                	return FALSE;	
                }
            }
        }

        // если мы таки узнаем pageId идем...
        //admin section
        if($this->constData->getConst('realDomain') == $_SERVER['HTTP_HOST']) {
            $this->printAdminSection();
        }
        
        // www section
        else {
            return $this->printWWWSection($dontCheckHidden);
        }
    }
	
		private function checkInGreenList() {
				// проверяем greenList
            	$greenListData = URIHandler::checkGreenList('/'.$_GET['request']);

            	if(is_array($greenListData)){
            		// если greenList вернул в результате массив
            		// отсылаем хедеры, перенаправляем юзера.
            		URIHandler::sendGLHeaders($greenListData);
					return TRUE;
            	}
            	
            	// если greenList ничего не вернул
            	// проверяем Extended greenList
//				VAR_DUMP($_SERVER['REQUEST_URI']);
//				$greenListData = URIHandler::extCheckGreenList('/'.$_GET['request']);
				$greenListData = URIHandler::extCheckGreenList($_SERVER['REQUEST_URI']);				
				if(is_array($greenListData)) {
            		// если greenList вернул в результате массив
            		// отсылаем хедеры, перенаправляем юзера.
					URIHandler::sendGLHeaders($greenListData);
					return TRUE;
				}
				return FALSE;
		}		

    private function printAdminSection() {

        /***********************************/
        include_once(ENGINE_PATH.'class/classPage.php');
        include_once(ENGINE_PATH.'class/classBlocksData.php');
        include_once(ENGINE_PATH.'class/classRealPageHandler.php');
        include_once(ENGINE_PATH.'class/classVAuth.php');
        include_once(ENGINE_PATH.'class/classVSession.php');
        include_once(LIB_PATH.'Smarty/Smarty.class.php');
        /***********************************/

        $auth = new VAuth();

        if(!$auth->isValid())
        {
            if($this->pageId != $this->constData->getConst('loginPage')) {
                header('Location: http://'.$this->constData->getConst('realDomain').URIHandler::getAddressById($this->constData->getConst('loginPage')));
                return;
            }

            $auth->authenticate('username', 'password');

            if($auth->isValid()) {
               header('Location: http://'.$this->constData->getConst('realDomain').'/');
                return;
            }
        }

        $page = new Page($this->pageId);
        VBox::set('Page', $page);

        $pageHandler = new RealPageHandler();
        $pageHandler->printPage();
    }

    private function printWWWSection($dontCheckHidden=false) {

        /***********************************/
        include_once(ENGINE_PATH.'class/classPage.php');
        include_once(ENGINE_PATH.'class/classPageReCacher.php');
        /***********************************/

        if($this->pageId == $this->constData->getConst('loginPage')) {
            header('HTTP/1.1 403 Forbidden');
            return;
        }

        if($this->isCacher) {
            $page = new PageReCacher($this->pageId);
        } else {
            $page = new Page($this->pageId);
        }

        VBox::set('Page', $page);
        // if page is hidden

//        if(!$dontCheckHidden && $page->checkHidden() && substr_count($_SERVER['HTTP_REFERER'],$page->address['base_address'])==0) {
        if(!$dontCheckHidden && $page->checkHidden()) {	
			//if (isset($_GET['t'])) { echo $_GET['t'].' test '.$this->pageId;	die();	}
			if ( $this->checkInGreenList() )
				return TRUE;
		    header('HTTP/1.1 302 Found');
            header('Location: '.$page->address['base_address']);
            return;
        }


            /***********************************/
            include_once(ENGINE_PATH.'class/classBlocksData.php');
            include_once(ENGINE_PATH.'class/classRealPageHandler.php');
            include_once(LIB_PATH.'Smarty/Smarty.class.php');
            /***********************************/

            //proceed uncached version code
            $pageHandler = new RealPageHandler();
            $pageHandler->printPage();

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