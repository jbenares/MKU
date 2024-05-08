<?php


function new_pcform(){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<div class='form-div'>
					Requested By : <br>	
					".$options->option_employee_list($employeeID,'employeeID','Select Employee')."
				</div>
								
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox' name='amount' value=''>
				</div>
				
				<div class='form-div'>
					Purpose : <br>
					<input type='text' class='textbox' name='purpose' value=''>
				</div>
								
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_pc(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_pc($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$employeeID 	= $form_data['employeeID'];
	$amount = $form_data['amount'];
	$purpose		= $form_data['purpose'];
	
	$sql = "select * from employee_contracts where employeeID = '$employeeID' order by contract_id desc";
	$result = mysql_query($sql);
	$department=0;
	if(mysql_num_rows($result)){
		$row = mysql_fetch_assoc($result);
		$department = $row['projectsID'];
	}else{
		$sql2 = "select * from employee where employeeID = '$employeeID'";
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_assoc($result2);
		$department = $row2['projectsID'];
	}
	
	if($department==0){
		$objResponse->alert("Update Employee Contract on Master File..");
	}else{
		mysql_query("		
		
			insert into
				petty_cash
			set
				department_id 	= '$department',
				employeeID	= '$employeeID',
				amount		= '$amount',
				purpose		= '$purpose',
				date_requested		= NOW()
		") or die($objResponse->alert(mysql_error()));
		
		$objResponse->alert("Query Successful");
		$objResponse->script("window.location.reload();");
	}
	return $objResponse;
}


function edit_pcform($id){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			petty_cash p, employee e
		where
			p.petty_cash_id = '$id' AND p.employeeID = e.employeeID
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$employeeID = $r['employeeID'];
	$amount = $r['amount'];
	$purpose = $r['purpose'];

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='petty_cash_id' value='$id' >
			
				<div class='form-div'>
					Requested By : <br>
					".$options->option_employee_list($r['employeeID'])."
				</div>
				
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox' name='amount' value='$amount'>
				</div>
								
				<div class='form-div'>
					Purpose : <br>
					<input type='text' class='textbox' name='purpose' value='$purpose'>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_pc(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function edit_pc($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$petty_cash_id 	= $form_data['petty_cash_id'];
	$employeeID 	= $form_data['employeeID'];
	$amount = $form_data['amount'];
	$purpose = $form_data['purpose'];
	
	$sql = "select * from employee_contracts where employeeID = '$employeeID' order by contract_id desc";
	$result = mysql_query($sql);
	$department=0;
	if(mysql_num_rows($result)){
		$row = mysql_fetch_assoc($result);
		$department = $row['projectsID'];
	}else{
		$sql2 = "select * from employee where employeeID = '$employeeID'";
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_assoc($result2);
		$department = $row2['projectsID'];
	}
	if($department==0){
		$objResponse->alert("Update Employee Contract on Master File..");
	}else{
		mysql_query("
			update
				petty_cash
			set
				department_id 	= '$department',
				employeeID	= '$employeeID',
				amount		= '$amount',
				purpose		= '$purpose',
				date_modified		= NOW()
			where
				petty_cash_id		= '$petty_cash_id'
		") or die($objResponse->alert(mysql_error()));
		
		$objResponse->alert("Query Successful");
		$objResponse->script("window.location.reload();");
	}
	return $objResponse;
}

# Petty Cash Amount
function new_pcamtform(){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
									
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox' name='amount' value='' required>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_pcamt(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH BUDGET' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_pcamt($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
		
	$amount = $form_data['amount'];	
	
	mysql_query("		
	
		insert into
			petty_cash_budget
		set			
			amount		= '$amount',			
			date_added		= NOW()
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

# Liquidation
function liquidate_pcform($id){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			petty_cash p, employee e
		where
			p.petty_cash_id = '$id' AND p.employeeID = e.employeeID
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
	
	$employeeID = $r['employeeID'];
	$amount = $r['amount'];
	$purpose = $r['purpose'];
	$liquidated_amount = $r['liquidated_amount'];
	$remarks = $r['remarks'];
	$employee = $r['employee_lname'] . ',&nbsp;' . $r['employee_fname'];
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='petty_cash_id' value='$id' >
				<input type='hidden' name='amount' value='$amount' >
			
				<div class='form-div'>
					<b>Requested By :</b> <br>
					".$employee."
				</div>
				
				<div class='form-div'>
					<b>Amount :</b> <br>
					".number_format($amount,2)."
				</div>
								
				<div class='form-div'>
					<b>Purpose :</b> <br>
					".$purpose."
				</div>
				<div class='form-div'>
					<b>Liquidated Amount :</b> <br>
					<input type='text' class='textbox' name='liquidated' value='$liquidated_amount' required />
				</div>
				<div class='form-div'>
					<b>Returned Amount :</b> <br>
					<input type='text' class='textbox' name='returned' value='$returned_amount' required />
				</div>
				<div class='form-div'>
					<b>Remarks :</b> <br>
					<textarea class='textarea_small' name='remarks'>$remarks</textarea>
				</div>
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_liquidate_pc(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH LIQUIDATION' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function liquidate_pc($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$petty_cash_id 	= $form_data['petty_cash_id'];
	$amount 	= $form_data['amount'];
	$liquidated 	= $form_data['liquidated'];
	$returned 	= $form_data['returned'];
	$remarks 	= $form_data['remarks'];

	$total_liquidation = $liquidated + $returned;
	
	#Get difference
	$difference = $amount - $total_liquidation;
	if($difference != 0){$is_liquidated = '1';}else{$is_liquidated = '0';}
	
	#Check if liquidated amount is greater than requested
	if($total_liquidation > $amount)
	{
		$objResponse->alert("Liquidated amount is greater than released amount. Saving Failed!");
		return $objResponse;
	}else{
		$sql = "select * from employee_contracts where employeeID = '$employeeID' order by contract_id desc";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$department = $row['projectsID'];
		
		#Add returned amount if not 0 to petty_cash_budget
		if($returned > 0){

			$in = "INSERT INTO petty_cash_budget (amount, date_added, is_liquidation, petty_cash_id)
				VALUES ('$returned', NOW(), '1', '$petty_cash_id')";
			mysql_query($in);
		}else{}
	
		mysql_query("
			update
				petty_cash
			set
				returned_amount 	= '$returned',
				liquidated_amount 	= '$liquidated',
				difference	= '$difference',		
				remarks	= '$remarks',
				date_liquidated		= NOW(),
				is_liquidated = '$is_liquidated'
			where
				petty_cash_id		= '$petty_cash_id'
		") or die($objResponse->alert(mysql_error()));				
	
		$objResponse->alert("Query Successful");
		$objResponse->script("window.location.reload();");
	
	return $objResponse;
	}
}

/* RJR Petty Cash */

function new_pcform_rjr(){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
									
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox' name='amount' value=''>
				</div>
				
				<div class='form-div'>
					Purpose : <br>
					<input type='text' class='textbox' name='purpose' value=''>
				</div>
								
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_pc_rjr(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH RJR' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_pc_rjr($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
		
	$amount = $form_data['amount'];
	$purpose		= $form_data['purpose'];
		
	mysql_query("		
	
		insert into
			petty_cash_rjr
		set			
			amount		= '$amount',
			purpose		= '$purpose',
			date_requested		= NOW()
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}


function edit_pcform_rjr($id){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			petty_cash_rjr
		where
			petty_cash_id = '$id'
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);
		
	$amount = $r['amount'];
	$purpose = $r['purpose'];

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='petty_cash_id' value='$id' >
							
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox' name='amount' value='$amount'>
				</div>
								
				<div class='form-div'>
					Purpose : <br>
					<input type='text' class='textbox' name='purpose' value='$purpose'>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_pc_rjr(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH RJR' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function edit_pc_rjr($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
	
	$petty_cash_id 	= $form_data['petty_cash_id'];	
	$amount = $form_data['amount'];
	$purpose = $form_data['purpose'];		
	
	mysql_query("
		update
			petty_cash_rjr
		set		
			amount		= '$amount',
			purpose		= '$purpose',
			date_modified		= NOW()
		where
			petty_cash_id = '$petty_cash_id'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

# Petty Cash Amount RJR
function new_pcamtform_rjr(){
	$options = new pc_options();
	$objResponse = new xajaxResponse();
	
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
									
				<div class='form-div'>
					Amount : <br>
					<input type='text' class='textbox' name='amount' value='' required>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_pcamt_rjr(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PETTY CASH BUDGET RJR' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_pcamt_rjr($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new pc_options();
		
	$amount = $form_data['amount'];	
	
	mysql_query("		
	
		insert into
			petty_cash_budget_rjr
		set			
			amount		= '$amount',			
			date_added		= NOW()
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

?>