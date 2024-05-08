<?php
	require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
	
	$return_arr = array();
	$term       = mysql_escape_string($_GET['term']);
		

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					productmaster
				where
				(
					stock like '$term%' 
					or stockcode like '$term%'
				)
				and status != 'D'
				order by stock asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value']       = str_replace("&quot;","\"",str_replace("&deg;","°",htmlentities($row['stock'])));		
		$row_array['stock_id']    = $row['stock_id'];		
		$row_array['cost']        = $row['cost'];
		$row_array['description'] = $row['description'];
		$row_array['stock_length'] = $row['stock_length'];
		$row_array['kg'] = $row['kg'];


		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>
