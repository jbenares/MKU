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
					equipment
				where
					eq_name like '$term%'
				order by
					eq_name asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 	= "$row[eq_name]";
		$row_array['id'] 		= $row['eqID'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>