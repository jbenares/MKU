<?php
function display_subworkcategory($work_category_id,$sub_work_category_id = NULL){
	$objResponse	= new xajaxResponse();
	$options		= new options();
	
	$arg = array(
		'level' => '2',
		'work_subcategory_id' => $work_category_id
	);
	
	$content=$options->option_workcategory($sub_work_category_id,'sub_work_category_id','Select Sub Work Category',$arg);
	
	$objResponse->script("j(\"#subworkcategory_div\").show();");
	$objResponse->assign('subworkcategory','innerHTML',$content);
	
	return $objResponse;
}

function update_budget($id,$form_data)
{
	//$objResponse>alert("Query Successfull!");
	$objResponse	= new xajaxResponse();
	$objResponse->script("xajax_update(j('#q_$id').val(),$id);toggleBox('demodiv',1);");
	/*$objResponse>alert("Query Successfull!");
	$objResponse->script("window.location.reload();");
	*/
	return $objResponse;
}

function update($qty,$id){
	$objResponse = new xajaxResponse();
	//$objResponse->alert($qty."-".$id);
	$sq=mysql_query("select * from budget_detail WHERE budget_detail_id = '$id'");
	$r=mysql_fetch_assoc($sq);
	
	$amount = $r[cost] * $qty;
	$q=mysql_query("update budget_detail set quantity = '$qty',amount='$amount' WHERE budget_detail_id='$id'");
	$objResponse->script("toggleBox('demodiv',0)");
	//$objResponse->script("window.location.reload();");
	//$objResponse->script("showBox()");
	//$objResponse->alert($qty."-".$id);
	if(!mysql_error()) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
			else
				$objResponse->alert(mysql_error());
	
	//$objResponse->alert($qty."-".$id);
	
	return $objResponse;
}
?>