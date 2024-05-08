<?php
function spo_form($spo_detail_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$content = "
		<div class='module_actions'>
			<input type=\"hidden\" name=\"spo_detail_id\" value=\"$spo_detail_id\" />
			<table>
				<tr>
					<td>Description:</td>
					<td><input type='text' class='textbox' name='sub_description' autocomplete=\"off\"></td>
				</tr>
				
				<tr>
					<td>Quantity:</td>
					<td><input type='text' class='textbox' name='quantity' id='_quantity' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>Unit:</td>
					<td><input type='text' class='textbox' name='unit' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>Unit Cost :</td>
					<td><input type='text' class='textbox' name='unit_cost' id='_unit_cost' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>Amount :</td>
					<td><input type='text' class='textbox' name='amount' id='_amount'  autocomplete=\"off\" readonly='readonly' ></td>
				</tr>
				
				<tr>
					<td>Chargables :</td>
					<td><input type='text' class='textbox' name='chargables' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>Person :</td>
					<td><input type='text' class='textbox' name='person' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>
						<input type='button' value='Add Item' name='b' onclick=\"xajax_spo(xajax.getFormValues('dialog_form'), xajax.getFormValues('header_form'));\" />		
					</td>
				</tr>
			</table>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			j(\"#dialog\").dialog(\"option\",\"title\",\"SUB DETAILS ENTRY\");
			
			j(\"#_quantity,#_unit_cost\").keyup(function(){
				var quantity = j(\"#_quantity\").val();
				var cost = j(\"#_unit_cost\").val();
				
				var amount = quantity * cost;
				j(\"#_amount\").val(amount);
			});
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}



function spo($form_data,$header_form){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$spo_detail_id		= $form_data['spo_detail_id'];
	$sub_description	= $form_data['sub_description'];
	$quantity			= $form_data['quantity'];
	$unit				= $form_data['unit'];
	$unit_cost			= $form_data['unit_cost'];
	$amount				= $form_data['amount'];
	$chargables			= $form_data['chargables'];
	$person				= $form_data['person'];
	
	$view = $header_form['view'];
	$po_header_id = $header_form['po_header_id'];
	
		
	mysql_query("
		insert into
			sub_spo_detail
		set
			spo_detail_id = '$spo_detail_id',
			sub_description = '$sub_description',
			quantity = '$quantity',
			unit = '$unit',
			unit_cost = '$unit_cost',
			amount = '$amount',
			chargables = '$chargables',
			person = '$person'
	") or $objResponse->alert(mysql_error());
	
	$objResponse->alert("Details Successfully Added.");	
	$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");

	
	return $objResponse;
}

?>