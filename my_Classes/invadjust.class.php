<?php
		
	function removeInventoryAdjustmentDetail($invadjust_detail_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			delete from
				invadjust_details
			where
				invadjust_detail_id = '$invadjust_detail_id'
		";
		
		mysql_query($query) or die(mysql_error());
		
		$objResponse->script("xajax_getUpdatedInventoryAdjustmentTable(xajax.getFormValues('header_form'))");
		return $objResponse;
	}
	
	function getUpdatedInventoryAdjustmentTable($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$invadjust_header_id = $form_data['invadjust_header_id'];
		$content=$options->getUpdatedInventoryAdjustmentTable($invadjust_header_id);
		$objResponse->assign("table_container","innerHTML",$content);
		return $objResponse;	
	}
	
	function addInventoryAdjustmentDetail($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$invadjust_header_id	= $form_data[invadjust_header_id];
		$stock_id				= $form_data[stock_id];
		$quantity				= $form_data[quantity];

		
		$query="
			insert into
				invadjust_details
			set
				invadjust_header_id = '$invadjust_header_id',
				stock_id = '$stock_id',
				quantity = '$quantity'
		";
		
		//$objResponse->alert($query);
		mysql_query($query) or die(mysql_error());
		$objResponse->assign("stock_name","value","");
		$objResponse->assign("quantity","value","");
		
		$objResponse->script("xajax_getUpdatedInventoryAdjustmentTable(xajax.getFormValues('header_form'))");
		
		return $objResponse;
	}
?>