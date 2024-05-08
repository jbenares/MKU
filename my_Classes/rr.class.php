<?php
function asset_details_form($rr_detail_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$result = mysql_query("
		select * from rr_detail where rr_detail_id = '$rr_detail_id'
	") or $objResponse->alert(mysql_error());
	$r = mysql_fetch_assoc($result);
	$content="
		
		<input type='hidden' name='rr_detail_id' value='$rr_detail_id'>
		<table class='table-contents' style='display:inline-table;'>
			<tr>
				<td>ASSET CODE :</td>
				<td><input type='text' class='textbox hinder-submit'  name='asset_code' value='$r[asset_code]'  /></td>
			</tr>
			<tr>
				<td>SERIAL NO :</td>
				<td><input type='text' class='textbox hinder-submit'  name='serial_no' value='$r[serial_no]'  /></td>
			</tr>
			<tr>
				<td>BRAND/MODEL NO :</td>
				<td><input type='text' class='textbox hinder-submit'  name='model' value='$r[model]'  /></td>
			</tr>
			<tr>
				<td>DATE ACQUIRED:</td>
				<td>
					<input type='textbox' class='textbox datepicker hinder-submit' name='date_acquired' value='$r[date_acquired]' />
				</td>        
			</tr>
			<tr>
				<td>ESTIMATED USEFUL LIFE IN MONTHS: </td>
				<td><input type='text' class='textbox hinder-submit' name='estimated_life' id='estimated_life' value='$r[estimated_life]' /></td>
			</tr>
			<tr>
				<td>DETAILS: </td>
				<td><textarea name='details' style='border:1px solid #c0c0c0; width:100%; height:100px;' >$r[details]</textarea></td>
			</tr>
		</table>
			
		<div class='module_actions'>
			<input type='button' value='UPDATE' onclick=\"xajax_update_asset_details(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form'));\" >
		</div>
	";
	
	$content.="
		<script type='text/javascript'>
			j(\".datepicker\").each(function(){
				j(this).datepicker({ 
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true 
				})
			});
		</script>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", 'PPE DETAILS' );");
	$objResponse->script('openDialog();');

	return $objResponse;	
}

