<?php

function pr_labor_form($labor_budget_details_id, $pr_header_id){
	$objResponse = new xajaxResponse();
	$options		= new options();	
	
	$s = "SELECT * FROM work_type as w, labor_budget l, labor_budget_details d
			WHERE 
					d.id = '$labor_budget_details_id' 
				and
					l.id = d.labor_budget_id
				and
					d.work_code_id = w.work_code_id";
	$rs = mysql_query($s);
	$rw = mysql_fetch_assoc($rs);
	extract($rw);
	
	$content = "
		<div class='ui-widget-content' style='padding:10px;'>
			<div class='form-div'>
				Description : <br>
				$description
			</div>
			
			<div class='form-div'>
				Unit : <br>
				$unit
			</div>
			
			<div class='form-div'>
				Budgeted Qty: <br>
				$qty
			</div>
			<div class='form-div'>
				Budgeted No. of Person: <br>
				$no_per
			</div>
			
			<div class='form-div'>
				Requested Qty per $unit : <br>
				<input type='text' class='textbox' name='req_qty' id='req_qty' value='' />
			</div>
			<div class='form-div'>
				Requested No. of Person: <br>
				<input type='text' class='textbox' name='per' id='per' value='' />
			</div>
			
			<div class='form-div'>
				<input type='hidden' name='pr_header_id' value='$pr_header_id' >
				<input type='hidden' name='labor_budget_details_id' value='$labor_budget_details_id' >
				<input type='button' class='button' name='submit' value='Submit' onclick=xajax_pr_labor(xajax.getFormValues('dialog_form')); >
			</div>
		</div>
				
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'LABOR PR' );");	
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function pr_labor($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$pr_header_id		= $form_data['pr_header_id'];	
	$labor_budget_details_id		= $form_data['labor_budget_details_id'];
	$req_qty		= $form_data['req_qty'];
	$per			= $form_data['per'];
	$t_q			= $req_qty * $per;
	
	mysql_query("
		insert into
			labor_budget_pr
		set	
			labor_budget_details_id		= '$labor_budget_details_id',
			pr_header_id = '$pr_header_id',
			requested_qty			= '$req_qty',
			requested_no_per		= '$per',
			total_req_qty			= '$t_q',
			date_requested			= NOW()
			
	") or $objResponse->alert(mysql_error());
	
		$objResponse->alert("Labor Successfully Requested.");	
		$objResponse->redirect("admin.php?view=f4e9cf5b43526307214f&pr_header_id=$pr_header_id");
	
	return $objResponse;
}

function delete_labor_pr($pr_lb_id, $pr_header_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
		
	
	mysql_query("
		update
			labor_budget_pr
		set	
			is_deleted = '1'
		where
			pr_lb_id = '$pr_lb_id'
			
	") or $objResponse->alert(mysql_error());
	
		$objResponse->alert("Labor Successfully Deleted.");	
		$objResponse->redirect("admin.php?view=f4e9cf5b43526307214f&pr_header_id=$pr_header_id");
	
	return $objResponse;
}

?>