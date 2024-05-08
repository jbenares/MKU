<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					contractor
				where
				(
					contractor like '%$term%' 
				or
					contractor_code like '%$term%'
				)
				order by
					contractor asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = "$row[contractor]";
		$row_array['id'] = $row['contractor_id'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>