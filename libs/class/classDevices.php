<?php

class SMDevices{
	
	public function getDevices()
	{
		$q = ' SELECT * FROM `devices` ORDER BY `device_order`';// limit 10';
        DB::executeQuery($q, 'getDevices');
        $rows = DB::fetchResults('getDevices');
        return $rows;
	}

	public function getOs()
	{
		$q = ' SELECT * FROM `devices_os` ORDER BY `os_order`';
        DB::executeQuery($q, 'getOs');
        $rows = DB::fetchResults('getOs');
        return $rows;
	}



}


?>