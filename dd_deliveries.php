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
					dr_header
				where
					dr_header_id like '%$term%' 
				order by
					dr_header_id asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		$dr_header_id		=  $row[dr_header_id];
		$dr_header_id_pad	= str_pad($dr_header_id,7,0,STR_PAD_LEFT);
		
		$row_array['value'] = "$dr_header_id_pad";
		$row_array['id'] = $dr_header_id;

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>