<?php

function returns_stock_id_form($stock_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$stock = $options->attr_stock($stock_id,'stock');
	
	$content = "
		<div class='module_actions'>
			<div class='form-div'>
				Quantity : <br>
				<input type='text' class='textbox' name='quantity' id='qty' value='' autocomplete=\"off\">
			</div>
			<div class='form-div'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Return Item' name='b' onclick=xajax_returns_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />
			</div>
		</div>
		
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	/*$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"htmlentities($stock)\" );");*/
	$objResponse->script('openDialog();');
	return $objResponse;
}

function returns_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$quantity	= $form_data['quantity'];
	$stock_id	= $form_data['stock_id'];
	
	$return_header_id	= $form_data2['return_header_id'];
	$project_id			= $form_data2['project_id'];	
	$view				= $form_data2['view'];
	
	
	$scope_of_work			= $form_data2['scope_of_work'];
	$work_category_id 		= $form_data2['work_category_id'];
	$sub_work_category_id 	= $form_data2['sub_work_category_id'];
	
	$date = $options->getAttribute('return_header','return_header_id',$return_header_id,'date');
	
	#$project_qty 	= $options->inventory_projectqty(NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
	#$issued_qty		= $options->issuance_issuedToProject($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work);
	#$remaining = $project_qty - $issued_qty;
	
	$project_qty	= $options->inventory_projectwarehousebalance($date,$stock_id,$project_id);
	
	#$objResponse->alert($project_qty);
	
	if($project_qty < $quantity){
		$objResponse->alert("Error : Not enough quantity");	
	}else{

		mysql_query("
			insert into 
				return_detail
			set
				stock_id = '$stock_id',
				quantity = '$quantity',
				return_header_id = '$return_header_id'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Item Successfully Returned.");	
		$objResponse->redirect("admin.php?view=$view&return_header_id=$return_header_id");
	}
	
	return $objResponse;
}

	
?>