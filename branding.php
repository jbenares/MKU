<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				 select
					branding_number
				 from
					productmaster
				 where
				 	branding_number 
				 like 
				 	'%$term%'
				 AND
				 	categ_id1 = '10'
				 AND
				 	categ_id2 = '30'
				 AND
				 	branding_number !=''
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		$query=mysql_query("SELECT * FROM junk_tires WHERE branding_num = '$row[branding_number]'");
		if(mysql_num_rows($query) == 0){
				$row_array['value'] = "$row[branding_number]";
				array_push($return_arr,$row_array);
		}
		
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>