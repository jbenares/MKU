<?php
function service_po_stock_id_form($stock_id,$quantity,$days,$rate_per_day,$amount){
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
				<input type='button' value='Add Item' name='b' onclick=xajax_service_po_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
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

function service_po_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	$stock_id		= $form_data['stock_id'];
	$quantity		= $form_data['quantity'];
	$days			= $form_data['days'];
	$rate_per_day	= $form_data['rate_per_day'];
	$amount			= $form_data['amount'];
	
	
	$project_id		= $form_data2['project_id'];
	$po_header_id	= $form_data2['po_header_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	
	$service_rr_header_id	= $form_data2['service_rr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$pr_amount	= $options->service_pr($pr_header_id,$stock_id);
	$po_amount = $options->service_po_pr($pr_header_id,$stock_id);
	
	
	$balance = $pr_amount - $po_amount;
	
	if($amount > $balance){
		$objResponse->alert("AMOUNT GIVEN IS GREATER THAN PR BALANCE");	
	}else{
		mysql_query("
			insert into
				po_service_detail
			set	
				po_header_id		= '$po_header_id',
				stock_id			= '$stock_id',
				quantity			= '$quantity',
				days				= '$days',
				rate_per_day		= '$rate_per_day',
				amount				= '$amount'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Service Transacted Successfully.");	
		$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
	}
	return $objResponse;
}

	function search_purchase_request($pr_header_id){
		$objResponse	= new xajaxResponse();
		
		$objResponse->script("closeDialog();");
		$objResponse->script("xajax_show_purchase_request(\"$pr_header_id\")");
		
		return $objResponse;
	}


	function show_purchase_request($pr_header_id = NULL){
		$objResponse 	= new xajaxResponse();
		$options		= new options();	
		
		
		$query = "
			select
				*
			from
				pr_header
			where
				approval_status = 'A'
		";
		
		if(!empty($pr_header_id)){
		$query.="
			and
				pr_header_id = '$pr_header_id'
		";
		}
		
		
		$result=mysql_query($query) or $objResponse->alert(mysql_error());
		
		$content.="
			<div class='module_actions'>
				<input type='text' class='textbox' id='search_keyword' >
				<input type='button' value='Search' id='search_button'>
			</div>
		";
		
		
		
		$content.="
		<div id='header_content'>
			<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
				<tr>				
					<th width='20'>#</th>
					<th>PR #</th>
					<th>Project</th>
					<th>Scope of Work</th>
					<th>Work Category</th>
					<th>Sub Work Category</th>
					<th>Description</th>
					<th>Status</th>
					<th>Approval Status</th>
				</tr>  
		";
		
		$i=1;
		while($r=mysql_fetch_assoc($result)) {
			$pr_header_id		= $r['pr_header_id'];
			$pr_header_id_pad	= str_pad($pr_header_id,7,0,STR_PAD_LEFT);
			$project_id			= $r['project_id'];
			$project_name		= $options->attr_Project($project_id,'project_name');
			$project_code		= $options->attr_Project($project_id,'project_code');
			$project_name_code	= ($project_id)?"$project_name - $project_code":"";
			$description		= $r['description'];
			$status				= $r['status'];
			$approval_status	= $r['approval_status'];
			
			$scope_of_work		= $r['scope_of_work'];
			$work_category_id 	= $r['work_category_id'];
			$work_category  = $options->attr_workcategory($work_category_id,'work');
			$sub_work_category_id = $r['sub_work_category_id'];
			$sub_work_category  = $options->attr_workcategory($sub_work_category_id,'work');
		
			$content.="
				<tr class='pr_details' style='cursor:pointer;' rel='$pr_header_id'>
					<td width='20'>".$i++."</td>
					<td>$pr_header_id_pad</td>
					<td>$project_name_code</td>	
					<td>$scope_of_work</td>
					<td>$work_category</td>	
					<td>$sub_work_category</td>	
					<td>$description</td>	
					<td>".$options->getTransactionStatusName($status)."</td>	
					<td>".$options->getApprovalStatus($approval_status)."</td>
				</tr>
			";
		}
		$content.="
			</table>
		</div>
		";
		
		$content.="
			<div id='detail_content' >
				
			</div>
		";
		
		
		
		$content.="	
			<script type='text/javascript'>
				j(\"input:button\").button();
				
				j(\".pr_details\").each(function(){
					j(this).click(function(){
						xajax_show_purchase_request_details(j(this).attr('rel'));
					});
					
					j(this).dblclick(function(){
						xajax_purchase_request_place_details(j(this).attr('rel'));
					});
				});	
				
				j('#search_button').click(function(){
					xajax_search_purchase_request(j('#search_keyword').val());
				});
			</script>
		";

		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", 'PURCHASE REQUEST' );");
		$objResponse->script('openDialog();');
		return $objResponse;
		
	}


	function show_purchase_request_details($pr_header_id){
		$objResponse 	= new xajaxResponse();
		$options		= new options();	
		
		$result=mysql_query("
			select
				*
			from
				pr_detail as d,
				productmaster as pm
			where
				d.pr_header_id	= '$pr_header_id' 
			and
				pm.stock_id = d.stock_id
		") or $objResponse->alert(mysql_error());
		
		$content="
			<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
				<caption class='ui-widget-header' style='padding:3px;'>Purchase Request Details PR # ".str_pad($pr_header_id,7,0,STR_PAD_LEFT)."</caption>
				<tr>				
					<th width='20'>#</th>
					<th>Item</th>
					<th>Quantity</th>
					<th>Unit</th>
					<th>Status</th>
				</tr>  
		";
		
		$i=1;
		while($r=mysql_fetch_assoc($result)) {
			$pr_detail_id		= $r['pr_detail_id'];
			$stock_id			= $r['stock_id'];
			$stock				= $r['stock'];
			$unit				= $r['unit'];
			$quantity			= $r['quantity'];
			$in_stock			= $r['in_stock'];
			$in_budget			= $r['in_budget'];
			$allowed			= $r['allowed'];
			
			$allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
	
			$content.="
				<tr>
                	<td>".$i++."</td>
                    <td>".$stock."</td>
                    <td>".$quantity."</td>
         

         }