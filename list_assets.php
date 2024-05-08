<?php
	include_once("conf/ucs.conf.php");
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select
					h.rr_header_id,rr_detail_id,stock,asset_code,details,date_acquired,d.cost,estimated_life,quantity
				from
					rr_header as h, rr_detail as d, productmaster as p
				where 
					h.rr_header_id = d.rr_header_id
				and
					d.stock_id = p.stock_id
				and
					h.status != 'C'
				and
					rr_type = 'A'
				and
					stock like '%$term%'
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 			= "$row[stock]";
		$row_array['rr_detail_id'] 		= $row['rr_detail_id'];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>