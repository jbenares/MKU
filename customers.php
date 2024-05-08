<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					account_id,
					account,
					account_code,
					creditlimit
				from	
					account
				where
				(
					account like '%$term%' 
				or
					account_code like '%$term%'
				)
				order by
					account asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = "$row[account]";
		$row_array['id'] = $row['account_id'];
		$row_array['creditlimit'] = $row['creditlimit'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>