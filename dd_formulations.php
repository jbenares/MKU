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
					formulation_header
				where
					formulation_code like '%$term%' 
				and
					product_id = '$stock_id'
				order by
					formulation_code asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = "$row[formulation_code]";
		$row_array['id'] = $row['formulation_header_id'];
		$row_array['output'] = $row['output'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>