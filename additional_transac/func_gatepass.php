<?php
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
require_once(dirname(__FILE__).'/../library/lib.php');

if( !empty($_REQUEST['action']) ) call_user_func($_REQUEST['action'], $_REQUEST['data']);

function displayGroupAddForm($form_data){


	$aReturn = array();
	$aReturn['error_status'] = 0;
	$aReturn['html'] = "";

	if( empty($form_data['transfer_header_id']) ){
		$aReturn['error_status'] = 1;
		$aReturn['error_msg'] = "Please enter Stocks transfer reference.";
		echo json_encode($aReturn);
		return false;
	} 

      
	$arr = lib::getArrayDetails("
		select 
			d.*, stock
		from 
			transfer_header as h 
			inner join transfer_detail as d on h.transfer_header_id = d.transfer_header_id
			inner join productmaster as p on d.stock_id = p.stock_id
			and h.transfer_header_id = '$form_data[transfer_header_id]'			
			and h.status != 'C'
	");

	if( count($arr) <= 0 ){
		$aReturn['error_status'] = 1;
		$aReturn['error_msg'] = "No items are found in Stocks Transfer Reference.";
		echo json_encode($aReturn);
		return false;	
	}

	$aReturn['html'] .= "
		<form id='group_form' method='post'>
			<input type='hidden' name='data[gatepass_id]' value='$form_data[gatepass_id]' >			
			<input type='hidden' name='gatepass_id' value='$form_data[gatepass_id]' >			


			<table class='table-css'>
				<thead>
					<td>QTY</td>
					<td>ITEM</td>
					<!--<td>COST</td>
					<td>AMOUNT</td>-->
				</thead>
				<tbody>";
	if( count($arr) ){
		foreach ($arr as $r) {
			$aReturn['html'] .= "
				<tr>
					<td>
						<input type='text' class='textbox3 quantity' name='data[arr_quantity][]' value='$r[quantity]' onkeyup='computeAmount(this);' >
						<input type='hidden' name='data[arr_transfer_header_id][]' value='$r[transfer_header_id]' onkeyup='computeAmount(this);' >
					</td>
					<td>
						".htmlentities($r['stock'])."
						<input type='hidden' name='data[arr_stock_id][]' value='$r[stock_id]' >
					</td>
					<!--<td><input type='text' class='textbox3 price' style='text-align:right;' name='data[arr_price][]' value='$r[price]'  onkeyup='computeAmount(this);' readonly ></td>
					<td><input type='text' class='textbox3 amount' style='text-align:right;' name='data[arr_amount][]' value='$r[amount]'  onkeyup='computeAmount(this);' readonly ></td>-->
				</tr>
			";
		}
	}
	$aReturn['html'] .="
				</tbody>    	
				<tfoot>
					<tr>
						<td><button value='addTransferItems' name='action' >Add Items</button></td>						
					</tr>
					
				</tfoot>
			</table>
		</form>";
	

	echo json_encode($aReturn);
}

function addTransferItems($form_data){

	$i = 0;
	foreach ($form_data['arr_transfer_header_id'] as $transfer_header_id) {

		if( $form_data['arr_quantity'][$i] <= 0 ){
			$i++;
			continue;
		}

		DB::conn()->query("
			insert into 
				gatepass_detail
			set
				gatepass_id = '".$form_data['gatepass_id']."',
				stock_id    = '".$form_data['arr_stock_id'][$i]."',
				quantity    = '".$form_data['arr_quantity'][$i]."',
				cost        = '".$form_data['arr_price'][$i]."',
				amount      = '".$form_data['arr_amount'][$i]."',
				header_id   = '".$form_data['arr_transfer_header_id'][$i]."'

		");		

		$i++;
	}
}

function returnItem($form_data){

	DB::conn()->query("
		update 
			gatepass_detail
		set
			is_returned = '1'
		where
			gatepass_detail_id = '$form_data[id]'
	");

}

?>