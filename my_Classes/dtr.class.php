<?php

function new_dtr_form(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
		<table border='0'>
		<tr>
			<td>
				<div class='form-div'>
					Employee : <br/>						  		
					".lib::getTableAssocEmp($r['employeeID'],'employeeID',"Select Employee","select * from employee where inactive = '0' and employee_void = '0' order by employee_lname asc",'employeeID','employee_lname')."								
				</div>
			</td>
			<td colspan='2'>
				<div class='form-div'>
					Remarks : <br>
					<input type='text' name='remarks' class='textbox'>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class='form-div'>
					Period Date From: <br><input type=\"text\" name=\"period_from\" id=\"period_from\" class=\"textbox datepicker\"  onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('period_from')\" autocomplete=off value='".((empty($r['period_from'])) ? "" : $r['period_from'])."' />
				</div>
			</td>
			<td>
				<div class='form-div'>
					Period Date To: <br><input type=\"text\" name=\"period_to\" id=\"period_to\" class=\"textbox datepicker\"  onmouseover=\"Tip('Choose a date.');\" onclick=\"fPopCalendar('period_to')\" autocomplete=off value='".((empty($r['period_to'])) ? "" : $r['period_to'])."' />
				</div>
			</td>
			
		</tr>
		<tr>
			<td>
				<div class='form-div'>
					Days : <br>
					<input type='text' name='day' class='textbox' placeholder='How many Work Days Present?' required>
				</div>
			</td>
			<td colspan='2'>
				<div class='form-div'>
					Overtime Hr/s : <br>
					<input type='text' name='overtime_hr' class='textbox' placeholder='Input Overtime Hr/s if Exist'>
				</div>
			</td>
		</tr>	
		<tr>
			<td>
				<div class='form-div'>
					LEGAL HOLIDAY :<br><input type='hidden' name='legal' value='0'>
					`				   <input type='checkbox' name='legal' value='1'>
				</div>
			</td>
			<td colspan='2'>
				<div class='form-div'>
					SPECIAL LEGAL HOLIDAY :<br><input type='hidden' name='special' value='0'>
												<input type='checkbox' name='special' value='1'>
				</div>
			</td>
		</td>
		<tr>
			<td>
				<div class='form-div'>
					<input type='submit' id='submit' name=b value='Submit' class=buttons >
					<input type='reset' value='Clear Form' class=buttons>
				</div>	
			</td>
		</tr>
		</table>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_dtr(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> DTR Entry \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_dtr($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$employeeID			= $form_data['employeeID'];
	$dtr_date 	  		= $form_data['dtr_date'];
	$period_from		= $form_data['period_from'];
	$period_to			= $form_data['period_to'];
	
	$count = 0;
	$q = mysql_query("Select * from dtr where employeeID = '$employeeID' and period_from = '$period_from' and period_to = '$period_to' and dtr_void = 0") or die (mysql_error());
	$count = mysql_num_rows($q);
	
	$remarks			= $form_data['remarks'];
	$dtr_date			= $form_data['dtr_date'];
	$overtime_hr		= $form_data['overtime_hr'];
	$day				= $form_data['day'];
	
	
	if($form_data['legal'] == 0){
		$legal = 0;
	}else{
		$legal = 1;
	}	
	
	if($form_data['special'] == 0){
		$special = 0;
	}else{
		$special = 1;
	}
	
	
	
	
	if(!empty($employeeID) && !empty($period_from) && !empty($period_to)) {

		if($count == 0){
		$_void = 0;
			
			$sql = "insert into dtr set
					employeeID='$employeeID',
					remarks='$remarks',
					period_from='$period_from',	
					period_to='$period_to',
					`day`='$day',					
					overtime_hr='$overtime_hr',
					legal_holiday='$legal',
					special_holiday='$special',
					date_encoded=NOW()
				";
		
			$query = mysql_query($sql);					
			$objResponse->alert("Query Successful!");
			$objResponse->script("window.location.reload();");		
		}else if($count > 0){
			$objResponse->alert("Employee already exist for this Period.");
		}else{
			$objResponse->alert(mysql_error());
		}
		
	}else {
		$objResponse->alert("Fill in all fields!");
	}
	
	$objResponse->script("toggleBox('demodiv',0)");
	return $objResponse;  			   
}

?>