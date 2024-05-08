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
					employee
				where
				(
					employee_lname like '$term%' 
				or
					employee_fname like '$term%'
				)
				order by
					employee_lname asc
				") or die(mysql_error()); 
				

	/* Retrieve and store in array the results of the query.*/
	while ($r = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = "$r[employee_lname], $r[employee_fname]";
		$row_array['id'] 	= $r['employeeID'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>