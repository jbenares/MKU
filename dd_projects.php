<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);
	$stock_id = mysql_real_escape_string($_GET['stock_id']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					projects
				where
					project_name like '%$term%' 
				or
					project_code like '%$term%'
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = "$row[project_name] - $row[project_code]";
		$row_array['id'] = $row['project_id'];
		$row_array['contract_amount'] = $row['contract_amount'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>