function update_asset_details($form_data,$header_form){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$asset_code		= $form_data['asset_code'];
	$serial_no		= $form_data['serial_no'];
	$model		= $form_data['model'];
	$date_acquired	= $form_data['date_acquired'];
	$estimated_life	= $form_data['estimated_life'];
	$rr_detail_id	= $form_data['rr_detail_id'];
	$details		= $form_data['details'];
	
	$rr_header_id	= $header_form['rr_header_id'];
	$view			= $header_form['view'];
	
	mysql_query("
		update
			rr_detail
		set
			asset_code 		= '$asset_code',
			serial_no 		= '$serial_no',
			model 			= '$model',
			date_acquired 	= '$date_acquired',
			estimated_life 	= '$estimated_life',
			details = '$details'
		where
			rr_detail_id 	= '$rr_detail_id'
	") or $objResponse->alert(mysql_error());
	
	$objResponse->alert("ASSET DETAILS UPDATED.");
	$objResponse->redirect("admin.php?view=$view&rr_header_id=$rr_header_id");
	
	return $objResponse;
}

function deductToBudget($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$rr_detail_id			= $form_data['rr_detail_id'];
	$budget_stock_id		= $form_data['budget_stock_id'];
	$work_category_id		= $form_data['work_category_id'];
	$sub_work_category_id	= $form_data['sub_work_category_id'];
	
	mysql_query("
		insert into
			budget_deduction
		set
			rr_detail_id = '$rr_detail_id',
			stock_id = '$budget_stock_id',
			work_category_id = '$work_category_id',
			sub_work_category_id = '$sub_work_category_id'
	") or die(mysql_error());
	
	$objResponse->alert("ITEM DEDUCTED TO BUDGET");
	$objResponse->script("closeDialog();");
	
	return $objResponse;
}
function updateDeductions($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	
	$rr_detail_id			= $form_data['rr_detail_id'];
	$budget_stock_id		= $form_data['budget_stock_id'];
	$work_category_id		= $form_data['work_category_id'];
	$sub_work_category_id	= $form_data['sub_work_category_id'];
	
	mysql_query("
		update
			budget_deduction
		set
			stock_id = '$budget_stock_id',
			work_category_id = '$work_category_id',
			sub_work_category_id = '$sub_work_category_id'
		where
			rr_detail_id = '$rr_detail_id'
	") or die(mysql_error());
	
	$objResponse->alert("ITEM DEDUCTION UPDATED");
	$objResponse->script("closeDialog();");
	
	return $objResponse;
}
function displayBudgetDeductionForm($rr_detail_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$rr_header_id			= $options->getAttribute('rr_detail','rr_detail_id',$rr_detail_id,'rr_header_id');
	$project_id 			= $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'project_id');
	$po_header_id			= $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'po_header_id');
	$work_category_id		= $options->getAttribute('po_header','po_header_id',$po_header_id,'work_category_id');
	$sub_work_category_id	= $options->getAttribute('po_header','po_header_id',$po_header_id,'sub_work_category_id');
	
		
	$result = mysql_query("
		select
			work_category_id,
			sub_work_category_id,
			stock,
			quantity,
			d.cost,
			unit,
			amount,
			b.stock_id,
			d.discount
		from
			rr_detail as d, budget_deduction as b, productmaster as p
		where
			d.rr_detail_id = b.rr_detail_id
		and
			d.stock_id = p.stock_id
		and
			d.rr_detail_id = '$rr_detail_id'
	") or $objResponse->alert(mysql_error());
	$rows = mysql_num_rows($result);
	$r = mysql_fetch_assoc($result);
	
	if($rows <= 0){
		$result = mysql_query("
			select
				stock,
				quantity,
				d.cost,
				unit,
				amount,
				d.stock_id,
				d.discount
			from
				rr_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				d.rr_detail_id = '$rr_detail_id'
		") or die(mysql_error());	
		$r = mysql_fetch_assoc($result);
	}
	
	$q = "
		select
			stock,
			d.stock_id
		from
			budget_header as h,
			budget_detail as d,
			productmaster as p
		where
			h.budget_header_id = d.budget_header_id
		and
			d.stock_id = p.stock_id
		and
			h.work_category_id = '$work_category_id'
		and
			h.sub_work_category_id = '$sub_work_category_id'
		and
			h.status != 'C'
		and
			project_id = '$project_id'
		group by
			d.stock_id
		order by stock asc
	";
	
	$material_select = $options->getTableAssoc($r['stock_id'],'budget_stock_id','Select From Budget',$q,'stock_id','stock');
	
	$discount = $r[discount] * $r[quantity];
	$discount = number_format($discount,2);
	$content = "
		<input type='hidden' name='rr_detail_id' value='$rr_detail_id' > 
		<div class='module_actions'>
			<table class='table-contents'>				
				<tr>
					<td>DEDUCTED FROM BUDGET : </td>
					<td colspan=\"3\">
						$material_select
					</td>
				</tr>
				<tr>
					<td>QUANTITY : </td>
					<td><input type='text'class='textbox' value='$r[quantity]' readonly='readonly' ></td>
					<td>UNIT : </td>
					<td><input type='text' class='textbox' value='$r[unit]' readonly='readonly'></td>
				</tr>				
				<tr>
					<td>U.PRICE : </td>
					<td><input type='text' class='textbox' value='$r[cost]' readonly='readonly'></td>
					<td>AMOUNT : </td>
					<td><input type='text' class='textbox' value='$r[amount]' readonly='readonly'></td>
				</tr>
				<tr>
					<td>DISCOUNT: </td>
					<td><input type='text' class='textbox' value='$discount' readonly='readonly'></td>
				</tr>
			</table>
		</div>
		
		<div class='module_actions'>
	";
	$content.= ($rows > 0) ? "<input type='button' value='UPDATE DEDUCTION' onclick=\"xajax_updateDeductions(xajax.getFormValues('dialog_form'));\" >" : "<input type='button' value='DEDUCT TO BUDGET' onclick=\"xajax_deductToBudget(xajax.getFormValues('dialog_form'));\" >" ;
	
	
	$stock_id = ($r['stock_id']) ? " , $r[stock_id] " : NULL;
	$content.="
		</div>
		
		<script>
		
			j(\"input:button\").button();					
	";
	
	$content .= "
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", 'BUDGET DEDUCTION DETAILS' );");
	$objResponse->script('openDialog();');
	return $objResponse;
}


function getFromBudget($dialog_form,$rr_form,$budget_stock_id=NULL){
	$objResponse	= new xajaxResponse();
	$options 		= new options();
	
	$project_id				= $rr_form['project_id'];
	$work_category_id		= $dialog_form['work_category_id'];
	$sub_work_category_id	= $dialog_form['sub_work_category_id'];
	
	$result = mysql_query("
		select 
			d.stock_id,p.stock
		from
			budget_header as h, budget_detail as d, productmaster as p
		where
			h.budget_header_id = d.budget_header_id
		and
			d.stock_id = p.stock_id
		and
			h.status != 'C'
		and
			project_id = '$project_id'
		and
			work_category_id = '$work_category_id'
		and
			sub_work_category_id = '$sub_work_category_id'
		group by stock
		order by 
			stock asc
		
	") or die(mysql_error());
	
	$content = "<option value=''>SELECT MATERIAL : </option>";
	
	while($r = mysql_fetch_assoc($result)){
		
		$selected = ( $budget_stock_id == $r['stock_id'] ) ? "selected = 'selected'" : "";
		$content.= "
			<option value='$r[stock_id]' $selected >$r[stock]</option>
		";	
	}
	
	$objResponse->assign('budget_stock_id','innerHTML',$content);
	
	return $objResponse;
	
}

function search_po($po_header_id){
	$objResponse	= new xajaxResponse();
	
	$objResponse->script("closeDialog();");
	$objResponse->script("xajax_show_po(\"$po_header_id\")");
	
	return $objResponse;
}


function show_po($po_header_id = NULL){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	
	$query = "
		select * 
		from po_header as h,
		po_detail as d,
		productmaster as p
		where
		h.po_header_id = d.po_header_id and
		p.stock_id = d.stock_id and
		h.`status` != 'C' and
		(h.status = 'F' OR h.status = 'S')and
		h.approval_status = 'A' and
		(h.po_header_id = '$po_header_id' or p.stock like '%$po_header_id%')
		group by
		h.po_header_id
	";
	
	/*if(!empty($po_header_id)){
	$query.="
		and
			po_header_id = '$po_header_id'
	";
	}*/
	
	$query.="
		order by h.date desc, h.po_header_id desc limit 0,20
	";
	
	
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
				<th>PO #</th>
				<th>Date</th>
				<th>Project</th>
				<th>Scope of Work</th>
				<th>Work Category</th>
				<th>Sub Work Category</th>
				<th>Supplier</th>
				<th>Terms</th>
				<th>Status</th>
				<th>Approval Status</th>
			</tr>  
	";
	
	$i=1;
	while($r=mysql_fetch_assoc($result)) {
		$po_header_id		= $r['po_header_id'];
		$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
		
		$date				= date("F j, Y",strtotime($r['date']));
		
		$project_id			= $r['project_id'];
		$project_name		= $options->attr_Project($project_id,'project_name');
		$project_code		= $options->attr_Project($project_id,'project_code');
		$project_name_code	= ($project_id)?"$project_name - $project_code":"";
		
		$supplier_id		= $r['supplier_id'];
		$supplier 			= $options->attr_Supplier($supplier_id,'account');
		
		$terms				= $r['terms'];
		
		$scope_of_work			= $r['scope_of_work'];
		$work_category_id 		= $r['work_category_id'];
		$work_category  		= $options->attr_workcategory($work_category_id,'work');
		$sub_work_category_id 	= $r['sub_work_category_id'];
		$sub_work_category  	= $options->attr_workcategory($sub_work_category_id,'work');
		$approval_status		= $r['approval_status'];
		
		$status					= $r['status'];
	
		$content.="
			<tr class='po_details' style='cursor:pointer;' rel='$po_header_id'>
				<td width='20'>".$i++."</td>
				<td>$po_header_id_pad</td>
				<td>$date</td>
				<td>$project_name_code</td>	
				<td>$scope_of_work</td>
				<td>$work_category</td>	
				<td>$sub_work_category</td>	
				<td>$supplier</td>	
				<td>$terms</td>
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
			
			j(\".po_details\").each(function(){
				j(this).click(function(){
					xajax_show_po_details(j(this).attr('rel'));
					
					
				});
				
				j(this).dblclick(function(){
					xajax_po_place_details(j(this).attr('rel'));
				});
			});	
			
			j('#search_button').click(function(){
				xajax_search_po(j('#search_keyword').val());
			});
		</script>
	";

	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", 'PURCHASE ORDER' );");
	$objResponse->script('openDialog();');
	return $objResponse;
	
}


function show_po_details($po_header_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$result=mysql_query("
		select
			d.po_detail_id,
			d.stock_id,
			stock,
			stockcode,
			unit,
			d.cost,
			quantity,
			amount
		from
			po_detail as d,
			productmaster as pm
		where
			d.po_header_id	= '$po_header_id' 
		and
			pm.stock_id = d.stock_id
	") or $objResponse->alert(mysql_error());
	
	$content="
		<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
			<caption class='ui-widget-header' style='padding:3px;'>Purchase Order Material Details PO # ".str_pad($po_header_id,7,0,STR_PAD_LEFT)."</caption>
			<tr>				
				<th width='20'>#</th>
				<th>CODE</th>
				<th>DESCRIPTION</th>
				<th>QTY</th>
				<th>UOM</th>
				<th>U.PRICE</th>
				<th>AMOUNT</th>
			</tr>  
	";
	
	$i=1;
	while($r=mysql_fetch_assoc($result)) {
		$po_detail_id		= $r['po_detail_id'];
		$stock_id			= $r['stock_id'];
		$stock				= $r['stock'];
		$stockcode			= $r['stockcode'];
		$unit				= $r['unit'];
		$cost				= $r['cost'];
		$quantity			= $r['quantity'];
		$amount				= $r['amount'];

		$content.="
			<tr>
				<td>".$i++."</td>
				<td>".$stockcode."</td>
				<td>".$stock."</td>
				<td class='align-right'>".number_format($quantity,2,'.',',')."</td>
				<td>".$unit."</td>
				<td class='align-right'>".number_format($cost,2,'.',',')."</td>
				<td class='align-right'>".number_format($amount,2,'.',',')."</td>
			</tr>
		";
	}
	$content.="
		</table>
	";
	
	$result=mysql_query("
		select
			*
		from
			po_service_detail as d,
			productmaster as pm
		where
			d.po_header_id	= '$po_header_id' 
		and
			pm.stock_id = d.stock_id
	") or $objResponse->alert(mysql_error());
	
	$content.="
		<table cellspacing='2' cellpadding='5' width='100%' align='center' class='display_table'>
			<caption class='ui-widget-header' style='padding:3px;'>Purchase Order Service Details PO # ".str_pad($po_header_id,7,0,STR_PAD_LEFT)."</caption>
			<tr>				
				<th width='20'>#</th>
				<th>Designation</th>
                <th>No</th>
                <th>No. of Days</th>
                <th>Rate/Day</th>
                <th>Amount</th>
			</tr>  
	";
	
	$i=1;
	while($r=mysql_fetch_assoc($result)) {
		$pr_service_detail_id		= $r['pr_service_detail_id'];
		$stock_id			= $r['stock_id'];
		$stock				= $r['stock'];
		$quantity			= $r['quantity'];
		$days				= $r['days'];
		$rate_per_day		= $r['rate_per_day'];
		$unit				= $r['unit'];
		$amount				= $r['amount'];

		$content.="
			<tr>
				<td>".$i++."</td>
				<td>".$stock."</td>
				<td class='align-right'>".number_format($quantity,2,'.',',')."</td>
				<td class='align-right'>".$days."</td>
				<td class='align-right'>".number_format($rate_per_day,2,'.',',')."</td>
				<td class='align-right'>".number_format($amount,2,'.',',')."</td>
			</tr>
		";
	}
	$content.="
		</table>
	";

	$objResponse->assign('detail_content','innerHTML',$content);
	return $objResponse;
	
}

function po_place_details($po_header_id){
	$objResponse	= new xajaxResponse();
	$options		= new options();
	
	$result=mysql_query("
		select
			  *
		 from
			po_header
		where 
			po_header_id = '$po_header_id'
	") or $objResponse->alert(mysql_error());
	
	$r=mysql_fetch_assoc($result);
	$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	$supplier_id		= $r['supplier_id'];
	$supplier			= $options->attr_Supplier($supplier_id,'account');
	
	$scope_of_work			= $r['scope_of_work'];
	$work_category_id		= $r['work_category_id'];
	$sub_work_category_id	= $r['sub_work_category_id'];
	
	$work_category	= $options->attr_workcategory($work_category_id,'work');
	$sub_work_category = $options->attr_workcategory($sub_work_category_id,'work');
	
	
	
	$objResponse->assign('po_name','value',$po_header_id_pad);
	$objResponse->assign('po_header_id','value',$po_header_id);
	$objResponse->assign('project_display','value',$project_name_code);
	$objResponse->assign('project_id','value',$project_id);
	$objResponse->assign('supplier_name','value',$supplier);
	$objResponse->assign('supplier_id','value',$supplier_id);
	
	$objResponse->assign('scope_of_work','value',$scope_of_work);
	$objResponse->assign('work_category_id','value',$work_category_id);
	$objResponse->assign('work_category','value',$work_category);
	$objResponse->assign('sub_work_category_id','value',$sub_work_category_id);
	$objResponse->assign('sub_work_category','value',$sub_work_category);
			
	$objResponse->script('closeDialog();');
	
	return $objResponse;
}


function getProjectFromPO($po_header_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$result=mysql_query("
		select
			*
		from
			po_header as h, projects as p
		where
			h.project_id = p.project_id
		and
			po_header_id = '$po_header_id'
	") or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$project_id 	= $r['project_id'];
	$project_code	= $r['project_code'];
	$project_name	= $r['project_name'];
	
	$project_display = "$project_name - $project_code";
	
	$supplier_id	= $r['supplier_id'];
	$supplier		= $options->attr_Supplier($supplier_id,'account');
	
	$objResponse->assign('project_id','value',$project_id);
	$objResponse->assign('project_display','value',$project_display);
	$objResponse->assign('supplier_id','value',$supplier_id);
	$objResponse->assign('supplier_name','value',$supplier);
	
	return $objResponse;
}

function receive_stock_id_form($stock_id,$cost,$form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$project_id				= $form_data['project_id'];
	$po_header_id			= $form_data['po_header_id'];
	$rr_type				= $form_data['rr_type'];
	
	
	$work_category_id		= $options->getAttribute('po_header','po_header_id',$po_header_id,'work_category_id');
	$sub_work_category_id	= $options->getAttribute('po_header','po_header_id',$po_header_id,'sub_work_category_id');

	//get discount
	$query=mysql_query("SELECT discount from po_detail WHERE po_header_id = '$po_header_id' AND stock_id = '$stock_id'");
	$rr=mysql_fetch_assoc($query);
	$discount = $rr['discount'];
	
	$stock 	= addslashes($options->attr_stock($stock_id,'stock'));
	$unit	= $options->attr_stock($stock_id,'unit');
	
	$q = "
		select
			stock,
			d.stock_id
		from
			budget_header as h,
			budget_detail as d,
			productmaster as p
		where
			h.budget_header_id = d.budget_header_id
		and
			d.stock_id = p.stock_id
		and
			h.work_category_id = '$work_category_id'
		and
			h.sub_work_category_id = '$sub_work_category_id'
		and
			h.status != 'C'
		and
			project_id = '$project_id'
		group by
			d.stock_id
		order by stock asc
	";
	
	$material_select = $options->getTableAssoc($r['budget_stock_id'],'budget_stock_id','Select From Budget',$q,'stock_id','stock');
	
	$content = "
		<div class='module_actions'>
			<table class='table-contents' style='display:inline-table;'>
				<tr>
					<td>INVOICE # : </td>
					<td><input type='text' class='textbox' name='invoice' ></td>
				</tr>
				<!--<tr>
					<td>DRIVER  : </td>
					<td>".$options->getTableAssoc(NULL,'driverID',"Select Driver","select * from drivers order by driver_name asc","driverID","driver_name")."</td>
				</tr>
				<tr>
					<td>EQUIPMENT : </td>
					<td>
						<input tpe='textbox' class='equipment_name textbox' autocomplete=\"off\">
						<input type='hidden' name='equipment_id'>
					</td>
				</tr>
				<tr>
					<td>CHARGE TO ACCT: </td>
					<td>
						<input tpe='textbox' class='account_name textbox' autocomplete=\"off\">
						<input type='hidden' name='account_id'>
					</td>
				</tr> -->
				<td>ITEM DESCRIPTION: </td>
					<td>
						<input tpe='textbox' class='textbox' name='details'  autocomplete=\"off\">
					</td>
				<tr>
					<td>QUANTITY : </td>
					<td><input type='text' id='quantity' class='textbox' name='quantity' id='quantity' value=''></td>
					
				</tr>				
				<tr>
					<td>UNIT : </td>
					<td><input type='text' class='textbox' value='$unit' readonly='readonly'></td>
				</tr>
				<!--<tr>
					<td><em>(OPTIONAL)</em> QTY : </td>
					<td><input type='text' class='textbox3' name='quantity_cum' id='quantity_cum' value='' autocomplete=\"off\"></td>
					
				</tr>
				<tr>
					<td><em>(OPTIONAL)</em> UNIT : </td>
					<td><input type='text' class='textbox3' name='_unit' value='' autocomplete=\"off\"></td>
				</tr>-->
				<tr>
					<td>U.PRICE : </td>
					<td>".$options->option_price_issuance('cost',$stock_id,NULL,NULL,NULL)."</td>
				</tr>
				<tr>
					<td>DISCOUNT : </td>
					<td><input type='text' class='textbox' name='discount' id='discount' readonly='readonly' value='".$discount."'></td>
				</tr>
				<tr>
					<td>AMOUNT : </td>
					<td><input type='text' class='textbox' id='amount' readonly='readonly'></td>
				</tr>
				<tr>
					<td>INSTALLED BY : </td>
					<td><input type='text' class='textbox' name='installed_by' id='installed_by'></td>
				</tr>
				<tr>
					<td>DATE INSTALLED : </td>
					<td><input type='text' class='datepicker textbox3' name='date_installed' title='Please enter date'  id='date_installed'></td>
				</tr>
				<tr>
					<td>WITHDRAWAL # : </td>
					<td><input type='text' class='textbox' name='withdrawal' id='withdrawal'></td>
				</tr>
			</table>
		";
		if($rr_type == "A" || 1): #DISPLAY ONLY IF ASSEET
		
		$content.="
			
			<table class='table-contents' style='display:inline-table;'>
				<!--<tr>
                	<td>ASSET CODE :</td>
                    <td><input type='text' class='textbox hinder-submit'  name='asset_code'  /></td>
                </tr>
				<tr>
                	<td>SERIAL NO :</td>
                    <td><input type='text' class='textbox hinder-submit'  name='serial_no'  /></td>
                </tr>
				<tr>
                	<td>DATE ACQUIRED:</td>
                    <td>
                    	<input type='textbox' class='textbox datepicker hinder-submit' name='date_acquired' />
                    </td>        
                </tr>
                <tr>
                	<td>ESTIMATED USEFUL LIFE IN MONTHS: </td>
                    <td><input type='text' class='textbox hinder-submit' name='estimated_life' id='estimated_life' /></td>
                </tr>-->
			</table>
	";
	
	endif;
	$content.="
			<hr style=\"border-top:1px solid #C0C0C0\"> 
			<table>
				<tr>
					<!--<td>DEDUCT TO BUDGET</td>
					<td id='budget_stock'>
						$material_select
					</td>-->
				</tr>
			</table>	
			<div class='form-div'>
				<input type='button' value='Add Item' name='b' onclick=xajax_receive_stock_id(xajax.getFormValues('dialog_form'),xajax.getFormValues('header_form')); />
			</div>
			<input type='hidden' name='stock_id' value='$stock_id' >
		</div>
		
		<script type='text/javascript'>
		
			j(\"input:button\").button();
			j(\"#dialog\").dialog(\"option\",\"title\",\"STOCKS RECEIVING\");
			j('#quantity').keyup(function(){
				var quantity = j('#quantity').val();
				var cost = j('#cost').val();
				var discount = j('#discount').val();
				
				var amount = quantity * (cost - discount);
				j('#amount').val(amount);
			});
			j('#cost').change(function(){
				var quantity = j('#quantity').val();
				var cost = j('#cost').val();
				var discount = j('#discount').val();
				
				var amount = quantity * (cost - discount);
				j('#amount').val(amount);
			});
			j(\".equipment_name\").autocomplete({
					source: \"dd_equipment_he.php\",
					minLength: 2,
					select: function(event, ui) {
						j(this).val(ui.item.value);
						j(this).next().val(ui.item.id);
					}
				});
				
			j(\".account_name\").autocomplete({
					source: \"dd_accounts.php\",
					minLength: 2,
					select: function(event, ui) {
						j(this).val(ui.item.value);
						j(this).next().val(ui.item.id); 
					}
				});
			j(\".datepicker\").each(function(){
				j(this).datepicker({ 
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true 
			});
		});	
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
	function receive_stock_id($form_data,$form_data2){
		$objResponse 	= new xajaxResponse();
		$options		= new options();
			
		$invoice		= $form_data['invoice'];
		$quantity		= $form_data['quantity'];
		$stock_id		= $form_data['stock_id'];
		$cost			= $form_data['cost'];
		$discount		= $form_data['discount'];
		$quantity_cum	= $form_data['quantity_cum'];
		$driverID		= $form_data['driverID'];
		$equipment_id	= $form_data['equipment_id'];
		$_unit			= $form_data['_unit'];
		$account_id		= $form_data['account_id'];
		$details		= $form_data['details'];
		$installed_by	= $form_data['installed_by'];
		$date_installed	= $form_data['date_installed'];
		$withdrawal		= $form_data['withdrawal'];
		
		#DEPRECIATION DATA
		$asset_code		= $form_data['asset_code'];
		$serial_no		= $form_data['serial_no'];
		$date_acquired	= $form_data['date_acquired'];
		$estimated_life	= $form_data['estimated_life'];
		
		$amount		= $quantity * ($cost - $discount);
		
		$work_category_id		= $form_data['work_category_id'];
		$sub_work_category_id	= $form_data['sub_work_category_id'];
		$budget_stock_id		= $form_data['budget_stock_id'];
		
		$po_header_id	= $form_data2['po_header_id'];
		$rr_header_id	= $form_data2['rr_header_id'];
		$view			= $form_data2['view'];
		$balance	= $options->rr_getBalance($po_header_id,$stock_id);
		
		$balance = round($balance,4);	
		$quantity = round($quantity,4);
		
		if($balance < $quantity){
			$objResponse->alert("Error : Quantity is Greater than Balance");	
		}else{
			
			mysql_query("
				insert into
					rr_detail
				set
					stock_id		= '$stock_id',
					quantity 		= '$quantity',
					cost 			= '$cost',
					discount 		= '$discount',
					amount 			= '$amount',
					rr_header_id 	= '$rr_header_id',
					invoice 		= '$invoice',
					quantity_cum 	= '$quantity_cum',
					driverID 		= '$driverID',
					equipment_id 	= '$equipment_id',
					_unit 			= '$_unit',
					account_id 		= '$account_id',
					details 		= '$details',
					asset_code		= '$asset_code',
					serial_no		= '$serial_no',
					date_acquired	= '$date_acquired',
					estimated_life	= '$estimated_life',
					installed_by    = '$installed_by',
					date_installed  = '$date_installed',
					withdrawal	    = '$withdrawal'
			") or $objResponse->alert(mysql_error());
			
			$rr_detail_id = mysql_insert_id();
			
			if(!empty($budget_stock_id)){
				mysql_query("
					insert into
						budget_deduction
					set
						stock_id = '$budget_stock_id',
						rr_detail_id = '$rr_detail_id'
				") or $objResponse->alert(mysql_error());
			}
			
			$objResponse->alert("Item Successfully Received.");	
			$objResponse->redirect("admin.php?view=$view&rr_header_id=$rr_header_id");
		}
		
		return $objResponse;
	}

	function print_rr($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("rr_header_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	
	function updateRRStatus($id){
		$objResponse=new xajaxResponse();
		//$objResponse->alert($id);
		$query="
			select
				*
			from 
				rr_header
			where
				rr_header_id='$id'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$status=$r[status];
		
		if($status!='C'):
			$query="
				update
					rr_header
				set
					status='P'
				where
					rr_header_id='$id'
			";
			mysql_query($query);
		endif;
		
		
		
		return $objResponse;
	}
	
	function removeRRDetails($rr_detail_id,$rr_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$query="
			delete from
				rr_detail
			where
				rr_detail_id='$rr_detail_id'
		";
		mysql_query($query);
		
		$objResponse->script("xajax_getRRTable('$rr_header_id')");
		
		return $objResponse;
	}
	
	function getRRTable($rr_header_id){
		$objResponse=new xajaxResponse();
		$options=new options();
		$content=$options->getUpdatedRRTable($rr_header_id);
		
		$objResponse->assign("table_container","innerHTML",$content);
		return $objResponse;	
	}
	
	function addRRDetails($form_data){
		$objResponse=new xajaxResponse();
		$options=new options();
		
		$rr_header_id		= $form_data[rr_header_id];
		$stock_id			= $form_data[stock_id];
		$quantity			= $form_data[quantity];
		$cost				= $form_data[cost];
		$package_id			= $form_data[package_id];
		$amount				= $form_data[amount];
		
		
		$query="
			insert into
				rr_detail
			set
				rr_header_id='$rr_header_id',
				stock_id='$stock_id',
				quantity='$quantity',
				cost='$cost',
				amount='$amount',
				package_id='$package_id'
		";
		mysql_query($query);
		
		$objResponse->script("xajax_getRRTable('$rr_header_id')");
		
		return $objResponse;
	}
	
	
?>