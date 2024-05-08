<?php
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
require_once(dirname(__FILE__).'/../library/DB.php');
require_once(dirname(__FILE__).'/../library/lib.php');

call_user_func($_REQUEST['action'], $_REQUEST['params']);

function joIsAvailable($params){
	$joborder_header_id = $params['joborder_header_id'];
	$sql = "
		select * from joborder_header where joborder_header_id = '$joborder_header_id'
	";

	echo (DB::conn()->query($sql)->num_rows > 0 ) ? TRUE : FALSE;	 
}

?>