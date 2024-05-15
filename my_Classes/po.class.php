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

function equipment_po_stock_id_form($stock_id,$quantity,$days,$rate_per_day,$amount){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$stock = $options->attr_stock($stock_id,'stock');
	
	$content = "
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				No : <br>
				<input type='text' class='textbox' name='quantity' id='equipment_quantity'  value='$quantity' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='days' id='equipment_days' value='$days' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Rate/Day: <br>
				<input type='text' class='textbox' name='rate_per_day' id='equipment_cost' value='$rate_per_day' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='amount' id='equipment_amount' value='$amount' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='button' value='Add Item' name='b' onclick=xajax_equipment_po_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j(\"#equipment_quantity,#equipment_days,#equipment_cost\").keyup(function(){
				var quantity = j(\"#equipment_quantity\").val();
				var days = j(\"#equipment_days\").val();
				var cost = j(\"#equipment_cost\").val();
				
				var amount = quantity * days * cost;
				j(\"#equipment_amount\").val(amount);
			});
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$stock' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function equipment_po_stock_id($form_data,$form_data2){
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
	
	$equipment_rr_header_id	= $form_data2['equipment_rr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$pr_amount	= $options->equipment_pr($pr_header_id,$stock_id);
	$po_amount = $options->equipment_po_pr($pr_header_id,$stock_id);
	
	
	$balance = $pr_amount - $po_amount;
	
	if($amount > $balance){
		$objResponse->alert("AMOUNT GIVEN IS GREATER THAN PR BALANCE");	
	}else{
		mysql_query("
			insert into
				po_equipment_detail
			set	
				po_header_id		= '$po_header_id',
				stock_id			= '$stock_id',
				quantity			= '$quantity',
				days				= '$days',
				rate_per_day		= '$rate_per_day',
				amount				= '$amount'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Equipment Rental Transacted Successfully.");	
		$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
	}
	return $objResponse;
}

function fuel_po_stock_id_form($pr_fuel_detail_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$result = mysql_query("
		select
			*
		from
			pr_fuel_detail
		where
			pr_fuel_detail_id = '$pr_fuel_detail_id'
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	
	$pr_fuel_detail_id	= $r['pr_fuel_detail_id'];
	$fuel_id					= $r['fuel_id'];
	$equipment_id				= $r['equipment_id'];
	$consumption_per_day		= $r['consumption_per_day'];
	$request_quantity			= $r['request_quantity'];
	$warehouse_quantity			= $r['warehouse_quantity'];
	$total_quantity			 	= $r['quantity'];
	
	$days						= $r['days'];
	$cost_per_litter			= $r['cost_per_litter'];
	$amount						= $r['amount'];
	
	$po_quantity = $request_quantity - $warehouse_quantity;
	
	
	$fuel = $options->attr_stock($fuel_id,'stock');
	$equipment = $options->attr_stock($equipment_id,'stock');
	
	$content = "
		<input type='hidden' name='fuel_id' value='$fuel_id' >
		<input type='hidden' name='equipment_id' value='$equipment_id' >
	
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				Consumption / Day : <br>
				<input type='text' class='textbox' name='consumption_per_day' id='fuel_consumption'  autocomplete='off'>
			</div>
		
			<div class='form-div'>
				Request Quantity : <br>
				<input type='text' class='textbox' id='fuel_quantity'  autocomplete='off' name='request_quantity' >
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='fuel_days' id='fuel_days' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Fuel Cost/Litter: <br>
				<input type='text' class='textbox' name='fuel_cost_per_litter' id='fuel_cost' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='fuel_amount' id='fuel_amount' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='button' value='Add Item' name='b' onclick=xajax_fuel_po_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j(\"#fuel_quantity,#fuel_days,#fuel_cost,#fuel_consumption,#fuel_warehouse_quantity\").keyup(function(){
				var quantity = j(\"#fuel_quantity\").val();
				var days = j(\"#fuel_days\").val();
				var cost = j(\"#fuel_cost\").val();
				var consumption	= j('#fuel_consumption').val();
				var warehouse = j('#fuel_warehouse_quantity').val();
				
				var amount = ((consumption * quantity * days) ) * cost ;
				j(\"#fuel_amount\").val(amount);
			});
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$fuel | $equipment' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function fuel_po_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$fuel_id				= $form_data['fuel_id'];
	$equipment_id			= $form_data['equipment_id'];
	$consumption_per_day	= $form_data['consumption_per_day'];
	$fuel_quantity			= $form_data['fuel_quantity'];
	$fuel_days				= $form_data['fuel_days'];
	$fuel_cost_per_litter	= $form_data['fuel_cost_per_litter'];
	$fuel_amount			= $form_data['fuel_amount'];
	$request_quantity		= $form_data['request_quantity'];
	$warehouse_quantity		= $form_data['warehouse_quantity'];
	
	
	$project_id		= $form_data2['project_id'];
	$po_header_id	= $form_data2['po_header_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	
	$fuel_rr_header_id	= $form_data2['fuel_rr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$pr_amount	= $options->fuel_pr($pr_header_id,$fuel_id,$equipment_id);
	$po_amount = $options->fuel_po_pr($pr_header_id,$fuel_id,$equipment_id);
	
	
	$balance = $pr_amount - $po_amount;
	
	if($fuel_amount > $balance){
		$objResponse->alert("AMOUNT GIVEN IS GREATER THAN PR BALANCE");	
	}else{
		mysql_query("
			insert into
				po_fuel_detail
			set	
				po_header_id		= '$po_header_id',
				fuel_id				= '$fuel_id',
				equipment_id		= '$equipment_id',
				consumption_per_day	= '$consumption_per_day',
				request_quantity	= '$request_quantity',
				days				= '$fuel_days',
				cost_per_litter		= '$fuel_cost_per_litter',
				amount				= '$fuel_amount'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Fuel Rental Transacted Successfully.");	
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
		select * 
		from pr_header as h,
		pr_detail as d,
		productmaster as p
		where
		h.pr_header_id = d.pr_header_id and
		p.stock_id = d.stock_id and
		h.`status` != 'C' and
		h.approval_status = 'A' and
		h.status = 'F' and
		(h.pr_header_id = '$pr_header_id' or p.stock like '%$pr_header_id%')
		group by
		h.pr_header_id
	";
	
	/*if(!empty($pr_header_id)){
	$query.="
		and
			(pr_header_id = '$pr_header_id'
	";
	}*/
	
	$query.="
		order by h.date desc, h.pr_header_id desc
		limit 0,20
	";	
	
	//$objResponse->alert($query);
	$result=mysql_query($query) or $objResponse->alert(mysql_error());
	
	$content.="<div class='module_actions'>
					<input type='text' class='textbox' id='search_keyword' >
					<input type='button' value='Search' id='search_button'>
				</div>";
	$content.="<div id='header_content'>
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
					</tr>";
	
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
				<td>".htmlentities($description)."</td>	
				<td>".$options->getTransactionStatusName($status)."</td>	
				<td>".$options->getApprovalStatus($approval_status)."</td>
			</tr>";
	}
	$content.="</table></div>
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
	
	//$objResponse->alert($content);
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", 'PURCHASE REQUEST' );");
	$objResponse->script('openDialog();');
	return $objResponse;
	
}


function show_purchase_request_details($pr_header_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	/***************************
	MATERIALS
	***************************/
	
	
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
	
	$rows = mysql_num_rows($result);
	if($rows > 0):
	
	$content="
		<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
			<caption class='ui-widget-header' style='padding:3px;'>Purchase Request Details PR # ".str_pad($pr_header_id,7,0,STR_PAD_LEFT)."</caption>
			<tr>				
				<th width='20'>#</th>
				<th>Item</th>
				<th>Request Quantity</th>
				<th>Warehouse Quantity</th>
				<th>Total Quantity</th>
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
		$request_quantity	= $r['request_quantity'];
		$warehouse_quantity = $r['warehouse_quantity'];
		$in_stock			= $r['in_stock'];
		$in_budget			= $r['in_budget'];
		$allowed			= $r['allowed'];
		
		$allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";

		$content.="
			<tr>
				<td>".$i++."</td>
				<td>".$stock."</td>
				<td>".$request_quantity."</td>
				<td>".$warehouse_quantity."</td>
				<td>".$quantity."</td>
				<td>$unit</td>
				<td>$allowed_name</td>
			</tr>
		";
	}
	$content.="
		</table>
	";
	endif;
	
	/*************************
	SERVICE
	*************************/
	
	$result=mysql_query("
		select
			*
		from
			pr_service_detail as d,
			productmaster as pm
		where
			d.pr_header_id	= '$pr_header_id' 
		and
			pm.stock_id = d.stock_id
	") or $objResponse->alert(mysql_error());
	$rows = mysql_num_rows($result);
	if($rows > 0):
	
	$content.="
		<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
			<caption class='ui-widget-header' style='padding:3px;'>Purchase Request Service Details PR # ".str_pad($pr_header_id,7,0,STR_PAD_LEFT)."</caption>
			<tr>				
				<th width='20'>#</th>
				<th>Designation</th>
				<th>No</th>
				<th>No. of Days</th>
				<th>Rate/Day</th>
				<th>Amount</th>
				<th>Status</th>
			</tr>  
	";
	
	$i=1;
	while($r=mysql_fetch_assoc($result)) {		
		$stock_id			= $r['stock_id'];
		$stock				= $r['stock'];
		$quantity			= $r['quantity'];
		$days				= $r['days'];
		$rate_per_day		= $r['rate_per_day'];
		$unit				= $r['unit'];
		$amount				= $r['amount'];
		$allowed			= $r['allowed'];

		$allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
		$content.="
			<tr>
				<td>".$i++."</td>
				<td>".$stock."</td>
				<td class='align-right'>".number_format($quantity,2,'.',',')."</td>
				<td class='align-right'>".$days."</td>
				<td class='align-right'>".number_format($rate_per_day,2,'.',',')."</td>
				<td class='align-right'>".number_format($amount,2,'.',',')."</td>
				<td>".$allowed_name."</td>
			</tr>
		";
	}
	$content.="
		</table>
	";
	
	endif;
	
	/*************************
	EQUIPMENT
	*************************/
	
	$result=mysql_query("
		 	select
				*
			from
				pr_equipment_detail as d,
				productmaster as pm
			where
				d.pr_header_id	= '$pr_header_id' 
			and
				pm.stock_id = d.stock_id
	") or $objResponse->alert(mysql_error());
	$rows = mysql_num_rows($result);
	if($rows > 0):
	
	$content.="
		<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
			<caption class='ui-widget-header' style='padding:3px;'>Purchase Request Equipment Details PR # ".str_pad($pr_header_id,7,0,STR_PAD_LEFT)."</caption>
			<tr>				
				<th width='20'>#</th>
				<th>Description</th>
				<th width='60'>No</th>
				<th width='60'>No. of Days</th>
				<th width='60'>Rental/Day</th>
				<th width='100'>Amount</th>
				<th width='100'>Status</th>
			</tr>  
	";
	
	$i=1;
	while($r=mysql_fetch_assoc($result)) {		
		$pr_equipment_detail_id		= $r['pr_equipment_detail_id'];
		$stock_id			= $r['stock_id'];
		$stock				= $r['stock'];
		$quantity			= $r['quantity'];
		$days				= $r['days'];
		$rate_per_day		= $r['rate_per_day'];
		$unit				= $r['unit'];
		$amount				= $r['amount'];
		$allowed			= $r['allowed'];
		
		$allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
		
		$equipment_received = $options->equipment_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
		$budget = $options->equipment_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
		$equipment_approved_request = $options->equipment_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id);
		$content.="
			<tr>
				<td>".$i++."</td>	
				<td>".$stock."</td>
				<td>".$quantity."</td>
				<td>".$days."</td>
				<td>".$rate_per_day."</td>
				<td class=\"align-right\">".number_format($amount,2,'.',',')."</td>
				<td>".$allowed_name."</td>
			</tr>
		";
	}
	$content.="
		</table>
	";
	endif;
	
	/*************************
	FUEL
	*************************/
	
	$result=mysql_query("
		 	select
				*
			from
				pr_fuel_detail
			where
				pr_header_id	= '$pr_header_id' 
	") or $objResponse->alert(mysql_error());
	$rows = mysql_num_rows($result);
	if($rows > 0):
	
	$content.="
		<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
			<caption class='ui-widget-header' style='padding:3px;'>Purchase Request Fuel Details PR # ".str_pad($pr_header_id,7,0,STR_PAD_LEFT)."</caption>
			<tr>				
				<th width='20'>#</th>
				<th>Fuel</th>
				<th>Equipment</th>
				<th width='60'>Consumption / Day</th>
				<th width='60'>No. of Days</th>
				<th width='60'>Quantity</th>
				
				<th width='60'>Fuel Request</th>
				<th width='60'>Warehouse Request</th>
				<th width='60'>Total Fuel Quantity</th>
				
				<th width='60'>Fuel Cost/Litter</th>
				<th width='100'>Amount</th>
				<th width='100'>Status</th>
			</tr>  
	";
	
	$i=1;
	while($r=mysql_fetch_assoc($result)) {		
		$pr_fuel_detail_id	= $r['pr_fuel_detail_id'];
		$fuel_id					= $r['fuel_id'];
		$equipment_id				= $r['equipment_id'];
		$consumption_per_day		= $r['consumption_per_day'];
		$request_quantity			= $r['request_quantity'];
		$warehouse_quantity			= $r['warehouse_quantity'];
		$total_quantity			 	= $r['quantity'];
		
		$days						= $r['days'];
		$cost_per_litter			= $r['cost_per_litter'];
		$amount						= $r['amount'];
		$allowed			= $r['allowed'];
		$fuel		= $options->attr_stock($fuel_id,'stock');
		$equipment	= $options->attr_stock($equipment_id,'stock');
		
		$allowed_name		= ($allowed)?"ALLOWED":"NOT ALLOWED";
		
		$content.="
			<tr>
				<td>".$i++."</td>
				<td>$fuel</td>
				<td>$equipment</td>
				<td>$consumption_per_day</td>
				<td>$days</td>
				
				<td>$request_quantity</td>
				<td>".$request_quantity * $consumption_per_day * $days."</td>
				<td>$warehouse_quantity</td>
				<td>$total_quantity</td>
				
				
				<td>$cost_per_litter</td>
				<td class=\"align-right\">".number_format($amount,2,'.',',')."</td>
				<td>".$allowed_name."</td>
			</tr>
		";
	}
	$content.="
		</table>
	";
	endif;
	
	$objResponse->assign('detail_content','innerHTML',$content);
	return $objResponse;
	
}

function purchase_request_place_details($pr_header_id){
	$objResponse	= new xajaxResponse();
	$options		= new options();
	
	$result=mysql_query("
		select
			  *
		 from
			pr_header
		where 
			pr_header_id = '$pr_header_id'
	") or $objResponse->alert(mysql_error());
	
	$r=mysql_fetch_assoc($result);
	$pr_header_id_pad	= str_pad($pr_header_id,7,0,STR_PAD_LEFT);
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$scope_of_work			= $r['scope_of_work'];
	$work_category_id		= $r['work_category_id'];
	$sub_work_category_id	= $r['sub_work_category_id'];
	
	$work_category	= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
	
	
	
	$objResponse->assign('pr_name','value',$pr_header_id_pad);
	$objResponse->assign('pr_header_id','value',$pr_header_id);
	$objResponse->assign('project_display','value',$project_name_code);
	$objResponse->assign('project_id','value',$project_id);
	
	$objResponse->assign('scope_of_work','value',$scope_of_work);
	$objResponse->assign('work_category_id','value',$work_category_id);
	$objResponse->assign('work_category','value',$work_category);
	$objResponse->assign('sub_work_category_id','value',$sub_work_category_id);
	$objResponse->assign('sub_work_category','value',$sub_work_category);
	
	$objResponse->script('closeDialog();');
	
	return $objResponse;
}


function getProjectFromPR($pr_header_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$result=mysql_query("
		select
			*
		from
			pr_header as h, projects as p
		where
			h.project_id = p.project_id
		and
			pr_header_id = '$pr_header_id'
	") or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$project_id 	= $r['project_id'];
	$project_code	= $r['project_code'];
	$project_name	= $r['project_name'];
	
	$project_display = "$project_name - $project_code";
	
	$objResponse->assign('project_id','value',$project_id);
	$objResponse->assign('project_display','value',$project_display);
	
	return $objResponse;
}


function po_stock_id_form($stock_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$cost	= $options->attr_stock($stock_id,'cost');
	$stock	= mysql_real_escape_string($options->attr_stock($stock_id,'stock'));
	
	$content = "
		<div class='module_actions'>
			<table>
				<tr>
					<td>Item</td>
					<td>".lib::getTableAssoc('','stock_id','Select Item',"select * from productmaster where parent_stock_id = '$stock_id' or stock_id = '$stock_id'",'stock_id','stock')."</td>
					<input type='hidden' name='parent_stock_id' value='$stock_id' >
				</tr>
				<tr>
					<td>Details:</td>
					<td><input type='text' class='textbox' name='details' value=''></td>
				</tr>
				<tr>
					<td>Quantity :</td>
					<td><input type='text' class='textbox' name='quantity' value='' id='quantity' autocomplete='off' ></td>
				</tr>
				
				<tr>
					<td>Cost :</td>
					<td><input type='text' class='textbox' name='cost' value='$cost' id='cost'></td>
				</tr>
				<tr>
					<td>Discount :</td>
					<td><input type='text' class='textbox' name='discount' value='0.00' id='discount'></td>
				</tr>
				
				<tr>
					<td>Amount :</td>
					<td><input type='text' class='textbox' name='amount' value='$amount' id='amount' autocomplete='off' ></td>
				</tr>
				
				<!--<tr>
					<td>Factor :</td>
					<td><input type='text' class='textbox' name='factor' value='$factor' id='factor' ></td>
				</tr> -->
				
				<tr>
					<td>Chargables :</td>
					<td><input type='text' class='textbox' name='chargables' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>Person :</td>
					<td><input type='text' class='textbox' name='person' autocomplete=\"off\" ></td>
				</tr>
				
				<tr>
					<td>
						<!--<input type='hidden' name='stock_id' value='$stock_id' > -->
						<input type='button' value='Add Item' name='b' onclick=xajax_po_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
					</td>
				</tr>
			</table>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();			
			
			jQuery('#quantity, #cost, #discount').keyup(function(){
				var quantity = jQuery('#quantity').val();
				var cost	 = jQuery('#cost').val();
				var discount	 = jQuery('#discount').val();
				jQuery('#amount').val(quantity * (cost - discount));
			});
			
			jQuery('#amount').keyup(function(){
				var amount 		= jQuery('#amount').val();
				var quantity 	= jQuery('#quantity').val();
				
				jQuery('#cost').val(amount / quantity);
			});
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}

	function admin_override($po_detail_id, $po_header_id, $stock, $stock_id, $quantity, $cost, $view, $form_data){
		$objResponse 	= new xajaxResponse();
		$options		= new options();	
		
		$stock 		= addslashes(htmlentities($options->attr_stock($stock_id,'stock')));
		
		$driver_dd = $options->getTableAssoc(NULL,'driverID',"Select Driver","select * from drivers order by driver_name asc","driverID","driver_name");

		$content = "
			<div class='module_actions'>
				<input type='hidden' name='view' value='$view' >
				<input type='hidden' name='stock_id' value='$stock_id' >
				<input type='hidden' name='po_detail_id' value='$po_detail_id' >
				<input type='hidden' name='po_header_id' value='$po_header_id' >
				
				<div class='form-div'>
					Stock : 
					". $stock . "
				</div>
				
				
				<div class='form-div'>
					Quantity : <br>
					<input type='text' class='textbox3' name='quantity' id='quantity' value='".$quantity."' autocomplete=\"off\">
				</div>
				
		
				<div class='form-div'>
					Price : <br>
					<input type='text' class='textbox3' id='cost' name='cost'   value='".$cost."'>
				</div>
			
			
				<hr style='border:none; border-top:1px solid #CCC;' >
				
				<div class='form-div'>
					Password: <br>
					<input type='password' class='textbox3' name='password'>
				</div>
				
				
				
				<div class='form-div'>
					<input type='button' value='Edit Item' name='b' onclick=xajax_override(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />
				</div>
			</div>
			
				
		";
		
		$objResponse->assign('dialog_content','innerHTML',$content);
		$objResponse->script('openDialog();');
		//$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"$stock\" );");
		return $objResponse;
	}

function override($form_data,$form_data2){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
		$po_header_id	= $form_data['po_header_id'];
		$po_detail_id	= $form_data['po_detail_id'];
		$quantity		= $form_data['quantity'];
		$stock_id		= $form_data['stock_id'];
		$cost			= $form_data['cost'];
		$view			= $form_data['view'];

		$amount			= $quantity * $cost;
		$password		= $form_data['password'];
		
		
		// if($project_id == 14){
		// 	#FOR WAREHOUSE	
		// 	$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);
		// }else{
		// 	#FOR PROJECT
		// 	$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
			
		// }

		//$project_warehouse_qty = $options->inventory_warehouse($date,$stock_id);	
		
	
		
		// $balance = $project_warehouse_qty;
		
		// $balance = round($balance,6);
		// $quantity = round($quantity,6);
		
		
		// if($balance < $quantity){
		// 	$objResponse->alert("Error : Quantity is Greater than Balance or Project does not have enough STOCKS");	
		// }else{
			
		

	  $sql = mysql_query("select userID from  admin_access where (password='$password' or password = '$pw') and active='1' and access = '2'");
	  $num = mysql_num_rows($sql);
	 	 if($num > 0){
		  $fetch =mysql_fetch_assoc($sql);
		  $approver = $fetch['userID'];
		  $now = date("Y-m-d H:i:s");
		  mysql_query("
					insert into
						override_logs
					set
						date = '$now',
						po_detail_id = '$po_detail_id',
						quantity = '$quantity',
						cost = '$cost',
						approved_by = '$approver'
				") or $objResponse->alert(mysql_error());
			  

			  $sql_disc = mysql_query("select discount from  po_details where po_detail_id = '$po_detail_id'");
			  $fetch_disc = mysql_fetch_assoc($sql_disc);
			  $discount = $fetch_disc['discount'];
 				$total = $amount - $discount;
				mysql_query("
					update
						po_detail
					set
						quantity='$quantity', 
						cost='$cost',
						amount = $total
					where
						po_detail_id='$po_detail_id'
				") or $objResponse->alert(mysql_error());
				
				$objResponse->alert("PO data Successfully updated.");	
				$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
			
			return $objResponse;
		} else {
			$objResponse->alert("Password incorrect.");	
			//$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
			return $objResponse;
		}
	}

function po_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$parent_stock_id = $form_data['parent_stock_id'];
	
	$details	= $form_data['details'];	
	
	$quantity		= $form_data['quantity'];
	$stock_id		= $form_data['stock_id'];
	$amount			= $form_data['amount'];
	$cost			= $form_data['cost'];
	$discount		= $form_data['discount'];
	$factor			= $form_data['factor'];	 #not used
	
	$chargables		= $form_data['chargables'];
	$person			= $form_data['person'];
	$amount			= $form_data['amount'];
	
	$po_header_id	= $form_data2['po_header_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	$view			= $form_data2['view'];
	
	//$balance	= $options->po_getBalance($pr_header_id,$stock_id);
	$balance	= $options->po_getBalance($pr_header_id,$parent_stock_id);
	
	$balance = round($balance,8);
	$quantity = round($quantity,8);
	
	#if stock_id is empty prompt error
	if(empty($stock_id)){
		$objResponse->alert("Fill in Item");	
		return $objResponse;
	}
	
	
	if($balance < $quantity){
		$objResponse->alert("Error : Quantity is Greater than Balance");	
	}else{
		
		mysql_query("
			insert into
				po_detail
			set
				details	= '$details',
				stock_id = '$stock_id',
				quantity = '$quantity',
				cost = '$cost',
				discount = '$discount',
				amount = '$amount',
				po_header_id = '$po_header_id',
				chargables = '$chargables',
				person = '$person'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Item Successfully Ordered.");	
		$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
	}
	
	return $objResponse;
}

function po_warehouse_stock_id_form($stock_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$cost	= $options->attr_stock($stock_id,'cost');
	$stock	= mysql_real_escape_string($options->attr_stock($stock_id,'stock'));
	
	$content = "
		<div class='module_actions'>
			<table>
				<tr>
					<td>Details:</td>
					<td><input type='text' class='textbox3' name='details' value=''></td>
				</tr>
				<tr>
					<td>Quantity :</td>
					<td><input type='text' class='textbox3' name='quantity' value=''></td>
				</tr>
				
				<tr>
					<td>Cost :</td>
					<td><input type='text' class='textbox3' name='cost' value='$cost'></td>
				</tr>
				
				<tr>
					<td>
						<input type='hidden' name='stock_id' value='$stock_id' >
						<input type='button' value='Add Item' name='b' onclick=xajax_po_warehouse_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
					</td>
				</tr>
			</table>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			j(\"#dialog\").dialog(\"option\",\"title\",\"$stock | MCD\");
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}



function po_warehouse_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$details	= $form_data['details'];	
	$quantity	= $form_data['quantity'];
	$stock_id	= $form_data['stock_id'];
	$cost		= $form_data['cost'];
	
	$amount		= $quantity * $cost;
	
	$po_header_id	= $form_data2['po_header_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	$view			= $form_data2['view'];
	
	$balance	= $options->po_getBalance($pr_header_id,$stock_id,0);
	
	if($balance < $quantity){
		$objResponse->alert("Error : Quantity is Greater than Balance");	
	}else{
		
		mysql_query("
			insert into
				po_detail
			set
				details	= '$details',
				stock_id = '$stock_id',
				quantity = '$quantity',
				cost = '$cost',
				amount = '$amount',
				po_header_id = '$po_header_id'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Item Successfully Ordered.");	
		$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
	}
	
	return $objResponse;
}

function fuel_po_warehouse_stock_id_form($pr_fuel_detail_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$result = mysql_query("
		select
			*
		from
			pr_fuel_detail
		where
			pr_fuel_detail_id = '$pr_fuel_detail_id'
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	
	$pr_fuel_detail_id	= $r['pr_fuel_detail_id'];
	$fuel_id					= $r['fuel_id'];
	$equipment_id				= $r['equipment_id'];
	$consumption_per_day		= $r['consumption_per_day'];
	$request_quantity			= $r['request_quantity'];
	$warehouse_quantity			= $r['warehouse_quantity'];
	$total_quantity			 	= $r['quantity'];
	
	$days						= $r['days'];
	$cost_per_litter			= $r['cost_per_litter'];
	$amount						= $r['amount'];
	
	$po_quantity = $request_quantity - $warehouse_quantity;
	
	
	$fuel = $options->attr_stock($fuel_id,'stock');
	$equipment = $options->attr_stock($equipment_id,'stock');
	
	$content = "
		<input type='hidden' name='fuel_id' value='$fuel_id' >
		<input type='hidden' name='equipment_id' value='$equipment_id' >
	
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				Consumption / Day : <br>
				<input type='text' class='textbox' name='consumption_per_day' id='fuel_consumption'  autocomplete='off'>
			</div>
		
			<div class='form-div'>
				Request Quantity : <br>
				<input type='text' class='textbox' id='fuel_quantity' name='request_quantity'   autocomplete='off'  >
			</div>
			
			<div class='form-div'>
				No. of Days : <br>
				<input type='text' class='textbox' name='fuel_days' id='fuel_days' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Fuel Cost/Litter: <br>
				<input type='text' class='textbox' name='fuel_cost_per_litter' id='fuel_cost'  autocomplete='off'>
			</div>
			
			<div class='form-div'>
				Amount : <br>
				<input type='text' class='textbox' name='fuel_amount' id='fuel_amount' readonly='readonly' autocomplete='off'>
			</div>
			
			<div class='form-div'>
				<input type='button' value='Add Item' name='b' onclick=xajax_fuel_po_warehouse_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />		
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:button\").button();
			
			j(\"#fuel_quantity,#fuel_days,#fuel_cost,#fuel_consumption,#fuel_warehouse_quantity\").keyup(function(){
				var quantity = j(\"#fuel_quantity\").val();
				var days = j(\"#fuel_days\").val();
				var cost = j(\"#fuel_cost\").val();
				var consumption	= j('#fuel_consumption').val();
				var warehouse = j('#fuel_warehouse_quantity').val();
				
				var amount = ((consumption * quantity * days)) * cost ;
				j(\"#fuel_amount\").val(amount);
			});
	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", '$fuel | $equipment' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}

function fuel_po_warehouse_stock_id($form_data,$form_data2){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$fuel_id				= $form_data['fuel_id'];
	$equipment_id			= $form_data['equipment_id'];
	$consumption_per_day	= $form_data['consumption_per_day'];
	$fuel_quantity			= $form_data['fuel_quantity'];
	$fuel_days				= $form_data['fuel_days'];
	$fuel_cost_per_litter	= $form_data['fuel_cost_per_litter'];
	$fuel_amount			= $form_data['fuel_amount'];
	$request_quantity		= $form_data['request_quantity'];
	$warehouse_quantity		= $form_data['warehouse_quantity'];
	
	
	$project_id		= $form_data2['project_id'];
	$po_header_id	= $form_data2['po_header_id'];
	$pr_header_id	= $form_data2['pr_header_id'];
	
	$fuel_rr_header_id	= $form_data2['fuel_rr_header_id'];
	$work_category_id = $form_data2['work_category_id'];
	$sub_work_category_id = $form_data2['sub_work_category_id'];
	$scope_of_work	= $form_data2['scope_of_work'];
	$view			= $form_data2['view'];
	
	$pr_amount	= $options->fuel_warehouse_pr($pr_header_id,$fuel_id,$equipment_id);
	$po_amount = $options->fuel_po_pr($pr_header_id,$fuel_id,$equipment_id,1);	
	
	
	$balance = $pr_amount - $po_amount;
	
	if($fuel_amount > $balance){
		$objResponse->alert("AMOUNT GIVEN IS GREATER THAN PR BALANCE");	
	}else{
		mysql_query("
			insert into
				po_fuel_detail
			set	
				po_header_id		= '$po_header_id',
				fuel_id				= '$fuel_id',
				equipment_id		= '$equipment_id',
				consumption_per_day	= '$consumption_per_day',
				request_quantity	= '$request_quantity',
				days				= '$fuel_days',
				cost_per_litter		= '$fuel_cost_per_litter',
				amount				= '$fuel_amount'
		") or $objResponse->alert(mysql_error());
		
		$objResponse->alert("Fuel Rental Transacted Successfully.");	
		$objResponse->redirect("admin.php?view=$view&po_header_id=$po_header_id");
	}
	return $objResponse;
}
?>