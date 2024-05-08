<?php
	function getOrderDetailsForJO($order_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$content=$options->table_orderForJO($order_header_id);
		
		$objResponse->assign("table_container","innerHTML",$content);
		return $objResponse;	
	}
	
?>