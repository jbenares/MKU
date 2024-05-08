<?php
function getDataFromInvoiceNo($form_data){
	$objResponse 	= new xajaxResponse();
	$options 		= new options();
	
	$invoice_no = $form_data['invoice_no'];
	$result = mysql_query("select * from sales_invoice where invoice_no = '$invoice_no' and status != 'C'") or $objResponse->alert(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	$date					= $r['date'];
	$project_id				= $r['project_id'];
	$project 				= $options->getAttribute('projects','project_id',$project_id,'project_name');
	$amount					= $r['amount'];
	
	$objResponse->assign("project_id",'value',$project_id);
	$objResponse->assign('project','value',$project);
	$objResponse->assign('amount','value',$amount);
	
	return $objResponse;
}

function getLatestORSeries($form_data){
	$objResponse 	= new xajaxResponse();
	$options 		= new options();
	
	$or_type = $form_data['or_type'];
	
	$result = mysql_query("
		select
			*
		from
			cr_header
		where
			or_type = '$or_type'
		order by
			or_no desc
	") or $objResponse->alert(mysql_error());
	
	$r 		= mysql_fetch_assoc($result);
	$or_no 	= $r['or_no'];
	
	$or_no++;
	
	$objResponse->assign('or_no','value',$or_no);
	
	return $objResponse;
}
?>