<?php
class Price {
    private static $strict = array('AUD','CHF','EUR','GBP','CAD');
    private static $bigger = array('CNY','NOK','SEK','PLN');
    private static $large = array('JPY','RUB');

    public static function calcPrice($usdPrice, $ratio, $curCode) {
	/*
	$strict = array('AUD','CHF','EUR','GBP','CAD');
	$bigger = array('CNY','NOK','SEK','PLN');
	$large = array('JPY','RUB');
	*/
	if(1 > $usdPrice) {
	    return '0.00';
	}
	$price = floatval($usdPrice) * floatval($ratio);
	if(0 == substr_count($price,'.')) {
	    $price .= '.00';
	}
	list($integer,$fractional) = explode('.',$price);
	$fractional = (in_array($curCode,self::$large) ? '00' : '95');
	$integer = intval($integer);
	if(0 < $integer) {
	    //for large currencies, i.e. japan, we increasing or reducing to 50 or 100
	    if(in_array($curCode, self::$large)) {
		$modulus = $integer % 100;
		if(0 < $modulus) {
		    $modulus = ((50 > $modulus) ? ($modulus - 25) : ($modulus - 75));
		    $sign = (0 == $modulus) ? 1 : ($modulus/abs($modulus));
		    $integer+= (-$modulus) + ($sign * 25);
		}
		if(1 > $integer) {
		    $integer = 50;
		}
	    }
	    else {
		if(1000 < $integer) {
		    $modulus = $integer % 1000;
		    if(10 > $modulus) {
			$integer = self::toThousand($integer,$curCode);
		    }
		    elseif(50 > $modulus) {
			if(in_array($curCode,self::$bigger)) {
			    $integer = self::toThousand($integer,$curCode);
			}
			else {
			    //--
			    $modulus = $integer % 10;
			    if(3 > $modulus) {
				$integer = self::toTen($integer,$curCode);
			    }
			    else {
				$integer = self::toNine($integer,$curCode);
			    }
			    //--
			}
		    }
		    elseif(100 > $modulus) {
			//--
			$modulus = $integer % 10;
			if(3 > $modulus) {
			    $integer = self::toTen($integer,$curCode);
			}
			else {
			    $integer = self::toNine($integer,$curCode);
			}
			//--
		    }
		    else {
			$modulus = $integer % 100;
			if(10 > $modulus) {
			    $integer = self::toHundred($integer,$curCode);
			}
			//--
			$modulus = $integer % 10;
			if(3 > $modulus) {
			    $integer = self::toTen($integer,$curCode);
			}
			else {
			    $integer = self::toNine($integer,$curCode);
			}
			//--
		    }
		}
		elseif(100 < $integer) {
		    $modulus = $integer % 100;
		    if(10 > $modulus) {
			$integer = self::toHundred($integer,$curCode);
		    }
		    //--
		    $modulus = $integer % 10;
		    if(3 > $modulus) {
			$integer = self::toTen($integer,$curCode);
		    }
		    else {
			$integer = self::toNine($integer,$curCode);
		    }
		    //--
		}
		elseif(10 < $integer) {
		    //--
		    $modulus = $integer % 10;
		    //print $curCode.'=>'.$integer.'=>'.$modulus.'&nbsp;&nbsp;&nbsp;&nbsp;';
		    if(3 > $modulus) {
			//print $curCode.'=>'.$integer.'=>'.$modulus.'&nbsp;&nbsp;&nbsp;&nbsp;';
			$integer = self::toTen($integer,$curCode);
			//print $curCode.'=>'.$integer.'=>'.$modulus.'&nbsp;&nbsp;&nbsp;&nbsp;';
		    }
		    else {
			$integer = self::toNine($integer,$curCode);
		    }
		    //--
		}
		else {
		    $integer = self::toNine($integer,$curCode);
		}
	    }
	}
	return $integer.'.'.$fractional;
    }

    // 1009 => 999
    private static function toThousand($intPrice,$curCode) {
	$intPrice -= (($intPrice % 1000) + 1);
	return $intPrice;
    }

    //212 => 199
    private static function toHundred($intPrice,$curCode) {
	if(($curCode != 'GBP') && (200 > $intPrice)) {
	    $intPrice -= (($intPrice % 100) + 1);
	}
	return $intPrice;
    }

    //12 => 09
    private static function toTen($intPrice,$curCode) {
	//print $intPrice.'<br>';
	//print ($intPrice % 10).'<br>';
	$intPrice -= (($intPrice % 10) + 1);
	//print $intPrice.'<br>';
	return $intPrice;
    }

    //33 =>33 || 33 => 39
    private static function toNine($intPrice,$curCode) {
	if(in_array($curCode,self::$bigger)) {
	    $intPrice += (9 - ($intPrice % 10));
	}
	return $intPrice;
    }
}
/*
		$modulus = $integer % 10;
		//if value is 100 we'll make it 99
		if(0 == $modulus) {
		    $integer--;
		}
		//if currency is not in strict price list - the last digit should be 9
		if(!in_array($curCode,$eq)) {
		    $integer+= (9 - $modulus);
		}
	    */
?>