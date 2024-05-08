<?php
function service_receive_stock_id_form($stock_id,$quantity,$days,$rate_per_day,$amount){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$stock = $options->attr_stock($stock_id,'stock');
	
	$content = "
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				No : <br>
				<input type='text' class='textbox' name='quantity' id='service_quantity'  value='$quantity' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='days' id='service_days' value='$days' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Rate/Day: <br>
				<input type='text' class='textbox' name='rate_per_day' id='service_cost' value='$rate_per_day' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='amount' id='service_amount' value='$amount' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Add Item' name='b' onclick=xajax_service_receive_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j(\"#service_quantity,#service_days,#service_cost\").keyup(function(){
				var quantity = j(\"#service_quantity\").val();
				var days = j(\"#service_days\").val();
				var cost = j(\"#service_cost\").val();
				
				var amount = quantity * days * cost;
				j(\"#service_amount\").val(amount);
			});
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$stock' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function service_receive_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$stock_id		= $form_data['stock_id'];
	$quantity		= $form_data['quantity'];
	$days			= $form_data['days'];
	$rate_per_day	= $form_data['rate_per_day'];
	$amount			= $form_data['amount'];
	
	
	$project_id		= $form_data2['project_id'];
	$po_header_id	= $form_data2['po_header_id'];
	
	$service_rr_header_id	= $form_data2['service_rr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	//$po_amount = $options->service_po($po_header_id,$stock_id);
	//$service_received = $options->service_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
	$po_amount = $options->service_po($po_header_id,$stock_id);
	$rr_amount = $options->service_rr_po($po_header_id,$stock_id);
	
	$balance = $po_amount - $rr_amount;
	
	if($amount > $balance){
		$objResponse->alert("AMOUNT GIVEN IS GREATER THAN BALANCE");	
	}else{
		mysql_query("
			insert into
				service_rr_detail
			set	
				service_rr_header_id		= '$service_rr_header_id',
				stock_id			= '$stock_id',
				quantity			= '$quantity',
				days				= '$days',
				rate_per_day		= '$rate_per_day',
				amount				= '$amount'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Service Received Successfully.");	
		$objResponse->redirect("admin.php?view=$view&service_rr_header_id=$service_rr_header_id");
	}
	return $objResponse;
}
	
?>