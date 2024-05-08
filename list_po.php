<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr 	= array();
	$term 			= mysql_real_escape_string($_GET['term']);
	$po_header_id	= $_REQUEST['po_header_id'];

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
		select
			po_header_id, stock, po_detail_id
		from
			po_detail as d, productmaster as p
		where
			d.stock_id = p.stock_id
		and
			po_header_id = '$po_header_id'
	") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 	= "$row[stock]";
		$row_array['id'] 		= "$row[po_detail_id]";

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>