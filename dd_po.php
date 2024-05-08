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
					po_header
				where
					po_header_id like '%$term%' 
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] = str_pad($row['po_header_id'],7,0,STR_PAD_LEFT);
		$row_array['id'] = $row['po_header_id'];
		$row_array['supplier_id'] = $row['supplier_id'];
		$row_array['supplier_name'] = $options->attr_Supplier($row_array['supplier_id'],'account');

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>