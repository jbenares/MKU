<?php
	include_once("conf/ucs.conf.php");
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);
	
	
	function getProjectName($project_id){
		$result = mysql_query("
			select project_name from projects where project_id = '$project_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['project_name'];
	}

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					p.stock , p.stock_id, d.rr_detail_id, d.details, d.serial_no, invoice, d.rr_detail_id, h.rr_header_id, h.project_id
				from
					rr_detail as d, productmaster as p, rr_header as h
				where
					h.rr_header_id = d.rr_header_id
				and
					d.stock_id = p.stock_id
				and
					rr_type = 'A'
				and
					p.stock like '%$term%'
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 			= "$row[stock] ($row[details] Serial No. $row[serial_no])";
		$row_array['rr_detail_id'] 		= $row['rr_detail_id'];
		$row_array['project_id'] 		= $row['project_id'];
		$row_array['project_name'] 		= getProjectName($row['project_id']);

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>