<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$return_arr = array();
	$term = mysql_real_escape_string($_GET['term']);
	
	$account_id = $_GET['account_id'];
	if(!empty($account_id)){
		$price_level = $options->attr_Account($account_id,'pricelevel');
	}

	/* If connection to database, run sql statement. */
	$fetch = mysql_query("
				select 
					*
				from	
					productmaster as p, categories as c
				where
					p.categ_id1 = c.categ_id
				and
					category_type = 'S'
				and
				(
					stock like '%$term%' 
				or
					stockcode like '%$term%'
				)
				order by
					stock asc
				") or die(mysql_error()); 

	/* Retrieve and store in array the results of the query.*/
	while ($row = mysql_fetch_assoc($fetch)) {
		
		$row_array['value'] 	= "$row[stock] - $row[stockcode]";
		$row_array['id'] 		= $row['stock_id'];
		$row_array['price'] 	= number_format($row['price1'],2);
		$row_array['cost'] 		= number_format($row['cost'],2);
		$row_array['buffer']	= $row['buffer'];
		
		$row_array['stock_price'] = $row['price'.$price_level];

		array_push($return_arr,$row_array);
	}
	/* Toss back results as json encoded array. */
	echo json_encode($return_arr);
?>