<?php
function new_employeeform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Employee No : <br>
				<input type='text' class='textbox' name='employee_no'>
			</div>
			
			<div class='form-div'>
				Last Name : <br>
				<input type='text' name='last_name' class='textbox'>
			</div>
			
			<div class='form-div'>
				First Name : <br>
				<input type='text' name='first_name' class='textbox'>
			</div>
			
			<div class='form-div'>
				Middle Name : <br>
				<input type='text' name='middle_name' class='textbox'>
			</div>
			
			
			<div class='form-div'>
				Contact # : <br>
				<input type='text' name='contact_no' class='textbox'>
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_employee(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Employee \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_employee($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[last_name])) {
	
		$sql = "insert into employee set
					employee_no 	= \"$form_data[employee_no]\",
					last_name		= \"$form_data[last_name]\",
					first_name		= \"$form_data[first_name]\",
					middle_name		= \"$form_data[middle_name]\",
					contact_no		= \"$form_data[contact_no]\"
				";
		
		$query = mysql_query($sql) or $objResponse->alert(mysql_error());		
		
		if(!mysql_error()) {
			$objResponse->alert("Query Successful!");
			$objResponse->script("window.location.reload();");
		}					
		else
			$objResponse->alert(mysql_error());
	}
	else {
		$objResponse->alert("Fill in all fields!");
	}
	
	$objResponse->script("toggleBox('demodiv',0)");
	return $objResponse;  			   
}
	
function edit_employeeform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							employee
						where
							employee_id='$id'");
					  
	$r = mysql_fetch_assoc($sql);
					  
	$employee_id	= $r['employee_id'];
	$employee_no	= $r['employee_no'];
	$last_name		= $r['last_name'];
	$first_name		= $r['fist_name'];
	$middle_name	= $r['middle_name'];
	$contact_no		= $r['contact_no'];

	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Employee No : <br>
				<input type='text' class='textbox' name='employee_no' value='$employee_no' >
				<input type='hidden' name='employee_id' value='$employee_id' >
			</div>
			
			<div class='form-div'>
				Last Name : <br>
				<input type='text' name='last_name' class='textbox' value='$last_name' >
			</div>
			
			<div class='form-div'>
				First Name : <br>
				<input type='text' name='first_name' class='textbox' value='$first_name' >
			</div>
			
			<div class='form-div'>
				Middle Name : <br>
				<input type='text' name='middle_name' class='textbox' value='$middle_name' >
			</div>
			
			
			<div class='form-div'>
				Contact # : <br>
				<input type='text' name='contact_no' class='textbox' value='$contact_no' >
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_employee(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Employee \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
	function edit_employee($form_data) {
		$objResponse = new xajaxResponse();
		
		
		if(!empty($form_data[last_name])){
		   
			$sql = "update employee set
						employee_no 	= \"$form_data[employee_no]\",
						last_name		= \"$form_data[last_name]\",
						first_name		= \"$form_data[first_name]\",
						middle_name		= \"$form_data[middle_name]\",
						contact_no		= \"$form_data[contact_no]\"
					where
						employee_id		=\"$form_data[employee_id]\"
					";
			
			$query = mysql_query($sql) or $objResponse->alert(mysql_error());
			
			
			if($query) {
				$objResponse->alert("Query Successful!");
				$objResponse->script("window.location.reload();");
			}					
		}
		else {
			$objResponse->alert("Fill in all fields!");
		}
		
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;  			   
	}
	
?>