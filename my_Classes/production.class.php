<?php

function productionFormulationDetails($formulation_header_id){
	$options		= new options();
	$objResponse	= new xajaxResponse();
	
	$content = "
		<tr bgcolor=\"#C0C0C0\">				
			<th width=\"20\"><b>#</b></th>
			<th>Item</th>
			<th>Unit</th>
			<th>Quantity</th>
			<th>Cost</th>
			<th>Amount</th>
		</tr> 
	";

	$result=mysql_query("
			select
				d.stock_id,
				stock,
				quantity,
				unit,
				cost
			from
				formulation_details as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				formulation_header_id = '$formulation_header_id'
		") or $objResponse->alert(mysql_error());
		
	$i=1;
	while($r=mysql_fetch_assoc($result)){
		$stock_id 		= $r['stock_id'];
		$stock			= $r['stock'];
		$quantity		= $r['quantity'];
		$unit			= $r['unit'];
		$cost			= $r['cost'];
		$amount	= $quantity * $cost;
		
		$content.="
			<tr>
				<td>".$i++."</td>
				<td>$stock</td>
				<td>$unit</td>
				<td class=\"align-right\">".number_format($quantity,2,'.',',')."</td>
				<td class=\"align-right\">".number_format($cost,2,'.',',')."</td>
				<td class=\"align-right\">".number_format($amount,2,'.',',')."</td>
				<input type='hidden' name='detail_stock_id[]' value='$stock_id'>
				<input type='hidden' name='detail_quantity[]' value='$quantity'>
				<input type='hidden' name='detail_cost[]' value='$cost'>
			</tr>
		";
	}
	
	$objResponse->assign('search_table','innerHTML',$content);

	
	return $objResponse;
}


?>