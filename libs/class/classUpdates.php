<?php

class Updates {

    public $subscribeMess;
    protected $siteDb;
    public function __construct($db='') {
        $this->siteDb        = (!empty($db) ? $db.'.' : '');
    	$this->subscribeMess = "Hi there,\n
Thank you for subscribing to Garbagecat Updates Notifications!
You've chosen the quickest and the most effective way to
stay informed about all Garbagecat Software news, including
program updates and upgrades, new software versions and
totally new shareware and freeware releases.
Stay tuned to the freshest news cuts from Garbagecat Software!

Your subscription includes:

- [NOTIF_LIST]

If it wasn't you who has input this e-mail address to Garbagecat
Updates Subscription or you just don't want to receive any news
notifications from Garbagecat Software, click here: http://www.Garbagecat.com/software-updates/
and use the password provided at the bottom of this e-mail to unsubscribe from Updates Subscription.

Your unsubscription password: [PASS]

Thank you very much for your interest in our solutions!
Yours, Garbagecat Software Team
		";

    }

    public function addSubscriber($email, $productList, $sendNewProductsNotif = false, $sayecho = true)
    {
    	$pass = substr(md5(mt_rand(1,1000000)), 0, 12);

    	$q = '
    	INSERT
    	INTO '.$this->siteDb.'update_subscribers
    	SET
            user_email = "'.trim($email).'",
            user_pass = "'.$pass.'",
            user_new_prod = '.($sendNewProductsNotif ? 1 : 0).',
            user_blocked = 0
    	';

    	DB::executeAlter($q,'update_subscribers');
		$userId = DB::getLastInsertId();
    	if ($sayecho) 
			{
				echo $userId;
			}

        $q = 'INSERT INTO '.$this->siteDb.'update_subscribers_products(suser_id, prod_id) ';
        $firstIteration = true;
        foreach ($productList as $productId => $productTitle) {
    		$q .= (!$firstIteration ? ',' : 'VALUES').'('.$userId.', '.$productId.')';
                $firstIteration = false;
    	}

        DB::executeAlter($q, 'update_subscribers_products');

        $msg = str_replace("[NOTIF_LIST]", implode(",\n- ", $productList), $this->subscribeMess);
    	$msg = str_replace("[PASS]", $pass, $msg);

    	$this->sendMailUp(trim($email), $msg);
    }
    
    
	public function checkValidEmailAddress($email)
	{
		if (empty($email) || ( !empty($email) && !ereg( "^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$", $email)))
		{
			return false;
		}			
	 	return true;	
	}
	
	
    public function checkEmail($email) {
	if (!$this->checkValidEmailAddress($email))
		return -1;

	$q = 'SELECT user_id
	      FROM '.$this->siteDb.'update_subscribers
	      WHERE user_email = "'.$email.'"
	      LIMIT 1';

	$result = DB::executeQuery($q,'update_subscribers');
	$row 	= DB::fetchResults('update_subscribers');

        if(is_array($row) && count($row) > 0) {
            return ($row[0]['user_id'] + 0);
        }
        return 0;
    }

    public function checkEmailPass($email, $password) {
		if (!$this->checkValidEmailAddress($email))
			return -1;
        $q = 'SELECT user_id
              FROM '.$this->siteDb.'update_subscribers
              WHERE user_email 	= "'.$email.'" AND user_pass = "'.$password.'"
              LIMIT 1';

        $result = DB::executeQuery($q, 'update_subscribers');
        $row 	= DB::fetchResults('update_subscribers');

        if(is_array($row) && count($row) > 0) {
            return ($row[0]['user_id'] + 0);
        } else {
            return 0;
        }
    }

    public function deleteUser($userId) {
        $q = 'DELETE FROM '.$this->siteDb.'update_subscribers
              WHERE user_id = ?';

        $result = DB::executeAlter($q, array($userId));

        $q = 'DELETE FROM '.$this->siteDb.'update_subscribers_products
              WHERE suser_id = ?';

        $result = DB::executeAlter($q, array($userId));
    }

    public function sendMailUp($to, $msg)
    {
        $subject = 'Garbagecat - Updates notification subscription';
        $message = $msg;
        $headers = 
'From: Garbagecat Software' . "\r\n" .
'Reply-To: support@Garbagecat.com' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

        $mail = mail($to, $subject, $message, $headers);
        
        //var_dump($mail);
    }

    public function sendPassword($email) {
	$pass = substr(md5(mt_rand(1,1000000)), 0, 12);

	$q = 'SELECT user_id
	      FROM '.$this->siteDb.'update_subscribers
	      WHERE user_email 	= ?
	      LIMIT 1';

	$result = DB::executeQuery($q, 'email', array($email));
	$row 	= DB::fetchResults('email');

	if(is_array($row) && count($row) > 0) {
	    $q = 'UPDATE '.$this->siteDb.'update_subscribers
   		  SET user_pass = ?
  		  WHERE user_email = ?';

    	    DB::executeAlter($q, array($pass, $email));

    	    $msg = "Hi there,

Your new unsubscription password is: ".$pass."

Thank you very much for your interest in our solutions!
Yours, Garbagecat Software Team";

            $this->sendMailUp($email, $msg);

            return ($row[0]['user_id'] + 0);
        } else {
                return 0;
        }
    }


}

?>