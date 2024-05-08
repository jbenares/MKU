<?php

function removeFormulationDetail($formulation_detail_id,$formulation_header_id){
	$objResponse=new xajaxResponse();
	$options=new options();
	
	$query="
		delete from
			formulation_details
		where
			formulation_detail_id='$formulation_detail_id'
	";
	mysql_query($query) or $objResponse->alert(mysql_error());
	
	$objResponse->script("xajax_getFormulationTable('$formulation_header_id')");
	
	return $objResponse;
}

function getFormulationTable($formulation_header_id){
	$objResponse=new xajaxResponse();
	$options=new options();
	$content=$options->getFormulationTable($formulation_header_id);
	
	$objResponse->assign("table_container","innerHTML",$content);
	return $objResponse;	
}

function addFormulationDetail($form_data){
	$objResponse=new xajaxResponse();
	$options=new options();
	
	$formulation_header_id	= $form_data[formulation_header_id];
	$stock_id				= $form_data[stock_id];
	$quantity				= $form_data[quantity];
	
	
	$query="
		insert into
			formulation_details
		set
			formulation_header_id='$formulation_header_id',
			stock_id='$stock_id',
			quantity='$quantity'
	";
	mysql_query($query) or $objResponse->alert(mysql_error());
	
	$objResponse->script("xajax_getFormulationTable('$formulation_header_id')");
	
	return $objResponse;
}
	
?>