<?php


/**
* Print data
*
* @param $data
*/
if (! function_exists('pr')) {
	function pr($data){
	    echo '<pre>';
	    print_r($data);
	}
}

/**
* Convert sql date time to custom date time 24hr format
*
* @param $date_time
*/
if (! function_exists('config_date_time')) {
	function config_date_time($date_time){
	    $date = date("d M Y H:i:s",strtotime(str_replace(array("/"),"-",trim($date_time))));
	    return $date;
	}
}

/**
* Convert sql date time to custom date time 12hour format
*
* @param $date_time
*/
if (! function_exists('config_date_time_12hour')) {
	function config_date_time_12hour($date_time){
	    $date = date("d M Y h:i A",strtotime($date_time));
	    return $date;
	}
}

/**
* Convert sql date time to custom date time 12hour format
*
* @param $date_time
*/
if (! function_exists('config_date_time_12hour_short_form')) {
	function config_date_time_12hour_short_form($date_time){
	    $date = date("d M h:iA",strtotime($date_time));
	    return $date;
	}
}

/**
* Convert sql date to custom date format
*
* @param $date
*/
if (! function_exists('config_date')) {
	function config_date($date,$dateFormat = ""){
		if(!empty($dateFormat)){
			$date = date($dateFormat,strtotime($date));	
		}else{
			$date = date("d/m/Y",strtotime($date));	
		}
	    return $date;
	}
}

/**
* Convert sql date to custom month format
*
* @param $date
*/
if (! function_exists('config_month')) {
	function config_month($date,$dateFormat = ""){
		if(!empty($dateFormat)){
			$date = date($dateFormat,strtotime($date));	
		}else{
			$date = date("m/Y",strtotime($date));	
		}
	    return $date;
	}
}

/**
* Convert sql time to custom time format
*
* @param $time
*/
if (! function_exists('config_24hour_time')) {
	function config_24hour_time($time){
	    $date = date("H:i",strtotime(str_replace(array("/"),"-",trim($time))));
	    return $date;
	}
}

/**
* Convert sql time to custom time format
*
* @param $time
*/
if (! function_exists('config_time')) {
	function config_time($time){
	    $date = date("h:i A",strtotime(str_replace(array("/"),"-",trim($time))));
	    return $date;
	}
}

/**
* Convert custom date time to sql date time format
*
* @param $date_time
*/
if (! function_exists('sql_date_time')) {
	function sql_date_time($date_time){
	    $date = date("Y-m-d H:i:s",strtotime(str_replace(array("/"),"-",trim($date_time))));
	    return $date;
	}
}

/**
* Convert custom date to sql date format
*
* @param $date
*/
if (! function_exists('sql_date')) {
	function sql_date($date){
	    $date = date("Y-m-d",strtotime(str_replace(array("/"),"-",trim($date))));
	    return $date;
	}
}

/**
* Convert custom month to sql date format
*
* @param $date
*/
if (! function_exists('sql_month')) {
	function sql_month($date){
	    $date = date("Y-m",strtotime(str_replace(array("/"),"-",trim($date))));
	    return $date;
	}
}

/**
* Convert custom time to sql time format
*
* @param $time
*/
if (! function_exists('sql_time')) {
	function sql_time($time){
	    $date = date('H:i:s',strtotime(str_replace("/","-",$time)));
	    return $date;
	}
}

/**
* Convert date to short date format
*
* @param $date
*/
if (! function_exists('short_date')) {
	function short_date($date){
	    $date = date("d M",strtotime(str_replace(array("/"),"-",trim($date))));
	    return $date;
	}
}

