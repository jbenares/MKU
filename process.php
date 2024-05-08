<?php

	$msg      = $_GET["msg"];
	$sender   = $_GET["sender"];
	$receiver = $_GET["receiver"];
	$operator = $_GET["operator"];
	
	include_once("conf/sms.conf.php");
	include_once("my_Classes/query.class.php");
	include_once("my_Classes/process.class.php");
	
	$transac = new query();
	$process = new process();

	//$msg = "As of 02/01/01,00:04:26CKT #:2345 AC  Power off";
	//$msg = "BRW 1";
	//$msg = "R Catague,Michael Test";
	//$sender = "+639228923626";

	$code_array = explode(" ", $msg);
	
	$to = $receiver;
	$from = $sender;	
	$command = strtoupper($code_array[0]);
	
	$getR = mysql_query("select value from sms_config where name='Report Keyword'");
	$rR = mysql_fetch_array($getR);
	
	$getI = mysql_query("select value from sms_config where name='Inquiry Keyword'");
	$rI = mysql_fetch_array($getI);
	
	switch($command) {
		case "$rR[value]":
			$process->save_report($code_array, $from);
			$transac->get_msg(1, $to, $from); // Report
			break;
		case 'HELP':	
			$transac->get_msg(2, $to, $from); // Help
			break;
		case 'AS':
			$process->sensor_alerts($code_array, $from);
			break;
		case "$rI[value]":
			$station_code = strtoupper($code_array[1]);
			$process->check_station_status($station_code, $to, $from);	
			break;
		default:
	}
	
	mysql_close($conn);

?>