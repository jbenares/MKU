<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_escape_string($_GET['term']);
	
	$account_id = $_GET['account_id'];
	if(!empty($account_id)){
		$price_level = $options->attr_Account($account_id,'pricelevel');
	}

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					productmaster
				where
				(
					stock like '$term%' 
				or
					stockcode like '$term%'
				or
					barcode like '$term%'
				)
				and status != 'D'
				order by
					stock asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 	= str_replace("&quot;","\"",str_replace("&deg;","°",htmlentities($row['stock'])))." - $row[stockcode]";
		#$row_array['value'] 	= mysql_escape_string(htmlentities($row['stock'],ENCQUOTEDPRINTABLE))." $row[stockcode]";
		#$row_array['value'] 	= htmlentities($row['stock'],ENCQUOTEDPRINTABLE)." $row[stockcode]";
		#$row_array['value'] 	= addslashes($row['stock'])." - $row[stockcode]";
		$row_array['id'] 		= $row['stock_id'];
		$row_array['price'] 	= $row['price1'];
		$row_array['price1'] 	= $row['price1'];
		$row_array['cost'] 		= $row['cost'];
		$row_array['buffer']	= $row['buffer'];
		$row_array['unit']		= $row['unit'];
		
		$row_array['unit']		= $row['unit'];
		$row_array['rate_per_hour']		= $row['rate_per_hour'];
		
		
		$row_array['stock_price'] = $row['price'.$price_level];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>
