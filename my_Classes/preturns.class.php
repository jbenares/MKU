<?php
	function addPReturnDetails($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$preturn_header_id		= $form_data[preturn_header_id];
		$stock_id				= $form_data[stock_id];
		$quantity				= $form_data[quantity];
		$cost					= $form_data[cost];
		$amount					= $form_data[amount];
		
		
		$query="
			insert into
				preturn_details
			set
				preturn_header_id='$preturn_header_id',
				stock_id='$stock_id',
				quantity='$quantity',
				cost='$cost',
				amount='$amount'
		";
		mysql_query($query);
		
		$objResponse->script("xajax_refreshPReturnDetails(xajax.getFormValues('header_form'))");
		$objResponse->assign("stock_name","value","");
		$objResponse->assign("stock_id","value","");
		$objResponse->assign("quantity","value","");
		$objResponse->assign("cost","value","");
		$objResponse->assign("amount","value","");
		
		return $objResponse;
	}
	
	function removePReturnDetails($preturn_detail_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			delete from
				preturn_details
			where
				preturn_detail_id='$preturn_detail_id'
		";
		mysql_query($query);
		
		$objResponse->script("xajax_refreshPReturnDetails(xajax.getFormValues('header_form'))");
		
		return $objResponse;
	}
	
	function refreshPReturnDetails($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$preturn_header_id=$form_data[preturn_header_id];
		
		$content=$options->getUpdatedPReturnTable($preturn_header_id,$void);
		
		$objResponse->assign("table_container","innerHTML",$content);
		
		return $objResponse;
	}
	
?>