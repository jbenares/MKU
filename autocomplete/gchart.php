<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					gchart
				where
				(
					gchart like '$term%' 
				)
				order by
					gchart asc
				") or die(mysql_error()); 
				

	/* Retrieve and store in array the results of the query.*/
	while ($r = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = "$r[gchart]";
		$row_array['id'] 	= $r['gchart_id'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>