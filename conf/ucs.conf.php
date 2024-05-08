<?php
	ob_start();
	if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
	error_reporting(0);

	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_SERVER'	, 'localhost');
	define('DB_NAME'	, 'mku_db');	

	//define('DB_HE', 'dynamic');

	require_once(dirname(__FILE__).'/../library/DB.php');

	$conn = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
	$db   = mysql_select_db(DB_NAME) or die(mysql_error());

	$GLOBALS['aStatus'] = array(
		"S" => "Saved",
		"C" => "Cancelled",
		"F" => "Finished"
	);

	$registered_access = $_SESSION['access_id'];
	$registered_userID = $_SESSION['userID'];
	$registered_username = $_SESSION['username'];
	$registered_branchID = $_SESSION['branchID'];
	$registered_companyID = 1;
	
	$getCompany = mysql_query("select * from companies where companyID='$registered_companyID'");
	$rCompany   = mysql_fetch_array($getCompany);
	
	if(!empty($rCompany)) {
		$title = $rCompany[company_name];
		$company_logo = $rCompany[company_logo];
	}
	else
		$title = " MKU CONSTRUCTION";	

	$company_address="Lot 8 & 29, Block 28 Circumferential Road, Taculing Bacolod City";
	$company_tel_no="Tel. No. (034)460-1504 – Fax No. (034) 441-3972";
	
	$limit = 50; //	Listings and search result limit for pagination.
	$link_limit = 20; // Number of page links shown at the bottom for pagination.
	
	date_default_timezone_set("Asia/Manila");	
	$today = date("Y-m-d");
	$gateway = '+639994531825';

 	//$GLOBALS[db_rmy] = "rmy";
?>