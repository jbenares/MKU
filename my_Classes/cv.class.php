<?php
function displayTotalCheckAmount($form_data){
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$cleared_check_amount = $options->getCheckStatusAmount(1);
	$uncleared_check_amount = $options->getCheckStatusAmount(0);
	
	$list = $form_data['list'];
	#$objResponse->alert(implode($list,","));
	$total_amount = 0;
	if(!empty($list)){
		foreach($list as $cv_header_id){
			$total_amount += $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'cash_amount');
		}
	}
	$balance = ($uncleared_check_amount - $total_amount) - ($cleared_check_amount + $total_amount);
	
	$objResponse->assign("cleared_checks","value",number_format($cleared_check_amount + $total_amount,2,'.',','));
	$objResponse->assign("uncleared_checks","value",number_format($uncleared_check_amount - $total_amount,2,'.',','));
	$objResponse->assign("balance","value",number_format($balance,2,'.',','));
	
	$objResponse->assign("total_checked_amount","value",number_format($total_amount,2,'.',','));
	return $objResponse;		
}

?>