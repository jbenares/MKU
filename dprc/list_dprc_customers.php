<?php
	include_once("../conf/ucs.conf.php");
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					customer
				where
				(
					customer_last_name like '%$term%'
					or
					customer_first_name like '%$term%'
				)
				order by
					customer_last_name asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 	= "$row[customer_first_name] $row[customer_last_name]";
		$row_array['id'] 		= $row['customer_id'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>