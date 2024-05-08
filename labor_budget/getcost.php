<?php
	require_once("../conf/ucs.conf.php");
	
	$id = $_POST[id];
	
	$sql=mysql_query("SELECT * FROM work_type WHERE work_code_id='$id'");
	$fetch=mysql_fetch_assoc($sql);
	
	echo $fetch['wt_price_per_unit'];
?>