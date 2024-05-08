<?php
function new_project_typeform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Project Type : <br>
				<input type='text' class='textbox' name='account_type'>
			</div>
		
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_project_type(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Project Type \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_project_type($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[account_type])) {
	
		$sql = "insert into project_type set
					project_type=\"$form_data[account_type]\"
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
	
function edit_project_typeform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							project_type
						where
							project_type_id='$id'");
					  
	$r = mysql_fetch_assoc($sql);
	
	$account_type	= $r['project_type'];


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Project Type : <br>
				<input type='text' class='textbox' name='account_type' value='$account_type'>
				<input type='hidden' name='account_type_id' value='$id'>
			</div>
			
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_project_type(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> account_type \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
function edit_project_type($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[account_type])){
	   
		$sql = "update project_type set
					project_type=\"$form_data[account_type]\"
				where
					project_type_id=\"$form_data[account_type_id]\"
				";
		
		$query = mysql_query($sql);
		
		
		if($query) {
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