<?php
/* requrements
  class DB
  class VBox
*/
class URIHandler {
    /*
     Ищет в базе редирект для не существующей страници по адресу
     Возвращает массив header-ов, если найдено соответствие
     в противном случае - FALSE
    */
    public static function checkGreenList($addr) {
        $q = 'SELECT gl_header, gl_destination
              FROM greenlist
              WHERE gl_address = ?
              LIMIT 1';

        DB::executeQuery($q, 'greenList', array($addr));
        $res = DB::fetchRow('greenList');
        if(!empty($res)) {
            $headers['headers'] = (!empty($res) ? unserialize($res['gl_header']) : array());
            $headers['location'] = $res['gl_destination'];
            return $headers;
        }
        return FALSE;
    }

    /*
     Ищет в базе редирект для не существующей страници по адресу
     с использованием preg_match выражений
    */
    public static function extCheckGreenList($addr) {
		$q = '	SELECT 	gle_expression,
						gle_header,
						gle_destination,
						gle_regular
				FROM greenlistext
				ORDER BY gle_order';
        DB::executeQuery($q, 'extGreenList');
        $res = DB::fetchResults('extGreenList');
		
		if(!empty($res)) {
			$tsize 		= sizeof($res);
			$regex 		= array();
			$headers 	= FALSE;
	    
			for($i=0; $i<$tsize; $i++) {
				$exp = $res[$i]['gle_expression'];
				if($res[$i]['gle_regular'] == '0') {
					$exp 	= trim($exp, '*');
					$exp 	= explode('*',$exp);
					$match 	= TRUE;
					$ls = substr($addr,(strlen($addr)-1),strlen($addr));
					if($ls != '/'){
						foreach($exp as $val) {
							if(strpos($addr.'/',$val) === FALSE) {
								$match = FALSE;
							}else{
								$match = TRUE;
							}
						}		
					}else{
						foreach($exp as $val) {
							if(strpos($addr,$val) === FALSE) {
								$match = FALSE;
							}else{
								$match = TRUE;
							}
						}
					}
					if($match) {
						$headers['headers'] = (!empty($res[$i]['gle_header']) ? unserialize($res[$i]['gle_header']) : array());
						$headers['location'] = $res[$i]['gle_destination'];
						break;
					}
				}else{
					$pattern = '/^'.$exp.'/';
					@preg_match($pattern, $addr, $matches); 
					
					if($matches) {
						$headers['headers'] 	= (!empty($res[$i]['gle_header']) ? unserialize($res[$i]['gle_header']) : array());
					//	$headers['location'] 	= $res[$i]['gle_destination']; //add garbagecat76 26.03.2013	
						$headers['location']  = @preg_replace($pattern, $res[$i]['gle_destination'], $addr);	//add garbagecat76 26.03.2013		
						break;
					}
				}
			}
			return $headers;
		}
		return FALSE;
    }

    public static function sendGLHeaders($headers) {
		$tsize = sizeof($headers['headers']);
		$is404 = FALSE;
		for($i=0; $i<$tsize; $i++) {
			header($headers['headers'][$i]);
			if($headers['headers'][$i] == 'HTTP/1.1 404 Not Found') {
				
				$is404 = TRUE;
			}
		}

		if($is404) {
			if(file_exists(LOCAL_PATH.VBox::get('ConstData')->getConst('404page'))) {
				include_once(LOCAL_PATH.VBox::get('ConstData')->getConst('404page'));
			}
		}elseif(!empty($headers['location'])){
			header('Location: '.$headers['location']);
		}
    }

    /*
     Ищет в базе и возвращает id страници по адресу,
     если не находит то возвращает null
    */
    public static function getPageIdByAddress($pageAddress) {

        $q = 'SELECT pg_id
              FROM pages
              WHERE pg_address = ?
              LIMIT 1';
        DB::executeQuery($q, 'faddress', array($pageAddress));
        $address = DB::fetchOne('faddress');
        //DB::freeResults('faddress');
        if(!empty($address)) {
            return intval($address);
        }
        return FALSE;
    }

    public static function getAddressById($id) {
	$q = 'SELECT pg_address
              FROM pages
              WHERE pg_id = ?
              LIMIT 1';
        DB::executeQuery($q, 'faddress', array($id));
        $address = DB::fetchOne('faddress');
        if(!empty($address)) {
            return $address;
        }
        return '/';
    }

/*

*/
    public static function getPartialAddress($address) {
	
		$urlStyle = VBox::get('ConstData')->getConst('addrType');
        
		if($urlStyle == 'searchfriendly') {
            $address = str_replace('.html','',$address);
        }

		if($urlStyle == 'oldschool' && VBox::get('ConstData')->getConst('trailingSlash')) {
			if(substr($address, (strlen($address)-1), 1) != '/') {
				if($id = self::getPageIdByAddress($address.'/')) {
        	        return $id;
            	}
			}
		}

        $addrMaxLength 	= VBox::get('ConstData')->getConst('addrMaxLength');
		$address 		= explode('/', trim($address, '/'));

        $_address = '';
		for($i = $addrMaxLength; $i > 0; $i--) {
			if(empty($address[$i])) {
				continue;
			}

			$_address = '/'.implode('/', array_slice($address, 0, $i));

            if($urlStyle == 'searchfriendly') {
                $_address .= '.html';
            }

			if($urlStyle == 'oldschool' && VBox::get('ConstData')->getConst('trailingSlash')) {
				$_address .= '/';
			}

			if($id = self::getPageIdByAddress($_address)) {
				if(substr($address, (strlen($address)-1), 1) != '/') {
					if($id = self::getPageIdByAddress($_address)) {

						if($_address == '/uninstall/' || $_address == '/buynow/'){
							return $id;
						}else{
							header('HTTP/1.1 301 Found');
							header('Location: '.$_address);
							return $id;
						}
					}else{
						return FALSE;
					}
				}
				header('HTTP/1.1 301 Found');
				header('Location: '.$_address);
            }
			
        }
        return FALSE;
    }
}

?>