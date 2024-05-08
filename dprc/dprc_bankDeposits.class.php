<?php
function getUndepositedAmount($form_data){
	$objResponse 	= new xajaxResponse();
	$options   		= new options();
	
	$from_date		= $form_data['from_date'];
	$to_date		= $form_data['to_date'];
	
	$result = mysql_query("
		select
			sum(payment_amount) as total
		from
			dprc_payment
		where
			or_date between '$from_date' and '$to_date'
	") or die(mysql_error());
	
	#FINISHED SHOULD NOT BE CANCELLED
	$r = mysql_fetch_assoc($result);
	
	$total	= ($r['total']) ? $r['total'] : 0;
	
	$objResponse->assign("undeposited","value",$total);
	
	return $objResponse;
}
?>