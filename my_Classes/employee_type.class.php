<?php
function new_employee_type_form(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			
			<div class='form-div'>
				Employee Type : <br>
				<input type='text' name='employee_type' class='textbox'>
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_employee_type(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Employee Type \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_employee_type($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$employee_type			= $form_data['employee_type'];
	$employee_type_id		= $form_data['employee_type_id'];
	
	if(!empty($employee_type)) {

		$_void = 0;
		
		$sql = "insert into employee_type set
					employee_type=\"$employee_type\",
					employee_type_void=\"$_void\"
				";
		
		$query = mysql_query($sql);		
		
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
	
function edit_employee_type_form($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							employee_type
						where
							employee_type_id = '$id'");
					  
	$r = mysql_fetch_assoc($sql);

	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			
			<input type='hidden' name='employee_type_id' value='$id' >
			
			<div class='form-div'>
				Employee Type : <br>
				<input type='text' name='employee_type' class='textbox' value='$r[employee_type]'>
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_employee_type(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Employee Type \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
function edit_employee_type($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$employee_type_id 	  	= $form_data['employee_type_id'];
	$employee_type			= $form_data['employee_type'];
	
	if(!empty($employee_type)) {

	
		$sql = "
			update 
				employee_type 
			set
				employee_type=\"$employee_type\"
			where
				employee_type_id = '$employee_type_id'
			";
		
		$query = mysql_query($sql);		
		
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

?>