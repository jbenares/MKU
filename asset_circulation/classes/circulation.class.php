<?php

# Add Asset Circulation Form
function new_acform(){
	$options = new ac_options();
	$objResponse = new xajaxResponse();
	
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<div class='form-div'>
					From Project Name : <br>	
					".$options->option_project_list($project_id,'from_project_id','Select Project')."
				</div>
				
				<div class='form-div'>
					To Project Name : <br>	
					".$options->option_project_list($project_id,'to_project_id','Select Project')."
				</div>
				
				<div class='form-div'>
					Employee : <br>	
					".$options->option_employee_list($employeeID,'employeeID','Select Employee')."
				</div>							
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_ac(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'ASSET CIRCULATION' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

# Insert DB Asset Circulation
function new_ac($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new ac_options();
		
	$from_project_id 	= $form_data['from_project_id'];
	$to_project_id 	= $form_data['to_project_id'];
	$employeeID 	= $form_data['employeeID'];
	
	$chk = "SELECT * FROM asset_circulation_header WHERE from_project_id = '$from_project_id' AND to_project_id = '$to_project_id'";
	$rs_chk = mysql_query($chk);
	$num_chk = mysql_num_rows($rs_chk);
	if($num_chk > 0)
	{
		# From Project
		$frm = "SELECT * FROM projects WHERE project_id = '$from_project_id'";
		$rs_frm = mysql_query($frm);
		$rw_frm = mysql_fetch_assoc($rs_frm);
		$frm_name = $rw_frm['project_name'];
		# To Project
		$to = "SELECT * FROM projects WHERE project_id = '$to_project_id'";
		$rs_to = mysql_query($to);
		$rw_to = mysql_fetch_assoc($rs_to);
		$to_name = $rw_to['project_name'];
		
		$objResponse->alert($frm_name . " to " . $to_name . " project already exist. Data entry failed");
		$objResponse->script("window.location.reload();");
	
		return $objResponse;
	}else{
		mysql_query("		
		
			insert into
				asset_circulation_header
			set
				from_project_id 	= '$from_project_id',
				to_project_id 	= '$to_project_id',
				employeeID	= '$employeeID',				
				date_added		= NOW()
		") or die($objResponse->alert(mysql_error()));
		
		$objResponse->alert("Query Successful");
		$objResponse->script("window.location.reload();");
	
		return $objResponse;
	}
}

# Edit Asset Circulation Form
function edit_acform($id){
	$options = new ac_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			projects s, employee e, asset_circulation_header ac
		where
			ac.ach_id = '$id' AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$project_id 	= $form_data['from_project_id'];
	$project_id2 	= $form_data['to_project_id'];
	$employeeID 	= $form_data['employeeID'];

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='ach_id' value='$id' >
			
				<div class='form-div'>
					From Project Name : <br>
					".$options->option_project_list($r['from_project_id'])."
				</div>
				
				<div class='form-div'>
					To Project Name : <br>
					".$options->option_project_list2($r['to_project_id'])."
				</div>
			
				<div class='form-div'>
					Requested By : <br>
					".$options->option_employee_list($r['employeeID'])."
				</div>
									
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_ac(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'ASSET CIRCULATION' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

# Update DB Asset Circulation
function edit_ac($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new ac_options();
	
	$ach_id 	= $form_data['ach_id'];
	$project_id 	= $form_data['project_id'];
	$project_id2 	= $form_data['project_id2'];
	$employeeID 	= $form_data['employeeID'];	
		
		mysql_query("
			update
				asset_circulation_header
			set
				from_project_id 	= '$project_id',
				to_project_id 	= '$project_id2',
				employeeID	= '$employeeID',				
				date_modified		= NOW()
			where
				ach_id		= '$ach_id'
		") or die($objResponse->alert(mysql_error()));
		
		$objResponse->alert("Query Successful");
		$objResponse->script("window.location.reload();");
		
	return $objResponse;
}

# Receive Items Form
function rec_itemform($id){
	$options = new ac_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			projects s, employee e, asset_circulation_header ac
		where
			ac.ach_id = '$id' AND ac.employeeID = e.employeeID AND ac.from_project_id = s.project_id
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$project_id = $r['from_project_id'];
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='ach_id' value='$id' >
				
				<div class='form-div'>
					Item Name : <br>	
					".$options->option_stock_list($project_id,'stock_id','Select Item')."
				</div>								
				
				<div class='form-div'>
					Quantity : <br>
					<input type='text' class='textbox' name='qty' value=''>
				</div>							
				
				<div class='form-div'>
					Date Received : <br>
					<input type='date' class='textbox' name='daterec' value=''>
				</div>
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_rec_item(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'RECEIVE ITEMS' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}
# Insert DB Receive Items
function rec_item($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new ac_options();
		
	$ach_id 	= $form_data['ach_id'];
	$stock_id 	= $form_data['stock_id'];
	$qty 	= $form_data['qty'];
	$daterec 	= $form_data['daterec'];
		
		mysql_query("		
		
			insert into
				asset_circulation_detail
			set
				ach_id 	= '$ach_id',
				stock_id 	= '$stock_id',
				quantity	= '$qty',
				status = 'I',
				date_received	= '$daterec',
				date_added		= NOW()
		") or die($objResponse->alert(mysql_error()));
		
		$objResponse->alert("Query Successful");
		$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

# Return Items Form
function ret_itemform($id){
	$options = new ac_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			asset_circulation_header ac, productmaster p, asset_circulation_detail ad
		where
			ac.ach_id = '$id' AND ac.ach_id = ad.ach_id AND ad.stock_id = p.stock_id AND ad.status = 'I' AND ac.is_deleted != '1'
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$stock_id = $r['stock_id'];
	$stock_name = $r['stock'];
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='ach_id' value='$id' >
				
				<div class='form-div'>
					Item Name : <br>	
					".$stock_name."
				</div>								
				
				<div class='form-div'>
					Quantity : <br>
					<input type='text' class='textbox' name='qty' value=''>
				</div>							
				
				<div class='form-div'>
					Date Returned : <br>
					<input type='date' class='textbox' name='daterec' value=''>
				</div>
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_ret_item(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'RETURN ITEMS' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

?>